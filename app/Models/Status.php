<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function unit(): HasOne
    {
        return $this->hasOne(unit::class);
    }
}
