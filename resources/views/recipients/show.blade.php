<h2>Informations: </h2>
<div class="infos">
    <p class="field"> Username </p><p class="value">{!!  $recipient->user->username  !!}</p><div class="clear"></div>
    <p class="field"> Email </p><p class="value">{!!  $recipient->user->email  !!}</p><div class="clear"></div>
    <p class="field"> Skrill Account </p><p class="value">{!!  $recipient->skrill_acc  !!}</p><div class="clear"></div>
    <p class="field"> Join date </p><p class="value">{!!  $recipient->created_at  !!}</p><div class="clear"></div>
    <p class="field"> Number of sponsors </p><p class="value">
        @if(!is_null($recipient->sponsors()))
            {!!  $recipient->sponsors()->count()  !!}
        @else
            0
        @endif
    </p><div class="clear"></div>
</div>

@if(!is_null($recipient->sponsors))
    <h2> Sponsors: </h2><?php $sponsors = $recipient->sponsors; ?>
    @include('sponsors.sponsorbyrecipient')
@endif

<h2> Received Payments: </h2><?php $payments = $recipient->receivedPayments; $profileAccountType = 'Recipient'; ?>
@include('payments.index')