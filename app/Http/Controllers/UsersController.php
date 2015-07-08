<?php
namespace App\Http\Controllers;

use App\Http\Controllers\DirectSponsorBaseController;
use Auth;
use Session;
use Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends DirectSponsorBaseController {

    public function __construct(){
        $this->middleware('auth', array('only'=> array(
            'edit','panel','logout', 'edit', 'updateEmail', 'updatePass', 'updateDetails', 'permissionsDenied', 'visitorOnly', 'notFound'
        )));
    }

    public function loginForm(){ // Shows the login page
        if (Auth::check()) {
            return redirect()->route('panel');
        }
        $this->layout = 'layouts.login';
        $this->render('users.login');
        return $this->layout;
    }

    public function loginAction(Request $request){ // Login the user
        $credentials = array(
            'username' => $request->input('username'),
            'password' => $request->input('password')
        );

        if(!Auth::attempt($credentials)){
            Session::put('error','Incorrect Username or Password!');
            return redirect()->route('login.form');
        }

        $accountType = Auth::user()->account_type;
        if (($accountType == 'Admin') or ($accountType == 'Coordinator') ) {
            return redirect()->route('panel');
        }

        $recipientOrSponsorObj = Auth::user()->account;

        if(!$recipientOrSponsorObj->confirmed){
            Auth::logout();
            Session::put('info','You have to confirm your email before login to your account. Please check your email inbox !');
            return redirect()->route('login.form');
        }

        if($accountType == 'Sponsor'){
            if($recipientOrSponsorObj->suspended){
                Auth::logout();
                Session::put('info','Your account is suspended now !');
                return redirect()->route('login.form');
            }
        }
        return redirect()->route('panel');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login.form');
    }

    public function resendConfEmailForm(){
        $this->layout = 'layouts.resend-conf-email';
        $this->render('users.resend-conf-email');
        return $this->layout;
    }

    public function resendConfEmailAction(Request $request, Encrypter $encrypter, Mailer $mailer){
        $email = $request->input('username');
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            Session::put('error','Such email do not exist !');
            return redirect()->route('users.resendConfEmailForm');
        }
        if ($user->account->confirmed == 0) {
            $this->resendConfirmationEmail($user, $request, $encrypter, $mailer);
            Session::put('success','A confirmation email was sent to your email ' . $user->email);
            return redirect()->route('users.resendConfEmailForm');
        }else{
            Session::put('error','The User for that email address has already been confirmed. <br/>Use the password reset option !');
            return redirect()->route('users.resendConfEmailForm');
        }
    }

    private function resendConfirmationEmail(User $user, Request $request, Encrypter $encrypter, Mailer $mailer) {
        Session::flash('email',$user->email);
        $account = $user->account;
        //send email
        switch($user->account_type) {
            case 'Sponsor':
                $subject = 'Sponsor account created (Copy) !';
                $emailTemplate = 'emails.sponsors.confirmation';
                $data = array(
                    'name' => $account->name,
                    'hash' => $encrypter->encrypt($account->id),
                    'project' => $account->projects()->first(),
                    'recipient' => $account->recipientOfProject($account->projects()->first()->id)
                );
                break;
            default:
                $subject = 'Confirm account created (Copy) !';
                $emailTemplate = 'emails.recipients.confirm';
                $data = array(
                    'name' => (isset($account->name) ? $account->name : $user->username),
                    'hash' => $encrypter->encrypt($account->id)
                );
                break;
        }
        $mailer->send($emailTemplate, $data, function($message) use($request, $subject) {
            $message->to($request->input('username'))->subject($subject);
        });
        return true;
    }

    public function forgetPasswordForm(){
        $this->layout = 'layouts.forget-password';
        $this->render('users.forget-password');
        return $this->layout;
    }

    public function forgetPasswordAction(Request $request){
        $credentials = array('email' => $request->input('email'));
        return Password::remind($credentials, function($message){
            $message->subject('Your Password Reminder');
        });
    }

    public function forgetPasswordConfirmForm($token){
        $this->layout = 'layouts.forget-password-confirm';
        $this->render('users.forget-password-confirm', false, array('token'=>$token));
        return $this->layout;
    }

    public function forgetPasswordConfirmAction(Request $request, $token, Hasher $hashService){
        $credentials = array(
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation')
        );

        return Password::reset($credentials,
                function($user, $password) use($hashService) {
                    $user->password = $hashService->make($password);
                    $user->save();
                    Session::put('success','Password successfully updated');
                    return redirect()->route('login.form');
                    });
    }

    public function panel(){
        $user = Auth::user();
        switch($user->account_type){
            case 'Admin':
                return redirect()->route('projects.index');
            case 'Coordinator':
            case 'Recipient':
            case 'Sponsor':
            default:
                return redirect()->route('myProject');
        }
    }

    public function edit($id){
        if (Auth::user()->account_type == 'Admin') {
            $profile = null;
        } else {
            $profile = Auth::user()->account;
        }
        $this->render('users.edit','Edit Account Details',array(
            'user' => Auth::user(),
            'profile' => $profile
        ));
        return $this->layout;
    }

    public function updateEmail(Request $request, $id, Hasher $hashService){
        $userId = Auth::user()->id;
        if ($userId != $id) {
            abort(403, 'You are not authorised for this action');
        }
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,'.$id,
            'current_password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            Session::put('error','There were some errors !');
            return redirect()->route('users.edit',$id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::find($id);

        if(!$hashService->check($request->input('current_password'),$user->password)){
            Session::put('error','The password you entered was not correct !');
            return redirect()->route('users.edit',$id)->withInput();
        }

        $user->email = $request->input('email');
        if(!$user->save()){
            Session::put('error','There were some errors !');
            return redirect()->route('users.edit',$id)->withErrors($user->errors())->withInput();
        }
        Session::put('success','Your email was modified !');
        return redirect()->route('users.edit',$id);
    }

    public function updatePass(Request $request, $id, Hasher $hashService){
        $userId = Auth::user()->id;
        if($userId != $id){
            abort(403, 'You are not authorised for this action');
        }
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Session::put('error','There were some errors !');
            return redirect()->route('users.edit',$id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::find($id);

        if(!$hashService->check($request->input('current_password'),$user->password)){
            Session::put('error','The password you entered was not correct !');
            return redirect()->route('users.edit',$id)->withInput();
        }

        $user->password = $hashService->make($request->input('password'));
        if(!$user->save()){
            Session::put('error','There was some errors !');
            return redirect()->route('users.edit',$id)->withErrors($user->errors())->withInput();
        }
        Session::put('success','Your password was modified !');
        return redirect()->route('users.edit',$id);
    }

    public function updateDetails(Request $request, $id, Hasher $hashService){
        $userId = Auth::user()->id;
        if($userId != $id){
            abort(403, 'You are not authorised for this action');
        }
        $user = User::find($id);
        $account = $user->account;
        /*
         * Validate request inputs
         */
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'skrill_acc' => 'required|email|unique:recipients,skrill_acc,'.$account->id.'|unique:sponsors,skrill_acc,'.$account->id,
            'mepsa' => 'min:3',
            'current_password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            Session::put('error','There were some errors !');
            return redirect()->route('users.edit',$id)
                        ->withErrors($validator)
                        ->withInput();
        }

        if(!$hashService->check($request->input('current_password'),$user->password)){
            Session::put('error','The password you entered was not correct !');
            return redirect()->route('users.edit',$id)->withInput();
        }

        $account->fill($request->all());
        if(!$account->save()){
            Session::put('error','There was some errors !');
            return redirect()->route('users.edit',$id)->withErrors($account->errors())->withInput();
        }
        Session::put('success','Your details was modified !');
        return redirect()->route('users.edit',$id);
    }

    public function permissionsDenied(){
        $this->render('static.permissions_denied');
        return $this->layout;
    }
    public function visitorOnly(){
        $this->render('static.visitor_only');
        return $this->layout;
    }
    public function notFound(){
        $this->render('static.not_found');
        return $this->layout;
    }
}
