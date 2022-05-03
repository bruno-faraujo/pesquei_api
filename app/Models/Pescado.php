<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pescado extends Model
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

    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }

}
