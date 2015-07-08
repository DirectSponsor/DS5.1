{!!  Form::open(array('route'=>array('projects.spends.store',$project->id)))  !!}

    {!!  Form::label('amount','Amount :'), Form::text('amount',Input::old('amount'),array('style'=>'width:70px')) !!} <p class="remarques" style ="background:transparent"> {!!  $project->currency  !!} </p>
        @if($errors->has('amount')) {!!  $errors->first('amount','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>
    
    {!!  Form::label('description','Description :'), Form::textarea('description',Input::old('description'))  !!}
        @if($errors->has('description')) {!!  $errors->first('description','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>

{!!  Form::submit('Add'), Form::close()  !!}