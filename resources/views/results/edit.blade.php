@extends('layouts.main')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div style="display:grid; grid-template-columns:15% 70% 15%; align-items:center;" class="mt-4">
                <div></div>
                <div class="row">

                    <!-- Student Details Card -->
                    <div class="col-xl-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Student Details</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-6">
                                            <label for="student_name" class="form-label">Student Name *</label>
                                            <input type="text" class="form-control" id="student_name"
                                                placeholder="Enter Student Name" required
                                                value="{{ old('student_name', $student->name) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-6">
                                            <label for="roll_no" class="form-label">Roll No *</label>
                                            <input type="text" class="form-control" id="roll_no"
                                                placeholder="Enter Roll No" required disabled
                                                value="{{ old('roll_no', $student->rollno) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="class" class="form-label">Class *</label>
                                            <input type="text" class="form-control" id="class"
                                                placeholder="Enter Class" required
                                                value="{{ old('class', $student->class) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="section" class="form-label">Section *</label>
                                            <input type="text" class="form-control" id="section"
                                                placeholder="Enter Section" required
                                                value="{{ old('section', $student->section) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="promoted_class" class="form-label">Promoted To *</label>
                                            <input type="text" name="promoted_class"  class="form-control" id="promoted_class"
                                                placeholder="Next Class" required
                                                value="{{ old('promoted_class', $student->promoted_class) }}">
                                        </div>
                                    </div>
                                </div>
                                {{-- </div> --}}
                                <div class="row session-row mt-2" data-row="0">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label class="form-label">Session</label>
                                            <select class="form-select session-select" required>
                                                <option selected disabled value="">Select Session</option>
                                                @foreach ($wdays as $swday)
                                                    <option value="{{ $swday->id }}"
                                                        data-term-one="{{ $swday->t1_working_days }}"
                                                        data-term-two="{{ $swday->t2_working_days }}"
                                                        {{ $student->session_id == $swday->id ? 'selected' : '' }}>
                                                        {{ $swday->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label class="form-label">Term One Working Days</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control term-one-working" min="0"
                                                    step="0.01" placeholder="Term One Working Days"
                                                    value="{{ old('term_one_working_days', $student->t1_working_days ?? '') }}">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text term-one-label">/
                                                        {{ $student->session->t1_working_days ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label class="form-label">Term Two Working Days</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control term-two-working" min="0"
                                                    step="0.01" placeholder="Term Two Working Days"
                                                    value="{{ old('term_two_working_days', $student->t2_working_days ?? '') }}">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text term-two-label">/
                                                        {{ $student->session->t2_working_days ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row mt-12"> --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Teacher's Remarks</label>
                                            <textarea class="form-control" id="remarks" rows="4" placeholder="Enter teacher's remarks">{{ old('remarks', $student->remarks ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    {{-- </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subjects Marks Card -->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Subjects Marks</h4>
                                <hr>

                                <span id="message"></span>

                                <form class="needs-validation" novalidate>
                                    @csrf

                                    {{-- Container for all subject rows --}}
                                    <div id="subject-rows-container">
                                        {{-- Existing subjects appear first --}}
                                        @foreach ($student->marks as $i => $mark)
                                            <div class="row subject-row mt-3" data-row="{{ $i }}">
                                                <div class="col-md-4">
                                                    <label class="form-label">Subject Name</label>
                                                    <select class="form-select subject-select" required>
                                                        <option selected disabled value="">Select Subject</option>
                                                        @foreach ($subjects as $subject)
                                                            <option value="{{ $subject->id }}"
                                                                {{ $subject->id == $mark->subject_id ? 'selected' : '' }}>
                                                                {{ $subject->subject_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Term One Mark</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control term-one-mark"
                                                            min="0" max="100" step="0.01"
                                                            value="{{ $mark->term_one_mark }}">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text term-one-label">/
                                                                {{ $mark->subject?->term_one_marks ?? 100 }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Term Two Mark</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control term-two-mark"
                                                            min="0" max="100" step="0.01"
                                                            value="{{ $mark->term_two_mark }}">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text term-two-label">/
                                                                {{ $mark->subject?->term_two_marks ?? 100 }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 mt-4" style="display: flex; justify-content:end;">
                                                    @if ($i == 0)
                                                        <button class="btn btn-primary add-subject-btn" type="button">
                                                            <i class="ri-add-line"></i>&nbsp;Add
                                                        </button>
                                                    @else
                                                        <button class="btn btn-danger delete-subject-btn" type="button">
                                                            <i class="ri-delete-bin-line"></i>&nbsp;Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-4" >
                                        <div class="col-md-12" style="display: flex; justify-content:end;">
                                            <button class="btn btn-success btn-lg" type="submit" id="submitBtn">
                                                <i class="ri-save-line"></i>&nbsp;Update Data
                                            </button>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                </div>
                <div></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function() {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }

            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let rowCounter = {{ count($student->marks) }};

                // SESSION SELECT HANDLING
                $(document).on('change', '.session-select', function() {
                    const selectedOption = $(this).find('option:selected');
                    const termOneMax = selectedOption.data('term-one') || 0;
                    const termTwoMax = selectedOption.data('term-two') || 0;
                    const currentRow = $(this).closest('.session-row');
                    currentRow.find('.term-one-working').attr('max', termOneMax);
                    currentRow.find('.term-two-working').attr('max', termTwoMax);
                    currentRow.find('.term-one-label').text('/ ' + termOneMax);
                    currentRow.find('.term-two-label').text('/ ' + termTwoMax);
                });

                // SUBJECT SELECTION HANDLING
                function getSelectedSubjectIds() {
                    const selectedIds = [];
                    $('.subject-select').each(function() {
                        const val = $(this).val();
                        if (val) selectedIds.push(val);
                    });
                    return selectedIds;
                }

                function updateAvailableSubjects() {
                    const selectedIds = getSelectedSubjectIds();
                    $('.subject-select').each(function() {
                        const currentSelect = $(this);
                        const currentValue = currentSelect.val();
                        currentSelect.find('option').each(function() {
                            const optionValue = $(this).val();
                            if (optionValue === '') return;
                            $(this).prop('disabled', selectedIds.includes(optionValue) &&
                                    optionValue !== currentValue)
                                .toggleClass('text-muted', selectedIds.includes(optionValue) &&
                                    optionValue !== currentValue);
                        });
                    });
                }

                $(document).on('change', '.subject-select', function() {
                    updateAvailableSubjects();
                    const subjectId = $(this).val();
                    const currentRow = $(this).closest('.subject-row');
                    const termOneInput = currentRow.find('.term-one-mark');
                    const termTwoInput = currentRow.find('.term-two-mark');
                    const termOneLabel = currentRow.find('.term-one-label');
                    const termTwoLabel = currentRow.find('.term-two-label');

                    if (!subjectId) {
                        termOneLabel.text('/ 0');
                        termTwoLabel.text('/ 0');
                        termOneInput.attr('max', 0);
                        termTwoInput.attr('max', 0);
                        return;
                    }

                    $.ajax({
                        type: "GET",
                        url: "{{ route('subject-total-marks') }}",
                        data: {
                            subject_id: subjectId
                        },
                        success: function(response) {
                            const termOneMax = response.data?.term_one_total_mark ?? 100;
                            const termTwoMax = response.data?.term_two_total_mark ?? 100;
                            termOneLabel.text('/ ' + termOneMax);
                            termTwoLabel.text('/ ' + termTwoMax);
                            termOneInput.attr('max', termOneMax);
                            termTwoInput.attr('max', termTwoMax);
                        },
                        error: function() {
                            termOneLabel.text('/ 100');
                            termTwoLabel.text('/ 100');
                        }
                    });
                });

                // ADD/DELETE SUBJECT ROW
                $(document).on('click', '.add-subject-btn', function(e) {
                    e.preventDefault();
                    const currentRow = $(this).closest('.subject-row');
                    $(this).removeClass('add-subject-btn btn-primary').addClass(
                            'delete-subject-btn btn-danger')
                        .html('<i class="ri-delete-bin-line"></i>&nbsp;Delete');

                    rowCounter++;
                    const newRowHtml = `
                <div class="row subject-row mt-3">
                    <div class="col-md-3">
                        <label class="form-label">Subject Name</label>
                        <select class="form-select subject-select">
                            <option selected disabled value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term One Mark</label>
                        <div class="input-group">
                            <input type="number" class="form-control term-one-mark" min="0" max="1000" step="0.01" placeholder="Term One Marks">
                            <div class="input-group-prepend">
                                <span class="input-group-text term-one-label">/ 100</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Term Two Mark</label>
                        <div class="input-group">
                            <input type="number" class="form-control term-two-mark" min="0" max="1000" step="0.01" placeholder="Term Two Marks">
                            <div class="input-group-prepend">
                                <span class="input-group-text term-two-label">/ 100</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button class="btn btn-primary add-subject-btn" type="button">
                            <i class="ri-add-line"></i>&nbsp;Add
                        </button>
                    </div>
                </div>
            `;
                    $('#subject-rows-container').append(newRowHtml);
                    updateAvailableSubjects();
                });

                $(document).on('click', '.delete-subject-btn', function(e) {
                    e.preventDefault();
                    $(this).closest('.subject-row').remove();
                    updateAvailableSubjects();
                });

                // REAL-TIME VALIDATION
                $(document).on('input', '.term-one-mark, .term-two-mark, .term-one-working, .term-two-working',
                    function() {
                        const value = parseFloat($(this).val());
                        const max = parseFloat($(this).attr('max'));
                        $(this).toggleClass('is-invalid', value < 0 || value > max);
                    });

                // FORM SUBMISSION
                $(document).on('submit', '.needs-validation', function(e) {
                    e.preventDefault();
                    const studentName = $('#student_name').val();
                    const rollNo = $('#roll_no').val();
                    const studentClass = $('#class').val();
                    const section = $('#section').val();
                    const sessionId = $('.session-select').val();
                    const termOneWorking = $('.term-one-working').val();
                    const termTwoWorking = $('.term-two-working').val();
                    const remarks = $('#remarks').val();
                    const promoted_class = $('#promoted_class').val();

                    if (!studentName || !rollNo || !studentClass || !section) {
                        return alertMessage(
                            'Please fill all student details (Name, Roll No, Class, Section).');
                    }
                    if (!sessionId) return alertMessage('Please select a session.');

                    const subjectsData = [];
                    let isValid = true;

                    $('.subject-row').each(function() {
                        const subjectId = $(this).find('.subject-select').val();
                        const termOne = $(this).find('.term-one-mark').val();
                        const termTwo = $(this).find('.term-two-mark').val();
                        if (!subjectId) return true;
                        if (!termOne && !termTwo) {
                            isValid = false;
                            return false;
                        }
                        subjectsData.push({
                            subject_id: subjectId,
                            term_one_mark: termOne || null,
                            term_two_mark: termTwo || null
                        });
                    });
                    if (!isValid) return alertMessage(
                        'Please enter marks for at least one term for each subject.');
                    if (subjectsData.length === 0) return alertMessage(
                        'Please add at least one subject with marks.');

                    const formData = {
                        student_name: studentName,
                        roll_no: rollNo,
                        class: studentClass,
                        section: section,
                        promoted_class: promoted_class,
                        session_id: sessionId,
                        working_days: {
                            term_one: termOneWorking || null,
                            term_two: termTwoWorking || null
                        },
                        subjects: subjectsData,
                        remarks: remarks
                    };

                    const submitBtn = $('#submitBtn');
                    submitBtn.prop('disabled', true).html(
                        '<i class="ri-loader-4-line"></i>&nbsp;Updating...');

                    $.ajax({
                        type: "POST",
                        url: "{{ route('results.update', $student->id) }}",
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                alertMessage(response.message ||
                                    'Data updated successfully!', 'success');
                                setTimeout(() => window.location.href =
                                    "{{ route('students.result') }}", 1500);
                            } else {
                                alertMessage(response.message || 'Failed to update data.',
                                    'danger');
                                submitBtn.prop('disabled', false).html(
                                    '<i class="ri-save-line"></i>&nbsp;Update Data');
                            }
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON?.message ||
                                'Error while updating data.';
                            alertMessage(errorMsg, 'danger');
                            submitBtn.prop('disabled', false).html(
                                '<i class="ri-save-line"></i>&nbsp;Update Data');
                        }
                    });
                });

                // ALERT HELPER
                function alertMessage(msg, type = 'warning') {
                    $('#message').html(
                        `<div class="alert alert-${type} alert-dismissible fade show">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`
                    );
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                }

                updateAvailableSubjects();
            });
        })();
    </script>
@endsection
