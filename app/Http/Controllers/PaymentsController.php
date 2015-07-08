<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;

use App\Models\Payment;


class PaymentsController extends DirectSponsorBaseController {
    public function __construct(){
        $this->middleware('auth', array('only' => array(
                'index', 'myTransactions', 'accept', 'reject', 'add', 'save'
        )));
    }

    public function index(){
        if (Auth::user()->account_type != 'Admin') {
            return redirect()->route('myTransactions');
        }
        $payments = Payment::all();
        $this->render('payments.index','Transactions',array(
            'payments' => $payments
        ));
        return $this->layout;
    }

    public function myTransactions(){
        $user = Auth::user();
        switch($user->account_type){
            case 'Admin' :
                return redirect()->route('payments.index');
            case 'Coordinator' :
                $payments = $user->account->receivedPayments;
                $this->render('payments.index','Received Transactions',array(
                    'payments' => $payments
                ));
                return $this->layout;
            case 'Recipient' :
                $payments = $user->account->receivedPayments;
                $this->render('payments.index','Received Transactions',array(
                    'payments' => $payments
                ));
                return $this->layout;
            case 'Sponsor' :
                $payments = $user->account->payments;
                $this->render('payments.index','My Transactions',array(
                    'payments' => $payments
                ));
                return $this->layout;
            default:
                abort(404, 'Account Type could not be identified');
        }
    }

    public function accept($id){
        $payment = Payment::find($id);
        if(is_null($payment)) {
            abort(404, 'Payment Not Found');
        }
        $user = Auth::user();

        if($user->id != $payment->receiver->id || $user->account_type == 'Admin' || $user->account_type == 'Sponsor') {
            abort(403, 'Not Authorised to Confirm Payment');
        }

        if($user->account_type == 'Coordinator'){
            $payment->processAcceptedPaymentCoordinator();
            Session::put('success','Transaction was successfuly confirmed !');
            return  redirect()->route('myTransactions');
        } else {
            $payment->processAcceptedPaymentRecipient();
            Session::put('success','Transaction was successfuly confirmed !');
            Session::put('info','Please make sure you sent the group fund commission to coordinator !');
            return  redirect()->route('myTransactions');
        }
    }

}
