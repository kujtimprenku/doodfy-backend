<?php

namespace App\Http\Resources;

use App\Activity;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityGalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => User::find($this->user_id)->only('id', 'username'),
            'activity_id' => $this->activity_id,
            'image' => $this->img_name,
            'my_image' => auth('api')->user() ? ($this->user_id == auth('api')->user()->id) : false
        ];
    }
}
