<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider_service extends Model
{
	protected $fillable = [
        'user_id', 'service_id', 'price','created_at','updated_at'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
