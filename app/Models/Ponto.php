<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pescados()
    {
        return $this->hasMany(Pescado::class);
    }

}
