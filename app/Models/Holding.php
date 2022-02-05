<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holding extends Model
{
    use HasFactory;

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract');
    }

    public function network()
    {
        return $this->belongsTo('App\Models\Network');
    }
}
