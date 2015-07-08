{!!  Form::open(array('route'=> array('projects.invitations.store',$pid))),
    Form::label('sent_to','Email :'), Form::text('sent_to',Input::old('sent_to'))  !!}
        @if($errors->has('sent_to')) {!!  $errors->first('sent_to','<p class="remarques"> :message </p>')  !!} @endif
{!!  Form::submit('Send invitation'), Form::close()  !!}

@if(!count($invitations)) <table> @else <table class="data"> @endif 
    <thead><tr>
        <th>Date</th>
        <th>Sent To</th>
    </tr></thead>
    <tbody>
    @if(!count($invitations))
    <tr>
        <td colspan="4"><p>There is no invitations yet !</p></td>
    </tr>
    @endif
    @foreach($invitations as $invitation)
    <tr>
        <td>{!! $invitation->created_at !!}</td>
        <td>{!! $invitation->sent_to !!}</td>
    </tr>
    @endforeach
</tbody></table>