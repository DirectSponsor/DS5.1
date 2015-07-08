<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use App\Models\DirectSponsorBaseModel;
use App\Models\MailerDS as Mailer;

class Invitation extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invitations';

    protected $guarded = array();

    protected $fillable = array('sent_to');

    protected $mailer;

    public function project() {
        return $this->belongsTo("App\Models\Project");
    }

    public function validateRequest(Request $request) {
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'sent_to' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $validator;
        } else {
            return $this;
        }
    }

    public function saveData(Request $request, $project) {
        DB::transaction(function() use($request, $project) {
            $this->fill($request->all());
            /*
             *  Generating the invitation's url
             */
            do{
                $url = str_replace('/','',substr(Crypt::encrypt($project->id.e($request->input('sent_to')).rand(15,1000)), 51));
                $count = self::where('url','=',$url)->count();
            }while($count > 0);
            $this->url = $url;

            /*
             * Link Project
             */
            $this->project()->associate($project);

            $this->save();
        });
        return $this;
    }

    public function send() {
        $this->getMailer()->sendInvitation($this);
        return $this;
    }

    private function getMailer() {
        if (!isset($this->mailer)) {
            $this->mailer = new Mailer;
        }
        return $this->mailer;
    }
}
