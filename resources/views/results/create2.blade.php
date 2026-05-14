@extends('layouts.main')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-4">
                <div class="col-xl-10">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h4 class="card-title mb-0">{{ $mode === 'edit' ? 'Edit Result' : 'Create Result' }}</h4>
                                <span class="badge {{ $workflowStatusLabel['class'] }}">{{ $workflowStatusLabel['text'] }}</span>
                            </div>
                            <hr>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Student Name</label>
                                    <input type="text" class="form-control" id="student_name"
                                        value="{{ $syncedStudent->stdname }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Roll No</label>
                                    <input type="text" class="form-control" id="roll_no"
                                        value="{{ $syncedStudent->rollno }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Father Name</label>
                                    <input type="text" class="form-control" value="{{ $syncedStudent->fathername }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Phone No</label>
                                    <input type="text" class="form-control" value="{{ $syncedStudent->phone_no }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Branch</label>
                                    <input type="text" class="form-control" value="{{ $branchName }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Class</label>
                                    <input type="text" class="form-control" id="class" value="{{ $classRecord->name }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Section</label>
                                    <input type="text" class="form-control" id="section" value="{{ $sectionRecord->name }}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Session</label>
                                    <input type="text" class="form-control" value="{{ $activeSession?->title }}" readonly>
                                </div>

                                <input type="hidden" id="student_id" value="{{ $syncedStudent->id }}">
                                <input type="hidden" id="branch_id" value="{{ $syncedStudent->owned_by }}">
                                <input type="hidden" id="class_id" value="{{ $classRecord->id }}">
                                <input type="hidden" id="section_id" value="{{ $sectionRecord->id }}">
                                <input type="hidden" id="session_id" value="{{ $activeSession?->id }}">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Result Details</h4>
                            <hr>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Promoted To</label>
                                    <input type="text" class="form-control" id="promoted_class"
                                        value="{{ $studentResult?->promoted_class }}"
                                        placeholder="Next Class" {{ $canEditDetails ? '' : 'readonly' }}>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Term One Working Days</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control term-one-working"
                                            min="0" max="{{ $activeSession?->t1_working_days ?? 0 }}"
                                            step="0.01" value="{{ $studentResult?->t1_working_days }}"
                                            {{ $canEditDetails ? '' : 'readonly' }}>
                                        <span class="input-group-text">/ {{ $activeSession?->t1_working_days ?? 0 }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Term Two Working Days</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control term-two-working"
                                            min="0" max="{{ $activeSession?->t2_working_days ?? 0 }}"
                                            step="0.01" value="{{ $studentResult?->t2_working_days }}"
                                            {{ $canEditDetails ? '' : 'readonly' }}>
                                        <span class="input-group-text">/ {{ $activeSession?->t2_working_days ?? 0 }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Remarks</label>
                                    <textarea id="remarks" class="form-control" rows="3" placeholder="Enter remarks"
                                        {{ $canEditDetails ? '' : 'readonly' }}>{{ $studentResult?->remarks }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Subjects Marks</h4>
                            <hr>
                            <span id="message"></span>

                            <form class="needs-validation" novalidate data-action="{{ $formAction }}">
                                @csrf

                                @forelse ($subjectRows as $subject)
                                    <div class="row subject-row g-3 align-items-end {{ $loop->first ? '' : 'mt-2' }}">
                                        <div class="col-md-4">
                                            <label class="form-label">Subject</label>
                                            <input type="hidden" class="subject-id" value="{{ $subject->id }}">
                                            <input type="text" class="form-control" value="{{ $subject->subject_name }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Term One Mark</label>
                                            <div class="input-group">
                                        <input type="number" class="form-control term-one-mark"
                                            min="0" max="{{ $subject->term_one_total }}" step="0.01"
                                                    value="{{ $subject->term_one_mark }}" {{ $canEditForm ? '' : 'readonly' }}>
                                                <span class="input-group-text">/ {{ $subject->term_one_total }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Term Two Mark</label>
                                            <div class="input-group">
                                        <input type="number" class="form-control term-two-mark"
                                            min="0" max="{{ $subject->term_two_total }}" step="0.01"
                                                    value="{{ $subject->term_two_mark }}" {{ $canEditForm ? '' : 'readonly' }}>
                                                <span class="input-group-text">/ {{ $subject->term_two_total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">No subjects assigned to this class. Contact the administrator.</div>
                                @endforelse

                                <div class="row mt-4">
                                    <div class="col-md-12 d-flex flex-wrap justify-content-end gap-2">
                                        <button class="btn btn-success btn-lg" type="submit" id="submitBtn"
                                            data-action-type="save" {{ $subjectRows->isEmpty() || !$canEditForm ? 'disabled' : '' }}>
                                            <i class="ri-save-line"></i>&nbsp;{{ $mode === 'edit' ? 'Update Result' : 'Save Result' }}
                                        </button>

                                        @if ($canForwardToClassTeacher)
                                            <button class="btn btn-primary btn-lg workflow-submit" type="submit"
                                                data-action-type="forward_class_teacher"
                                                {{ $subjectRows->isEmpty() ? 'disabled' : '' }}>
                                                <i class="ri-send-plane-line"></i>&nbsp;Save & FW to Class Teacher
                                            </button>
                                        @endif

                                        @if ($canForwardToCoordinator)
                                            <button class="btn btn-primary btn-lg workflow-submit" type="submit"
                                                data-action-type="forward_coordinator"
                                                {{ $subjectRows->isEmpty() ? 'disabled' : '' }}>
                                                <i class="ri-send-plane-line"></i>&nbsp;Save & FW to Coordinator
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function() {
            $(document).ready(function() {
                let submitAction = 'save';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $(document).on('input', 'input[type="number"]', function() {
                    const value = parseFloat($(this).val());
                    const max = parseFloat($(this).attr('max'));
                    $(this).toggleClass('is-invalid', !Number.isNaN(value) && value > max);
                });

                $(document).on('click', 'button[type="submit"][data-action-type]', function() {
                    submitAction = $(this).data('action-type') || 'save';
                });

                $(document).on('submit', '.needs-validation', function(e) {
                    e.preventDefault();
                    const clickedButton = e.originalEvent?.submitter;
                    submitAction = clickedButton?.dataset?.actionType || submitAction || 'save';

                    const subjectsData = [];
                    let isValid = true;

                    $('.subject-row').each(function() {
                        const termOneInput = $(this).find('.term-one-mark');
                        const termTwoInput = $(this).find('.term-two-mark');
                        const subjectId = $(this).find('.subject-id').val();
                        const termOne = termOneInput.val();
                        const termTwo = termTwoInput.val();

                        if (termOne && parseFloat(termOne) > parseFloat(termOneInput.attr('max'))) {
                            isValid = false;
                            return false;
                        }

                        if (termTwo && parseFloat(termTwo) > parseFloat(termTwoInput.attr('max'))) {
                            isValid = false;
                            return false;
                        }

                        if (termOne || termTwo) {
                            subjectsData.push({
                                subject_id: subjectId,
                                term_one_mark: termOne || null,
                                term_two_mark: termTwo || null
                            });
                        }
                    });

                    if (!isValid) {
                        alertMessage('Marks cannot exceed total marks.', 'warning');
                        return;
                    }

                    if (subjectsData.length === 0) {
                        alertMessage('Please enter marks for at least one subject.', 'warning');
                        return;
                    }

                    const formData = {
                        student_id: $('#student_id').val(),
                        branch_id: $('#branch_id').val(),
                        class_id: $('#class_id').val(),
                        section_id: $('#section_id').val(),
                        session_id: $('#session_id').val(),
                        student_name: $('#student_name').val(),
                        roll_no: $('#roll_no').val(),
                        class: $('#class').val(),
                        section: $('#section').val(),
                        promoted_class: $('#promoted_class').val() || null,
                        working_days: {
                            term_one: $('.term-one-working').val() || null,
                            term_two: $('.term-two-working').val() || null
                        },
                        remarks: $('#remarks').val(),
                        subjects: subjectsData,
                        submit_action: submitAction
                    };

                    const submitBtn = $(`button[type="submit"][data-action-type="${submitAction}"]`);
                    const originalHtml = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line"></i>&nbsp;Saving...');

                    $.ajax({
                        type: 'POST',
                        url: $(this).data('action'),
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                alertMessage(response.message || 'Result saved successfully.', 'success');
                                setTimeout(function() {
                                    window.location.href = "{{ route('students.result') }}";
                                }, 1000);
                            } else {
                                alertMessage(response.message || 'Failed to save result.', 'danger');
                                submitBtn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Error while saving result.';
                            alertMessage(message, 'danger');
                            submitBtn.prop('disabled', false).html(originalHtml);
                        }
                    });
                });

                function alertMessage(msg, type = 'warning') {
                    $('#message').html(
                        `<div class="alert alert-${type} alert-dismissible fade show">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`
                    );
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                }
            });
        })();
    </script>
@endsection
