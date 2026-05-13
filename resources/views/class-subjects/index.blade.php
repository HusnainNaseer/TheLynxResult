@extends('layouts.main')

@section('content')

<style>
    .filter-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: .75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .filter-card-title {
        font-size: .8rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 1.25rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f1f3f4;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .filter-card-title i { color: #405189; font-size: 1rem; }

    .col-label {
        font-size: .75rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: .45rem;
        display: flex;
        align-items: center;
        gap: .35rem;
    }
    .col-label i { font-size: .85rem; color: #405189; }

    select[multiple] {
        border-radius: .5rem;
        border: 1px solid #dee2e6;
        padding: .4rem .5rem;
        font-size: .875rem;
        width: 100%;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
        appearance: none;
    }
    select[multiple]:focus {
        outline: none;
        border-color: #405189;
        box-shadow: 0 0 0 .2rem rgba(64,81,137,.12);
    }
    select[multiple] option {
        padding: .35rem .5rem;
        border-radius: .25rem;
        margin-bottom: 1px;
        font-size: .875rem;
    }
    select[multiple] option:checked {
        background: #405189;
        color: #fff;
    }
    select[multiple] option:disabled {
        color: #adb5bd;
        font-style: italic;
        background: #f8f9fa;
    }

    /* ── table ─────────────────────────────────────────────── */
    .assignments-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: .75rem;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .assignments-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f4;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }
    .assignments-card-header h6 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .assignments-card-header h6 i { color: #405189; }
    .total-badge {
        background: #405189;
        color: #fff;
        border-radius: 20px;
        padding: .15rem .65rem;
        font-size: .72rem;
        font-weight: 600;
    }

    .table-search-wrap {
        padding: .75rem 1.25rem;
        border-bottom: 1px solid #f1f3f4;
        position: relative;
    }
    .table-search-wrap i {
        position: absolute;
        left: 2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        font-size: .9rem;
        pointer-events: none;
    }
    .table-search-wrap input {
        padding-left: 2.1rem;
        height: 36px;
        border: 1px solid #e9ecef;
        border-radius: .45rem;
        font-size: .85rem;
        width: 280px;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .table-search-wrap input:focus {
        border-color: #405189;
        box-shadow: 0 0 0 .18rem rgba(64,81,137,.12);
    }

    .assign-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .assign-table thead tr { background: #f8f9fa; }
    .assign-table thead th {
        padding: .7rem 1.25rem;
        font-size: .72rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .07em;
        border-bottom: 1px solid #e9ecef;
        white-space: nowrap;
    }
    .assign-table tbody tr {
        border-bottom: 1px solid #f1f3f4;
        transition: background .15s;
    }
    .assign-table tbody tr:last-child { border-bottom: none; }
    .assign-table tbody tr:hover { background: #fafbff; }
    .assign-table tbody td {
        padding: .85rem 1.25rem;
        color: #212529;
        vertical-align: middle;
    }
    .assign-table tbody td.td-class { font-weight: 600; }
    .assign-table tbody td.td-index {
        font-size: .75rem;
        color: #adb5bd;
        font-weight: 600;
    }

    .subject-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        background: #f0fdf4;
        color: #0a7c5c;
        border: 1px solid #bbf7d0;
        border-radius: 20px;
        padding: .1rem .5rem;
        font-size: .72rem;
        font-weight: 500;
        margin: .1rem .15rem;
        white-space: nowrap;
    }
    .subject-pill i { font-size: .65rem; }

    .branch-tag {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        background: #eef2ff;
        color: #405189;
        border: 1px solid #c7d2fe;
        border-radius: 20px;
        padding: .1rem .5rem;
        font-size: .72rem;
        font-weight: 500;
    }

    .td-actions { text-align: right; }
    .btn-del {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #e9ecef;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        color: #adb5bd;
        transition: all .2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-del:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #ef4444;
    }

    .empty-row td {
        text-align: center;
        padding: 3rem 1rem;
        color: #adb5bd;
    }
    .empty-row i { font-size: 2rem; display: block; margin-bottom: .5rem; }
</style>

<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Class Subjects</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Class Subjects</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-2"></i>
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── Selection Form ── --}}
        <div class="filter-card">

            <div class="filter-card-title">
                <i class="ri-book-mark-line"></i>
                Assign Subjects to a Class
            </div>

            <form action="{{ route('class-subjects.store') }}" method="POST">
                @csrf

                <div class="row g-3 align-items-start">

                    {{-- Col 1: Branch --}}
                    <div class="col-md-3">
                        <div class="col-label">
                            <i class="ri-building-2-line"></i> Branch
                        </div>
                        <select
                            name="branch_id"
                            id="branchSelect"
                            class="form-select"
                            required
                        >
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                                <option
                                    value="{{ $branch['id'] }}"
                                    {{ old('branch_id') == $branch['id'] ? 'selected' : '' }}>
                                    {{ $branch['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" id="branchHint"></div>
                    </div>

                    {{-- Col 2: Class --}}
                    <div class="col-md-3">
                        <div class="col-label">
                            <i class="ri-book-open-line"></i> Class
                        </div>
                        <select
                            name="class_id"
                            id="classSelect"
                            class="form-select"
                            required
                            disabled
                        >
                            <option value="">-- Select Branch First --</option>
                        </select>
                        <div class="form-text" id="classHint"></div>
                    </div>

                    {{-- Col 3: Subjects --}}
                    <div class="col-md-4">
                        <div class="col-label">
                            <i class="ri-price-tag-3-line"></i> Subjects
                        </div>
                        <select
                            name="subjects[]"
                            id="subjectSelect"
                            multiple
                            size="8"
                            required
                            disabled
                        ></select>
                        <div class="form-text" id="subjectHint" style="color:#adb5bd">
                            Select a class first
                        </div>
                    </div>

                    {{-- Col 4: Submit --}}
                    <div class="col-md-2 d-flex flex-column justify-content-start" style="padding-top:1.7rem">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            id="submitBtn"
                            disabled
                        >
                            <i class="ri-check-line me-1"></i> Assign
                        </button>
                    </div>

                </div>

            </form>
        </div>

        {{-- ── Assignments Table ── --}}
        <div class="assignments-card">

            <div class="assignments-card-header">
                <h6>
                    <i class="ri-list-check-2"></i>
                    Assigned Subjects
                </h6>
                <span class="total-badge">{{ $assignedSubjects->count() }} {{ Str::plural('class', $assignedSubjects->count()) }}</span>
            </div>

            @if($assignedSubjects->count() > 4)
            <div class="table-search-wrap">
                <i class="ri-search-line"></i>
                <input
                    type="text"
                    id="tableSearch"
                    placeholder="Search class or subject…"
                >
            </div>
            @endif

            <div class="table-responsive">
                <table class="assign-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Class</th>
                            <th>Branch</th>
                            <th>Subjects</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="assignTableBody">

                        @forelse($assignedSubjects as $classId => $rows)
                            @php
                                $class        = $rows->first()->class;
                                $subjectIds   = $rows->pluck('subject_id')->toArray();
                                $subjectNames = DB::table('subject_wise_marks')
                                    ->whereIn('id', $subjectIds)
                                    ->pluck('subject_name');

                                $branchName = '—';
                                if ($class && $class->erp_branch_id) {
                                    $matched = collect($branches)->firstWhere('id', $class->erp_branch_id);
                                    $branchName = $matched['name'] ?? '—';
                                }
                            @endphp

                            <tr data-search="{{ strtolower(($class?->name ?? '') . ' ' . $subjectNames->implode(' ') . ' ' . $branchName) }}">

                                <td class="td-index">{{ $loop->iteration }}</td>

                                <td class="td-class">
                                    {{ $class?->name ?? 'Unknown Class' }}
                                </td>

                                <td>
                                    <span class="branch-tag">
                                        <i class="ri-building-2-line"></i>
                                        {{ $branchName }}
                                    </span>
                                </td>

                                <td>
                                    @foreach($subjectNames as $subject)
                                        <span class="subject-pill">
                                            <i class="ri-price-tag-3-line"></i>
                                            {{ $subject }}
                                        </span>
                                    @endforeach
                                </td>

                                <td class="td-actions">
                                    <a href="#"
                                       class="btn-del"
                                       title="Remove all subjects from this class"
                                       onclick="return confirm('Remove all subjects from {{ addslashes($class?->name ?? 'this class') }}?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                </td>

                            </tr>

                        @empty
                            <tr class="empty-row">
                                <td colspan="5">
                                    <i class="ri-inbox-2-line"></i>
                                    No subjects assigned yet
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const classOptions = @json($classOptions);
    const allSubjects  = @json($subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->subject_name])->values());
    const assignedMap  = @json($assignedSubjects->map(fn($rows) => $rows->pluck('subject_id')->toArray()));

    const branchSel   = document.getElementById('branchSelect');
    const classSel    = document.getElementById('classSelect');
    const subjectSel  = document.getElementById('subjectSelect');
    const classHint   = document.getElementById('classHint');
    const subjectHint = document.getElementById('subjectHint');
    const submitBtn   = document.getElementById('submitBtn');

    function hint(el, msg, color) {
        el.textContent = msg;
        el.style.color = color || '#adb5bd';
    }

    function resetSel(sel, placeholder) {
        sel.innerHTML = placeholder
            ? `<option value="">${placeholder}</option>`
            : '';
    }

    /* ── Branch → Classes ───────────────────────────────────── */
    branchSel.addEventListener('change', function () {
        const branchId = String(this.value);

        resetSel(classSel, '-- Select Class --');
        resetSel(subjectSel, '');
        classSel.disabled  = true;
        subjectSel.disabled = true;
        submitBtn.disabled = true;
        hint(classHint, '', '');
        hint(subjectHint, 'Select a class first', '#adb5bd');

        if (!branchId) return;

        const filtered = classOptions.filter(c => String(c.branch) === branchId);

        if (!filtered.length) {
            hint(classHint, 'No classes found for this branch', '#dc3545');
            return;
        }

        filtered.forEach(c => {
            classSel.appendChild(new Option(c.name, c.id));
        });

        classSel.disabled = false;
        hint(classHint, filtered.length + ' class(es) available', '#0ab39c');
    });

    /* ── Class → Subjects ───────────────────────────────────── */
    classSel.addEventListener('change', function () {
        const classId = String(this.value);

        resetSel(subjectSel, '');
        subjectSel.disabled = true;
        submitBtn.disabled  = true;
        hint(subjectHint, '', '');

        if (!classId) return;

        const taken = (assignedMap[classId] || []).map(String);
        let available = 0;

        allSubjects.forEach(s => {
            const already = taken.includes(String(s.id));
            const opt = new Option(
                already ? s.name + '  ✓ already assigned' : s.name,
                s.id
            );
            if (already) {
                opt.disabled = true;
            } else {
                available++;
            }
            subjectSel.appendChild(opt);
        });

        subjectSel.disabled = false;

        if (available === 0) {
            hint(subjectHint, 'All subjects already assigned to this class', '#dc3545');
            submitBtn.disabled = true;
        } else {
            hint(subjectHint, available + ' available · Hold Ctrl/⌘ to select multiple', '#6c757d');
        }
    });

    /* ── Enable submit only when something is selected ──────── */
    subjectSel.addEventListener('change', function () {
        const any = Array.from(this.options).some(o => o.selected && !o.disabled);
        submitBtn.disabled = !any;
    });

    /* ── Restore old input after validation error ────────────── */
    @if(old('branch_id'))
        branchSel.value = '{{ old("branch_id") }}';
        branchSel.dispatchEvent(new Event('change'));
        setTimeout(() => {
            classSel.value = '{{ old("class_id") }}';
            classSel.dispatchEvent(new Event('change'));
        }, 30);
    @endif

    /* ── Table live search ───────────────────────────────────── */
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#assignTableBody tr[data-search]').forEach(row => {
                row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
            });
        });
    }

});
</script>

@endsection