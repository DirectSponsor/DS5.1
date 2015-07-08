{!!  Form::open(array('route'=>'projects.store'))  !!}

    {!!  Form::label('name','Project name :'), Form::text('name',Input::old('name'))  !!}
        @if($errors->has('name')) {!!  $errors->first('name','<p class="remarques"> :message </p>')  !!} @endif
        <div class="clear"></div>

    {!!  Form::label('max_recipients',' Max recipients number :'), Form::text('max_recipients',Input::old('max_recipients'))  !!}
    @if($errors->has('max_recipients')) {!!  $errors->first('max_recipients','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

    {!!  Form::label('max_sponsors_per_recipient',' Max sponsors per recipient :'), Form::text('max_sponsors_per_recipient',Input::old('max_sponsors_per_recipient'))  !!}
    @if($errors->has('max_sponsors_per_recipient')) {!!  $errors->first('max_sponsors_per_recipient','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

    {!!  Form::label('currency',' Currency :'), Form::text('currency',Input::old('currency'))  !!}
    @if($errors->has('currency')) {!!  $errors->first('currency','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

    {!!  Form::label('amount',' Amount :'), Form::text('amount',Input::old('amount'))  !!}
    @if($errors->has('amount')) {!!  $errors->first('amount','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

    {!!  Form::label('euro_amount',' Amount in euro :'), Form::text('euro_amount',Input::old('euro_amount'))  !!}
    @if($errors->has('euro_amount')) {!!  $errors->first('euro_amount','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

    {!!  Form::label('gf_commission',' group fund amount :'), Form::text('gf_commission',Input::old('gf_commission'))  !!}
    @if($errors->has('gf_commission')) {!!  $errors->first('gf_commission','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>
    {!!  Form::label('username',' Coordinator\'s username :'), Form::text('username',Input::old('username'))  !!}
    @if($errors->has('username')) {!!  $errors->first('username','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>
    {!!  Form::label('email',' Coordinator\'s email :'), Form::text('email',Input::old('email'))  !!}
    @if($errors->has('email')) {!!  $errors->first('email','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>
    {!!  Form::label('password',' Coordinator\'s password :'), Form::password('password')  !!}
    @if($errors->has('password')) {!!  $errors->first('password','<p class="remarques"> :message </p>')  !!} @endif
    <div class="clear"></div>

{!!  Form::submit('Add'), Form::close()  !!}