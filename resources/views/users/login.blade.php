{!! Form::open(array('route'=>'login.action')) !!}
{!! Form::label('username','Username : ') !!}
{!! Form::text('username'),'<div class="clear"></div>' !!}
{!! Form::label('password','password : ') !!}
{!! Form::password('password'),'<div class="clear"></div>' !!}
{!! Form::submit('Login',['class' => 'button']) !!}
{!! Form::close() !!}