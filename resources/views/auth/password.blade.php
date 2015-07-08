{!!  Form::open(array('route'=>'users.forgetPasswordAction')),
    Form::label('email','Email: '),
    Form::text('email'),'<div class="clear"></div>',
    Form::submit('Send confirmation link'),
Form::close()  !!}