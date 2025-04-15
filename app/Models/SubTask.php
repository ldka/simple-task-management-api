<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTask extends Model
{
    use SoftDeletes;

    public $table = 'tasks';

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
