<?php
namespace Tests\Feature;

use ConcertFactory;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationEmail;
use App\Concert;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use App\Facades\TicketCode;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
        Mail::fake();
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_tocken' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertValidationError('email');
    }

    /** @test */
    function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);
        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function ticket_quantiry_must_be_at_least_1_to_purpose_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ]);
        $this->assertValidationError('payment_token');
    }

    private function orderTickets($concert, $params)
    {
        $saveRequest = $this->app['request'];
        $this->response = $this->json('POST', "/concerts/{$concert->id}/orders", $params);

        $this->app['request'] = $saveRequest;
    }

    private function assertResponseStatus($status)
    {
        $this->response->assertStatus($status);
    }

    private function decodeResponseJson()
    {
        return $this->response->decodeResponseJson();
    }

    private function seeJsonSubset($data)
    {
        $this->response->assertJson($data);
    }

    private function assertValidationError($field)
    {
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey($field, $this->decodeResponseJson()['errors']);
    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $this->assertResponseStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {
        OrderConfirmationNumber::shouldReceive('generate')->andReturn('ORDERCONFIRMATION1234');
        TicketCode::shouldReceive('generateFor')->andReturn('TICKETCODE1', 'TICKETCODE2', 'TICKETCODE3');

        $concert = ConcertFactory::createPublished([
            'ticket_price' => 3250,
            'ticket_quantity' => 3,
        ]);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(201);

        $this->seeJsonSubset([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 9750,
            'tickets' => [
              ['code' => 'TICKETCODE1'],
              ['code' => 'TICKETCODE2'],
              ['code' => 'TICKETCODE3'],
            ],
        ]);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertEquals(3, $concert->orders()->count());
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        Mail::assertSent(OrderConfirmationEmail::class, function ($mail) use ($order){
            return $mail->hasTo('john@example.com')
                && $mail->order->id == $order->id;
        });
    }

    /** @test */
    function cannot_purchse_tickets_to_an_unpublished_concert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();
        $concert->addTickets(3);
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    function cannot_purchase_more_tickets_than_remain()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(50);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(422);
        $order = $concert->orders()->where('email' , 'john@example.com')->first();
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());

    }

}

