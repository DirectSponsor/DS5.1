<h2>Informations: </h2>
<div class="infos">
    <p class="field">Name :</p><p class="value">{!!  $project->name  !!}</p><div class="clear"></div>
    <p class="field">Recipients Number :</p><p class="value">{!!  $project->recipients->count()  !!}</p><div class="clear"></div>
    <p class="field">Sponsors Number :</p><p class="value">{!!  $project->sponsors->count()  !!}</p><div class="clear"></div>
    <p class="field">Sponsors Registration link:</p><p class="value">{!!  URL::route('sponsors.join',$project->url)  !!}</p><div class="clear"></div>
    <p class="field">Amount :</p><p class="value">{!!  $project->amount  !!} in {!!  $project->currency  !!} (approx {!!  $project->euro_amount  !!} Euro) </p><div class="clear"></div>
    <p class="field">Fund Group amount :</p><p class="value">{!!  $project->gf_commission  !!} in {!!  $project->currency  !!}</p><div class="clear"></div>
</div>

<h2>Recipients: </h2><?php $recipients = $project->recipients; ?>
@include('recipients.index')

<h2>Sponsors: </h2><?php $sponsors = $project->sponsors; ?>
@include('sponsors.index')

<h2>Payments: </h2><?php $payments = $project->payments; $profileAccountType = 'Coordinator'; ?>
@include('payments.index')