<?php $accountType = Auth::user()->account_type; ?>
<h2>Informations: </h2>
<div class="infos">
    <p class="field"> Username </p><p class="value">{!!  $sponsor->user->username  !!}</p><div class="clear"></div>
    <p class="field"> Email </p><p class="value">{!!  $sponsor->user->email  !!}</p><div class="clear"></div>
    <p class="field"> Skrill Account </p><p class="value">{!!  $sponsor->skrill_acc  !!}</p><div class="clear"></div>
    <p class="field"> Join date </p><p class="value">{!!  $sponsor->created_at  !!}</p><div class="clear"></div>
    <p class="field"> Number of projects </p><p class="value">{!!  $sponsor->projects->count()  !!}</p><div class="clear"></div>
    @if($accountType == 'Admin')
    <div class="links">
        @if($sponsor->suspended)
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete',
                'route'=>array('sponsors.activate',$sponsor->id))),
                Form::submit('Activate'),Form::close()  !!}
        @else 
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete',
                'route'=>array('sponsors.suspend',$sponsor->id))),
                Form::submit('Suspend'),Form::close()  !!}
        @endif
    </div>
     @endif
</div>

<h2>Projects: </h2><?php $projects = $sponsor->projects; ?>
@include('projects.index')

<h2>Recipients: </h2><?php $recipients = $sponsor->recipients; ?>
@include('recipients.index')

<h2>Payments: </h2><?php $payments = $sponsor->payments; $profileAccountType = 'Sponsor'; ?>
@include('payments.index')