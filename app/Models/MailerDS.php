<?php

namespace App\Models;

//use Auth;
//use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use App\Models\Payment;
use App\Models\Sponsor;
use App\Models\Recipient;
use App\Models\Project;
use App\Models\User;


class MailerDS
{
    protected $hasher;
    protected $cryptService;

    public function sendSponsorConfirmationMail(Sponsor $sponsor, Project $project) {
        $recipient = $sponsor->recipients()->first();
        $hash = Crypt::encrypt($sponsor->id);
        $url = URL::route('sponsors.confirm',$hash);
        $mepsa = (empty($recipient->mepsa) ? "Not Available" : $recipient->mepsa);
        Mail::send('emails.sponsors.confirmation',
                array(
                    'name' => $sponsor->name,
                    'hash' => $hash,
                    'project' => $project,
                    'recipient' => $recipient
                ),
                function($message) use ($sponsor) {
                    $message->to($sponsor->user->email)
                            ->subject('Sponsor account created !');

                }
        );
        return $url;
    }

    public function sendRecipientConfirmationMail(Recipient $recipient) {
        $hash = Crypt::encrypt($recipient->id);
        $url = URL::route('recipients.confirm',$hash);
        Mail::send('emails.recipients.confirm', array(
            'name' => $recipient->name,
            'hash' => $hash
            ), function($message) use($recipient) {
            $message->to($recipient->user->email)->subject('Confirm your Direct Sponsor account');
        });
        return $url;
    }

    public function sendJoinProjectEmail(User $userSponsor, Project $project, Sponsor $sponsor, Recipient $recipient) {
        Mail::send('emails.sponsors.joining', array(
                'name'=>$sponsor->name,
                'id'=>$sponsor->id,
                'project' => $project,
                'recipientSkrill' => $recipient->skrill_acc
            ), function($message) use($userSponsor) {
            $message->to($userSponsor->email)->subject('Thank you for joining new project');
        });
        return true;
    }

    public function sendInvitation(Invitation $invitation) {
        Mail::send('emails.recipients.invitation', array(
                'name' => $invitation->project->name,
                'url' => $invitation->url
                ), function($message) use($invitation) {
            $message->to($invitation->sent_to)->subject('Invitation to Direct Sponsor');
        });
        return true;
    }
    /**
     * This email was being used when a payment was rejected.
     * May no longer be required.
     * Or may be used when a payment is very overdue from a sponsor.
     *
     * @param Payment $payment
     * @return boolean
     */
    public function sendEmailAdminAndCoordinator(Payment $payment) {
        $coordEmail = Project::find($payment->project->id)->coordinator->user->email;
        Mail::send('emails.sponsor_payment_denied', array(
            'recipient'=>$payment->recipient,
            'sponsor'=>$payment->sponsor,
            'project'=>$payment->project
        ),function($message) use($coordEmail) {
            $message->to(User::where('account_type','Admin')->first()->email)
                ->cc($coordEmail)
                ->subject('Sponsorship Denied !');
        });
        return true;
    }

}
