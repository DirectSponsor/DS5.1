<!doctype html>
<html>
    <head>
        <title> Control Panel </title>
        <meta charset='utf-8'>
        <!-- Style -->
        {!! HTML::style('css/admin.css') !!}
    </head>
    <body>
        <div id="container" style="margin-top: 200px;">
            <div id="content">
                @yield('content')
            </div>
            <div id="footer">
            Directsponsor &copy; 2015
            </div>
        </div>
    </body>
</html>
