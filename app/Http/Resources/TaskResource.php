<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'statusText' => $this->statusText,
            'status' => $this->status,
            'subTaskCount' => $this->subTaskCount,
            'subTaskDoneCount' => $this->SubTaskDoneCount,
            'attachment' => $this->attachment_path,
            'subTasks' => new TaskCollection($this->whenLoaded('subTasks')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
