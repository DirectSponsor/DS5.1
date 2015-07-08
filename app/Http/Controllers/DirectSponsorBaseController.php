<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App as app;

use View;
use Session;
use Auth;


class DirectSponsorBaseController extends Controller {

    public $layout = 'layouts.cpanel';

    protected function setupLayout() {

    }

    protected function render($view, $title = false, $data = array()) {
        $l = $this->layout;
        $this->layout = View::make($this->layout);
        $this->setLinksForLayout();
        $app = app::getFacadeApplication();
        if ($title) {
            $this->layout->title = $title;
        }
        if (Session::has('error')) {
            $this->layout->error = Session::get('error');
            Session::forget('error');
        }
        if (Session::has('success')) {
            $this->layout->success = Session::get('success');
            Session::forget('success');
        }
        if (Session::has('info')) {
            $this->layout->info = Session::get('info');
            Session::forget('info');
        }
        // General Messages:
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->account_type == 'Sponsor') {
                $sponsor = $user->sponsor;
                if ($sponsor->hasNotPaidProjects()) {
                    $this->layout->notification = 'Some of your promised payments are overdue. Please send payments as soon as possible !';
                }
            }
        }

        $this->layout->content = View::make($view)->with($data);
    }

    private function setLinksForLayout() {
        /* Views Composers */
        // Navigation
        $links = array();

        if(Auth::check()){
            $user = Auth::user();
            $accountType = $user->account_type;
            switch($accountType){
                case 'Admin':
                    $links = array(
                        array(
                            'name' => 'Projects',
                            'link' => route('projects.index'),
                            'page' =>'projects'
                        ),
                        array(
                            'name' => 'Coordinators',
                            'link' => route('coordinators.index'),
                            'page' =>'cordinators'
                        ),
                        array(
                            'name' => 'Recipients',
                            'link' => route('recipients.index'),
                            'page' =>'recipients'
                        ),
                        array(
                            'name' => 'Sponsors',
                            'link' => route('sponsors.index'),
                            'page' =>'sponsors'
                        ),
                        array(
                            'name' => 'My Account',
                            'link' => route('users.edit',$user->id),
                            'page' =>'myaccount'
                        ),
                        array(
                            'name' => 'Logout',
                            'link' => route('logout'),
                            'page' =>'logout'
                        )
                    );
                    break;
                case 'Coordinator' :
                    $links = array(
                        array(
                            'name' => 'My Project',
                            'link' => route('myProject'),
                            'page' =>'myproject'
                        ),
                        array(
                            'name' => 'Recipients',
                            'link' => route('myRecipients'),
                            'page' =>'recipients'
                        ),
                        array(
                            'name' => 'Invitations',
                            'link' => route('projects.invitations.index',$user->account->project_id),
                            'page' =>'invitations'
                        ),
                        array(
                            'name' => 'Sponsors',
                            'link' => route('mySponsors'),
                            'page' =>'sponsors'
                        ),
                        array(
                            'name' => 'Transactions',
                            'link' => route('myTransactions'),
                            'page' =>'transactions'

                        ),
                        array(
                            'name' => 'Activities',
                            'link' => route('myActivities'),
                            'page' =>'activities'
                        ),
                        array(
                            'name' => 'My Account',
                            'link' => route('users.edit',$user->id),
                            'page' =>'myaccount'
                        ),
                        array(
                            'name' => 'Logout',
                            'link' => route('logout'),
                            'page' =>'logout'
                        )
                    );
                    break;
                case 'Recipient' :
                    $links = array(
                        array(
                            'name' => 'My Project',
                            'link' => route('myProject'),
                            'page' =>'myproject'
                        ),
                        array(
                            'name' => 'Sponsors',
                            'link' => route('mySponsors'),
                            'page' =>'sponors'
                        ),
                        array(
                            'name' => 'Transactions',
                            'link' => route('myTransactions'),
                            'page' =>'transactions'
                        ),
                        array(
                            'name' => 'My Account',
                            'link' => route('users.edit',$user->id),
                            'page' =>'myaccount'
                        ),
                        array(
                            'name' => 'Logout',
                            'link' => route('logout'),
                            'page' =>'logout'
                        )
                    );
                    break;
                case 'Sponsor' :
                default:
                    $links = array(
                        array(
                            'name' => 'Projects',
                            'link' => route('myProject'),
                            'page' =>'projects'
                        ),
                        array(
                            'name' => 'Transactions',
                            'link' => route('myTransactions'),
                            'page' =>'transactions'
                        ),
                        array(
                            'name' => 'Join Project',
                            'link' => route('joinProject'),
                            'page' =>'joinproject'
                        ),
                        array(
                            'name' => 'My Account',
                            'link' => route('users.edit',$user->id),
                            'page' =>'myaccount'
                        ),
                        array(
                            'name' => 'Logout',
                            'link' => route('logout'),
                            'page' =>'logout'
                        )
                    );
                    break;
            }
        }
        $this->layout->links = $links;
    }
}
