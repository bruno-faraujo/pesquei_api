<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peixe extends Model
{
    use HasFactory;

    public function pescados()
    {
        return $this->hasMany(Pescado::class);
    }

}
