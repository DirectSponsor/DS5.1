<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DirectSponsorBaseModel;

use App\Models\User;
use App\Models\Project;
use App\Models\Sponsor;
use App\Models\Recipient;

class Payment extends DirectSponsorBaseModel
{
        /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

    protected $dates = ['due_date'];

    protected $guarded = array();
    public static $rules = array();

//    public $autoHydrateEntityFromInput = true;    // hydrates on new entries' validation
//    public $forceEntityHydrationFromInput = true; // hydrates whenever validation is called

    public function project() {
        return $this->belongsTo("App\Models\Project");
    }

    public function sender() {
        return $this->belongsTo("App\Models\User", "sender_id");
    }

    public function receiver() {
        return $this->belongsTo("App\Models\User", "receiver_id");
    }

    public function initialiseFirstSponsorProjectPayment(Project $project, Sponsor $sponsor, Recipient $recipient) {
        DB::transaction(function() use($project, $sponsor, $recipient){
            $monthEndDate = new Carbon();
            $monthEndDate->endOfMonth();
            $recipient->pivot->next_pay = $monthEndDate->format('Y-m-d');
            $recipient->pivot->save();

            $this->newFirstMonthPayment($project, $monthEndDate, $sponsor->user->id, $recipient->user->id);
        });
        return true;
    }

    public function sponsorJoinProjectSetupFirstPayment(Project $project, Sponsor $sponsor, Recipient $recipient) {
        DB::transaction(function() use($project, $sponsor, $recipient){
            $monthEndDate = new Carbon();
            $monthEndDate->endOfMonth();
            $sponsor->projects()->attach($project->id, ['recipient_id' => $recipient->id, 'next_pay' => $monthEndDate]);

            $this->newFirstMonthPayment($project, $monthEndDate->format('M'), $sponsor->user->id, $recipient->user->id);
        });
        return true;
    }

    public function processAcceptedPaymentCoordinator() {
        $this->stat = 'accepted';
        $this->save();
        return $this;
    }

    public function processAcceptedPaymentRecipient() {
        DB::transaction(function() {
            $this->stat = 'accepted';
            $this->save();

            /*
             *  Create a group funds payment to coordinator from this recipient
             */
            $user = Auth::user();
            $this->newPaymentForCoordinator($user);

            /*
             *  The recipient accepts payment from sponsor,
             *  - Update next payment date for this sponsor for this project
             *  - Create next months pending payment for Recipient
             */
            $monthEndDate = $this->setNextPayDateForSponsor();
            $this->newNextMonthPayment($monthEndDate);
        });
        return true;
    }

    private function getNewPaymentDataFormatted($senderId=0, $receiverId=0, $type='', $stat='', $monthPay='', $monthEndDate) {
        return array(
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'type' => $type,
            'stat' => $stat,
            'pay_month' => $monthPay,
            'due_date' => $monthEndDate
            );
    }

    private function newPaymentForCoordinator(User $user) {
        $data = $this->getNewPaymentDataFormatted(
                $user->id,
                $this->project->coordinator->user_id,
                'group fund',
                'pending',
                $this->pay_month,
                $this->due_date
                );
        $this->newPayment($data);
        return true;
    }

    private function newNextMonthPayment($monthEndDate) {
        $data = $this->getNewPaymentDataFormatted(
                $this->sender->id,
                $this->receiver->id,
                'sponsoring',
                'pending',
                $monthEndDate->format('M'),
                $monthEndDate->format('Y-m-d')
                );
        $this->newPayment($data);
        return true;
    }

    private function newFirstMonthPayment(Project $project, $monthEndDate, $senderId, $receiverId) {
        $data = $this->getNewPaymentDataFormatted(
                $senderId,
                $receiverId,
                'sponsoring',
                'pending',
                $monthEndDate->format('M'),
                $monthEndDate->format('Y-m-d')
                );
        $this->newPayment($data, $project);
        return true;
    }

    private function newPayment($data, $project=null) {
        $payment = new Payment($data);
        if (is_null($project)) {
            $payment->project()->associate($this->project);
        } else {
            $payment->project()->associate($project);
        }
        $payment->save();
        return true;
    }

    private function setNextPayDateForSponsor() {
        $sponsor = $this->sender->account;
        $recipient = $sponsor->recipientOfProject($this->project_id);
        if ($recipient) {
            $lastPaymentDate = new Carbon($recipient->pivot->next_pay);
            if (! $lastPaymentDate instanceof Carbon) {
                $lastPaymentDate = new Carbon();
            }
            $recipient->pivot->next_pay = $lastPaymentDate->addDays(5)->endOfMonth()->format('Y-m-d');
            $recipient->pivot->save();
            return $lastPaymentDate;
        } else {
            abort(404, 'System Error : A valid Recipient-Sponsor relationship could not be found.');
        }
    }
}
