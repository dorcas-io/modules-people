@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">

        <div class="container" id="listing_tasks">
            <div class="row mt-3" v-show="tasks.length > 0">
                <task-card class="s12 m4" v-for="(task, index) in tasks"
                :key="task.id" :task="task" :index="index"
{{--                :employeeCount="employeeCount" --}}
                           :employees="employees"
                v-on:edit-task="editTask" v-on:delete-task="deleteTask"
                ></task-card>
               
            </div>
            <div class="col s12" v-if="tasks.length === 0">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Tasks
                    @endslot
                    You can add one or more takss to organise your projects and workflow.
                    @slot('buttons')
                        <a href='#' v-on:click.prevent="createTask" class="btn btn-primary btn-sm">Add Task</a>
                    @endslot
                    
                @endcomponent
            </div>
            @include('modules-people::modals.task')
        </div>

    </div>

</div>


@endsection
@section('body_js')
<script type="text/javascript">
   var vm = new Vue({
       el: '#listing_tasks',
       data: {
           tasks: {!! json_encode(!empty($tasks) ? $tasks : []) !!},
           task:  {name: '', task_description: ''},
           projects: {!! json_encode(!empty($projects) ? $projects : []) !!},
           employees : [],
           employeeCount : 0,
          
       },
       created() {


         //   this.tasks.forEach((task , index)=> {
         //    task.employees.data.map((employee , index)  => {
         //      this.employeeCount = task.employees.data.length
         //      this.employees.push(employee.email)
         //    })
         // })
        },
       methods: {
           createTask :function () {
               // this.task = {name: '', description: ''};
               $('#manage-task-modal').modal('show');
           },
           editTask: function (index) {

                    let task = typeof this.data.tasks[index] !== 'undefined' ? this.data.tasks[index] : null;
                    if (task === null) {
                        return;
                    }
                    this.task = task;
                    $('#manage-task-modal').modal('show');
            },
            deleteTask: function (index) {
                    let tasks = typeof this.tasks !== 'undefined' ? this.tasks : null;
                    let task = typeof this.tasks[index] !== 'undefined' ? this.tasks[index] : null;
                    if (task === null) {
                        return;
                    }

                    ///team.is_default = team.is_default ? 1 : 0;

                    this.task = task;
                    let e_count = typeof task.employees.data.length !== 'undefined' ? task.employees.data.length : 0;

                    let context = this;
                    if (e_count < 1) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You are about to delete task " + context.task.name,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            showLoaderOnConfirm: true,
                            preConfirm: (teams_delete) => {
                            return axios.delete("/mpe/people-task/" + task.id)
                                .then(function (response) {
                                    context.tasks.splice(index, 1);
                                    return swal("Deleted!", "The task was successfully deleted.", "success");
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
                                    return swal("Delete Failed", message, "warning");
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })
                    // /*.then(function() {
                    //     //Swal.fire('Ajax request finished!')
                    // })*/
                    } else {
                        Swal.fire({
                            title: "Unable to Delete!",
                            text: "The task \"" + task.name + "\" has " + e_count + " employee(s). Remove them first and the retry deleting.",
                            type: "error"
                        })
                    }
                }
       }
   });

   new Vue({
       el: '#sub-menu-action',
       data: {

       },
       methods: {
           createTask: function () {
               vm.createTask();
           }
       }
   })
</script>
@endsection