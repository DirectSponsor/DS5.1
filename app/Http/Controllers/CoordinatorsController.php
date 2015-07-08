<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use App\Models\Coordinator;

class CoordinatorsController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth',array('only'=> array(
            'index','show'
        )));
    }

    public function index(){
        $coords = Coordinator::all();
        $this->render('coordinators.index','Coordinators',array(
            'coords' => $coords
        ));
        return $this->layout;
    }

    public function show($id){
        $coord = Coordinator::find($id);
        if (is_null($coord)) {
            abort(404, 'Coordinator Not Found');
        }
        $this->render('coordinators.show','',array(
            'coord' => $coord
        ));
        return $this->layout;
    }
}
