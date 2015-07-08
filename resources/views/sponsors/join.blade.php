{!!  Form::open(array('route'=>array('sponsors.register',$pid))),
    Form::label('name','Full name :'), Form::text('name',Input::old('name'))  !!}
        @if($errors->has('name')) {!!  $errors->first('name','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!! Form::label('email','Email :'), Form::text('email',Input::old('email'))  !!}
        @if($errors->has('email')) {!!  $errors->first('email','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!! Form::label('username','Username :'), Form::text('username',Input::old('username'))  !!}
        @if($errors->has('username')) {!!  $errors->first('username','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!!  Form::label('password','Password :'), Form::password('password')  !!}
        @if($errors->has('password')) {!!  $errors->first('password','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!!  Form::label('password_confirmation','Password Confirmation :'), Form::password('password_confirmation')  !!}
        @if($errors->has('password_confirmation')) {!!  $errors->first('password_confirmation','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    {!! Form::label('skrill_acc','Skrill Email :'), Form::text('skrill_acc',Input::old('skrill_acc'))  !!}
        @if($errors->has('skrill_acc')) {!!  $errors->first('skrill_acc','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
{!!  Form::submit('Join'), Form::close()  !!}