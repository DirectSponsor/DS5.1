<?php

namespace App\Models;

use App\Models\DirectSponsorBaseModel;
use App\Models\Sponsor;


class Coordinator extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coordinators';

    protected $guarded = array();
    protected $fillable = array();

    public function project() {
        return $this->belongsTo("App\Models\Project");
    }

    public function user() {
        return $this->belongsTo("App\Models\User");
    }

    public function receivedPaymentsbySponsor($sid) {
        $sponsor = Sponsor::where('id', '=', $sid);
        return $this->user->receivedPayments()->where('receiver_id', '=', $sponsor->user->id);
    }

    public function receivedPayments() {
        return $this->user->receivedPayments();
    }

}
