{!!  Form::open(array('route'=>array('projects.invitations.store',$pid))),
    Form::label('sent_to','Email :'), Form::text('sent_to',Input::old('sent_to'))  !!}
        @if($errors->has('sent_to')) {!!  $errors->first('sent_to','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
{!!  Form::submit('Send invitation'), Form::close()  !!}