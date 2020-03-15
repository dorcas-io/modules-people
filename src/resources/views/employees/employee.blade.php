@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row" id="employee_profile_card">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-4">
        <div class="card card-profile">
            <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }"></div>
            <div class="card-body text-center">
                <img class="card-profile-img" v-bind:src="photo">
                <h3 class="mb-3">@{{ fullName }}</h3>
                <p class="mb-4">
                    <div class="list-group text-left">
                        <p class="list-group-item"><i class="fe fe-desktop"></i> @{{ employee.email }}</p>
                        <p class="list-group-item"><i class="fe fe-phone" aria-hidden="true"></i> @{{ employee.phone }}</p>
                        <p class="list-group-item"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> @{{ addedDate }}</p>
                        <p class="list-group-item" v-if="typeof employee.department !== 'undefined'"><i class="fe fe-grid" aria-hidden="true"></i> @{{ employee.department.data.name }}</p>
                    </div>
                </p>
                <button v-on:click.prevent="editEmployee" class="btn btn-outline-primary btn-sm text-center">
                    <span class="fa fa-address-card"></span> Edit Profile
                </button>
            </div>
            @include('modules-people::modals.employee')
        </div>
    </div>


    <div class="col-md-5 col-xl-5">
        <div class="card">
            <div class="card-status bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">Associations</h3>
            </div>
            <div class="card-body">
                Manage <strong>departments</strong>, <strong>teams</strong>, <strong>user account</strong> &amp; <strong>permissions</strong> for @{{ employee.firstname }}:
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#profile_departments">Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile_teams">Teams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile_permissions_user" v-bind:class="{'disabled': typeof employee.user !== 'undefined'}">User Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile_permissions_module" v-bind:class="{'disabled': typeof employee.user === 'undefined'}">Module</a>
                    </li>
                </ul>

                <!--  v-bind:class="{'disabled': typeof employee.user === 'undefined'} v-bind:class="{'active': typeof employee.user !== 'undefined'}" -->

                <div class="tab-content">
                    <div class="tab-pane container active" id="profile_departments">
                        <br/>
                        <form method="post" v-on:submit.prevent="addEmployeeToDepartment" action="">
                            {{ csrf_field() }}
                            <fieldset class="form-fieldset">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <select id="employee-departments" v-model="addToDepartment.employee" class="form-control" required>
                                            <option value="" disabled>Select a Department</option>
                                            <option v-for="department in departments" v-if="addedDepartments.indexOf(department.id) === -1"
                                            :key="department.id" :value="department.id">@{{ department.name }}</option><!--availableDepartments-->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" {{ !empty($employee->department["data"]["id"]) ? 'disabled' : '' }} type="submit" name="action">Add to Department</button>
                                    </div>
                                    <p>Employees can only belong to a single (1) department at a time.</p>
                                </div>
                            </fieldset>
                        </form>

                        <div v-if="typeof employee.department !== 'undefined'">
                            <div class="tag" :key="employee.department.data.id">
                              @{{ employee.department.data.name }}
                              <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="employee.department.data.id" v-bind:data-name="employee.department.data.name" v-on:click.prevent="removeDepartment($event)"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane container" id="profile_teams">
                        <br/>
                        <form method="post" v-on:submit.prevent="addEmployeeToTeam" action="">
                            {{ csrf_field() }}
                            <fieldset class="form-fieldset">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <select id="employee-teams" v-model="addToTeam.employee" class="form-control" required>
                                            <option value="" disabled>Select a Team</option>
                                            <option v-for="team in teams" v-if="addedTeams.indexOf(team.id) === -1"
                                            :key="team.id" :value="team.id">@{{ team.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" type="submit" name="action">Add to Team</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>

                        <div v-if="typeof employee.teams !== 'undefined' && employee.teams.data.length > 0">
                            <div class="tag" v-for="(team, index) in employee.teams.data" :key="team.id">
                              @{{ team.name }}
                              <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-name="team.name" v-bind:data-index="index" v-bind:data-id="team.id"
                                v-on:click.prevent="removeTeam($event)"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane container" id="profile_permissions_user">
                        <br/>
                        <form action="" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <input class="form-control" autocomplete="off" required name="email" type="email" maxlength="80" v-model="employee.email"
                                           class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email">
                                    <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Email</label>
                                </div>
                                <div class="col-md-6 form-group">
                                    <input class="form-control" autocomplete="off" required name="password" type="password"
                                           class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                                    <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>Password</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <input class="form-control" autocomplete="off" required name="firstname" type="text" maxlength="100" v-model="employee.firstname"
                                           class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" id="firstname">
                                    <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif class="center-align">Firstname</label>
                                </div>
                                <div class="col-md-6 form-group">
                                    <input class="form-control" autocomplete="off" required name="lastname" type="text" maxlength="30" v-model="employee.lastname"
                                           class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" id="lastname">
                                    <label for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif class="center-align">Lastname</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <input class="form-control" autocomplete="off" name="phone" type="text" maxlength="30" v-model="employee.phone"
                                           class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" id="phone">
                                    <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif class="center-align">Phone</label>
                                </div>
                            </div>
                            <div class="row">
                                <button class="btn btn-primary" type="submit" name="action" value="create_user">
                                    Create User Account
                                </button>
                            </div>
                        </form>

                    </div>
                    <div class="tab-pane container" id="profile_permissions_module" v-if="typeof employee.user !== 'undefined'">
                        <br/>
                        <form action="" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                @if (!empty($employee->user))
                                    @foreach ($setupUiFields as $field)
                                        <div class="col-md-12 m6">

                                            <div class="form-label">&nbsp;</div>
                                            <div class="custom-controls-stacked">
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="selected_apps[]" value="{{ $field['id'] }}" {{ !empty($field['enabled']) ? 'checked' : '' }} {{ !empty($field['is_readonly']) ? 'disabled' : '' }}>
                                                    <span class="custom-control-label">{{ $field['name'] }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <button class="btn btn-primary" type="submit" name="action" value="update_module_access">
                                    Update Module Access Permissions
                                </button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#employee_profile_card',
            data: {
                employee: {!! json_encode($employee) !!},
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/14.png') }}",
                departments: {!! json_encode($departments) !!},
                teams: {!! json_encode($teams) !!},
                deleting: false,
                processing: false,
                addedDepartments: [],
                addToDepartment: {
                    employee: ''
                },
                addedTeams: [],
                addToTeam: {
                    employee: ''
                },
                availableDepartments: [],
                availableDepartment: [],
                saving: false,
            },
            computed: {
                photo: function () {
                    return this.employee.photo.length > 0 ? this.employee.photo : this.defaultPhoto;
                },
                fullName: function () {
                    var names = [this.employee.firstname || '', this.employee.lastname || ''];
                    return names.join(' ').title_case();
                },
                addedDate: function () {
                    return moment(this.employee.created_at).format('DD MMM, YYYY')
                }
            },
            methods: {
                titleCase: function (string) {
                    return string.title_case();
                },
                editEmployee: function (index) {
                    $('#manage-employee-modal').modal('show');
                },
                postedAtDate: function (dateString) {
                    return moment(dateString).format('DD MMM, YYYY HH:mm')
                },
                updateEmployee: function () {
                    var context = this;
                    Swal.fire({
                        title: "Update Employee Profile?",
                        text: "You are about to update the details for this employee.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#1565C0",
                        confirmButtonText: "Yes, continue!",
                        showLoaderOnConfirm: true,
                        preConfirm: (update) => {
                        return axios.put("/mpe/people-employees/" + context.employee.id, {
                            firstname: context.employee.firstname,
                            lastname: context.employee.lastname,
                            email: context.employee.email,
                            phone: context.employee.phone,
                            gender: context.employee.gender,
                            staff_code: context.employee.staff_code,
                            job_title: context.employee.job_title,
                            salary_amount: context.employee.salary.raw,
                            salary_period: "month"
                        })
                           .then(function (response) {
                                //console.log(response);
                                //$('#edit-customer-modal').modal('hide');
                                $('#manage-employee-modal').modal('hide');
                                return swal("Saved!", "The changes were successfully saved!", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    console.log(error.response);
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                                    /*var e = error.response.data.errors[0];
                                    message = e.title;*/
                                    var e = error.response;
                                    message = e.data.message;
                                } else if (error.request) {
                                    // The request was made but no response was received
                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                    // http.ClientRequest in node.js
                                    message = 'The request was made but no response was received';
                                } else {
                                    // Something happened in setting up the request that triggered an Error
                                    message = error.message;
                                }
                                return swal("Save Failed", message, "warning");
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()                        
                    })
                },
                addEmployeeToTeam: function () {
                    let context = this;
                    let tm = typeof context.addToTeam.employee !== 'undefined' ? context.addToTeam.employee : null;
                    if (tm === null) {
                        return false;
                    }
                    let tm_index = context.teams.findIndex(x => x.id === tm );
                    team = typeof context.teams[tm_index] !== 'undefined' ? context.teams[tm_index] : [];
                    this.processing =  true;
                    //console.log(context.addToTeam.employee);
                    Swal.fire({
                        title: "Add Employee to Team?",
                        text: "Are you sure you want to add "+context.employee.firstname+" "+context.employee.lastname+" to "+team.name+"?",
                        type: "info",
                        showCancelButton: true,
                        //confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, add!",
                        showLoaderOnConfirm: true,
                        preConfirm: (add_employee_team) => {
                            this.processing =  true;
                            return axios.post("/mpe/people-teams/" + context.addToTeam.employee + "/employees", {
                                employees: [context.employee.id]
                            }).then(function (response) {
                                console.log(response);
                                context.processing = false;
                                window.location = '{{ url()->current() }}'
                                //context.departments.employees.data.splice(index, 1); //add to the current DOM array
                                //context.addedGroups = context.departments.employees.data.map(function (e) { return e.id; }); //add to the current DOM array
                                return swal("Added!", "Employee successfully added", "success");
                            })
                                .catch(function (error) {
                                    var message = '';
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                        //var e = error.response.data.errors[0];
                                        //message = e.title;
                                        var e = error.response;
                                        message = e.data.message;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    context.savingNote = false;
                                    //Materialize.toast('Error: '+message, 4000);
                                    swal("Add Failed:", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
                removeTeam: function (e) {
                    let context = this;
                    let attrs = app.utilities.getElementAttributes(e.target);
                    //console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let team_name = attrs['data-name'] || null;
                    let team_id = attrs['data-id'] || null;
                    
                    let tm = typeof context.addToTeam.employee !== 'undefined' ? context.addToTeam.employee : null;
                    if (tm === null) {
                        return false;
                    }
                    let tm_index = context.teams.findIndex(x => x.id === tm );
                    team = typeof context.teams[tm_index] !== 'undefined' ? context.teams[tm_index] : [];
                    if (this.processing) {
                        //Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.processing = true;
                    Swal.fire({
                        title: "Remove Employee?",
                        text: "Are you sure you want to remove "+context.employee.firstname+" "+context.employee.lastname+" from "+team_name+"?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, remove!",
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_employee_team) => {
                            this.processing = true;
                            return axios.delete("/mpe/people-teams/" + team_id + "/employees", {
                                data: {employees: [context.employee.id]}
                            }).then(function (response) {
                                //console.log(response);
                                //console.log(index);
                                if (index !== null) {
                                    context.employee.teams.data.splice(index, 1);
                                    context.addedTeams = context.employee.teams.data.map(function (e) { return e.id; });
                                }
                                context.processing = false;
                                //Materialize.toast('Group '+group.name+' removed.', 2000);
                                return swal("Deleted!", "Employee was successfully deleted from "+team_name, "success");
                            })
                                .catch(function (error) {
                                    var message = '';
                                    console.log(error);
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                            /*var e = error.response.data.errors[0];
                                            message = e.title;*/
                                            var e = error.response;
                                            message = e.data.message;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    //context.saving = false;
                                    return swal("Delete Failed", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
                addEmployeeToDepartment: function () {
                    let context = this;
                    let dept = typeof context.addToDepartment.employee !== 'undefined' ? context.addToDepartment.employee : null;
                    if (dept === null) {
                        return false;
                    }
                    let dept_index = context.availableDepartments.findIndex(x => x.id === dept );
                    department = typeof context.availableDepartments[dept_index] !== 'undefined' ? context.availableDepartments[dept_index] : [];
                    this.processing =  true;
                    Swal.fire({
                        title: "Add Employee to Department?",
                        text: "Are you sure you want to add "+context.employee.firstname+" "+context.employee.lastname+" to "+department.name+"?",
                        type: "info",
                        showCancelButton: true,
                        //confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, add!",
                        showLoaderOnConfirm: true,
                        preConfirm: (add_employee_department) => {
                            this.processing =  true;
                            return axios.post("/mpe/people-departments/" + context.addToDepartment.employee + "/employees", {
                                employees: [context.employee.id]
                            }).then(function (response) {
                                console.log(response);
                                context.processing = false;
                                window.location = '{{ url()->current() }}'
                                //context.departments.employees.data.splice(index, 1); //add to the current DOM array
                                //context.addedGroups = context.departments.employees.data.map(function (e) { return e.id; }); //add to the current DOM array
                                return swal("Added!", "Employee successfully added", "success");
                            })
                                .catch(function (error) {
                                    var message = '';
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
                                    context.savingNote = false;
                                    //Materialize.toast('Error: '+message, 4000);
                                    swal("Add Failed:", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
                removeDepartment: function (e) {
                    let attrs = app.utilities.getElementAttributes(e.target);
                    //console.log(attrs);
                    let index = attrs['data-index'] || null; //its the id not really an index
                    let dept_name = attrs['data-name'] || null;
                    //let group = typeof this.employee.department.data[index] !== 'undefined' ? this.customer.groups.data[index] : null;
                    if (index === null) {
                        return false;
                    }
                    if (this.processing) {
                        //Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.processing = true;
                    let context = this;
                    Swal.fire({
                        title: "Remove Employee?",
                        text: "Are you sure you want to remove "+context.employee.firstname+" "+context.employee.lastname+" from "+dept_name+"?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, remove!",
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_employee_department) => {
                            this.processing = true;
                            return axios.delete("/mpe/people-departments/" + index + "/employees", {
                                data: {employees: [context.employee.id]}
                            }).then(function (response) {
                                //console.log(response);
                                //console.log(index);
                                if (index !== null) {
                                    //context.customer.groups.data.splice(index, 1);
                                    //context.addedGroups = context.customer.groups.data.map(function (e) { return e.id; });
                                    //context.department.employees.data.splice(index, 1);
                                    //context.addedEmployees = context.department.employees.data.map(function (e) { return e.id; });
                                }
                                context.processing = false;
                                //Materialize.toast('Group '+group.name+' removed.', 2000);
                                window.location = '{{ url()->current() }}'
                                return swal("Deleted!", "Employee was successfully removed from "+dept_name, "success");
                            })
                                .catch(function (error) {
                                    var message = '';
                                    console.log(error);
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                            /*var e = error.response.data.errors[0];
                                            message = e.title;*/
                                            var e = error.response;
                                            message = e.data.message;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    //context.saving = false;
                                    return swal("Delete Failed", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
            },
            mounted: function () {
                var context = this;
                this.addedDepartments = typeof this.employee.department !== 'undefined' ? [this.employee.department.data.id] : [];
                this.addedTeams = typeof this.employee.teams !== 'undefined' ? this.employee.teams.data.map(function (e) { return e.id; }) : [];
                this.availableDepartments = typeof this.employee.department !== 'undefined' ? this.availableDepartment : this.departments;
                availableDepartment: [{
                    id: typeof this.employee.department !== 'undefined' ? this.employee.department.data.id : "",
                    name: typeof this.employee.department !== 'undefined' ? this.employee.department.data.name : "",
                    description: typeof this.employee.department !== 'undefined' ? this.employee.department.data.descriptionn : "",
                    created_at: typeof this.employee.department !== 'undefined' ? this.employee.department.data.created_at : "",
                    counts: typeof this.employee.department !== 'undefined' ? this.employee.department.data.counts : ""
                }];
                //console.log(this.addedDepartments);
                //console.log(this.employee);
                console.log(this.employee.user)
            }
        });
    </script>
@endsection