<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['employee_id', 'last_name', 'first_name', 'middle_name', 'suffix', 'role_request',
        'email', 'password', ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /***** RELATIONSHIPS *****/

    /**
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->using(CompanyUser::class);
    }

    /***** SCOPES *****/

    /**
     * @param  Builder  $query
     * @param  bool  $bool
     * @return Builder
     */
    public function scopeApproved(Builder $query, bool $bool = true): Builder
    {
        if ($bool) {
            return $query->whereNotNull('approved_at');
        }

        return $query->whereNull(['approved_at', 'disapproved_at']);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUnapproved(Builder $query): Builder
    {
        return $query->approved(false);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDisapproved(Builder $query): Builder
    {
        return $query->whereNotNull('disapproved_at');
    }

    /**
     * @param  Builder  $query
     * @param  bool  $bool
     * @return Builder
     */
    public function scopeDeactivated(Builder $query, bool $bool = true): Builder
    {
        if ($bool) {
            return $query->whereNotNull('deactivated_at');
        }

        return $query->whereNull('deactivated_at');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActivated(Builder $query): Builder
    {
        return $query->deactivated(false);
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @param  bool  $bool
     * @return bool
     */
    public function isApproved(bool $bool = true): bool
    {
        return isset($this->approved_at) == $bool;
    }

    /**
     * @return bool
     */
    public function isDisapproved(): bool
    {
        return isset($this->disapproved_at);
    }

    /**
     * @return bool
     */
    public function approve(): bool
    {
        if ($this->isApproved(false) || $this->isDisapproved()) {
            $this->approved_at = now();
            $this->disapproved_at = null;
            $this->roles()->sync($this->companies->isNotEmpty() == true ? [3] : [2]);

            return $this->save();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function disapprove(): bool
    {
        if ($this->isApproved(false)) {
            $this->disapproved_at = now();

            return $this->save();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function activate(): bool
    {
        $this->deactivated_at = null;

        return $this->save();
    }

    /**
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->deactivated_at = now();

        return $this->save();
    }

    /**
     * @param  bool  $bool
     * @return bool
     */
    public function isActivated(bool $bool = true): bool
    {
        return isset($this->deactivated_at) == $bool;
    }

    /**
     * @return bool
     */
    public function isDeactivated(): bool
    {
        return isset($this->deactivated_at);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $collect = collect($activity->properties)->get('attributes');
//            dd($collect);
        if (count($collect) == 1) {
            if (isset($collect['approved_at'])) {
                $activity->description = 'User has been approved.';
//            $activity->description = "User has been " . ($collect['approved_at'] ? 'approved': 'disapproved') . ".";
            } elseif (isset($collect['disapproved_at'])) {
                $activity->description = 'User has been disapproved.';
            } elseif (isset($collect['deactivated_at'])) {
                $activity->description = 'User has been '.($collect['deactivated_at'] ? 'deactivated' : 'activated').'.';
            } elseif (isset($collect['company_id'])) {
                $activity->description = 'User has been transfer to another company.';
            }
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly([
                'employee_id',
                'last_name',
                'first_name',
                'middle_name',
                'suffix',
                'company_id',
                'category_id',
                'approved_at',
                'disapproved_at',
                'deactivated_at',
            ])
            ->logOnlyDirty();
    }
}
