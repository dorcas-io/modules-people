@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">

        <div class="container" id="listing_projects">
            <div class="row mt-3" v-show="projects.length > 0">
                <project-card class="s12 m4" v-for="(project, index) in projects"
                :key="project.id" :project="project" :index="index"
                 v-on:edit-project="editProject" v-on:delete-task="deleteProject"
                ></project-card>

            </div>
            <div class="col s12" v-if="projects.length === 0">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Projects
                    @endslot
                    You can add one or more projects to organise your projects and workflow.
                    @slot('buttons')
                        <a href='#' v-on:click.prevent="createTask" class="btn btn-primary btn-sm">Add Project</a>
                    @endslot
                    
                @endcomponent
            </div>
            @include('modules-people::modals.project')
            @include('modules-people::modals.edit-project')
        </div>

    </div>

</div>


@endsection
@section('body_js')
<script type="text/javascript">
   var vm = new Vue({
       el: '#listing_projects',
       data: {
           projects:  {!! json_encode(!empty($projects) ? $projects : []) !!},
           project:  {name: '', description: '', start_date: '', end_date: ''},
           employees : [],
           employeeCount : 0,
          
       },
       created() {
         console.log(this.projects ,'here')
         // this.tasks.forEach((task , index)=> {
         //    task.employees.data.map((employee , index)  => {
         //      this.employeeCount = task.employees.data.length
         //      this.employees.push(employee.email)
         //    })
         // })
        },
       methods: {
           createTask :function () {
           
               // this.task = {name: '', description: ''};
               $('#manage-project-modal').modal('show');
           },
           editProject: function (index) {
                    let project = typeof this.projects[index] !== 'undefined' ? this.projects[index] : null;
                    if (project === null) {
                        return;
                    }
                    this.project = project;
                    $('#manage-update-project-modal').modal('show');
            },
            deleteProject: function (index) {
                    let tasks = typeof this.tasks !== 'undefined' ? this.tasks : null;
                    let task = typeof this.tasks[index] !== 'undefined' ? this.tasks[index] : null;
                    if (task === null) {
                        return;
                    }
                    ///team.is_default = team.is_default ? 1 : 0;
                    this.task = task;
                    let e_count = typeof task.counts.employees !== 'undefined' ? task.counts.employees : 0;
                    let context = this;
                    if (e_count<1) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You are about to delete team " + context.task.name,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            showLoaderOnConfirm: true,
                            preConfirm: (teams_delete) => {
                            return axios.delete("/mpe/people-tasks/" + context.task.id)
                                .then(function (response) {
                                    //console.log(response);
                                    context.task.splice(index, 1);
                                    return swal("Deleted!", "The team was successfully deleted.", "success");
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
                            text: "The team \"" + task.task + "\" has " + e_count + " employee(s). Remove them first and the retry deleting.",
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