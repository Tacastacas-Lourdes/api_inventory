<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserApproval extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'last_name', 'first_name', 'middle_name', 'suffix', 'role_request', 'email', 'password'];

}
