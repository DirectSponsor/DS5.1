<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\DirectSponsorBaseModel;
use App\Models\Invitation;
use App\Models\MailerDS as Mailer;


class Recipient extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recipients';

    protected $guarded = array();

    protected $fillable = array('name','skrill_acc','mepsa');

    protected $mailer;

    public function project() {
        return $this->belongsTo('App\Models\Project');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function sponsors() {
        return $this->belongsToMany('App\Models\Sponsor', 'project_sponsor')->withPivot('next_pay')->withTimestamps();
    }

    public function sentPayments(){
        return $this->user->sentPayments();
    }

    public function receivedPayments(){
        return $this->user->receivedPayments();
    }

    public function nextPaymentDueFromSponsor($sponsorId) {
    	$nextPaymentDueFromSponsor = $this->sponsors()
                ->where('sponsor_id', $sponsorId)
                ->first();
        if (is_null($nextPaymentDueFromSponsor)) {
            return '';
        }
        $dateObj = new Carbon($nextPaymentDueFromSponsor->pivot->next_pay);
        if ($dateObj->diffInYears() > 50) { /* Checks for no date set in next_pay */
            return '';
        } else {
            return $dateObj->format('d/m/Y');
        }
    }

    private function getMailer() {
        if (!isset($this->mailer)) {
            $this->mailer = new Mailer;
        }
        return $this->mailer;
    }

    public function validateCreateRequest(Request $request) {
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|min:3',
            'skrill_acc' => 'email|unique:recipients'
        ]);

        if ($validator->fails()) {
            return $validator;
        } else {
            return $this;
        }
    }

    public function initialSetup(Request $request, Invitation $invitation) {
        DB::transaction(function() use($request, $invitation) {
            $user = new User;
            $user->createNewUser($request->all(), 'Recipient');

            $this->fill($request->all());
            $this->confirmed = false;
            $this->user()->associate($user);
            $this->project()->associate($invitation->project);
            if (empty($this->skrill_acc)) {
                $this->skrill_acc = $request->input('email');
            }

            $this->save();
            $invitation->delete();
            $this->url = $this->getMailer()->sendRecipientConfirmationMail($this);
        });
        return $this;
    }

    public function confirmAndCompleteSetup() {
        $this->confirmed = true;
        $this->save();

        return $this;
    }

}
