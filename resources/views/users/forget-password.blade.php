{!!  Form::open(array('route'=>'users.forgetPasswordAction')),
    Form::label('email','Email: '),
    Form::text('email'),'<div class="clear"></div>',
    Form::submit('Send confirmation link'),
Form::close()  !!}
    @if (Session::has('status'))
       <p class="success">{{ Session::get('status') }}</p>
    @endif
    @if($errors)
        @foreach($errors->all() as $error)
            <p class="error">Error {!! $error !!}</p>
        @endforeach
    @endif
