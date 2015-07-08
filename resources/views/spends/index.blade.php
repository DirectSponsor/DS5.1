<?php $accountType = Auth::user()->account_type; ?>
@if($accountType == 'Admin' || $accountType == 'Coordinator')
<a href="{!!  URL::route('projects.spends.create',$project->id)  !!}" class="add_item button" style="clear: both; float: left; margin-left: 30px;">New Activity</a>
@endif
@if($spends->count() == 0) <table> @else <table class="data"> @endif
    <thead><tr>
        <th>Date</th>
        <th>Coordinator</th>
        <th>Amount</th>
        <th>Description</th>
        @if($accountType == 'Admin') <th></th> @endif
    </tr></thead>
    <tbody>
    @if($spends->count() == 0)
    <tr>
        <td colspan="5"><p>There is no activities yet ! </p></td>
    </tr>
    @endif
    @foreach($spends as $spend)
    <tr>
        <td>{!! $spend->created_at !!}</td>
        <td>{!! $spend->coordinator()->user->username !!}</td>
        <td>{!! $spend->amount !!}</td>
        <td>{!! $spend->description !!}</td>
        @if($accountType == 'Admin')
        <td>
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route' => array('projects.spends.destroy', $spend->project_id, $spend->id))),
                Form::submit('Delete'),
            Form::close()  !!}
        </td>
        @endif
    </tr>
    @endforeach
</tbody></table>