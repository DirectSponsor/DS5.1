<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Session;
use Illuminate\Contracts\Validation\Validator as Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Invitation;

class InvitationsController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index','create','store'
        )));
    }

    public function index($pid){
        $project = Project::find($pid);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $this->render('invitations.index',$project->name.' > Invitations',array(
            'invitations' => $project->invitations,
            'pid' => $pid
        ));
        return $this->layout;
    }

    public function create($pid){
        $project = Project::find($pid);
        $this->render('invitations.create',$project->name.' > Send new invitation',array(
            'pid' => $pid
        ));
        return $this->layout;
    }

    public function store(Request $request, $pid){
        $project = Project::find($pid);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $invitation = new Invitation;

        $result = $invitation->validateRequest($request);
        if ($result instanceof Validator) {
            Session::put('error','There were some errors !');
            return redirect()->route('invitations.create',$pid)
                        ->withErrors($result)
                        ->withInput();
        }

        $invitation->saveData($request, $project)->send();
        if (config('app.debug')) {
            $link = "<a href='".URL::route('recipients.join',$invitation->url)."'>Recipient Register Link</a>";
            Session::put('success','Invitation sent successfuly !  '.$link);
        } else {
            Session::put('success','Invitation sent successfuly !');
        }
        return redirect()->route('projects.invitations.index',$pid);
    }
}