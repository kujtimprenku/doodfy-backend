<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Subcategory;

class PlaceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'location' => $this->location,
            'address' => $this->address,
            'start_time' => date('F d, H:i', strtotime($this->start_time)),
            'end_time' => date('F d, H:i', strtotime($this->end_time)),
        ];
    }
}
