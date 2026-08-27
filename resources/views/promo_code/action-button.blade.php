<div class="project-actions text-right text-nowrap">
    <a href="{{ route('promo-codes.show', encrypt($row->id)) }}" class="btn btn-xs btn-primary" title="Show">
        <i class="fas fa-eye"></i> Show
    </a>
    @if($row->sms_count < 1)
        <a href="{{ route('promo-codes.edit', encrypt($row->id)) }}" class="btn btn-xs btn-info" title="Edit">
            <i class="fas fa-edit"></i> Edit
        </a>
    @endif
    <form action="{{ route('promo-codes.send-sms', encrypt($row->id)) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Send SMS to all assigned customers?');">
        @csrf
        <button type="submit" class="btn btn-xs btn-success" title="Send SMS">
            <i class="fas fa-envelope"></i> SMS
        </button>
    </form>
</div>
