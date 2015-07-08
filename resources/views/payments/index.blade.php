<?php
$visitorAccountType = Auth::user()->account_type;
if (!isset($profileAccountType)) {
    $profileAccountType = $visitorAccountType;
}
if ($profileAccountType == $visitorAccountType) {
    $showControls = true;
} else {
    $showControls = false;
}
?>

@if(!count($payments)) <table> @else <table class="data"> @endif
    <thead><tr>
<?php switch($profileAccountType){
    case 'Admin': ?>
        <th>Due Date</th>
        <th>For Month</th>
        <th>Project</th>
        <th>Type</th>
        <th>From</th>
        <th>To</th>
        <th>Status</th>
    </tr></thead><tbody>
    @if(!count($payments)) <tr><td colspan = "6">There are no transactions !</td></tr> @endif
    @foreach($payments as $payment)
    <tr>
        <td>{!!  $payment->due_date->format('Y/m/d')  !!}</td>
        <td>{!!  $payment->pay_month  !!}</td>
        <td>{!!  $payment->project->name  !!}</td>
        <td>{!!  $payment->type  !!}</td>
        <td> @if($payment->type == "sponsoring")
            {!!  $payment->sender->username  !!}  (Sponsor)
        @else
            {!!  $payment->sender->username  !!}  (Recipient)
        @endif </td>
        <td> @if($payment->type == "sponsoring")
            {!!  $payment->receiver->username  !!}  (Recipient)
        @else
            {!!  $payment->receiver->username  !!}  (Coordinator)
        @endif </td>
        <td>{!!  $payment->stat  !!}</td>
    </tr>
    @endforeach


<?php break; case 'Coordinator': ?>
        <th>Due Date</th>
        <th>For Month</th>
        <th>Recipient</th>
        <th>Status</th>
        @if($showControls) <th>Options</th> @endif
    </tr></thead><tbody>
    <?php
        if(!count($payments)){
            $n = ($showControls) ? 4 : 3;
            echo '<tr><td colspan = "'.$n.'">There are no transactions !</td></tr>';
        }
    ?>
    @foreach($payments as $payment)
    <tr>
        <td>{!!  $payment->due_date->format('Y/m/d')  !!}</td>
        <td>{!!  $payment->pay_month  !!}</td>
        <td> @if($payment->type != "group fund")
            Error ! (Please report it)
        @else
            {!!  $payment->sender->username  !!}
        @endif </td>
        <td>{!!  $payment->stat  !!}</td>
        @if($showControls)
        <td>
            @if($payment->stat == "pending")
            {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('payments.accept',$payment->id))),
                Form::submit('Confirm'),
            Form::close()  !!}
            @endif
        </td>
        @endif
    </tr>
    @endforeach


<?php break; case 'Recipient': ?>
        <th>Due Date</th>
        <th>For Month</th>
        <th>Sponsor</th>
        <th>Skrill Account</th>
        <th>Status</th>
        @if($showControls) <th>Options</th> @endif
    </tr></thead><tbody>
    <?php
        if(!count($payments)){
            $n = ($showControls) ? 5 : 4;
            echo '<tr><td colspan = "'.$n.'">There are no transactions !</td></tr>';
        }
    ?>
    @foreach($payments as $payment)
    <tr>
        <td>{!!  $payment->due_date->format('Y/m/d')  !!}</td>
        <td>{!!  $payment->pay_month  !!}</td>
        <td> @if($payment->type == "sponsoring")
            {!!  $payment->sender->username  !!}
        @else
            Error ! (Please report it)
        @endif </td>
        <td> @if($payment->type == "sponsoring")
            {!!  $payment->sender->account->skrill_acc  !!}
        @else
            Error ! (Please report it)
        @endif </td>
        <td>{!!  $payment->stat  !!}</td>
        @if($showControls)
        <td>
            @if($payment->stat == "pending")
                {!!  Form::open(array('class'=>'inline', 'method'=>'delete','route'=>array('payments.accept',$payment->id))),
                    Form::submit('Confirm'),
                Form::close()  !!}
            @endif
        </td>
        @endif
    </tr>
    @endforeach


<?php break; case 'Sponsor': ?>
        <th>Due Date</th>
        <th>For Month</th>
        <th>Project</th>
        <th>Status</th>
    </tr></thead><tbody>
    @if(!count($payments)) <tr><td colspan = "3">There is no transactions !</td></tr> @endif
    @foreach($payments as $payment)
    <tr>
        <td>{!!  $payment->due_date->format('Y/m/d')  !!}</td>
        <td>{!!  $payment->pay_month  !!}</td>
        <td>{!!  $payment->project->name  !!}</td>
        <td>{!!  $payment->stat  !!}</td>
    </tr>
    @endforeach
<?php break;
}?></tbody></table>