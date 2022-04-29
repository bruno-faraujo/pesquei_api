<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peixes()
    {
        return $this->belongsToMany(Peixe::class);
    }

    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }
}