<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function spec(): HasMany
    {
        return $this->hasMany(Specification::class);
    }

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
