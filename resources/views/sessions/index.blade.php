@extends('layouts.main')
@section('content')
    <style>
        .alert {
            display: block;
        }

        .alert-success {
            color: green !important;
        }

        .alert-danger {
            color: red !important;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Session</h4>


                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                                            <li class="breadcrumb-item active">Basic Tables</li> --}}
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center p-2">
                        <h4 class="card-title"></h4>
                        <div class="d-flex gap-1">
                            <form action="">
                                <input class="form-control mb-6" type="search" id="session_search" name="search"
                                    placeholder="Search Session">
                            </form>
                            <a href="{{ route('sessions.create') }}" type="button"
                                class="btn btn-primary waves-effect waves-light d-flex align-items-center"><i
                                    class="ri-add-line"></i>&nbsp; Create</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            @if (session('error'))
                                <span class="alert alert-danger text-white">
                                    {{ session('error') }}
                                </span>
                            @endif

                            @if (session('success'))
                                <span class="alert alert-success text-white">
                                    {{ session('success') }}
                                </span>
                            @endif


                            <div class="table-responsive">
                                <table id="session-table" class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Total Working Days Term One</th>
                                            <th>Total Working Days Term two</th>
                                            <th class="text-center">Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($wdays as $wday)
                                            <tr>
                                                <td>{{ $wday->title }}</td>
                                                <td>{{ $wday->t1_working_days }}</td>
                                                <td>{{ $wday->t2_working_days }}</td>

                                                <!-- Actions side by side -->
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('sessions.edit', $wday) }}"
                                                            class="btn btn-info btn-sm">
                                                            Edit
                                                        </a>

                                                        {{-- <form method="POST" action="{{ route('sessions.destroy', $wday) }}"
                                                            onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm">Delete</button>
                                                        </form> --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>



                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div> <!-- container-fluid -->
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function(){
        const search = document.getElementById('session_search');
        const table = document.getElementById('session-table');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        search.addEventListener('keyup', function(){
            const filter = this.value.toLowerCase();
            Array.from(rows).forEach(row => {
                const cells = row.getElementsByTagName('td');
                let match = false;

                Array.from(cells).forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
            });
        });
    });
</script>>
@endsection
