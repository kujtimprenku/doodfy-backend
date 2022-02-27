<?php

namespace App\Traits;

use App\Club;
use Illuminate\Database\Eloquent\Model;
use App\Membership;
use App\User;

trait Membershipable
{

    /**
     * @param $model
     * @return \Illuminate\Database\Eloquent\Collection|Membership[]
     */
    public function requestMembership(Model $recipient)
    {
        $membership = (new Membership())->fillRecipient($recipient)->fill(['status_id' => 0]);

        $this->memberships()->save($membership);

        return $membership;
    }

    
    /**
     * @return \Illuminate\Database\Eloquent\Collection|Membership[]
     */
    public function getPendingMemberships()
    {
        return $this->findMemberships(0)->get();
    }

    /**
     * @param $model
     * @return bool
     */
    public function acceptMemberRequest(Model $recipient)
    {
        $accepted = $this->findMembership($recipient)->whereRecipient($this)->where('status_id', 0)->update([
            'status_id' => 1,
        ]);

        return $accepted;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Membership[]
     */
    public function getAcceptedMemberships()
    {
        return $this->findMemberships(1)->get();
    }


    /**
     * @param $model
     * @return bool
     */
    public function cancelMemberRequest(Model $recipient)
    {
        $cancelled = $this->findMembership($recipient)->whereRecipient($this)->update([
            'status_id' => 2,
        ]);

        return $cancelled;
    }

    /**
     * @param $model
     * @return bool
     */
    public function hasMemberRequestFrom(Model $recipient){

        return $this->findMembership($recipient)->whereSender($recipient)->where('status_id', 0)->exists();
    }


    /**
     * @param Model $recipient
     *
     * @return bool
     */
    public function isMember(Model $recipient)
    {
        return $this->findMembership($recipient)->where('status_id', 1)->exists();
    }

    /**
     * @param $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function findMembership(Model $recipient){

        return Membership::betweenModels($this, $recipient);
    }

    /**
     * @param $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function findMemberships($status = null)
    {

        $query = Membership::where(function ($query) {
            $query->where(function ($q) {
                $q->whereSender($this);
            })->orWhere(function ($q) {
                $q->whereRecipient($this);
            });
        });

        //if $status is passed, add where clause
        if (!is_null($status)) {
            $query->where('status_id', $status);
        }

        return $query;
    }


}


?>


