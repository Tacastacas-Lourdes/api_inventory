<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'acronym'];

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
