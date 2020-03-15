@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="employees_new">
        

        <form action="" method="post" v-on:submit.prevent="create">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="firstname" v-model="employee.firstname" required type="text" class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" maxlength="30" id="input-firstname">
                            <label class="form-label" for="input-firstname" @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>Firstname</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="lastname" v-model="employee.lastname" required type="text" class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" maxlength="30"
                            id="input-lastname">
                            <label class="form-label" for="input-lastname" @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="email" v-model="employee.email" type="email" class="validate {{ $errors->has('email') ? ' invalid' : '' }}"
                            id="input-email" required>
                            <label class="form-label" for="input-email" @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Email</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="phone" v-model="employee.phone" type="text" class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" maxlength="14" id="input-phone" required>
                            <label class="form-label" for="input-phone" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone Number</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <select v-model="employee.gender" name="gender" id="gender" class="form-control" v-model="employee.gender" required>
                                    <option value="" disabled>Select Gender</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                </select>
                            <label class="form-label" for="gender" @if ($errors->has('gender')) data-error="{{ $errors->first('gender') }}" @endif>Gender</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="salary_amount" type="number" name="salary_amount" min="0" v-model="employee.salary_amount" required class="validate {{ $errors->has('salary_amount') ? ' invalid' : '' }}">
                            <label class="form-label" for="salary_amount" @if ($errors->has('salary_amount')) data-error="{{ $errors->first('salary_amount') }}" @endif>Base Salary</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" v-model="employee.staff_code" name="staff_code" type="text" class="validate {{ $errors->has('staff_code') ? ' invalid' : '' }}"
                            id="staff_code" required>
                            <label class="form-label" for="staff_code" @if ($errors->has('staff_code')) data-error="{{ $errors->first('staff_code') }}" @endif>Employee Code</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" v-model="employee.job_title" name="job_title" type="text" class="validate {{ $errors->has('job_title') ? ' invalid' : '' }}" maxlength="80" id="job_title" required>
                            <label class="form-label" for="job_title" @if ($errors->has('job_title')) data-error="{{ $errors->first('job_title') }}" @endif>Job Title</label>
                        </div>
                    </div>
                    <div class="row">
                        @if (!empty($departments))
                        <div class="col-md-6 form-group">
                            <select class="form-control" name="department" id="department" v-model="employee.department">
                                <option value="" disabled>Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <label class="form-label" for="department" @if ($errors->has('department')) data-error="{{ $errors->first('department') }}" @endif>Department</label>
                        </div>
                        @endif
                        @if (!empty($locations))
                        <div class="col-md-6 form-group">
                            <select name="location" id="location" class="form-control" v-model="employee.location">
                                <option value="" disabled>Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <label class="form-label" for="location" @if ($errors->has('location')) data-error="{{ $errors->first('location') }}" @endif>Location</label>
                        </div>
                        @endif
                    </div>
                </fieldset>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-block" type="submit" v-if="!saving" name="action">
                            Save Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</div>

@endsection
@section('body_js')
    <script type="text/javascript">

        new Vue({
            el: '#employees_new',
            data: {
                saving: false,
                employee: {
                    firstname: "{{ old('firstname') }}",
                    lastname: "{{ old('lastname') }}",
                    phone: "{{ old('phone') }}",
                    email: "{{ old('email') }}",
                    staff_code: "{{ old('staff_code') }}",
                    job_title: "{{ old('job_title') }}",
                    salary_amount: "{{ old('salary_amount') }}",
                    salary_period: "month",
                    department: "{{ old('department') }}",
                    location: "{{ old('location') }}",
                    gender: "{{ old('gender') }}"
                }
            },
            methods: {
                reset: function () {
                    for (var key in this.employee) {
                        if (!this.employee.hasOwnProperty(key)) {
                            continue;
                        }
                        this.employee[key] = '';
                    }
                },
                create: function () {
                    //this.saving = true;
                    var context = this;
                    Swal.fire({
                        title: "Add Employee Profile?",
                        text: "Are you ready to add this employee?",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#1565C0",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        preConfirm: (employees_new) => {
                            return axios.post("/mpe/people-employees-new", context.employee)
                                .then(function (response) {
                                    console.log(response);
                                    context.saving = false;
                                    context.reset();
                                    //Materialize.toast('Successfully added the employee', 4000);
                                    window.location = '{{ route("people-employees") }}';
                                    return swal("Profile Created!", "The Employee Profile was successfully created!", "success");
                                })
                                .catch(function (error) {
                                    var message = '';
                                    console.log(error);
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                        var e = error.response.data.errors[0];
                                        message = e.title;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    context.saving = false;
                                    return swal("Add Failed", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()                        
                    })

                }
            }
        });
    </script>
@endsection