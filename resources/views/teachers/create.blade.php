@extends('layouts.main')

@section('content')
    <style>
        /* ── Step indicator ─────────────────────────────────────── */
        .step-indicator {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            flex-shrink: 0;
            transition: background .3s, color .3s;
        }

        .step-circle.active {
            background: #405189;
            color: #fff;
        }

        .step-circle.done {
            background: #0ab39c;
            color: #fff;
        }

        .step-label {
            margin-left: .5rem;
            font-size: .8rem;
            color: #6c757d;
            white-space: nowrap;
        }

        .step-label.active {
            color: #405189;
            font-weight: 600;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e9ecef;
            margin: 0 .5rem;
        }

        /* ── Selected employee card ─────────────────────────────── */
        #employeePreview {
            display: none;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            padding: 1rem;
        }

        #employeePreview.show {
            display: flex;
        }

        /* ── Password strength bar ──────────────────────────────── */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all .3s;
        }

        /* ── Loader spinner inside select ──────────────────────── */
        .select-wrapper {
            position: relative;
        }

        .select-loader {
            position: absolute;
            right: 2.4rem;
            top: 50%;
            transform: translateY(-50%);
            display: none;
        }

        .select-loader.show {
            display: block;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Page Title ───────────────────────────────────────────── --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Add Employee</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('teachers.index') }}">Employees</a>
                                </li>
                                <li class="breadcrumb-item active">Add Employee</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Validation errors ────────────────────────────────────── --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-9">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom pt-4 pb-3">
                            <h5 class="mb-3 fw-semibold">
                                <i class="ri-user-add-line me-2 text-primary"></i>Assign Employee Role
                            </h5>

                            {{-- Step indicator --}}
                            <div class="step-indicator">
                                <div class="step">
                                    <div class="step-circle active" id="step1Circle">1</div>
                                    <span class="step-label active" id="step1Label">Branch & Role</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step">
                                    <div class="step-circle" id="step2Circle">2</div>
                                    <span class="step-label" id="step2Label">Select Employee</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step">
                                    <div class="step-circle" id="step3Circle">3</div>
                                    <span class="step-label" id="step3Label">Set Password</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('teachers.store') }}" id="assignRoleForm"
                                autocomplete="off">
                                @csrf

                                {{-- ═══════════════════════════════════════════════
                                 STEP 1 – BRANCH & ROLE
                            ═══════════════════════════════════════════════ --}}
                                <div id="step1">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                        style="font-size:.7rem;letter-spacing:.08em">
                                        Step 1 of 3 — Branch & Role
                                    </h6>

                                    <div class="row g-3">
                                        {{-- Branch --}}
                                        <div class="col-md-7">
                                            <label for="branch_id" class="form-label fw-medium">
                                                Branch <span class="text-danger">*</span>
                                            </label>
                                            <div class="select-wrapper">
                                                <select class="form-select @error('branch_id') is-invalid @enderror"
                                                    id="branch_id" name="branch_id" required>
                                                    <option value="" disabled selected>-- Select Branch --</option>
                                                </select>
                                                <div class="spinner-border spinner-border-sm text-primary select-loader"
                                                    id="branchLoader" role="status">
                                                    <span class="visually-hidden">Loading…</span>
                                                </div>
                                            </div>
                                            <div class="form-text" id="branchStatus"></div>
                                            @error('branch_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Role --}}
                                        <div class="col-md-5">
                                            <label for="role" class="form-label fw-medium">
                                                Role <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('role') is-invalid @enderror" id="role"
                                                name="role" required>
                                                <option value="" disabled selected>-- Select Role --</option>
                                                <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>
                                                    🎓 Teacher
                                                </option>
                                                <option value="Coordinator"
                                                    {{ old('role') == 'Coordinator' ? 'selected' : '' }}>
                                                    📋 Coordinator
                                                </option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- ═══════════════════════════════════════════════
                                 STEP 2 – SELECT EMPLOYEE
                            ═══════════════════════════════════════════════ --}}
                                <div id="step2">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                        style="font-size:.7rem;letter-spacing:.08em">
                                        Step 2 of 3 — Select Employee
                                    </h6>

                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label fw-medium">
                                            Employee <span class="text-danger">*</span>
                                        </label>
                                        <div class="select-wrapper">
                                            <select class="form-select @error('employee_id') is-invalid @enderror"
                                                id="employee_id" name="employee_id" required disabled>
                                                <option value="" disabled selected>
                                                    — Select a branch first —
                                                </option>
                                            </select>
                                            <div class="spinner-border spinner-border-sm text-primary select-loader"
                                                id="employeeLoader" role="status">
                                                <span class="visually-hidden">Loading…</span>
                                            </div>
                                        </div>
                                        <div class="form-text" id="employeeStatus"></div>
                                        @error('employee_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Selected employee preview card --}}
                                    <div id="employeePreview" class="align-items-center gap-3 mt-3">

                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center
                                            justify-content-center flex-shrink-0 overflow-hidden"
                                            style="width:48px;height:48px;">

                                            <img id="previewImage" src="" alt="Employee Image"
                                                style="width:100%; height:100%; object-fit:cover; display:none;">
                                            <i id="noPicture" class="ri-user-3-line text-primary fs-4"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold" id="previewName"></div>
                                            <div class="text-muted small" id="previewEmail"></div>
                                            <div class="text-muted small" id="previewDesignation"></div>
                                        </div>

                                    </div> {{-- Hidden email field (submitted with form for reference) --}}
                                    <input type="hidden" id="employee_email" name="employee_email">
                                </div>

                                <hr class="my-4">

                                {{-- ═══════════════════════════════════════════════
                                 STEP 3 – SET PASSWORD
                            ═══════════════════════════════════════════════ --}}
                                <div id="step3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3"
                                        style="font-size:.7rem;letter-spacing:.08em">
                                        Step 3 of 3 — Set Password
                                    </h6>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-medium">
                                                Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Min. 8 characters"
                                                    required>
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="togglePassword" tabindex="-1">
                                                    <i class="ri-eye-line" id="eyeIcon"></i>
                                                </button>
                                            </div>
                                            {{-- Strength bar --}}
                                            <div class="mt-2" id="strengthWrapper" style="display:none">
                                                <div class="progress" style="height:4px;">
                                                    <div class="progress-bar strength-bar" id="strengthBar"
                                                        style="width:0%"></div>
                                                </div>
                                                <small id="strengthLabel" class="text-muted"></small>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label fw-medium">
                                                Confirm Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    id="password_confirmation" name="password_confirmation"
                                                    placeholder="Re-enter password" required>
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="toggleConfirm" tabindex="-1">
                                                    <i class="ri-eye-line" id="eyeIconConfirm"></i>
                                                </button>
                                            </div>
                                            <div class="form-text" id="matchStatus"></div>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Actions ──────────────────────────────────── --}}
                                <div class="d-flex gap-2 mt-4 pt-2 border-top">
                                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                        <i class="ri-user-add-line me-1"></i> Assign Role
                                    </button>
                                    <a href="{{ route('teachers.index') }}" class="btn btn-light px-4">
                                        Cancel
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ── DOM refs ─────────────────────────────────────────────── */
            const branchSel = document.getElementById('branch_id');
            const roleSel = document.getElementById('role');
            const employeeSel = document.getElementById('employee_id');
            const branchLoader = document.getElementById('branchLoader');
            const employeeLoader = document.getElementById('employeeLoader');
            const branchStatus = document.getElementById('branchStatus');
            const employeeStatus = document.getElementById('employeeStatus');
            const preview = document.getElementById('employeePreview');
            const previewName = document.getElementById('previewName');
            const previewEmail = document.getElementById('previewEmail');
            const previewDesg = document.getElementById('previewDesignation');
            const hiddenEmail = document.getElementById('employee_email');
            const previewImage = document.getElementById('previewImage');
            const noPicture = document.getElementById('noPicture');
            const passInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthWrap = document.getElementById('strengthWrapper');
            const strengthBar = document.getElementById('strengthBar');
            const strengthLabel = document.getElementById('strengthLabel');
            const matchStatus = document.getElementById('matchStatus');

            const base = "{{ request()->root() }}";

            /* ── Step indicator helpers ───────────────────────────────── */
            function markStep(n, state) { // state: 'active' | 'done' | ''
                const circle = document.getElementById(`step${n}Circle`);
                const label = document.getElementById(`step${n}Label`);
                circle.className = 'step-circle ' + state;
                label.className = 'step-label ' + state;
                if (state === 'done') circle.innerHTML = '<i class="ri-check-line"></i>';
                else circle.textContent = n;
            }

            function refreshSteps() {
                const hasBranch = branchSel.value !== '';
                const hasRole = roleSel.value !== '';
                const hasEmployee = employeeSel.value !== '';

                markStep(1, (hasBranch && hasRole) ? 'done' : 'active');
                markStep(2, hasEmployee ? 'done' : (hasBranch ? 'active' : ''));
                markStep(3, hasEmployee ? 'active' : '');
            }

            /* ── Status helper ────────────────────────────────────────── */
            function setStatus(el, msg, type) { // type: 'success' | 'warning' | 'danger' | ''
                el.textContent = msg;
                el.className = `form-text ${type ? 'text-' + type : ''}`;
            }

            /* ── 1. Fetch active branches on load ─────────────────────── */
            branchLoader.classList.add('show');
            setStatus(branchStatus, 'Loading branches…', '');

            fetch(`${base}/api/branches`)
                .then(r => r.json())
                .then(data => {
                    const branches = data.data || data;

                    if (Array.isArray(branches) && branches.length) {
                        branchSel.innerHTML = '<option value="" disabled selected>-- Select Branch --</option>';

                        branches.forEach(b => {
                            const opt = new Option(b.branch_name || b.name, b.id);
                            // Restore old value after validation error
                            if (String(b.id) === '{{ old('branch_id') }}') opt.selected = true;
                            branchSel.appendChild(opt);
                        });

                        setStatus(branchStatus, `${branches.length} active branch(es) loaded`, 'success');
                    } else {
                        setStatus(branchStatus, 'No active branches found', 'warning');
                    }
                })
                .catch(() => setStatus(branchStatus, 'Error loading branches. Refresh to retry.', 'danger'))
                .finally(() => branchLoader.classList.remove('show'));

            /* ── 2. Fetch employees when branch changes ───────────────── */
            branchSel.addEventListener('change', function() {
                const branchId = this.value;
                refreshSteps();

                // Reset employee dropdown
                employeeSel.disabled = true;
                employeeSel.innerHTML = '<option value="" disabled selected>Loading employees…</option>';
                hidePreview();
                employeeLoader.classList.add('show');
                setStatus(employeeStatus, 'Fetching employees…', '');

                fetch(`${base}/api/employees?branch_id=${branchId}`)
                    .then(r => r.json())
                    .then(data => {
                        const employees = data.data || data;

                        employeeSel.innerHTML =
                            '<option value="" disabled selected>-- Select Employee --</option>';

                        if (Array.isArray(employees) && employees.length) {
                            employees.forEach(emp => {
                                console.log(emp);
                                const label = emp.name + (emp.employee.designation ?
                                    ` — ${emp.employee.designation.name}` : '');
                                const opt = new Option(label, emp.id);
                                // Embed data directly on the option for instant preview
                                opt.dataset.name = emp.name || '';
                                opt.dataset.email = emp.email || '';
                                opt.dataset.designation = (emp.employee.designation ?
                                    `${emp.employee.designation.name}` : '');
                                opt.dataset.picture = emp.employee.profile_img || '';
                                if (String(emp.id) === '{{ old('employee_id') }}') opt
                                    .selected = true;
                                employeeSel.appendChild(opt);
                            });

                            employeeSel.disabled = false;
                            setStatus(employeeStatus, `${employees.length} employee(s) found`,
                                'success');

                            // If we're restoring old input, trigger preview
                            if (employeeSel.value) employeeSel.dispatchEvent(new Event('change'));
                        } else {
                            employeeSel.innerHTML =
                                '<option value="" disabled selected>No employees in this branch</option>';
                            setStatus(employeeStatus, 'No employees found for this branch', 'warning');
                        }
                    })
                    .catch(() => {
                        employeeSel.innerHTML =
                            '<option value="" disabled selected>Error loading employees</option>';
                        setStatus(employeeStatus, 'Error loading employees. Try again.', 'danger');
                    })
                    .finally(() => employeeLoader.classList.remove('show'));
            });

            roleSel.addEventListener('change', refreshSteps);

            /* ── 3. Show employee preview when one is selected ────────── */
            employeeSel.addEventListener('change', function() {

                const selected = this.options[this.selectedIndex];

                const name = selected.dataset.name;
                const email = selected.dataset.email;
                const picture = selected.dataset.picture;
                const desg = selected.dataset.designation;
                const baseUrl = "{{ env('ERP_URL') }}";

                if (name || email) {

                    previewName.textContent = name || '—';
                    previewEmail.textContent = email || '';
                    previewDesg.textContent = desg || '';

                    hiddenEmail.value = email || '';

                    // Set image
                    if (picture) {
                        noPicture.style.display = 'none';
                        previewImage.src = `${baseUrl}/storage/emp_profile_images/${picture}`;
                        previewImage.style.display = 'block';
                    } else {
                        noPicture.style.display = 'block';
                        previewImage.style.display = 'none';
                    }

                    preview.classList.add('show');

                } else {

                    hidePreview();

                    // Fallback: fetch from API
                    fetchEmployeeDetails(this.value);
                }

                refreshSteps();
            });

            function hidePreview() {
                preview.classList.remove('show');
                hiddenEmail.value = '';
                previewName.textContent = '';
                previewEmail.textContent = '';
                previewDesg.textContent = '';
            }

            function fetchEmployeeDetails(empId) {
                fetch(`${base}/api/employees/${empId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.data) {
                            const e = data.data;
                            previewName.textContent = e.name || '—';
                            previewEmail.textContent = e.email || '—';
                            previewDesg.textContent = e.designation || '';
                            hiddenEmail.value = e.email || '';
                            preview.classList.add('show');
                        }
                    })
                    .catch(() => {});
            }

            /* ── 4. Password toggle ───────────────────────────────────── */
            function toggleVis(inputEl, iconEl) {
                const isPass = inputEl.type === 'password';
                inputEl.type = isPass ? 'text' : 'password';
                iconEl.className = isPass ? 'ri-eye-off-line' : 'ri-eye-line';
            }
            document.getElementById('togglePassword').addEventListener('click',
                () => toggleVis(passInput, document.getElementById('eyeIcon')));
            document.getElementById('toggleConfirm').addEventListener('click',
                () => toggleVis(confirmInput, document.getElementById('eyeIconConfirm')));

            /* ── 5. Password strength ─────────────────────────────────── */
            passInput.addEventListener('input', function() {
                const val = this.value;
                if (!val) {
                    strengthWrap.style.display = 'none';
                    return;
                }

                strengthWrap.style.display = 'block';

                let score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;

                const levels = [{
                        pct: 25,
                        cls: 'bg-danger',
                        label: 'Weak'
                    },
                    {
                        pct: 50,
                        cls: 'bg-warning',
                        label: 'Fair'
                    },
                    {
                        pct: 75,
                        cls: 'bg-info',
                        label: 'Good'
                    },
                    {
                        pct: 100,
                        cls: 'bg-success',
                        label: 'Strong'
                    },
                ];
                const lvl = levels[score - 1] || levels[0];

                strengthBar.style.width = lvl.pct + '%';
                strengthBar.className = 'progress-bar strength-bar ' + lvl.cls;
                strengthLabel.textContent = lvl.label;

                checkMatch();
            });

            /* ── 6. Password match indicator ─────────────────────────── */
            confirmInput.addEventListener('input', checkMatch);

            function checkMatch() {
                const p = passInput.value;
                const c = confirmInput.value;
                if (!c) {
                    matchStatus.textContent = '';
                    return;
                }

                if (p === c) {
                    matchStatus.innerHTML =
                        '<span class="text-success"><i class="ri-check-line"></i> Passwords match</span>';
                } else {
                    matchStatus.innerHTML =
                        '<span class="text-danger"><i class="ri-close-line"></i> Passwords do not match</span>';
                }
            }

            /* ── 7. Submit guard ──────────────────────────────────────── */
            document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
                if (passInput.value !== confirmInput.value) {
                    e.preventDefault();
                    confirmInput.classList.add('is-invalid');
                    confirmInput.focus();
                    return;
                }
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Assigning…';
            });

            /* ── Initial step state ────────────────────────────────────── */
            refreshSteps();
        });
    </script>
@endsection
