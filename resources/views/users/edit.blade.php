<?php $accountType = Auth::user()->account_type; ?>
@if($accountType == 'Recipient' || $accountType == 'Sponsor')
<h2> Change your details </h2>
{!!  Form::model($profile,array('route'=>array('users.updateDetails',$user->id), 'method'=>'put')),
    Form::label('name','New Name :'), Form::text('name',Input::old('name'))  !!}
        @if($errors->has('name')) {!!  $errors->first('name','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
        
    {!!  Form::label('skrill_acc','New Skrill Account :'), Form::text('skrill_acc',Input::old('skrill_acc'))  !!}
        @if($errors->has('skrill_acc')) {!!  $errors->first('skrill_acc','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
        
	{!!  Form::label('mepsa','M-EPSA :'), Form::text('mepsa',Input::old('mepsa'))  !!}
        @if($errors->has('mepsa')) {!!  $errors->first('mepsa','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
                
    {!!  Form::label('current_password','Current Password :'), Form::password('current_password')  !!}
        @if($errors->has('current_password')) {!!  $errors->first('current_password','<p class="remarques"> :message </p>')  !!} @endif
{!!  Form::submit('Save new Details'), Form::close()  !!}
@endif
<hr>
<h2> Change your email </h2>
{!!  Form::model($user,array('route'=>array('users.updateEmail',$user->id), 'method'=>'put')),
    Form::label('email','New Email :'), Form::text('email',Input::old('email'))  !!}
        @if($errors->has('email')) {!!  $errors->first('email','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!!  Form::label('current_password','Current Password :'), Form::password('current_password')  !!}
        @if($errors->has('current_password')) {!!  $errors->first('current_password','<p class="remarques"> :message </p>')  !!} @endif
{!!  Form::submit('Save new Email'), Form::close()  !!}
<hr>
<h2> Change your password </h2>
{!!  Form::model($user,array('route'=>array('users.updatePass',$user->id), 'method'=>'put')),
    Form::label('current_password','Current Password :'), Form::password('current_password')  !!}
        @if($errors->has('current_password')) {!!  $errors->first('current_password','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!!  Form::label('password','New Password :'), Form::password('password')  !!}
        @if($errors->has('password')) {!!  $errors->first('password','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!!  Form::label('password_confirmation','Retype New Password :'), Form::password('password_confirmation')  !!}
        @if($errors->has('password_confirmation')) {!!  $errors->first('password_confirmation','<p class="remarques"> :message </p>')  !!} @endif
{!!  Form::submit('Save new password'), Form::close()  !!}
