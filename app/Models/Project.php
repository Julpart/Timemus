<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User as projectUser;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];

    protected $attributes = [
        'estimate_time' => 0,
    ];

    public function users()
    {
        return $this->belongsToMany(projectUser::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
