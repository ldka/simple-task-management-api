<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;


class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $tasks = $user->tasks()->whereNull('task_id')->with('subTasks');
        if ($request->title)
        {
            $tasks = $tasks->where('title', $request->title)->first()->subTasks();
        }
        else
        {
            $tasks = $tasks->with('subTasks');
        }

        if($request->status){
            $tasks->where('status', $request->status);
        }

        if ($request->search) {
            $tasks->whereLike('title', '%'. $request->search .'%');
        }

        if ($request->sort) {
            if($request->sort == "date-asc"){
                $tasks->orderBy('created_at', 'asc');
            }
            elseif($request->sort == "date-desc") {
                $tasks->orderBy('created_at', 'desc');
            }
            elseif ($request->sort == "title-asc") {
                $tasks->orderBy('title', 'asc');
            }
            elseif ($request->sort == "title-desc") {
                $tasks->orderBy('title', 'desc');
            }
        }

        $itemPerPage = $request->itemsPerPage ?? 6;

        return new TaskCollection($tasks->paginate($itemPerPage)->appends($request->query()));
    }

    public function show(Request $request, $task)
    {
        $task = Task::where('title', $task)->firstOrFail();

        return $this->sendResponse(true, new TaskResource($task), 'Data retrieved.', 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = new Task();

        if($request->parentTitle){
            $parentTask = Task::where('title', $request->parentTitle)->whereNull('task_id')->firstOrFail();
            $task->task_id = $parentTask->id;
        }

        $task->fill($request->all());
        $task->save();

        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $imageExtension = $image->extension();
            $fileName = uniqid() . "." . $imageExtension;
            $filePath = 'task_images/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($image));

            $task->attachment = $filePath;
            $task->save();
        }

        return $this->sendResponse(true, new TaskResource($task), 'Data stored.', 200);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->all());

        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $imageExtension = $image->extension();
            $fileName = uniqid() . "." . $imageExtension;
            $filePath = 'task_images/' . $fileName;

            if($task->attachment && $request->attachment){
                Storage::disk('public')->delete($task->attachment);
            }

            Storage::disk('public')->put($filePath, file_get_contents($image));

            $task->attachment = $filePath;
            $task->save();
        }

        return $this->sendResponse(true, new TaskResource($task), 'Data updated.', 200);
    }

    public function destroy(Request $request, Task $task)
    {
        if($request->parentTitle){
            $deleteSubTask = Task::where('title', $task->title)->whereHas('task', function (Builder $query) use($request) {
                $query->where('title', $request->parentTitle);
            })->firstOrFail();
            $deleteSubTask->delete();
        }
        else{
            $task->delete();
        }

        return $this->sendResponse(true, null, 'Data deleted.', 200);
    }
}
