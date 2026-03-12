<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }
}
