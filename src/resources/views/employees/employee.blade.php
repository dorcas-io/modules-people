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
                        <p class="list-group-item"><i class="fa fa-desktop"></i> @{{ employee.email }}</p>
                        <p class="list-group-item"><i class="fa fa-phone" aria-hidden="true"></i> @{{ employee.phone }}</p>
                        <p class="list-group-item"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> @{{ addedDate }}</p>
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
                <h3 class="card-title">Groups</h3>
            </div>
            <div class="card-body">
                Manage <strong>departments</strong> &amp; <strong>teams</strong> that @{{ employee.firstname }} belongs to below:
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#profile_departments">Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#profile_teams">Teams</a>
                    </li>
                </ul>

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
                                            <option v-for="department in availableDepartments" v-if="addedDepartments.indexOf(department.id) === -1"
                                            :key="department.id" :value="department.id">@{{ department.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" {{ !empty($employee->department["data"]["id"]) ? 'disabled' : '' }} type="submit" name="action">Add to Department</button>
                                    </div>
                                    <p>Employees can only belong to a single (1) department at a time.</p>
                                </div>
                            </fieldset>
                        </form>

                        <div class="col-md-6" v-if="typeof employee.department !== 'undefined'">
                            <div class="tag" :key="employee.department.data.id">
                              @{{ employee.department.data.name }}
                              <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="employee.department.data.id" v-on:click.prevent="removeDepartment($event)"></i></a>
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

                        <div class="col-md-6" v-if="typeof employee.teams !== 'undefined' && employee.teams.data.length > 0">
                            <div class="tag" v-for="(team, index) in employee.teams.data" :key="team.id">
                              @{{ team.name }}
                              <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="index"
                                v-on:click.prevent="removeTeam($event)"></i></a>
                            </div>
                        </div>
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
                    $('#edit-employee-modal').modal('show');
                },
                postedAtDate: function (dateString) {
                    return moment(dateString).format('DD MMM, YYYY HH:mm')
                },
                updateCustomer: function () {
                    var context = this;
                    Swal.fire({
                        title: "Update Customer Profile?",
                        text: "You are about to update the details for this customer.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#1565C0",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        preConfirm: (update) => {
                        return axios.put("/mcu/customers-customers/" + context.customer.id, {
                            firstname: context.customer.firstname,
                            lastname: context.customer.lastname,
                            email: context.customer.email,
                            phone: context.customer.phone
                        })
                           .then(function (response) {
                                console.log(response);
                                //$('#edit-customer-modal').modal('hide');
                                return swal("Saved!", "The changes were successfully saved!", "success");
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
                                return swal("Save Failed", message, "warning");
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()                        
                    })
                },
                removeGroup: function (e) {
                    let attrs = app.utilities.getElementAttributes(e.target);
                    console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let group = typeof this.customer.groups.data[index] !== 'undefined' ? this.customer.groups.data[index] : null;
                    if (group === null) {
                        return false;
                    }
                    if (this.processing) {
                        //Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.processing = true;
                    let context = this;
                    axios.delete("/mcu/customers-groups/" + group.id, {
                        data: {customers: [context.customer.id]}
                    }).then(function (response) {
                        //console.log(response);
                        //console.log(index);
                        if (index !== null) {
                            context.customer.groups.data.splice(index, 1);
                            context.addedGroups = context.customer.groups.data.map(function (e) { return e.id; });
                        }
                        context.processing = false;
                        //Materialize.toast('Group '+group.name+' removed.', 2000);
                        return swal("Deleted!", "Group "+group.name+" was successfully deleted", "success");
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
                            //context.saving = false;
                            return swal("Delete Failed", message, "warning");
                        });
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
                                teams: [context.employee.id]
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
            }
        });
    </script>
@endsection