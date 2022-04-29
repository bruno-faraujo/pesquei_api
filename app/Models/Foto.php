<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;

    public function ponto()
    {
        return $this->belongsTo(Ponto::class);
    }

    public function peixe()
    {
        return $this->belongsTo(Peixe::class);
    }
}
