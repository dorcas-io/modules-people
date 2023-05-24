@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row" id="task_profile_card">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
    
        <div style="background: #072c4d;padding:10px;border-radius:10px;" v-if="task.project_status !== 'completed'">
            <h1 style="color:#fff;" id="countdown"></h1>
            <h5 style="color:#fff;" >( Start Date : @{{ task.start_date }} - End Date : @{{ task.end_date }}  )</h5>
        </div>
      
        <div class="card card-profile">
            <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }">
               
            </div>
            <div class="card-body text-center">
               
                <!-- <img class="card-profile-img" v-bind:src="photo"> -->
                <h3 class="mb-3" style="color: #467fcf;">@{{ task.name}}</h3>
                <h4 class="mb-4">@{{ task.description }}</h4>
            
            </div>
        </div>

        <div class="card">
            <div class="card-status bg-blue"></div>
            <div class="card-header">
                <h3 class="card-title">Employees Assign To This Task</h3>
            </div>
            <div class="card-body">
              
                Manage <em>employees</em> that belong to <strong>@{{ task.name }}</strong>:
            {{-- @{{ allEmployees }} --}}
                <form method="post" v-on:submit.prevent="addEmployeeToTask" action="">
                    {{ csrf_field() }}
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <select class="form-control"  v-model="addToTask.employee" required>
                                    <option value="" disabled>Select an Employee</option>
                                    {{-- employeesLeftToAssign --}}
                                    <option v-for="employee in allEmployees" 
                                            v-if="addedEmployees.indexOf(employee.id) === -1"
                                            :key="employee.id"
                                            :value="employee.email">
                                        @{{ employee.firstname}}  @{{ employee.lastname}}  - ( @{{ employee.email}} )

                                    </option>
                                </select>
                           
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-primary" type="submit" name="action">Add to Task</button>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <div v-if="typeof task.employees !== 'undefined' && task.employees.data.length > 0">
                    <p><div class="tag"v-for="(employee, index) in task.employees.data" :key="employee.id">
                      <strong>@{{ employee.firstname }} @{{ employee.lastname }}</strong>&nbsp;| @{{ employee.staff_code }} - @{{ employee.job_title }} &nbsp;
                      <a v-bind:href="'/mpe/people-employees/'+employee.uuid" target="_blank">View</a>
                      <a href="#" class="tag-addon tag-danger">
                        <i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="index"
                        v-on:click.prevent="removeEmployee($event)"></i>
                    </a>
                    </div></p>
                </div>

            </div>
        </div>
        
    </div>
</div>
@endsection

@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#task_profile_card',
            data: {
                task: {!! json_encode($task) !!},
                updating: false,
                employeesCount: {{ empty($task['employees']) ? 0 : count($task['employees']) }},
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/2.png') }}",
                employees: {!! json_encode($task['employees']['data']) !!},
                allEmployees : {!! json_encode($employees) !!},
                employeesLeftToAssign : [],
                employeeArray : [],
                notes: [],
                deleting: false,
                processing: false,
                addedEmployees: [],
                addToTask: {
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
                showTeamId: function () {
                    return typeof this.team.id !== 'undefined';
                }
            },
            created(){
               
                this.allEmployees.map(employee => { 
                    this.employeeArray.push(employee.email)     
                })

                if(this.task.employees.data.length > 0){
                    
                    this.employeeArray.map((staffAssigned , index) => {
                        if(this.task.employees.data[index].email === staffAssigned){
                            this.employeesLeftToAssign.push(staffAssigned)  
                        }
                    })
                 }else{
                   this.employeesLeftToAssign = this.employeeArray; 
                 }
            },
            methods: {
                // titleCase: function (string) {
                //     return string.title_case();
                // },
                // editTeam: function (index) {
                //     $('#manage-team-modal').modal('show');
                // },
                // postedAtDate: function (dateString) {
                //     return moment(dateString).format('DD MMM, YYYY HH:mm')
                // },
                // searchEmployees: function (employeeid) {
                //     for (var emp in this.team.employees.data) {
                //         console.log(emp);
                //       if (emp.hasOwnProperty("id")) {
                //         console.log(emp.id);
                //       }
                //     }
                // },
                // removeEmployee: function (e) {
                //     let attrs = app.utilities.getElementAttributes(e.target);
                //     //console.log(attrs);
                //     let index = attrs['data-index'] || null;
                //     let employee = typeof this.team.employees.data[index] !== 'undefined' ? this.team.employees.data[index] : null;
                //     if (employee === null) {
                //         return false;
                //     }
                //     if (this.processing) {
                //         //Materialize.toast('Please wait till the current activity completes...', 4000);
                //         return;
                //     }
                //     let context = this;
                //     Swal.fire({
                //         title: "Remove Employee?",
                //         text: "Are you sure you want to remove "+employee.firstname+" "+employee.lastname+" from "+context.team.name+"?",
                //         type: "warning",
                //         showCancelButton: true,
                //         confirmButtonColor: "#DD6B55",
                //         confirmButtonText: "Yes, remove!",
                //         showLoaderOnConfirm: true,
                //         preConfirm: (delete_employee_group) => {
                //             this.processing = true;
                //             return axios.delete("/mpe/people-teams/" + context.team.id + "/employees", {
                //                 data: {employees: [employee.id]}
                //             }).then(function (response) {
                //                 if (index !== null) {
                //                     context.team.employees.data.splice(index, 1);
                //                     context.addedEmployees = context.team.employees.data.map(function (e) { return e.id; });
                //                 }
                //                 context.processing = false;
                //                 //Materialize.toast('Group '+group.name+' removed.', 2000);
                //                 return swal("Deleted!", "Employee "+employee.firstname+" "+employee.lastname+" was successfully removed", "success");
                //             })
                //                 .catch(function (error) {
                //                     var message = '';
                //                     //console.log(error);
                //                     if (error.response) {
                //                         console.log(error.response);
                //                         // The request was made and the server responded with a status code
                //                         // that falls out of the range of 2xx
                //                         var e = error.response.data.message;
                //                         message = e.title;
                //                     } else if (error.request) {
                //                         // The request was made but no response was received
                //                         // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                //                         // http.ClientRequest in node.js
                //                         message = 'The request was made but no response was received';
                //                     } else {
                //                         // Something happened in setting up the request that triggered an Error
                //                         message = error.message;
                //                     }
                //                     context.saving = false;
                //                     return swal("Delete Failed", message, "warning");
                //                 });
                //         },
                //         allowOutsideClick: () => !Swal.isLoading() 
                //     });
                // },
                removeEmployee: function (e) {
                    let attrs = app.utilities.getElementAttributes(e.target);
                    // console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let employee = typeof this.task.employees.data[index] !== 'undefined' ? this.task.employees.data[index] : null;
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
                        text: "Are you sure you want to remove "+employee.firstname +" "+employee.lastname+"  from "+context.task.name+"?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, remove!",
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_employee_group) => {
                            this.processing = true;
                            return axios.post("/mpe/task/" + this.task.id + "/remove_employee", {
                                employee_id:  [employee.uuid]
                            }).then(function (response) {
                                if (index !== null) {
                                    context.task.employees.data.splice(index, 1);
                                    context.addedEmployees = context.task.employees.data.map(function (e) { return e.id; });
                                }
                                context.processing = false;
                                //Materialize.toast('Group '+group.name+' removed.', 2000);
                                return swal("Removed!", "Employee "+employee.firstname+" "+employee.lastname+" was successfully removed", "success");
                            })
                                .catch(function (error) {
                                    var message = '';
                                    //console.log(error);
                                    if (error.response) {
                                        console.log(error.response);
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                        var e = error.response.data.message;
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
                addEmployeeToTask: function () {
                   
                    let context = this;
                    
                    let empl = typeof context.addToTask.employee !== 'undefined' ? context.addToTask.employee : null;
                    if (empl === null) {
                        return false;
                    }
                   
                   
                    let empl_index = context.employees.findIndex(x => x.id === empl );
                   
                    employee = typeof context.employees[empl_index] !== 'undefined' ? context.employees[empl_index] : [];
                   
                    Swal.fire({
                        title: "Assign Employee To Task?",
                        // text: "Are you sure you want to add "+employee.firstname+" "+employee.lastname+" to "+context.task.name+"?",
                        type: "info",
                        showCancelButton: true,
                        //confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, assign!",
                        showLoaderOnConfirm: true,
                        preConfirm: (add_employee_group) => {
                            this.processing =  true;
                            return axios.post("/mpe/task/" + this.task.id + '/assign', {
                                email: [context.addToTask.employee]
                            }).then(function (response) {
                                console.log(response)
                                context.processing = false;
                                // window.location.reload();
                                window.location = '{{ url()->current() }}'
                                //context.teams.employees.data.splice(index, 1); //add to the current DOM array
                                //context.addedGroups = context.teams.employees.data.map(function (e) { return e.id; }); //add to the current DOM array
                                return swal("Added!", "Employee successfully assigned", "success");
                            })
                                .catch(function (error) {
                                    //adding this success cos the employee still gets added
                                
                                    // return swal("Added!", "Employee successfully added", "success");
                                    return swal({
                                        title:"Added!",
                                        text:"Employee successfully assigned",
                                        type:"success",
                                        showLoaderOnConfirm: true,
                                    }).then(function () {
                                        // location.reload()
                                    });
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
                //console.log(this.team.employees);
                // console.log(this.team);
                var context = this;
                this.addedEmployees = this.task.employees.data.map(function (e) { return e.id; });
                // console.log(this.addedEmployees,'added');
            }
        });

        var end = new Date({!! json_encode($task['end_date']) !!},);

        var _second = 1000;
        var _minute = _second * 60;
        var _hour = _minute * 60;
        var _day = _hour * 24;
        var timer;

        function showRemaining() {
            var now = new Date();
            var distance = end - now;
            if (distance < 0) {

                clearInterval(timer);
                document.getElementById('countdown').innerHTML = 'OVERDUE!';

                return;
            }
            var days = Math.floor(distance / _day);
            var hours = Math.floor((distance % _day) / _hour);
            var minutes = Math.floor((distance % _hour) / _minute);
            var seconds = Math.floor((distance % _minute) / _second);

            document.getElementById('countdown').innerHTML = days + 'days ';
            document.getElementById('countdown').innerHTML += hours + 'hrs ';
            document.getElementById('countdown').innerHTML += minutes + 'mins ';
            document.getElementById('countdown').innerHTML += seconds + 'secs';
        }

        timer = setInterval(showRemaining, 1000);
    </script>
@endsection