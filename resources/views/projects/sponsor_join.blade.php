@if(!count($projects)) <table> @else <table class="data"> @endif
    <thead><tr>
        <th>Name</th>
        <th>Amount</th>
        <th>Currency</th>
        <th></th>
    </tr></thead>
    <tbody>
    @if(!count($projects))
    <tr>
        <td colspan="4"><p>There are no projects yet! </p></td>
    </tr>
    @endif
    @foreach($projects as $project)
    <tr>
        <td>{!! $project->name !!}</td>
        <td>{!! $project->amount !!}</td>
        <td>{!! $project->currency !!}</td>
        <td>
            <a href="{!!  URL::route('projects.show',$project->url)  !!}" class="button">Details</a>
            <?php if($project->getFreeRecipient()):?>
                <a href="{!! URL::route('joinProjectAction',$project->id) !!}" class="button">Join this project </a>
            <?php endif;?>
        </td>
    </tr>
    @endforeach
</tbody></table>