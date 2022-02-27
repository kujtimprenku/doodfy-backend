<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Subcategory;

class ActivityEdit extends JsonResource
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
            'category_id' => Subcategory::find($this->subcategory_id)->category_id,
            'subcategory_id' => $this->subcategory_id,
            'start_date' => date('D M d Y H:i:s e P', strtotime($this->start_date)),
            'start_time' => date('H:i', strtotime($this->start_date)),
            'end_date' => date('D M d Y H:i:s e P', strtotime($this->end_date)),
            'end_time' => date('H:i', strtotime($this->end_date)),
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'my_activity'=>$this->user_id === auth('api')->user()->id,
            'is_occurrence' => $this->is_occurrence == 1 ? true : false,
            'max_persons' => $this->max_persons,
            'location' => $this->location,
            'has_xp' => $this->has_xp == 1 ? true : false,
        ];
    }
}
