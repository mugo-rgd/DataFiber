<h4 class="mt-4">Survey Assignment History</h4>
<ul class="list-group">
    @foreach($designRequest->assignmentLogs()->latest()->get() as $log)
        <li class="list-group-item">
            <strong>{{ ucfirst($log->action) }}</strong>
            to <span class="text-primary">{{ $log->surveyor->name }}</span>
            by <span class="text-success">{{ $log->assignedBy->name }}</span>
            <br>
            <small class="text-muted">{{ $log->created_at->format('M d, Y H:i') }}</small>
            @if($log->notes)
                <br><em>{{ $log->notes }}</em>
            @endif
        </li>
    @endforeach
</ul>
