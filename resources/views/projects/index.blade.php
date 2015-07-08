<?php $accountType = Auth::user()->account_type; ?>
<script>
function ConfirmDelete()
{
  var x = confirm("Please note that by withdrawing, you will cease to be a sponsor for this project. You can rejoin this project again.");
  if (x)
    return true;
  else
    return false;
}
</script>
@if($accountType == 'Admin')
<a style="float: left; margin-left: 30px;" href="{!!  URL::route('projects.create')  !!}" class="add_item  button">New Project</a>
@endif
@if(!count($projects)) <table> @else <table class="data"> @endif
    <thead><tr>
        <th colspan="3">Project</th>
        @if($accountType == 'Sponsor')
        <th colspan="4">Recipient</th>
        @else
        <th colspan="2">Coordinator</th>
        @endif
    </tr></thead>
<thead><tr>
        <th>Name</th>
        <th>Recipients</th>
        <th>Sponsors</th>
        @if($accountType == 'Sponsor')
        <th>Name</th>
        <th>Skrill ID</th>
        <th>Last Paid</th>
        <th>Due</th>
        @else
        <th>Coordinator</th>
        @endif
        <th>Options</th>
    </tr></thead>
    <tbody>
    @if(!count($projects))
    <tr>
        <td colspan="5"><p>There are no projects yet ! </p></td>
    </tr>
    @endif
    @foreach($projects as $project)
    <tr>
        <td>{!! $project->name !!}</td>
        <td>{!! $project->recipients()->count() !!}</td>
        <td>{!! $project->sponsors()->count() !!}</td>
        @if($accountType == 'Sponsor')
            <?php
                $account = Auth::user()->account;
                $recipient = $project->recipientOfSponsor($account->id);
            ?>
            <td>{!! $recipient->user->account->name !!}</td>
            <td>{!! $recipient->skrill_acc !!}</td>
            <td>{!! $account->lastRecipientPaymentDate($recipient->user->id) !!}</td>
            <td>{!! $recipient->nextPaymentDueFromSponsor($account->id) !!}</td>
        @else
            <td>{!! $project->coordinator->user->username !!}</td>
        @endif
        <td>
            <a href="{!!  URL::route('projects.show',$project->url)  !!}" class="button">Details</a>
            <a href="{!!  URL::route('projects.spends.index',$project->id)  !!}" class="button"> Activities </a>
        @if($accountType == 'Sponsor')
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('projects.withdraw',$project->id), 'onsubmit' => 'return ConfirmDelete()')),
                Form::submit('Withdraw',['class' => 'red_button']),
            Form::close()  !!}
        @endif
        @if($accountType == 'Admin')
            <a href="{!!  URL::route('projects.invitations.index',$project->id)  !!}" class="button">Invitations</a>
            <a href="{!!  URL::route('projects.edit',$project->url)  !!}" class="orange_button">Edit</a>
            @if($project->open)
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('projects.close',$project->id))),
                Form::submit('Close'),
            Form::close()  !!}
            @else
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('projects.open',$project->id))),
                Form::submit('Reopen'),
            Form::close()  !!}
            @endif
        @endif
        </td>
    </tr>
    @endforeach
</tbody></table>