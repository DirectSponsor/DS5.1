<ul id="nav">
    @foreach($links as $link)
        <li class="{!! $link['page'] !!}"><a href="{!! $link['link'] !!}">{!! $link['name'] !!}</a></li>
    @endforeach
</ul>