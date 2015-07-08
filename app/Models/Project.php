<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\DirectSponsorBaseModel;


class Project extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

    protected $guarded = array();

    protected $fillable = array('name','content','max_recipients','max_sponsors_per_recipient','currency','amount','euro_amount','gf_commission');

    /*
     * Relationships
     */

    public function coordinator() {
        return $this->hasOne("App\Models\Coordinator");
    }

    public function recipients() {
        return $this->hasMany("App\Models\Recipient");
    }

    public function sponsors() {
        return $this->belongsToMany('App\Models\Sponsor', 'project_sponsor')->withPivot('recipient_id', 'next_pay')->withTimestamps();
    }

    public function invitations() {
        return $this->hasMany("App\Models\Invitation");
    }

    public function payments() {
        return $this->hasMany("App\Models\Payment");
    }

    public function spends() {
        return $this->hasMany("App\Models\Spend");
    }

    public function paymentsOfSponsor($sid){
        return $this->sponsors()->where('id', $sid)->paymentsOfProject($this->id);
    }

    public function recipientOfSponsor($sid){
        return $this->sponsors()->where('sponsor_id', $sid)->first()->recipientOfProject($this->id);
    }

    public function confirmedPaymentsOfSponsor($sid){
        return $this->paymentsOfSponsor($sid)->where('stat','=','accepted');
    }

    /*
     * Methods
     */

    public function getFreeRecipient(){
        foreach ($this->recipients as $recipient){
            if(!$recipient->confirmed){
                continue;
            }
            if(is_null($recipient->sponsors)) {
                return $recipient;
            }
            $count = $recipient->sponsors->count();
            if($count < $this->max_sponsors_per_recipient) {
                return $recipient;
            }
        }
        return false;
    }

    /**
     *
     * @param Request $request
     * @return \App\Models\Project
     */
    public function validateCreateProjectData(Request $request) {
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:projects',
            'max_recipients' => 'required|numeric',
            'max_sponsors_per_recipient' => 'required|numeric',
            'currency' => 'required',
            'amount' => 'required|numeric',
            'euro_amount' => 'required|numeric',
            'gf_commission' => 'required|numeric',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $validator;
        } else {
            return $this;
        }
    }

    /**
     *
     * @param Request $request
     * @return \App\Models\Project
     */
    public function setupProject(Request $request) {
        DB::transaction(function() use($request) {
            $user = new User;
            $user->createNewUser($request->all(), 'Coordinator');
            $coordinator = new Coordinator();
            $coordinator->user()->associate($user);

            $this->fill($request->all());
            $this->url = str_replace(" ", "_", strtolower(e($request->input('name'))));
            $this->open = true;
            $this->save();

            $coordinator->project()->associate($this);
            $coordinator->save();
        });
        return $this;
    }

    public function validateEditProjectData(Request $request) {
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:projects',
            'max_recipients' => 'required|numeric',
            'max_sponsors_per_recipient' => 'required|numeric',
            'currency' => 'required',
            'amount' => 'required|numeric',
            'euro_amount' => 'required|numeric',
            'gf_commission' => 'required|numeric'
            ]);

        if ($validator->fails()) {
            return $validator;
        } else {
            return $this;
        }
    }

    public function updateProject(Request $request) {
        $this->fill($request->all());
        $this->url = str_replace(" ", "_", strtolower(e($this->name)));
        $this->save();
        return $this;
    }

}
