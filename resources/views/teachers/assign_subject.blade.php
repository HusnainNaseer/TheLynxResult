@extends('layouts.main')

@section('content')

@php
    $subjects = \DB::table('subject_wise_marks')
        ->select('id', 'subject_name')
        ->distinct()
        ->orderBy('subject_name')
        ->get();
@endphp

<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">

    <div class="col-lg-12">

        {{-- Form --}}
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Assign Subjects to {{ $teacher->name }}
                </h5>

                <a href="{{ route('assign-subjects.index') }}"
                   class="btn btn-light btn-sm">

                    <i class="ri-arrow-left-line me-1"></i>
                    Back to List
                </a>
            </div>

            <div class="card-body">

                <div id="formAlert"></div>

                <div class="row g-3 align-items-end">

                    {{-- Branch --}}
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Branch
                        </label>

                        <select id="branchSelect" class="form-select">

                            <option value="">
                                — Select Branch —
                            </option>

                        </select>

                    </div>

                    {{-- Class --}}
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Class
                        </label>

                        <select id="classSelect"
                                class="form-select"
                                disabled>

                            <option value="">
                                — Select Class —
                            </option>

                        </select>

                    </div>

                    {{-- Section --}}
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Section
                        </label>

                        <select id="sectionSelect"
                                class="form-select"
                                disabled>

                            <option value="">
                                — Select Section —
                            </option>

                        </select>

                    </div>

                    {{-- Subject --}}
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Subject
                        </label>

                        <select id="subjectSelect"
                                class="form-select"
                                disabled>

                            <option value="">
                                — Select Subject —
                            </option>

                        </select>

                    </div>

                    {{-- Button --}}
                    <div class="col-12 text-end">

                        <button type="button"
                                id="assignBtn"
                                class="btn btn-primary"
                                disabled>

                            <i class="ri-add-line me-1"></i>
                            Add Subject

                        </button>

                    </div>

                </div>

            </div>
        </div>

        {{-- Assigned Subjects Table --}}
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="card-title mb-0">
                    Assigned Subjects
                </h5>

                <span class="badge bg-primary"
                      id="assignmentCount">

                    {{ $assignments->count() }}

                </span>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Branch</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th class="text-center">Action</th>
                            </tr>

                        </thead>

                        <tbody id="assignmentsBody">

                            @forelse($assignments as $index => $a)

                                <tr id="row-{{ $a->id }}">

                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $a->branch_name }}</td>

                                    <td>{{ $a->class_name }}</td>

                                    <td>{{ $a->section_name }}</td>

                                    <td>{{ $a->subject_name }}</td>

                                    <td class="text-center">

                                        <button class="btn btn-danger btn-sm btn-remove"
                                                data-id="{{ $a->id }}">

                                            <i class="ri-delete-bin-line"></i>

                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr id="emptyRow">

                                    <td colspan="6"
                                        class="text-center text-muted py-4">

                                        No subjects assigned yet.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

        

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function () {

    const teacherId = {{ $teacher->id }};
    const CSRF = $('meta[name="csrf-token"]').attr('content');

    // ALERT
    function showAlert(message, type = 'danger') {

        $('#formAlert').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        `);

    }

    // RESET SELECT
    function resetSelect(select, placeholder) {

        select.html(`<option value="">${placeholder}</option>`);
        select.prop('disabled', true);

    }

    // CHECK BUTTON
    function checkButton() {

        let valid =
            $('#branchSelect').val() &&
            $('#classSelect').val() &&
            $('#sectionSelect').val() &&
            $('#subjectSelect').val();

        $('#assignBtn').prop('disabled', !valid);

    }

    // LOAD BRANCHES
    $.ajax({

        url: '{{ route("assign-subjects.api.branches") }}',
        type: 'GET',

        success: function (response) {

            let branches = response.data ?? response;

            branches.forEach(branch => {

                $('#branchSelect').append(`
                    <option value="${branch.id}"
                            data-name="${branch.name}">
                        ${branch.name}
                    </option>
                `);

            });

        }

    });

    // BRANCH CHANGE → CLASSES
    $('#branchSelect').on('change', function () {

        let branchId = $(this).val();

        resetSelect($('#classSelect'), '— Select Class —');
        resetSelect($('#sectionSelect'), '— Select Section —');
        resetSelect($('#subjectSelect'), '— Select Subject —');

        checkButton();

        if (!branchId) return;

        $.ajax({

            url: '{{ route("assign-subjects.api.classes") }}',
            type: 'GET',

            data: {
                branch_id: branchId
            },

            success: function (response) {

                let classes = response.data ?? response;

                $('#classSelect').prop('disabled', false);

                classes.forEach(cls => {

                    $('#classSelect').append(`
                        <option value="${cls.id}"
                                data-name="${cls.name}">
                            ${cls.name}
                        </option>
                    `);

                });

            }

        });

    });

    // CLASS CHANGE → SECTIONS
    $('#classSelect').on('change', function () {

        let classId = $(this).val();

        resetSelect($('#sectionSelect'), '— Select Section —');
        resetSelect($('#subjectSelect'), '— Select Subject —');

        checkButton();

        if (!classId) return;

        $.ajax({

            url: '{{ route("assign-subjects.api.sections") }}',
            type: 'GET',

            data: {
                class_id: classId
            },

            success: function (response) {

                let sections = response.data ?? response;

                $('#sectionSelect').prop('disabled', false);

                sections.forEach(section => {

                    $('#sectionSelect').append(`
                        <option value="${section.id}"
                                data-name="${section.name}">
                            ${section.name}
                        </option>
                    `);

                });

            }

        });

    });

    // SECTION CHANGE → SUBJECTS
    $('#sectionSelect').on('change', function () {

        resetSelect($('#subjectSelect'), '— Select Subject —');

        let sectionId = $(this).val();

        checkButton();

        if (!sectionId) return;

        let subjects = @json($subjects);

        $('#subjectSelect').prop('disabled', false);

        subjects.forEach(subject => {

            $('#subjectSelect').append(`
                <option value="${subject.id}"
                        data-name="${subject.subject_name}">
                    ${subject.subject_name}
                </option>
            `);

        });

    });

    // SUBJECT CHANGE
    $('#subjectSelect').on('change', function () {
        checkButton();
    });

    // SAVE ASSIGNMENT
    $('#assignBtn').on('click', function () {

        const branch = $('#branchSelect');
        const cls = $('#classSelect');
        const section = $('#sectionSelect');
        const subject = $('#subjectSelect');

        let payload = {

            teacher_id: teacherId,

            branch_id: branch.val(),
            branch_name: branch.find(':selected').data('name'),

            class_id: cls.val(),
            class_name: cls.find(':selected').data('name'),

            section_id: section.val(),
            section_name: section.find(':selected').data('name'),

            subject_id: subject.val(),
            subject_name: subject.find(':selected').data('name'),

            _token: CSRF

        };

        $.ajax({

            url: '{{ route("assign-subjects.store") }}',
            type: 'POST',
            data: payload,

            success: function (res) {

                if (res.success) {

                    $('#emptyRow').remove();

                    let a = res.assignment;

                    let count = $('#assignmentsBody tr[id^="row-"]').length + 1;

                    $('#assignmentsBody').append(`

                        <tr id="row-${a.id}">

                            <td>${count}</td>

                            <td>${a.branch_name}</td>

                            <td>${a.class_name}</td>

                            <td>${a.section_name}</td>

                            <td>${a.subject_name}</td>

                            <td class="text-center">

                                <button class="btn btn-danger btn-sm btn-remove"
                                        data-id="${a.id}">

                                    <i class="ri-delete-bin-line"></i>

                                </button>

                            </td>

                        </tr>

                    `);

                    $('#assignmentCount').text(count);

                    showAlert(res.message, 'success');

                    $('#branchSelect').val('').trigger('change');

                }

            },

            error: function (xhr) {

                showAlert(
                    xhr.responseJSON?.message ??
                    'Something went wrong.'
                );

            }

        });

    });

    // REMOVE ASSIGNMENT
    $(document).on('click', '.btn-remove', function () {

        let id = $(this).data('id');

        if (!confirm('Remove assignment?')) return;

        $.ajax({

            url: `/assign-subjects/${id}`,
            type: 'DELETE',

            data: {
                _token: CSRF
            },

            success: function (res) {

                if (res.success) {

                    $('#row-' + id).remove();

                    let rows = $('#assignmentsBody tr[id^="row-"]').length;

                    $('#assignmentCount').text(rows);

                    if (rows === 0) {

                        $('#assignmentsBody').html(`
                            <tr id="emptyRow">
                                <td colspan="6"
                                    class="text-center text-muted py-4">

                                    No subjects assigned yet.

                                </td>
                            </tr>
                        `);

                    }

                    showAlert(res.message, 'success');

                }

            }

        });

    });

});

</script>

@endsection