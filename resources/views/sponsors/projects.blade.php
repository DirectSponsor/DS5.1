<?php $accountType = Auth::user()->account_type; ?>
@if(!count($projects)) <table> @else <table class="data"> @endif 
    <thead><tr>
        <th>Name</th>
        <th></th>
    </tr></thead>
    <tbody>
    @if(!count($projects))
    <tr>
        <td colspan="2"><p>There are projects! </p></td>
    </tr>
    @endif
    @foreach($projects as $project)
    <tr>
        <td>{!! $project->name !!}</td>
        <td>
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete',
                'route'=>array('sponsors.suspend',$sid,$project->id))),
            Form::submit('Suspend from this project'),Form::close()  !!}
        </td>
    </tr>
    @endforeach
</tbody></table>