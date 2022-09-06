<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'acronym', 'status'];

    /***** RELATIONSHIP *****/

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(CompanyUser::class);
    }

    /***** OTHER FUNCTIONS *****/

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('company')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}")
            ->logOnly(['name', 'acronym'])
            ->logOnlyDirty();
    }
}
