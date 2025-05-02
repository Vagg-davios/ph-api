<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pornstar extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'license',
        'wl_status',
        'link',
        'attributes',
        'aliases',
        'thumbnail_path'
    ];

    protected $casts = [
        'attributes' => 'array',
        'aliases' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }
}


