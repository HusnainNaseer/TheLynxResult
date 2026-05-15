@forelse ($students as $student)
    @php
        $result = $student->result;
    @endphp
    <tr>
        <td>
            @if ($student->can_forward_to_class_teacher || $student->can_forward_to_coordinator)
                <input type="checkbox" class="bulk-result-check" name="result_ids[]" value="{{ $result->id }}"
                    data-forward-action="{{ $student->can_forward_to_coordinator ? 'forward_coordinator' : 'forward_class_teacher' }}"
                    form="bulkForwardForm">
            @endif
        </td>
        <td>{{ $students->firstItem() + $loop->index }}</td>
        <td>{{ $student->stdname }}</td>
        <td>{{ $student->rollno }}</td>
        <td>{{ $student->branch_name }}</td>
        <td>{{ $student->class_name }}</td>
        <td>{{ $student->section_display }}</td>
        <td>
            @if ($result)
                <span class="badge bg-success">Created</span>
                <span class="badge {{ $student->workflow_status_label['class'] }}">{{ $student->workflow_status_label['text'] }}</span>
            @else
                <span class="badge bg-warning text-dark">Pending</span>
            @endif
        </td>
        <td>{{ $result?->overall_grade ?? 'N/A' }}</td>
        <td>{{ $result?->overall_percentage !== null ? $result->overall_percentage . '%' : 'N/A' }}</td>
        <td style="width: 100px;">
            <div class="d-flex flex-wrap gap-1 align-items-center">
            @if ($result)
                @if ($canManageResults)
                    <a href="{{ route('results.show', $result->id) }}" class="btn btn-info btn-sm">
                        <i class="ri-eye-line"></i>
                    </a>
                @endif

                @if ($student->can_edit_result)
                    <a href="{{ route('results.edit', $result->id) }}" class="btn btn-warning btn-sm ajax-nav">
                        <i class="ri-edit-line"></i>
                    </a>
                @endif

                @if ($student->can_forward_to_class_teacher)
                    <form action="{{ route('results.forward', $result->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Forward this result to class teacher?')">
                        @csrf
                        <input type="hidden" name="action" value="forward_class_teacher">
                        <button class="btn btn-primary btn-sm" type="submit" title="Forward to class teacher">
                            <i class="ri-send-plane-line"></i>
                        </button>
                    </form>
                @endif

                @if ($student->can_forward_to_coordinator)
                    <form action="{{ route('results.forward', $result->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Forward this result to coordinator?')">
                        @csrf
                        <input type="hidden" name="action" value="forward_coordinator">
                        <button class="btn btn-primary btn-sm" type="submit" title="Forward to coordinator">
                            <i class="ri-send-plane-line"></i>
                        </button>
                    </form>
                @endif

                @if ($student->can_approve_result)
                    <form action="{{ route('results.coordinator-approve', $result->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Approve this result?')">
                        @csrf
                        <button class="btn btn-success btn-sm" type="submit" title="Approve">
                            <i class="ri-check-line"></i>
                        </button>
                    </form>
                @endif

                @if ($student->can_delete_result)
                    <form action="{{ route('results.destroy', $result->id) }}" method="POST"
                        class="d-inline ajax-delete-result"
                        onsubmit="return confirm('Rollback this result to create state?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('results.create', ['student_id' => $student->id]) }}"
                    class="btn btn-primary btn-sm ajax-nav">
                    Create Result
                </a>
            @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center">No students found.</td>
    </tr>
@endforelse
