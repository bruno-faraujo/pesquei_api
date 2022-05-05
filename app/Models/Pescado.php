<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Pescado extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit('contain', 300, 300)
            ->optimize();

        $this->addMediaConversion('foto')
            ->fit('contain', 1000, 1000)
            ->optimize();
    }

    public function ponto()
    {
        return $this->belongsTo(Ponto::class);
    }

    public function peixe()
    {
        return $this->belongsTo(Peixe::class);
    }

}
