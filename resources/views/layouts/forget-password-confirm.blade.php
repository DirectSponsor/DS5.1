<!doctype html>
<html>
    <head>
        <title> Forget Password Confirm</title>
        <meta charset='utf-8'>
        {!! HTML::style('css/admin.css') !!}
        {!! HTML::script('js/jquery.js') !!}
    </head>
    <body>
        <div id="login">
            <h1>Forget Password Confirm</h1>
            @if(isset($error))
                <p class="error">{!!  trans(Session::get('reason'))  !!}</p>
            @endif
            @if(isset($success))
                <p class="success">{!!  $success  !!}</p>
            @endif
            @if(isset($info))
                <p class="info">{!!  $info  !!}</p>
            @endif
            {!!  $content  !!}
            <a href="{!!  URL::route('login.form')  !!}" class="add_item">Login</a>
        </div>
    </body>
</html>