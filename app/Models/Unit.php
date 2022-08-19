<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [ 'unit_id', 'brand', 'model', 'serial', 'count','company_id','category_id', 'status_id',
        'user_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }



    public static function boot()
    {
        parent::boot();

        static::creating(function(Model $unit) {
//            $unit->count= Unit::where('category_id', $unit->category_id)->max('count')+1;
//            $unit->unit_id = $unit->company->acronym. '-' . $unit->category->category_name. '-' . str_pad($unit->count, 6, 0, STR_PAD_LEFT);
        $unit->unit_id = "Company-category-count";
        });
    }
}
