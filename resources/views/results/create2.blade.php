@extends('layouts.main')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div style="display:grid; grid-template-columns:15% 70% 15%; align-items:center;" class="mt-4">
                <div class=""></div>
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
                                                placeholder="Enter Student Name" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-6">
                                            <label for="roll_no" class="form-label">Roll No *</label>
                                            <input type="text" class="form-control" id="roll_no"
                                                placeholder="Enter Roll No" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="class" class="form-label">Class *</label>
                                            <input type="text" class="form-control" id="class"
                                                placeholder="Enter Class" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="section" class="form-label">Section *</label>
                                            <input type="text" class="form-control" id="section"
                                                placeholder="Enter Section" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="promoted_class" class="form-label">Promoted To *</label>
                                            <input type="text" class="form-control" id="promoted_class"
                                                name="promoted_class" placeholder="Next Class">

                                        </div>
                                    </div>

                                    <div class="row session-row" data-row="0">
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label class="form-label">Session</label>
                                                <select class="form-select session-select" required>
                                                    <option selected disabled value="">Select Session</option>
                                                    @foreach ($wdays as $swday)
                                                        <option value="{{ $swday->id }}"
                                                            data-term-one="{{ $swday->t1_working_days }}"
                                                            data-term-two="{{ $swday->t2_working_days }}">
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
                                                    <input type="number" class="form-control term-one-working"
                                                        min="0" step="0.01" placeholder="Term One Working Days">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text term-one-label">/ 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label class="form-label">Term Two Working Days</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control term-two-working"
                                                        min="0" step="0.01" placeholder="Term Two Working Days">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text term-two-label">/ 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Enter remarks">{{ old('remarks', $student->remarks ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
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

                                    <div class="row subject-row" data-row="0">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Subject Name</label>
                                                <select class="form-select subject-select" required>
                                                    <option selected disabled value="">Select Subject</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Term One Mark</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control term-one-mark"
                                                        placeholder="Term One Marks" min="0" max="100"
                                                        step="0.01">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text term-one-label">/ 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Term Two Mark</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control term-two-mark"
                                                        placeholder="Term Two Marks" min="0" max="100"
                                                        step="0.01">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text term-two-label">/ 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mt-2">
                                            <div class="m-3" style="display: flex; justify-content:end;">
                                                <button style="width: 120px;" class="btn btn-primary add-subject-btn" type="button">
                                                    <i class="ri-add-line"></i>&nbsp;Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12" style="display: flex; justify-content:end;">
                                            <button class="btn btn-success btn-lg" type="submit" id="submitBtn">
                                                <i class="ri-save-line"></i>&nbsp;Save All Data
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
                <div class=""></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function() {
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let rowCounter = 0;

                /* ===================== SESSION SELECT HANDLING ===================== */
                $(document).on('change', '.session-select', function() {
                    const selectedOption = $(this).find('option:selected');
                    const termOneMax = selectedOption.data('term-one') || 0;
                    const termTwoMax = selectedOption.data('term-two') || 0;

                    const currentRow = $(this).closest('.session-row');
                    const termOneInput = currentRow.find('.term-one-working');
                    const termTwoInput = currentRow.find('.term-two-working');
                    const termOneLabel = currentRow.find('.term-one-label');
                    const termTwoLabel = currentRow.find('.term-two-label');

                    termOneInput.val('');
                    termTwoInput.val('');
                    termOneInput.attr('max', termOneMax);
                    termTwoInput.attr('max', termTwoMax);
                    termOneLabel.text('/ ' + termOneMax);
                    termTwoLabel.text('/ ' + termTwoMax);
                });

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
                            if (selectedIds.includes(optionValue) && optionValue !==
                                currentValue) {
                                $(this).prop('disabled', true).addClass('text-muted');
                            } else {
                                $(this).prop('disabled', false).removeClass('text-muted');
                            }
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

                    termOneLabel.text('/ ...');
                    termTwoLabel.text('/ ...');

                    $.ajax({
                        type: "GET",
                        url: "{{ route('subject-total-marks') }}",
                        data: {
                            subject_id: subjectId
                        },
                        success: function(response) {
                            if (response.success) {
                                const termOneMax = response.data.term_one_total_mark || 100;
                                const termTwoMax = response.data.term_two_total_mark || 100;
                                termOneLabel.text('/ ' + termOneMax);
                                termTwoLabel.text('/ ' + termTwoMax);
                                termOneInput.attr('max', termOneMax);
                                termTwoInput.attr('max', termTwoMax);
                            } else {
                                termOneLabel.text('/ 100');
                                termTwoLabel.text('/ 100');
                            }
                        },
                        error: function() {
                            termOneLabel.text('/ 100');
                            termTwoLabel.text('/ 100');
                        }
                    });
                });

                /* ===================== ADD / DELETE SUBJECT ROW ===================== */
                $(document).on('click', '.add-subject-btn', function(e) {
                    e.preventDefault();
                    const currentRow = $(this).closest('.subject-row');
                    const subjectSelect = currentRow.find('.subject-select');
                    const termOneMark = currentRow.find('.term-one-mark').val();
                    const termTwoMark = currentRow.find('.term-two-mark').val();
                    const termOneMax = parseFloat(currentRow.find('.term-one-mark').attr('max')) || 0;
                    const termTwoMax = parseFloat(currentRow.find('.term-two-mark').attr('max')) || 0;

                    if (!subjectSelect.val()) {
                        alertMessage('Please select a subject before adding a new row.');
                        return;
                    }
                    if (!termOneMark && !termTwoMark) {
                        alertMessage('Please enter marks for at least one term.');
                        return;
                    }
                    if (termOneMark && parseFloat(termOneMark) > termOneMax) {
                        alertMessage(`Term One marks cannot exceed ${termOneMax}`);
                        return;
                    }
                    if (termTwoMark && parseFloat(termTwoMark) > termTwoMax) {
                        alertMessage(`Term Two marks cannot exceed ${termTwoMax}`);
                        return;
                    }

                    $(this).removeClass('add-subject-btn btn-primary').addClass(
                        'delete-subject-btn btn-danger').html(
                        '<i class="ri-delete-bin-line"></i>&nbsp;Delete');

                    rowCounter++;

                    currentRow.after(`
                <div class="row subject-row mt-3">
                    <div class="col-md-4">
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

                    <div class="col-md-2 mt-4" style="diplay:flex; justify-content:end;">
                        <button style="width: 90px;" class="btn btn-primary add-subject-btn" type="button">
                            <i class="ri-add-line"></i>&nbsp;Add
                        </button>
                    </div>
                </div>
            `);

                    updateAvailableSubjects();
                });

                $(document).on('click', '.delete-subject-btn', function(e) {
                    e.preventDefault();
                    $(this).closest('.subject-row').remove();
                    updateAvailableSubjects();
                });

                /* ===================== FORM SUBMISSION ===================== */
                $(document).on('submit', '.needs-validation', function(e) {
                    e.preventDefault();

                    const studentName = $('#student_name').val();
                    const rollNo = $('#roll_no').val();
                    const studentClass = $('#class').val();
                    const section = $('#section').val();
                    const sessionId = $('.session-select').val();
                    const termOneWorking = $('.term-one-working').val();
                    const termTwoWorking = $('.term-two-working').val();
                    const remarks = $('#remarks').val(); // ✅ Added
                    const promotedClass = $('#promoted_class').val();


                    if (!studentName || !rollNo || !studentClass || !section) {
                        alertMessage(
                            'Please fill all student details (Name, Roll No, Class, Section).');
                        return;
                    }
                    if (!sessionId) {
                        alertMessage('Please select a session.');
                        return;
                    }

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

                    if (!isValid) {
                        alertMessage('Please enter marks for at least one term for each subject.');
                        return;
                    }
                    if (subjectsData.length === 0) {
                        alertMessage('Please add at least one subject with marks.');
                        return;
                    }

                    const formData = {
                        student_name: studentName,
                        roll_no: rollNo,
                        class: studentClass,
                        section: section,
                        promoted_class: promotedClass || null, // ✅ REQUIRED
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
                        '<i class="ri-loader-4-line"></i>&nbsp;Saving...');

                    $.ajax({
                        type: "POST",
                        url: "{{ route('student_result.store') }}",
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                alertMessage(response.message || 'Data saved successfully!',
                                    'success');
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('students.result') }}";
                                }, 1500);
                            } else {
                                alertMessage(response.message || 'Failed to save data.',
                                    'danger');
                                submitBtn.prop('disabled', false).html(
                                    '<i class="ri-save-line"></i>&nbsp;Save All Data');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Error while saving data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            alertMessage(errorMsg, 'danger');
                            submitBtn.prop('disabled', false).html(
                                '<i class="ri-save-line"></i>&nbsp;Save All Data');
                        }
                    });
                });

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
