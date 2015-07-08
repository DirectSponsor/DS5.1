<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Validation\Validator as Validator;

use App\Models\Sponsor;
use App\Models\Project;


class SponsorsController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index','mySponsors','perProject','show','suspend','activate'
        )));
    }

    public function index(){
        $sponsors = Sponsor::all();
        $this->render('sponsors.index','Sponsors',array('sponsors'=>$sponsors));
        return $this->layout;
    }

    public function show($id){
        $sponsor = Sponsor::find($id);
        if(is_null($sponsor)){
            abort(404, 'Sponsor Not Found');
        }
        $this->render('sponsors.show','Sponssor: '.$sponsor->user->username,array(
            'sponsor' => $sponsor
        ));
    }

    public function mySponsors(){
        $user = Auth::user();
        switch($user->account_type){
            case 'Admin' :
                return redirect()->route('sponsors.index');
            case 'Coordinator' :
                $sponsors = $user->account->project->sponsors;
                $this->render('sponsors.index','My Sponsors',array(
                    'sponsors' => $sponsors
                ));
                break;
            case 'Recipient' :
                $sponsors = $user->account->sponsors;
                $this->render('sponsors.index','My Sponsors',array(
                    'sponsors' => $sponsors
                ));
                break;
            default:
                abort(403, 'Your account type is not valid for this view.');
        }
        return $this->layout;
    }

    public function join($url){
        $project = Project::where('url','=',trim($url))->first();
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        if(!$project->open){
            $this->render('static.message','Project closed !',array(
                'msg' => 'You can\'t join this project because it is closed now !'
            ));
            return $this->layout;
        }
        $recipient = $project->getFreeRecipient();
        if(!$recipient){
            $this->render('static.message','All availabe places are now full',array(
                'msg' => 'You can\'t join this project because all available recipients have their max number of sponsors. Please try again later'
            ));
            return $this->layout;
        }
        $this->render('sponsors.join','Join '.$project->name,array(
            'pid' => $project->id
        ));
        return $this->layout;
    }

    public function register(Request $request, $id){
        $project = Project::find($id);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        if (!$project->open) {
            $this->render('static.message','Project closed !',array(
                'msg' => 'You can\'t join this project because it is closed now !'
            ));
            return $this->layout;
        }
        $recipient = $project->getFreeRecipient();
        if(!$recipient){
            $this->render('static.message','All availabe places are now full',array(
                'msg' => 'You can\'t join this project because all available recipients have their max number of sponsors. Please try again later'
            ));
            return $this->layout;
        }

        $sponsor = new Sponsor;
        $result = $sponsor->validateCreateRequest($request);
        if ($result instanceof Validator) {
            Session::put('error','There were some errors !');
            return redirect()->route('sponsors.join',$project->url)
                        ->withErrors($result)
                        ->withInput();
        } else {
            $sponsor->initialSetup($request, $project, $recipient);
            if (config('app.debug')) {
                $link = "<a href='".$sponsor->url."'>Confirm Email Link</a>";
                Session::put('success','Your sponsor account was successfully created ! DEBUG LINK: '.$link);
            } else {
                Session::put('success','Your sponsor account was successfully created !');
            }
            $this->render('static.message','Thanks for joining us !',
                    array('msg' => 'Please check your inbox to confirm you account')
                    );
            return $this->layout;
        }
    }

    public function confirm($hash, Encrypter $cryptService){
        $id = $cryptService->decrypt($hash);
        $sponsor = Sponsor::find($id);
        if (is_null($sponsor)) {
            abort(404, 'Sponsor Not Found');
        }
        $sponsor->confirmAndCompleteSetup();

        Auth::logout();
        Session::put('success','Your email adress was confirmed ! <br> You can now Login to your account ');
        return redirect()->route('login.form');
    }

    public function suspend($id, $pid){
        $sponsor = Sponsor::find($id);
        if(is_null($sponsor)){
            abort(404, 'Sponsor Not Found');
        }

        // Detach sponsor from recipient !
        $projectId = $sponsor->projects->first()->id;
        $sponsor->projects()->detach($projectId);
        Session::put('error','The sponsor\'s account has been suspended from the selected project !');
        return redirect()->route('sponsors.index');
    }

    public function projects($id){
        $sponsor = Sponsor::find($id);
        if(is_null($sponsor)){
            abort(404, 'Sponsor Not Found');
        }
        $this->render('sponsors.projects','Projects of the sponsor '.$sponsor->user->username,array(
            'projects' => $sponsor->projects,
            'sid' => $id
        ));
    }

}