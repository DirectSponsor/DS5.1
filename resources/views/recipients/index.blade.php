<?php $accountType = Auth::user()->account_type; ?>
@if(count($recipients)) <table class="data"> @else <table> @endif
    <thead><tr>
        <th>Full Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Skrill Account</th>
        <th></th>
    </tr></thead>
    <tbody>
    @if(!count($recipients))
    <tr>
        <td colspan="6"><p>There is no recipients yet ! </p></td>
    </tr>
    @endif
    @foreach($recipients as $recipient)
    <tr>
        <td>{!! $recipient->name !!}</td>
        <td>{!! $recipient->user->username !!}</td>
        <td>{!! $recipient->user->email !!}</td>
        <td>{!! $recipient->skrill_acc !!}</td>
        <td>
            @if($accountType == 'Admin' || $accountType == 'Coordinator')
            <a href="{!! URL::route('recipients.show',$recipient->id) !!}" class="button">Details</a>
            @endif
        </td>
    </tr>
    @endforeach
</tbody></table>