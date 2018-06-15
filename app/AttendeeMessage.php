<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendeeMessage extends Model
{
    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function withChunkedRecipients($chunk, $callback)
    {
        $this->orders()->chunk($chunk, function ($orders) use ($callback) {
            $callback($orders->pluck('email'));
        });

    }

    public function orders()
    {
        return $this->concert->orders();
    }
}
