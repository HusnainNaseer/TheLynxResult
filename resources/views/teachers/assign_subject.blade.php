@extends('layouts.main')

@section('content')

<style>
    .teacher-assign-card {
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }

    .subject-select-wrap .select2-container {
        width: 100% !important;
    }

    .subject-select-wrap .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding: 0 .45rem;
    }

    .subject-select-wrap .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #405189;
        box-shadow: 0 0 0 .2rem rgba(64,81,137,.12);
    }

    .subject-select-wrap .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #e8f0fe;
        border: 1px solid #c6dafc;
        color: #1a73e8;
        border-radius: 16px;
        font-size: .78rem;
        font-weight: 600;
        margin-top: 0;
        max-width: 100%;
    }

    .subject-select-wrap .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: .25rem;
        width: 100%;
        padding: 0;
        overflow: hidden;
    }

    .subject-select-wrap .select2-container--default .select2-search--inline .select2-search__field {
        height: 24px;
        margin-top: 0;
        font-size: .875rem;
        min-width: 120px;
    }

    .subject-select-wrap .select2-container--default.select2-container--disabled .select2-selection--multiple {
        background: #eff2f7;
    }

    #subjectHint {
        min-height: 18px;
        margin-top: .25rem;
        line-height: 1.2;
    }

    .assign-action {
        padding-top: 1.72rem;
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">

                <div class="card teacher-assign-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            Assign Subjects to {{ $teacher->name }}
                        </h5>

                        <a href="{{ route('assign-subjects.index') }}" class="btn btn-light btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>
                            Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <div id="formAlert"></div>

                        <div class="row g-3 align-items-start">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Branch</label>
                                <select id="branchSelect" class="form-select">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Class</label>
                                <select id="classSelect" class="form-select" disabled>
                                    <option value="">Select Class</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Section</label>
                                <select id="sectionSelect" class="form-select" disabled>
                                    <option value="">Select Section</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Subjects</label>
                                <div class="subject-select-wrap">
                                    <select id="subjectSelect" class="form-select" multiple disabled></select>
                                </div>
                                <div class="form-text" id="subjectHint">
                                    Select branch, class, and section first
                                </div>
                            </div>

                            <div class="col-12 text-end assign-action">
                                <button type="button" id="cancelEditBtn" class="btn btn-light d-none">
                                    Cancel Edit
                                </button>
                                <button type="button" id="assignBtn" class="btn btn-primary" disabled>
                                    <i class="ri-add-line me-1"></i>
                                    Add Subject(s)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card teacher-assign-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Assigned Subjects</h5>
                        <span class="badge bg-primary" id="assignmentCount">{{ $assignmentGroups->count() }}</span>
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
                                    @forelse($assignmentGroups as $index => $group)
                                        <tr id="row-{{ $group->key }}" data-group-key="{{ $group->key }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $group->branch_name }}</td>
                                            <td>{{ $group->class_name }}</td>
                                            <td>{{ $group->section_name }}</td>
                                            <td class="group-subjects">{{ $group->subject_names->implode(', ') }}</td>
                                            <td class="text-center">
                                                @if (!auth()->user()->hasRole('Coordinator') || (string) $group->branch_id === (string) auth()->user()->branch_id)
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-warning btn-sm btn-edit"
                                                            data-group-key="{{ $group->key }}" title="Edit">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm btn-remove"
                                                            data-ids="{{ $group->ids->implode(',') }}"
                                                            data-group-key="{{ $group->key }}" title="Delete">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">View only</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyRow">
                                            <td colspan="6" class="text-center text-muted py-4">
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
<script src="{{ asset('assets/auth/libs/select2/js/select2.full.min.js')}}"></script>

<script>
$(document).ready(function () {
    const teacherId = {{ $teacher->id }};
    const CSRF = $('meta[name="csrf-token"]').attr('content');
    const allValue = '__all__';
    let availableSubjects = [];
    let allClassSubjects = [];
    let editGroupKey = null;
    let editingSubjectIds = new Set();
    const localBranches = @json($branches);
    const assignmentGroupsByKey = @json($assignmentGroups->mapWithKeys(fn($group) => [$group->key => $group])->toArray());
    const assignedSubjectsByGroup = @json(
        $assignmentGroups->mapWithKeys(fn($group) => [$group->key => $group->subject_ids])->toArray()
    );
    const storeUrl = '{{ route("assign-subjects.store") }}';
    const updateGroupUrl = '{{ route("assign-subjects.update-group") }}';
    const deleteUrlTemplate = '{{ route("assign-subjects.destroy", ["assignment" => "__ID__"]) }}';

    $('#subjectSelect').select2({
        width: '100%',
        placeholder: 'Select Subject',
        closeOnSelect: false
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showAlert(message, type = 'danger') {
        $('#formAlert').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }

    function resetSelect(select, placeholder, multiple = false) {
        if (multiple) {
            availableSubjects = [];
            allClassSubjects = [];
            select.html(`<option value="${allValue}">All subjects</option>`);
            select.val(null).trigger('change.select2');
            $('#subjectHint').text('Select branch, class, and section first').css('color', '#6c757d');
        } else {
            select.html(`<option value="">${escapeHtml(placeholder)}</option>`);
        }

        select.prop('disabled', true);
    }

    function selectedSubjectIds() {
        const selected = $('#subjectSelect').val() || [];

        if (selected.includes(allValue)) {
            return allClassSubjects.map(subject => String(subject.id));
        }

        return selected;
    }

    function currentGroupKey() {
        const branchId = $('#branchSelect').val();
        const classId = $('#classSelect').val();
        const sectionId = $('#sectionSelect').val();

        if (!branchId || !classId || !sectionId) {
            return '';
        }

        return branchId + '|' + classId + '|' + sectionId;
    }

    function checkButton() {
        const valid =
            $('#branchSelect').val() &&
            $('#classSelect').val() &&
            $('#sectionSelect').val() &&
            selectedSubjectIds().length > 0;

        $('#assignBtn').prop('disabled', !valid);
    }

    function appendOption(select, value, label) {
        select.append(`
            <option value="${escapeHtml(value)}" data-name="${escapeHtml(label)}">
                ${escapeHtml(label)}
            </option>
        `);
    }

    function formatSubjectName(subject) {
        const firstTermMarks = subject.term_one_marks ?? 0;
        const secondTermMarks = subject.term_two_marks ?? 0;

        return subject.name + ' (First Term: ' + firstTermMarks + ', Second Term: ' + secondTermMarks + ')';
    }

    function appendSubjectOption(subject) {
        const subjectId = String(subject.id);
        const disabled = subject.disabled && !editingSubjectIds.has(subjectId);
        const subjectName = formatSubjectName(subject);
        const suffix = disabled && subject.assigned_to
            ? ' - assigned to ' + subject.assigned_to
            : '';

        $('#subjectSelect').append(`
            <option
                value="${escapeHtml(subject.id)}"
                data-name="${escapeHtml(subject.name)}"
                ${disabled ? 'disabled' : ''}
            >
                ${escapeHtml(subjectName + suffix)}
            </option>
        `);
    }

    function refreshAssignmentCount() {
        $('#assignmentCount').text($('#assignmentsBody tr[id^="row-"]').length);

        $('#assignmentsBody tr[id^="row-"]').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function rowSelector(key) {
        return '#row-' + $.escapeSelector(key);
    }

    function upsertAssignmentGroup(group) {
        $('#emptyRow').remove();

        const key = group.key;
        const existingRow = $(rowSelector(key));
        const existingIds = assignedSubjectsByGroup[key] || [];
        const incomingSubjectIds = (group.subject_ids || []).map(String);
        const incomingSubjectNames = group.subject_names || [];
        const mergedSubjectIds = [...new Set(existingIds.concat(incomingSubjectIds))];

        assignedSubjectsByGroup[key] = mergedSubjectIds;

        if (existingRow.length) {
            if (group.replace) {
                const replacementIds = incomingSubjectIds;
                const replacementNames = incomingSubjectNames;
                const replacementDeleteIds = (group.ids || []).map(String);

                assignedSubjectsByGroup[key] = replacementIds;
                assignmentGroupsByKey[key] = group;
                existingRow.find('.group-subjects').text(replacementNames.join(', '));
                existingRow.find('.btn-remove').attr('data-ids', replacementDeleteIds.join(',')).data('ids', replacementDeleteIds.join(','));
                return;
            }

            const currentNames = existingRow.find('.group-subjects').text()
                .split(',')
                .map(name => name.trim())
                .filter(Boolean);
            const mergedNames = [...new Set(currentNames.concat(incomingSubjectNames))];
            const currentDeleteIds = String(existingRow.find('.btn-remove').data('ids') || '')
                .split(',')
                .filter(Boolean);
            const mergedDeleteIds = [...new Set(currentDeleteIds.concat((group.ids || []).map(String)))];

            existingRow.find('.group-subjects').text(mergedNames.join(', '));
            existingRow.find('.btn-remove').attr('data-ids', mergedDeleteIds.join(',')).data('ids', mergedDeleteIds.join(','));
            existingRow.find('.btn-edit').data('group-key', key).attr('data-group-key', key);
            assignmentGroupsByKey[key] = Object.assign({}, group, {
                ids: mergedDeleteIds,
                subject_ids: mergedSubjectIds,
                subject_names: mergedNames
            });
            return;
        }

        const count = $('#assignmentsBody tr[id^="row-"]').length + 1;
        const deleteIds = (group.ids || []).join(',');

        $('#assignmentsBody').append(`
            <tr id="row-${escapeHtml(key)}" data-group-key="${escapeHtml(key)}">
                <td>${count}</td>
                <td>${escapeHtml(group.branch_name)}</td>
                <td>${escapeHtml(group.class_name)}</td>
                <td>${escapeHtml(group.section_name)}</td>
                <td class="group-subjects">${escapeHtml((group.subject_names || []).join(', '))}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-warning btn-sm btn-edit" data-group-key="${escapeHtml(key)}" title="Edit">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-remove" data-ids="${escapeHtml(deleteIds)}" data-group-key="${escapeHtml(key)}" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);

        assignmentGroupsByKey[key] = group;
    }

    function resetEditMode() {
        editGroupKey = null;
        editingSubjectIds = new Set();
        $('#branchSelect, #classSelect, #sectionSelect').prop('disabled', false);
        $('#assignBtn').html('<i class="ri-add-line me-1"></i> Add Subject(s)').removeClass('btn-warning').addClass('btn-primary');
        $('#cancelEditBtn').addClass('d-none');
    }

    function waitForOption(select, value) {
        return new Promise((resolve, reject) => {
            let tries = 0;
            const timer = setInterval(() => {
                if (select.find(`option[value="${value}"]`).length) {
                    clearInterval(timer);
                    resolve();
                }

                if (++tries > 40) {
                    clearInterval(timer);
                    reject();
                }
            }, 100);
        });
    }

    function waitForSubjects() {
        return new Promise((resolve, reject) => {
            let tries = 0;
            const timer = setInterval(() => {
                if (!$('#subjectSelect').prop('disabled') && $('#subjectSelect option').length > 1) {
                    clearInterval(timer);
                    resolve();
                }

                if (++tries > 40) {
                    clearInterval(timer);
                    reject();
                }
            }, 100);
        });
    }

    localBranches.forEach(branch => {
        appendOption($('#branchSelect'), branch.id, branch.name);
    });

    if (localBranches.length === 1) {
        $('#branchSelect').val(String(localBranches[0].id)).trigger('change');
    }

    $('#branchSelect').on('change', function () {
        const branchId = $(this).val();

        resetSelect($('#classSelect'), 'Select Class');
        resetSelect($('#sectionSelect'), 'Select Section');
        resetSelect($('#subjectSelect'), 'Select Subject', true);
        checkButton();

        if (!branchId) return;

        $.ajax({
            url: '{{ route("assign-subjects.api.classes") }}',
            type: 'GET',
            data: { branch_id: branchId },
            success: function (response) {
                const classes = response.data ?? response;

                if (!classes.length) {
                    showAlert('No classes found for this branch in local database.');
                    return;
                }

                $('#classSelect').prop('disabled', false);

                classes.forEach(cls => {
                    appendOption($('#classSelect'), cls.id, cls.name);
                });
            }
        });
    });

    $('#classSelect').on('change', function () {
        const classId = $(this).val();

        resetSelect($('#sectionSelect'), 'Select Section');
        resetSelect($('#subjectSelect'), 'Select Subject', true);
        checkButton();

        if (!classId) return;

        $.ajax({
            url: '{{ route("assign-subjects.api.sections") }}',
            type: 'GET',
            data: { class_id: classId },
            success: function (response) {
                const sections = response.data ?? response;

                if (!sections.length) {
                    showAlert('No sections found for this class in local database.');
                    return;
                }

                $('#sectionSelect').prop('disabled', false);

                sections.forEach(section => {
                    appendOption($('#sectionSelect'), section.id, section.name);
                });
            }
        });
    });

    $('#sectionSelect').on('change', function () {
        const sectionId = $(this).val();
        const classId = $('#classSelect').val();

        resetSelect($('#subjectSelect'), 'Select Subject', true);
        checkButton();

        if (!sectionId || !classId) return;

        $.ajax({
            url: '{{ route("assign-subjects.api.subjects") }}',
            type: 'GET',
            data: {
                branch_id: $('#branchSelect').val(),
                class_id: classId,
                section_id: sectionId
            },
            success: function (response) {
                allClassSubjects = response.data ?? response;
                availableSubjects = allClassSubjects.filter(subject => !subject.disabled);
                const totalSubjects = response.total ?? allClassSubjects.length;
                const assignedSubjects = response.assigned ?? 0;

                if (!totalSubjects) {
                    $('#subjectHint').text('No subjects assigned to this class yet').css('color', '#dc3545');
                    return;
                }

                $('#subjectSelect').prop('disabled', false);

                $('#subjectSelect option[value="' + allValue + '"]')
                    .prop('disabled', !!response.class_teacher_assigned)
                    .text(response.class_teacher_assigned
                        ? 'All subjects - assigned to ' + (response.class_teacher_name || 'class teacher')
                        : 'All subjects');

                allClassSubjects.forEach(subject => {
                    appendSubjectOption(subject);
                });

                $('#subjectSelect').trigger('change.select2');

                if (!availableSubjects.length) {
                    $('#subjectHint')
                        .text('All individual subjects already assigned')
                        .css('color', '#dc3545');
                    checkButton();
                    return;
                }

                if (response.class_teacher_assigned) {
                    $('#subjectHint')
                        .text('Class teacher: ' + (response.class_teacher_name || 'assigned') + '. Individual subjects can still be assigned.')
                        .css('color', '#6c757d');
                } else {
                    $('#subjectHint')
                        .text(availableSubjects.length + ' subject(s) available, ' + assignedSubjects + ' already assigned')
                        .css('color', '#6c757d');
                }
                checkButton();
            }
        });
    });

    $('#subjectSelect').on('change', function () {
        const selected = $(this).val() || [];

        if (selected.includes(allValue) && selected.length > 1) {
            $(this).val([allValue]).trigger('change.select2');
        }

        checkButton();
    });

    $('#assignBtn').on('click', function () {
        const branch = $('#branchSelect');
        const cls = $('#classSelect');
        const section = $('#sectionSelect');
        const subjectIds = selectedSubjectIds();

        if (!subjectIds.length) {
            showAlert('Please select at least one subject.');
            return;
        }

        $('#assignBtn').prop('disabled', true);

        $.ajax({
            url: editGroupKey ? updateGroupUrl : storeUrl,
            type: 'POST',
            data: {
                teacher_id: teacherId,
                branch_id: branch.val(),
                branch_name: branch.find(':selected').data('name'),
                class_id: cls.val(),
                class_name: cls.find(':selected').data('name'),
                section_id: section.val(),
                section_name: section.find(':selected').data('name'),
                subject_ids: subjectIds,
                assign_all: (($('#subjectSelect').val() || []).includes(allValue) ? 1 : 0),
                _token: CSRF
            },
            success: function (res) {
                if (res.success) {
                    if (editGroupKey) {
                        res.group.replace = true;
                    }

                    upsertAssignmentGroup(res.group);
                    refreshAssignmentCount();
                    showAlert(res.message, 'success');
                    resetEditMode();
                    $('#branchSelect').val('').trigger('change');
                }
            },
            error: function (xhr) {
                showAlert(xhr.responseJSON?.message ?? 'Something went wrong.');
                checkButton();
            }
        });
    });

    $(document).on('click', '.btn-edit', async function () {
        const groupKey = $(this).data('group-key');
        const group = assignmentGroupsByKey[groupKey];

        if (!group) {
            showAlert('Could not load this assignment for editing.');
            return;
        }

        editGroupKey = groupKey;
        editingSubjectIds = new Set((group.subject_ids || []).map(String));
        $('#cancelEditBtn').removeClass('d-none');
        $('#assignBtn').html('<i class="ri-save-3-line me-1"></i> Update Assignment').removeClass('btn-primary').addClass('btn-warning');

        try {
            $('#branchSelect').val(String(group.branch_id)).trigger('change');
            await waitForOption($('#classSelect'), String(group.class_id));
            $('#classSelect').val(String(group.class_id)).trigger('change');
            await waitForOption($('#sectionSelect'), String(group.section_id));
            $('#sectionSelect').val(String(group.section_id)).trigger('change');
            await waitForSubjects();
            $('#subjectSelect').val((group.subject_ids || []).map(String)).trigger('change.select2');
            $('#branchSelect, #classSelect, #sectionSelect').prop('disabled', true);
            checkButton();
            showAlert('Edit the selected subjects, then click Update Assignment.', 'info');
        } catch (e) {
            resetEditMode();
            showAlert('Could not prepare this assignment for editing.');
        }
    });

    $('#cancelEditBtn').on('click', function () {
        resetEditMode();
        $('#branchSelect').val('').trigger('change');
    });

    $(document).on('click', '.btn-remove', function () {
        const ids = String($(this).data('ids') || '').split(',').filter(Boolean);
        const groupKey = $(this).data('group-key');

        if (!confirm('Remove assignment?')) return;
        if (!ids.length) return;

        Promise.all(ids.map(id => $.ajax({
            url: deleteUrlTemplate.replace('__ID__', id),
            type: 'DELETE',
            data: { _token: CSRF }
        }))).then(function () {
            $(rowSelector(groupKey)).remove();
            delete assignedSubjectsByGroup[groupKey];
            delete assignmentGroupsByKey[groupKey];
            if (editGroupKey === groupKey) {
                resetEditMode();
                $('#branchSelect').val('').trigger('change');
            }
            refreshAssignmentCount();

            if ($('#assignmentsBody tr[id^="row-"]').length === 0) {
                $('#assignmentsBody').html(`
                    <tr id="emptyRow">
                        <td colspan="6" class="text-center text-muted py-4">
                            No subjects assigned yet.
                        </td>
                    </tr>
                `);
            }

            showAlert('Assignment removed successfully.', 'success');
        }).catch(function (xhr) {
            showAlert(xhr.responseJSON?.message ?? 'Could not remove assignment.');
        });
    });
});
</script>

@endsection
