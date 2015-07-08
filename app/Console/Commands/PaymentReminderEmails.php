<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Mail\Mailer;
use Carbon\Carbon;

use App\Models\Project;
use App\Models\Sponsor;
use App\Models\Recipient;


class PaymentReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payreminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for overdue payments.';

    protected $emailSentCount = 0;

    protected $today;
    protected $startOfCurrentMonth;
    protected $endOfCurrentMonth;

    protected $mailer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->today = new Carbon();
        $this->startOfCurrentMonth = new Carbon();
        $this->startOfCurrentMonth->startOfMonth();
        $this->endOfCurrentMonth = new Carbon();
        $this->endOfCurrentMonth->endOfMonth();

        Log::info("*** Payment Reminder Emails Started ***");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("*** Payment Reminder Emails Started ***");
        /*
         * @var App\Models\Project $project
         * @var App\Models\Sponsor $sponsor
         */
        foreach (Project::all() as $project) {
            $this->identifyOverdueConfirmations($project);

            if ($this->today->diffInDays($this->endOfCurrentMonth) <= 5) {
                $this->identifySponsorRemindersDue($project);
            }
        }
        Log::info("*** Payment Reminder Emails Finished - Sent ".$this->emailSentCount." Emails ***");
        return true;
    }

    private function identifyOverdueConfirmations(Project $project) {
        $sponsors = $project->sponsors()
                ->wherePivot('next_pay', '<', $this->startOfCurrentMonth)
                ->get();

        $subject='Reminder: Payment not confirmed !';

        foreach($sponsors as $sponsor) {
            $reminderIntervalCount = $this->getReminderIntervalCount($sponsor->pivot->next_pay);
            Log::info("*** Payment Reminder - Calculated Interval Count = ".$reminderIntervalCount
                    ." for Sponsor:".$sponsor->name
                    ." for Project:".$project->name."  ***");
            if ($reminderIntervalCount == 1) {
                $emailTemplate = 'emails.recipients.no_payment';
            } else {
                $emailTemplate = 'emails.recipients.no_payment_after';
            }
            $this->sendEmailToOverDueSponsorRecipient($sponsor, $emailTemplate, $subject);
        }
        return true;
    }

    private function identifySponsorRemindersDue(Project $project) {
        $sponsors = $project->sponsors()
                ->where('next_pay', '=', $this->endOfCurrentMonth)
                ->get();

        $emailTemplate = 'emails.sponsors.monthly_reminder';
        $subject = 'Reminder: Monthly payment due';

        foreach($sponsors as $sponsor) {
            $this->sendEmailReminderToSponsor($sponsor, $emailTemplate, $subject);
        }
        return true;
    }

    private function sendEmailToOverDueSponsorRecipient(Sponsor $sponsor, $emailTemplate, $subject='Reminder: Payment not confirmed !') {
        $recipient = Recipient::find($sponsor->pivot->recipient_id);
        $this->mailer->send($emailTemplate, array(
            'recipient' => $recipient,
            'sponsor' => $sponsor,
            'dueDate' => Carbon::createFromFormat('Y-m-d', substr($sponsor->pivot->next_pay, 0, 10))->format('d/m/Y')
        ),function($message) use($recipient, $subject) {
            $message->to($recipient->user->email)
                ->subject($subject);
        });
        Log::info("*** Payment Reminder - Sent to Recipient ".$recipient->user->email." ***");
        $this->emailSentCount++;
        return true;
    }

    private function sendEmailReminderToSponsor(Sponsor $sponsor, $emailTemplate, $subject='Reminder: Monthly payment due !') {
        $recipient = Recipient::find($sponsor->pivot->recipient_id);
        $this->mailer->send($emailTemplate, array(
            'recipient' => $recipient,
            'sponsor' => $sponsor,
            'dueDate' => Carbon::createFromFormat('Y-m-d', substr($sponsor->pivot->next_pay, 0, 10))->format('d/m/Y')
        ),function($message) use($sponsor, $subject) {
            $message->to($sponsor->user->email)
                ->subject($subject);
        });
        Log::info("*** Monthly Reminder - Sent to Sponsor ".$sponsor->user->email." ***");
        $this->emailSentCount++;
        return true;
    }

    private function getReminderIntervalCount($paymentDueDate) {
        $paymentDueDateObj = new Carbon($paymentDueDate);
        $today = new Carbon();
        $daysSinceDueDate = $paymentDueDateObj->diffInDays($today);
        return round($daysSinceDueDate / 5);
    }
}
