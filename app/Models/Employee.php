<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'birthdate',
        'position',
        'salary',
        'work_place',
        'gender'
    ];


}
