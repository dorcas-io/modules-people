@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row" id="department_profile_card">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="card card-profile">
            <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }"></div>
            <div class="card-body text-center">
                <!-- <img class="card-profile-img" v-bind:src="photo"> -->
                <h3 class="mb-3" style="color: #467fcf;">@{{ department.name }}</h3>
                <h4 class="mb-4">@{{ department.description }}</h4>
                <!-- <p class="mb-4">
                    <div class="list-group text-left">
                        <p class="list-group-item"><i class="fa fa-desktop"></i> @{{ department.description }}</p>
                        <p class="list-group-item"><i class="fa fa-phone" aria-hidden="true"></i> @{{ customer.phone }}</p>
                        <p class="list-group-item"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> @{{ addedDate }}</p>
                    </div>
                </p> -->
                <!-- <button v-on:click.prevent="editDepartment" class="btn btn-outline-primary btn-sm text-center">
                    <span class="fa fa-address-card"></span> Edit Department
                </button> -->
            </div>
        </div>

        <div class="card">
            <div class="card-status bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
            </div>
            <div class="card-body">
                Manage <em>employees</em> that belong to <strong>@{{ department.name }}</strong>:

                <form method="post" v-on:submit.prevent="addEmployeeToDepartment" action="">
                    {{ csrf_field() }}
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <select id="grp-customer" v-model="addToDepartment.employee" class="form-control" required>
                                    <option value="" disabled>Select an Employee</option>
                                    <option v-for="employee in employees" v-if="addedEmployees.indexOf(employee.id) === -1"
                                    :key="employee.id" :value="employee.id">@{{ employee.firstname }} @{{ employee.lastname }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-primary" type="submit" name="action">Add to Department</button>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <div class="col-md-6" v-if="typeof department.employees !== 'undefined' && department.employees.data.length > 0">
                    <div class="tag"v-for="(employee, index) in department.employees.data" :key="employee.id">
                      <strong>@{{ employee.firstname }} @{{ employee.lastname }}</strong>&nbsp;| @{{ employee.staff_code }} - @{{ employee.job_title }} &nbsp;<a v-bind:href="'/mpe/people-employees/'+employee.id" target="_blank">View</a>
                      <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="index"
                        v-on:click.prevent="removeEmployee($event)"></i></a>
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
            el: '#department_profile_card',
            data: {
                department: {!! json_encode($department) !!},
                updating: false,
                employeesCount: {{ empty($employees) ? 0 : $employees->count() }},
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/2.png') }}",
                employees: {!! json_encode($employees) !!},
                deleting: false,
                processing: false,
                addedEmployees: [],
                addToDepartment: {
                    employee: ''
                }
            },
            computed: {
                /*photo: function () {
                    return this.customer.photo.length > 0 ? this.customer.photo : this.defaultPhoto;
                },
                fullName: function () {
                    var names = [this.customer.firstname || '', this.customer.lastname || ''];
                    return names.join(' ').title_case();
                },
                addedDate: function () {
                    return moment(this.customer.created_at).format('DD MMM, YYYY')
                },*/
                showAddButton: function () {
                    return !this.updating && this.employeesCount > 0;
                },
                showDepartmentId: function () {
                    return typeof this.department.id !== 'undefined';
                }
            },
            methods: {
                titleCase: function (string) {
                    return string.title_case();
                },
                editDepartment: function (index) {
                    $('#manage-department-modal').modal('show');
                },
                postedAtDate: function (dateString) {
                    return moment(dateString).format('DD MMM, YYYY HH:mm')
                },
                searchEmployees: function (employeeid) {
					for (var emp in this.department.employees.data) {
						console.log(emp);
					  if (emp.hasOwnProperty("id")) {
					    console.log(emp.id);
					  }
					}
                },
                removeEmployee: function (e) {
                    let attrs = app.utilities.getElementAttributes(e.target);
                    //console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let employee = typeof this.department.employees.data[index] !== 'undefined' ? this.department.employees.data[index] : null;
                    if (employee === null) {
                        return false;
                    }
                    if (this.processing) {
                        //Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    let context = this;
                    Swal.fire({
                        title: "Remove Employee?",
                        text: "Are you sure you want to remove "+employee.firstname+" "+employee.lastname+" from "+context.department.name+"?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, remove!",
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_employee_department) => {
                        	this.processing = true;
		                    return axios.delete("/mpe/people-departments/" + context.department.id + "/employees", {
		                        data: {employees: [employee.id]}
		                    }).then(function (response) {
		                        if (index !== null) {
		                            context.department.employees.data.splice(index, 1);
		                            context.addedEmployees = context.department.employees.data.map(function (e) { return e.id; });
		                        }
		                        context.processing = false;
		                        //Materialize.toast('Group '+group.name+' removed.', 2000);
		                        return swal("Deleted!", "Employee "+employee.firstname+" "+employee.lastname+" was successfully removed", "success");
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
		                            return swal("Delete Failed", message, "warning");
		                        });
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
                addEmployeeToDepartment: function () {
                    let context = this;
                    let empl = typeof context.addToDepartment.employee !== 'undefined' ? context.addToDepartment.employee : null;
                    if (empl === null) {
                        return false;
                    }
                    //console.log(employee);
                    console.log(empl);
                    let empl_index = context.employees.findIndex(x => x.id === empl );
                    employee = typeof context.employees[empl_index] !== 'undefined' ? context.employees[empl_index] : [];
                    Swal.fire({
                        title: "Add Employee?",
                        text: "Are you sure you want to add "+employee.firstname+" "+employee.lastname+" to "+context.department.name+"?",
                        type: "info",
                        showCancelButton: true,
                        //confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, add!",
                        showLoaderOnConfirm: true,
                        preConfirm: (add_employee_group) => {
                        	this.processing =  true;
		                    return axios.post("/mpe/people-departments/" + context.department.id + "/employees", {
		                        employees: [context.addToDepartment.employee]
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
                }

            },
            mounted: function () {
            	//console.log(this.employees);
            	//console.log(this.department.employees);
                var context = this;
                this.addedEmployees = this.department.employees.data.map(function (e) { return e.id; });
            }
        });

    </script>
@endsection