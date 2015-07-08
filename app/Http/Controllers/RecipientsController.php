<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;
use Illuminate\Contracts\Validation\Validator as Validator;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;

use App\Models\Recipient;
use App\Models\Project;
use App\Models\Invitation;


class RecipientsController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index', 'myRecipients', 'perProject', 'show'
        )));
    }

    public function index(){
        $recipients = Recipient::all();
        $this->render('recipients.index','Recipients',array(
            'recipients' => $recipients
        ));
        return $this->layout;
    }

    public function show($id){
        $recipient = Recipient::where('id', $id)->with('sponsors')->first();
        if (is_null($recipient)) {
            abort(404, 'Recipient Not Found');
        }

        $this->render('recipients.show','Recipient: '.$recipient->name,
                array(
                    'recipient' => $recipient
                ));
        return $this->layout;
    }

    public function myRecipients(){
        $user = Auth::user();
        switch($user->account_type){
            case 'Admin':
                return $this->index();
            case 'Coordinator':
                $recipients = $user->account->project->recipients;
                $this->render('recipients.index','My Recipients',array(
                    'recipients' => $recipients
                ));
                return $this->layout;
            default:
                abort(403, 'Your account type is not valid for this view');
        }
    }

    public function perProject($id){
        $project = Project::find($id);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }

        $this->render('recipients.index',
            'Project : '.$project->name.' > Recipients',
            array('recipients' => $project->recipients));
        return $this->layout;
    }

    public function join($url){
        $invitation = Invitation::where('url','=',$url)->first();
        if (is_null($invitation)){
            abort(404, 'Invitation Not Found');
        }
        if ( !$invitation->project->open ) {
            $this->render('recipients.closed_project');
        }
        $this->render('recipients.join',
            'Recipient register for the project : '.$invitation->project->name,
                array('url'=>$url));
        return $this->layout;
    }

    public function register(Request $request, $url, Hasher $hashService, Encrypter $cryptService, Mailer $mailer){
        $invitation = Invitation::where('url','=',$url)->with('project')->first();
        if (is_null($invitation)) {
            abort(404, 'Invitation Not Found');
        }

        $recipient = new Recipient;
        $result = $recipient->validateCreateRequest($request);
        if ($result instanceof Validator) {
            Session::put('error','There were some errors !');
            return redirect()->route('recipients.join',$invitation->url)
                        ->withErrors($result)
                        ->withInput();
        }

        $recipient->initialSetup($request, $invitation);
        if (config('app.debug')) {
            $link = "<a href='".$recipient->url."'>Confirm Email Link</a>";
            Session::put('success',
                    'Your recipient account was successfully created ! an email has been sent to you to confirm your email '
                    .$recipient->user->email. '   '.$link);
        } else {
            Session::put('success',
                    'Your recipient account was successfully created ! an email has been sent to you to confirm your email '
                    .$recipient->user->email);
        }
        return redirect()->route('login.form');
    }

    public function confirm($hash, Encrypter $cryptService){
        $id = $cryptService->decrypt($hash);
        $recipient = Recipient::find($id);
        if (is_null($recipient)) {
            abort(404, 'Recipient Not Found');
        }
        $recipient->confirmAndCompleteSetup();

        Auth::logout();
        Session::put('success','Your email adress was confirmed ! <br> You can now Login to your account ');
        return redirect()->route('login.form');
    }

}
