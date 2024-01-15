<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $commentsData = $this->comments->map(function ($comment) {
            return [
                "id" => $comment->id,
                "post_id" => $comment->post_id,
                "user" => [
                    "id" => $comment->user->id,
                    "name" => $comment->user->name,
                    "image" => $comment->user->image !== null ? url($comment->user->image) : '',
                    "email" => $comment->user->email,
                ],
                "text" => $comment->text,
                "created_at" => $comment->created_at->thaidate('j F Y เวลา H:i') . ' น.',
            ];
        });
        return [
            "id" => $this->id,
            "text" => $this->text,
            "image" => $this->image != null ? url($this->image) : '',
            "comment" => $commentsData,
            "user" => [
                "id" => $this->users->id,
                "name" => $this->users->name,
                "image" => url($this->users->image),
                "email" => $this->users->email,
            ],
            "created_at" => $this->created_at->thaidate('j F Y เวลา H:i') . ' น.'
        ];
    }
}
