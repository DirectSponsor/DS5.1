<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;
use Illuminate\Contracts\Validation\Validator as Validator;
use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Payment;
use App\Models\MailerDS as Mailer;


class ProjectsController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index', 'myProject', 'create', 'store', 'show', 'edit', 'update', 'close', 'open', 'joinProject', 'joinProjectAction', 'withdraw'
        )));
    }

    public function index(){
        $projects = Project::all();

        $this->render('projects.index','Projects',array('projects'=>$projects));
        return $this->layout;
    }

    public function show($url){
        $project = Project::where('url','=',$url)->first();
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        } else {
            $this->render('projects.show','Project: '.$project->name, array('project'=>$project));
            return $this->layout;
        }
    }

    public function myProject(){
        $user = Auth::user();
        switch($user->account_type){
            case 'Admin' :
                return redirect()->route('projects.index');
            case 'Coordinator' :
                $project = Project::find($user->account->project_id);
                $this->render('projects.show',$project->name,array('project' => $project));
                return $this->layout;
            case 'Recipient' :
                $project = Project::find($user->account->project_id);
                $sponsors = $user->account->sponsors;
                $this->render('projects.show',$project->name,array(
                    'project' => $project,
                    'sponsors' => $sponsors
                ));
                return $this->layout;
            case 'Sponsor' :
                $this->render('projects.index','My Projects',array(
                    'projects' => $user->account->projects
                ));
                return $this->layout;
        }
        abort(404, 'Your account type is not known to the system.');
    }

    public function joinProject(){
        $accountType = Auth::user()->account_type;
        if ($accountType != 'Sponsor') {
            abort(403, 'You must be a Sponsor to join a Project');
        }
        $sponsorProjectsIds = Auth::user()->account->projects->lists('id');
        if (empty($sponsorProjectsIds)) {
            $projects = Project::where('open','=',true)->get();
        } else {
            $projects = Project::whereNotIn('id',$sponsorProjectsIds)->where('open','=',true)->get();
        }
        $this->render('projects.sponsor_join','Join A Project',array(
            'projects' => $projects
        ));
        return $this->layout;
    }

    public function joinProjectAction($pid, Mailer $mailer){
        $accountType = Auth::user()->account_type;
        $project = Project::find($pid);
        if ($accountType != 'Sponsor') {
            abort(403, 'You are not authorised for this action');
        }
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $sponsor = Auth::user()->account;

        $recipient = $project->getFreeRecipient();
        if ($recipient){
            $payment = new Payment;
            $payment->sponsorJoinProjectSetupFirstPayment($project, $sponsor, $recipient);
            $mailer->sendJoinProjectEmail(Auth::user(), $project, $sponsor, $recipient);

            Session::put('success','You have joined the project "'.$project->name.'" Please check your email inbox to make your first payment !');
            return redirect()->route('joinProject');
        } else {
            Session::put('error','Sorry : There is no empty place on this peoject currently. Please retry later ');
            return redirect()->route('joinProject');
        }
    }

    public function create(){
        $this->render('projects.create','Add new project');
        return $this->layout;
    }

    public function store(Request $request){
        $project = new Project;
        $result = $project->validateCreateProjectData($request);
        if ($result instanceof Validator) {
            Session::put('error','There were some errors creating a new Project!');
            return redirect()->route('projects.create')
                        ->withErrors($result)
                        ->withInput();
        }
        $project->setupProject($request);
        Session::put('success','Project has been added !');
        return redirect()->route('projects.show',$project->url);
    }

    public function edit($url){
        $project = Project::where('url','=',$url)->first();
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        } else {
            $this->render('projects.edit','Edit: '.$project->name,array('project'=>$project));
            return $this->layout;
        }
    }

    public function update(Request $request, $id){
        $project = Project::find($id);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $result = $project->validateCreateProjectData($request);
        if ($result instanceof Validator) {
            Session::put('error','There were some errors Editing this Project!');
            return redirect()->route('projects.edit')
                        ->withErrors($result)
                        ->withInput();
        }
        $project->updateProject($request);
        Session::put('success','Project editted successfully !');
        return redirect()->route('projects.show',$project->url);
    }


    public function close($id){
        $project = Project::find($id);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $project->open = false;
        $project->save();
        Session::put('error','The project \''.$project->name.'\' has been closed !');
        return redirect()->route('projects.index');
    }

    public function open($id){
        $project = Project::find($id);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $project->open = true;
        $project->save();
        Session::put('success','The project \''.$project->name.'\' has been opened !');
        return redirect()->route('projects.index');
    }

    public function withdraw($projectId){
        $accountType = Auth::user()->account_type;
        if ($accountType != 'Sponsor') {
            abort(403, 'You are not authorised for this action');
        }
        $project = Project::find($projectId);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $sponsor = Auth::user()->account;
        $sponsor->projects()->detach($projectId);

        Session::put('info','You don\'t sponsor the project "'.$project->name.'" anymore. You can still rejoin the project later if you want.');
        return redirect()->route('myProject');
    }
}
