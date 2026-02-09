@extends('layouts.main')
@section('content')
 <div class="page-content ">
        <div class="container-fluid">
            <div style="display:grid; grid-template-columns:25% 50% 25%; align-items:center;" class="mt-4">
                <div class=""></div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Session</h4>
                        <hr>
                        <form action="{{ route('sessions.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Session</label>
                                        <input type="text" name="Session" class="form-control"
                                            placeholder="2024-2025" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Term one Working Days </label>
                                        <input type="number" name="term_one_working_days" class="form-control"
                                            placeholder="Term 1 Working Days" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Term Two Working Days</label>
                                        <input type="number" name="term_two_working_days" class="form-control"
                                            placeholder="Term 2 Working Days" required>
                                    </div>
                                </div>

                                <div>
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class=""></div>
            </div>
        </div>
    </div>
@endsection