<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    // Relation Many to one
    public function user(){
    	return $this->belongsTo('\App\User', 'user_id');
    }
}
