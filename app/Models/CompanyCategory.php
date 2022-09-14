<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyCategory extends Pivot
{
    protected $table = 'category_company';

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
