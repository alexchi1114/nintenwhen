<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectTag extends Model
{
    protected $fillable = [
        'code',
        'display_name',
        'color_hex',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function directs()
    {
        return $this->belongsToMany(Direct::class);
    }
}
