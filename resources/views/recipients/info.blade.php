<div class="infos">
    <p class="field">Recipient Name :</p><p class="value">{!!  Auth::user()->account->name  !!}</p><div class="clear"></div>
    <p class="field">Recipient Email :</p><p class="value">{!!  Auth::user()->email  !!}</p><div class="clear"></div>
    <p class="field">Recipient Skrill Email :</p><p class="value">{!!  Auth::user()->account->skrill_acc  !!}</p><div class="clear"></div>
    <p class="field">Recipient M-EPSA :</p><p class="value">{!!  Auth::user()->account->mepsa  !!}</p><div class="clear"></div>
    <p class="field">Number of sponsors :</p><p class="value">{!!  Auth::user()->account->sponsors->count()  !!}</p><div class="clear"></div>
</div>
