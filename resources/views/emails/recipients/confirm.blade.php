Hello {!! $name !!}, <br>
Please follow this link to confirm your email address : <a href="{!! URL::route('recipients.confirm',$hash) !!}">Confirm Email</a>