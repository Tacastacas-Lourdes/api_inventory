<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $appends = [
        'uniqueId',
    ];

    protected $fillable = ['unit_id', 'brand', 'model', 'serial', 'count', 'company_id', 'category_id', 'status_id',
        'user_id', ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SpecDetails::class);
    }

    public function specs(): BelongsToMany
    {
        return $this
            ->belongsToMany(Specification::class, 'spec_details', 'unit_id', 'specification_id')
            ->withPivot('details')
            ->withTimestamps();
    }

//    public function getUniqueIdAttribute()
//    {
//        return implode('-', [$this->company->acronym, $this->category->name, str_pad($this->count, 6, 0, STR_PAD_LEFT)]);
//    }
    public static function boot()
    {
        parent::boot();

        static::creating(function (Model $unit) {
            $count = $unit->category->unit()->count();
            $unit->unit_id = $unit->company->acronym.'-'.$unit->category->name.'-'.str_pad($count + 1, 6, 0, STR_PAD_LEFT);
        });
    }
}
