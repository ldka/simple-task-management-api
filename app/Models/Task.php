<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
    ];

    public $appends =[
        'attachment_path'
    ];

    public function getRouteKeyName(): string
    {
        return 'title';
    }

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            if($model->status == 3){
                $model->subTasks()->update(['status' => 3]);
            }
        });
    }

    public function authorize()
    {
        $task = Task::find($this->route('task'));

        return $task && $this->user()->can('update', $task);
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
        case 'To-do':
            $this->attributes['status'] = 1;
            break;
        case 'In Progress':
            $this->attributes['status'] = 2;
            break;
        case 'Done':
            $this->attributes['status'] = 3;
            break;
        }
    }

    public function getStatusTextAttribute()
    {
        if ($this->status == 1) {
            return "To-do";
        } else if ($this->status == 2) {
            return "In Progress";
        } else if ($this->status == 3) {
            return "Done";
        } else {
            return "to-do";
        }
    }

    public function getSubTaskCountAttribute()
    {
        return $this?->subTasks?->count();
    }

    public function getAttachmentPathAttribute()
    {
        if ($this->attachment) {
            return url('/') . '/storage/' . $this->attachment;
        }
        return null;
    }

    public function getSubTaskDoneCountAttribute()
    {
        return $this->subTasks()->where('status', 3)->count();
    }

    public function subTasks()
    {
        return $this->hasMany(self::class);
    }

    public function task()
    {
        return $this->belongsTo(self::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
