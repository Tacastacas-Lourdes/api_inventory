<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SpecDetails extends Pivot
{
    use HasFactory;

    protected $table = 'spec_details';

    protected $fillable = ['details', 'spec_id', 'unit_id'];

    public function spec()
    {
        return $this->belongsTo(Specification::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
