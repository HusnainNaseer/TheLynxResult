<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!-- User details -->
        <div class="user-profile text-center mt-3">
            <div class="">
<img src="{{ Auth::user()->profile_picture 
    ? asset('storage/' . Auth::user()->profile_picture) 
    : asset('assets/auth/images/users/avatar2.png') }}" 
    alt="{{ Auth::user()->name }}" 
    class="avatar-md rounded-circle">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1">{{ Auth::user()->name }}</h4>
                    <span class="text-muted">
                        <i class="ri-record-circle-line align-middle font-size-14 text-success"></i>
                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                    </span>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title">Menu</li> --}}

                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        {{-- <span class="badge rounded-pill bg-success float-end">3</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>
                @role('Admin')
                <li>
                    <a href="{{ route('teachers.index') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        {{-- <span class="badge rounded-pill bg-success float-end">3</span> --}}
                        <span>Teachers</span>
                    </a>
                </li>
                @endrole

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-briefcase-line"></i>
                        <span>Setup</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @role('Teacher')
                        <li><a href="{{ route('subject-marks.index') }}">Subject Marks</a></li>
                        @endrole
                        <li><a href="{{ route('sessions.index') }}">Sessions</a></li>
                        {{-- Add more setup links here --}}
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-file-list-3-line"></i>
                        <span>Student Result</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('students.result') }}">Result List</a></li>
                        @role('Teacher')
                        <li><a href="{{ route('results.create') }}">Create Result</a></li>
                        @endrole
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
