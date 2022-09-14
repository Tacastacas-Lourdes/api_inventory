<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function specs(): HasMany
    {
        return $this->hasMany(Specification::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function company(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->using(CompanyCategory::class);
    }
}
