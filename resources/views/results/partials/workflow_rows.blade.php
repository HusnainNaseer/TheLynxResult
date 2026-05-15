@forelse ($results as $result)
    <tr>
        <td>{{ $results->firstItem() + $loop->index }}</td>
        <td>{{ $result->name }}</td>
        <td>{{ $result->rollno }}</td>
        <td>{{ $result->branch_name }}</td>
        <td>{{ $result->class_name }}</td>
        <td>{{ $result->section_display }}</td>
        <td>
            <span class="badge {{ $result->workflow_status_label['class'] }}">{{ $result->workflow_status_label['text'] }}</span>
        </td>
        <td>{{ $result->overall_grade ?? 'N/A' }}</td>
        <td>{{ $result->overall_percentage !== null ? $result->overall_percentage . '%' : 'N/A' }}</td>
        <td>{{ $result->coordinator_approved_at?->format('d M Y h:i A') ?? 'N/A' }}</td>
        <td style="width: 100px;">
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <a href="{{ route('results.show', $result->id) }}" class="btn btn-info btn-sm">
                    <i class="ri-eye-line"></i>
                </a>

                @if ($result->can_edit_result)
                    <a href="{{ route('results.edit', $result->id) }}" class="btn btn-warning btn-sm">
                        <i class="ri-edit-line"></i>
                    </a>
                @endif

                @if ($result->can_approve_result)
                    <form action="{{ route('results.coordinator-approve', $result->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Approve this result?')">
                        @csrf
                        <button class="btn btn-success btn-sm" type="submit" title="Approve">
                            <i class="ri-check-line"></i>
                        </button>
                    </form>
                @endif

                @if ($result->can_delete_result)
                    <form action="{{ route('results.destroy', $result->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Rollback this result to create state?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center">No results found.</td>
    </tr>
@endforelse
