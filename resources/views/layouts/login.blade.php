<!doctype html>
<html>
    <head>
        <title>Direct Sponsor</title>
        <meta charset='utf-8'>
        {!! HTML::style('css/admin.css') !!}
        {!! HTML::style('css/style.css') !!}
        {!! HTML::script('js/jquery.js') !!}
    </head>
    <body>
    	<center><a class='logo' href="#">{!! HTML::image('images/logo.png','Direct Sponsor') !!}</a></center>
        <div id="login">
            <h1>Members Login</h1>
            @if(isset($error))
                <p class="error">{!!  $error  !!}</p>
            @endif
            @if(isset($success))
                <p class="success">{!!  $success  !!}</p>
            @endif
            @if(isset($info))
                <p class="info">{!!  $info  !!}</p>
            @endif
            {!! $content !!}
            <a href="{!!  URL::route('users.forgetPasswordForm')  !!}" class="link_retrieve_password">Forgot your password? Reset here</a>
            <a href="{!!  URL::route('users.resendConfEmailForm')  !!}" class="link_resent_confirmation">Or resend the confirmation email</a>

        </div>
    </body>
</html>