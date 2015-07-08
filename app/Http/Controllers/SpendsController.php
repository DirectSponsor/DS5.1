<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;
use Illuminate\Http\Request;
use Validator;
use App\Models\Project;
use App\Models\Spend;

class SpendsController extends DirectSponsorBaseController {

public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index','create','store','destroy'
        )));
    }

    public function myActivities(){
        $accountType = Auth::user()->account_type;
        if ($accountType != 'Coordinator') {
            abort(403, 'You are not authorised for this action');
        }
        $project = Auth::user()->account->project;
        $this->render('spends.index',$project->name.' > Activities',array(
            'project' => $project,
            'spends' => $project->spends
        ));
        return $this->layout;
    }

    public function index($pid){
        $accountType = Auth::user()->account_type;
        $project = Project::find($pid);
        if ($accountType == 'Coordinator') {
            $myProjectId = Auth::user()->account->project_id;
            if($pid != $myProjectId) {
                abort(403, 'You are not authorised for this action');
            }
        }
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $this->render('spends.index',$project->name.' > Activities',array(
            'project' => $project,
            'spends' => $project->spends
        ));
        return $this->layout;
    }

    public function create($pid){
        $accountType = Auth::user()->account_type;
        $project = Project::find($pid);
        if (($accountType != 'Admin') && ($accountType != 'Coordinator')) {
            abort(403, 'You are not authorised for this action');
        }
        if ($accountType == 'Coordinator') {
            $myProjectId = Auth::user()->account->project_id;
            if($pid != $myProjectId) {
                abort(403, 'You are not authorised for this action');
            }
        }
        $this->render('spends.create',$project->name.' > Add Activity :',array('project'=>$project));
        return $this->layout;
    }

    public function store(Request $request, $pid){
        $project = Project::find($pid);
        if (is_null($project)) {
            Session::put('error','There was some errors - Could not find a Project with this ID !');
            return redirect()->route('projects.spends.create',$pid)->withInput();
        }
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'description' =>'required'
        ]);

        if ($validator->fails()) {
            Session::put('error','There were some errors !');
            return redirect()->route('projects.spends.create',$pid)
                        ->withErrors($validator)
                        ->withInput();
        }
        $spend = new Spend($request->all());
        $result = $project->spends()->save($spend);
        if ($result) {
            Session::put('success','Activity added with success !');
            if (Auth::user()->account_type == "Admin") {
                return redirect()->to("projects/".$project->id."/spends");
            } else {
                return redirect()->route("myActivities", $project->url);
            }
        } else {
            Session::put('error','There was some errors - Unable to Save new Spend Activity !');
            return redirect()->route('projects.spends.create',$pid)->withErrors($spend->errors())->withInput();
        }
    }

    public function destroy($pid, $id){
        $project = Project::find($pid);
        if (is_null($project)) {
            abort(404, 'Project Not Found');
        }
        $spend = Spend::find($id);
        $spend->delete();
        Session::put('error','Activity deleted !');
        if (Auth::user()->account_type == "Admin") {
            return redirect()->to("projects/".$project->id."/spends");
        } else {
            return redirect()->route("myActivities", $project->url);
        }
    }
}