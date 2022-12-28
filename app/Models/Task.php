<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['due_date','title','user_id','status'];

    public function sub_tasks()
    {
        return $this->hasMany('App\Models\SubTask');
    }
}
