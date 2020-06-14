<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service_request extends Model
{
    protected $fillable = [
        'provider_id','customer_id','service_id','price','status', 'created_at','updated_at'
    ];
}
