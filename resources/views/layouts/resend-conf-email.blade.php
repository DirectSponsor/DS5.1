<!doctype html>
<html>
    <head>
        <title> Resend Confirmation email </title>
        <meta charset='utf-8'>
        {!! HTML::style('css/admin.css') !!}
        {!! HTML::script('js/jquery.js') !!}
    </head>
    <body>
        <div id="login">
            <h1>Resend Confirmation email</h1>
            @if(isset($error))
                <p class="error">{!!  $error  !!}</p>
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