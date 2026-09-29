<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'is_active', 'required_fields'];

    protected $casts = [
        'is_active' => 'boolean',
        'required_fields' => 'array',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(CategoryField::class)->orderBy('order', 'asc');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
