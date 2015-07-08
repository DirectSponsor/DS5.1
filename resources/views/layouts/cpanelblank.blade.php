<?php
    $env = app()->env;
    $version = $app::VERSION
    ?>
<!doctype html>
<html>
    <head>
        <title> Control Panel</title>
        <meta charset='utf-8'>
        <!-- Style -->
        {!! HTML::style('css/admin.css') !!}
        {!! HTML::style('css/style.css') !!}
        <!-- jQuery & jQuery UI -->
        {!! HTML::script('js/jquery.js') !!}
        <!-- CKEditor -->
        {!! HTML::script('js/ckeditor/ckeditor.js') !!}
        <!-- DataTables jQuery Plugin -->
        {!! HTML::script('js/jquery.dataTables.min.js') !!}
        <link href="//fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="container" class="">
            <div id="header">
				<fieldset>
                <div class="inner">
                    <a class='logo' href="/" title="Home">{!!  HTML::image('images/logo.png','Direct Sponsor');  !!}</a>
                    @if($env != 'production')
                        <span class='button red_button'>
                            Env: {!!  $env  !!} Laravel Vers: {!!  $version  !!}</span>
                    @endif
                </div>
                </fieldset>
            </div>
            <div id="content">
                <div class="inner">
                    <a class='error' href="/" title="Home">Home</a>
                    @yield('content')
                </div>
            </div>
            <div id="footer">
                    <div class="inner">
                    Directsponsor &copy; 2015
                    </div>
            </div>
        </div>
    </body>
</html>
