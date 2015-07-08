<?php
    $accountType = Auth::user()->account_type;
    $strippedProjectDesc = strip_tags($project->content);
?>

@if($accountType == 'Recipient')
<h2>Recipient Information: </h2>
@include('recipients.info')
@endif


<?php // Payments showen only for Admin ?>
@if($accountType == 'Admin')
<h2>Payments: </h2><?php $payments = $project->payments; $profileAccountType = 'Admin'; ?>
@include('payments.index')
@endif

@if($accountType == 'Admin' || $accountType == 'Coordinator')
<h2>Recipients: </h2><?php $recipients = $project->recipients; ?>
@include('recipients.index')
@endif



@if($accountType == 'Recipient')
<h2>Sponsors: </h2>
@include('sponsors.index')
@elseif($accountType != 'Sponsor')
<h2>Sponsors: </h2><?php $sponsors = $project->sponsors; ?>
@include('sponsors.index')
@endif

<h2>Project Informations: </h2>
<div class="infos">
    <p class="field">Name :</p><p class="value">{!!  $project->name  !!}</p><div class="clear"></div>
    @if($accountType == 'Admin')
    <p class="field">Coordinator username :</p><p class="value">{!!  $project->coordinator->user->username  !!}</p><div class="clear"></div>
    <p class="field">Coordinator email :</p><p class="value"> {!!  $project->coordinator->user->email  !!} </p><div class="clear"></div>
    @endif
    <p class="field">Recipients Number :</p><p class="value">{!!  $project->recipients->count()  !!}</p><div class="clear"></div>
    <p class="field">Sponsors Number :</p><p class="value">{!!  $project->sponsors->count()  !!}</p><div class="clear"></div>
    <p class="field">Sponsors Registration link:</p><p class="value">{!!  URL::route('sponsors.join',$project->url)  !!}</p><div class="clear"></div>
    <p class="field">Amount :</p><p class="value">{!!  $project->amount  !!} in {!!  $project->currency  !!} (approx {!!  $project->euro_amount  !!} Euro) </p><div class="clear"></div>
    <p class="field">Fund Group amount :</p><p class="value">{!!  $project->gf_commission  !!} in {!!  $project->currency  !!}</p><div class="clear"></div>
    <p class="field">Description :</p><p class="value">{!! $strippedProjectDesc !!}</p><div class="clear"></div>
    @if($accountType == 'Admin')
    <div class="links">
        <a href="{!! URL::route('projects.edit',$project->url) !!}">Edit Project</a>
        @if($project->open)
        {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('projects.close',$project->id))),
        Form::submit('Close'),
        Form::close()  !!}
        @else
        {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('projects.open',$project->id))),
        Form::submit('Reopen'),
        Form::close()  !!}
        @endif
    </div>
    @endif
</div>

<h2>Project Activities: </h2><?php $spends = $project->spends; ?>
@include('spends.index')