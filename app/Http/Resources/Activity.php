<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Subcategory;

class Activity extends JsonResource
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
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'description' =>$this->description,
            'image' => $this->image,
            'start_date' => date('F d, H:i', strtotime($this->start_date)),
            'end_date' => date('F d, H:i', strtotime($this->end_date)),
            //'min_persons' => $this->min_persons,
            'max_persons' => $this->max_persons,
            'location' => $this->location,
            'subcategory' => Subcategory::find($this->subcategory_id)->only('id', 'name'),
            'category' => $this->categoryFromSubcategory($this->subcategory_id),
            'nr_joined' => $this->hasJoined->count() > 0 ? $this->hasJoined->sum('has_joined') : 0,
            'has_joined' => auth('api')->check() ? ($this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_joined', 1)->count() > 0 ? true : false) : false,
            'has_saved' => auth('api')->check() ? ($this->hasJoined->where('user_id', auth('api')->user()->id)->where('has_saved', 1)->count() > 0 ? true : false) : false,
            'my_activity'=> auth('api')->check() ? ($this->user_id === auth('api')->user()->id) : false,
            'has_xp' => $this->has_xp == 1 ? true : false,
            'allow_xp'=>$this->start_date < now(),
            'is_active' => $this->end_date > now(),
            'is_occurrence' => $this->is_occurrence == 1 ? true : false,
            'nr_occurrences' => $this->nrOccurrences($this->parent_id),
            'user' => $this->user->only(['id', 'username', 'profile_image']),
            'city' => $this->city->only(['id', 'name']),
            'users' => $this->usersHasJoined($this->id),
            'rating' =>$this->findNrRaters($this->id),
            'comments' =>$this->comments($this->id),
        ];
    }
}
