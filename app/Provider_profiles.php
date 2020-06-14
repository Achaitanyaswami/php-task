<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider_profiles extends Model
{
    protected $fillable = [
        'user_id', 'description', 'created_at','updated_at'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
