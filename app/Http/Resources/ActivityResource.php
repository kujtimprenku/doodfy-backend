<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Rating;


class ActivityResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'description'=>$this->description,
            'image' => $this->thumbnail($this->image, 300),
            'location' => $this->location,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nr_joined' => $this->hasJoined->count() > 0 ? $this->hasJoined->sum('has_joined') : 0,
            'has_joined' => auth('api')->check() ? ($this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_joined', 1)->count() > 0 ? true : false) : false,
            'has_saved' => auth('api')->check() ? ($this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_saved', 1)->count() > 0 ? true : false) : false,
            'has_xp' => $this->has_xp == 1 ? true : false,
            'my_activity'=> auth('api')->check() ? ($this->user_id === auth('api')->user()->id) : false,
            'is_occurrence' => $this->is_occurrence == 1 ? true : false,
            'nr_occurrences' => $this->nrOccurrences($this->parent_id),
            'is_active' => $this->end_date > now(),
            'user' => $this->usernameByRole($this->user->id, $this->user->role_id),
            'rating' =>$this->findNrRaters($this->id),
        ];
    }
}
