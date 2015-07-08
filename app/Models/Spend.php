<?php

namespace App\Models;

use App\Models\DirectSponsorBaseModel;
use App\Models\Coordinator;


class Spend extends DirectSponsorBaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'spends';

    protected $guarded = array();
    public static $rules = array(
        'amount' => 'required|numeric',
        'description' =>'required'
    );

//    public $autoHydrateEntityFromInput = true;    // hydrates on new entries' validation
//    public $forceEntityHydrationFromInput = true; // hydrates whenever validation is called

    public function project() {
        return $this->belongsTo('App\Models\Project');
    }

    public function coordinator(){
        return Coordinator::where("project_id", $this->project_id)->first();
    }

}
