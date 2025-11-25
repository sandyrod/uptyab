<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afluencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'evento_id',
        'cantidadvotantes',
        'hora',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}