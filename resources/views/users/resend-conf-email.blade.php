{!!  Form::open(array('route'=>'users.resendConfEmailAction')),
    Form::label('username','Email: '),
    Form::text('username'),'<div class="clear"></div>',
    Form::submit('Send'),
Form::close()  !!}