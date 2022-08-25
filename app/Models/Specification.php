<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SpecDetails::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'spec_details')->using(SpecDetails::class)->withTimestamps();
    }
}
