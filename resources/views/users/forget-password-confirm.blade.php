{!!  Form::open(array('route'=>array('users.forgetPasswordConfirmAction', $token))),
    Form::hidden('token', $token),
    Form::label('email','Email: '),
    Form::text('email'),'<div class="clear"></div>',
    Form::label('password','Password: '),
    Form::password('password'),'<div class="clear"></div>',
    Form::label('confirm-new-password', 'Password confirmation: '),
    Form::password('password_confirmation'),'<div class="clear"></div>',
    Form::submit('Update password'),
Form::close()  !!}
    @if($errors)
        @foreach($errors->all() as $error)
            <p class="error">Error {!! $error !!}</p>
        @endforeach
    @endif
