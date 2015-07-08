<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\DirectSponsorBaseModel;
use App\Models\Payment;
use App\Models\User;
use App\Models\MailerDS as Mailer;



class Sponsor extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sponsors';

    protected $guarded = array();
    public static $rules = array(
        'name' => 'required|min:3',
        'skrill_acc' => 'unique:sponsors|email'
    );

    protected $fillable = array('name','skrill_acc');

    protected $mailer;

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function projects() {
        return $this->belongsToMany('App\Models\Project', 'project_sponsor')->withPivot('next_pay')->withTimestamps();
    }

    public function recipients() {
        return $this->belongsToMany('App\Models\Recipient', 'project_sponsor')->withPivot('next_pay')->withTimestamps();
    }

    public function recipientOfProject($pid){
        foreach($this->recipients as $recipient) {
            if ($recipient->project_id == $pid) {
                return $recipient;
            }
        }
        return false;
    }

    public function payments(){
        return $this->user->sentPayments();
    }

    public function paymentsOfProject($pid){
        return $this->payments()->where('project_id','=',$pid);
    }

    public function lastPaymentDate() {
    	$lastPaymentbySponsor = $this->payments()
                ->where('stat', 'accepted')
                ->orderBy('due_date', 'desc')
                ->first();
    	return  (is_null($lastPaymentbySponsor) ? " " : $lastPaymentbySponsor->due_date->format('d/m/Y'));
    }

    public function lastProjectPaymentDate($projectId) {
    	$lastPaymentbySponsor = $this->payments()
                ->where('project_id', $projectId)
                ->where('stat', 'accepted')
                ->orderBy('due_date', 'desc')
                ->first();
    	return  (is_null($lastPaymentbySponsor) ? " " : $lastPaymentbySponsor->due_date->format('d/m/Y'));
    }

    public function lastRecipientPaymentDate($receiverUserId) {
    	$lastPaymentbySponsor = $this->payments()
                ->where('receiver_id', $receiverUserId)
                ->where('stat', 'accepted')
                ->orderBy('due_date', 'desc')
                ->first();
    	return  (is_null($lastPaymentbySponsor) ? " " : $lastPaymentbySponsor->due_date->format('d/m/Y'));
    }

    public function hasNotPaidProjects() {
        $nowMinus5Days = new Carbon();
        $nowMinus5Days->subDays(5);
        $overduePayments = $this->recipients()
                ->where('next_pay', '<', $nowMinus5Days)
                ->count();
        return $overduePayments;
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
            'skrill_acc' => 'unique:sponsors|email'
        ]);

        if ($validator->fails()) {
            return $validator;
        } else {
            return $this;
        }
    }

    public function initialSetup(Request $request, Project $project, Recipient $recipient) {
        DB::transaction(function() use($request, $project, $recipient) {
            $user = new User;
            $user->createNewUser($request->all(), 'Sponsor');

            $this->fill($request->all());
            $this->user()->associate($user);

            if (empty($this->skrill_acc)) {
                $this->skrill_acc = $request->input('email');
            }
            $this->save();

            $date = new Carbon();
            $date->endOfMonth();
            $this->projects()->attach($project->id, ['recipient_id' => $recipient->id, 'next_pay' => $date]);

            $this->url = $this->getMailer()->sendSponsorConfirmationMail($this, $project);
        });
        return $this;
    }

    public function confirmAndCompleteSetup() {
        DB::transaction(function() {
            $this->confirmed = true;
            $this->save();

            $project = $this->projects()->first();
            $recipient = $this->recipientOfProject($project->id);
            $payment = new Payment;
            $payment->initialiseFirstSponsorProjectPayment($project, $this, $recipient);
        });
        return $this;
    }

    private function getMailer() {
        if (!isset($this->mailer)) {
            $this->mailer = new Mailer;
        }
        return $this->mailer;
    }
}
