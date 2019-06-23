@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">

        <div class="container" id="listing_departments">
            <div class="row mt-3" v-show="departments.length > 0">
                <department-card class="s12 m4" v-for="(department, index) in departments" :key="department.id" :department="department" :index="index"
                             v-on:edit-department="editDepartment" v-on:delete-department="deleteDepartment"></department-card>
            </div>
            <div class="col s12" v-if="departments.length === 0">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Departments
                    @endslot
                    You can add one or more departments to organise your employees.
                    @slot('buttons')
                        <a href="#" v-on:click.prevent="createDepartment" class="btn btn-primary btn-sm">Add Department</a>
                    @endslot
                @endcomponent
            </div>
            @include('modules-people::modals.department')
        </div>

    </div>

</div>


@endsection
@section('body_js')
    <script type="text/javascript">
        var vm = new Vue({
            el: '#listing_departments',
            data: {
                departments: {!! json_encode(!empty($departments) ? $departments : []) !!},
                department: {name: '', description: ''},
            },
            computed: {
                showDepartmentId: function () {
                    return typeof this.department.id !== 'undefined';
                }
            },
            methods: {
                createDepartment: function () {
                    this.department = {name: '', description: ''};
                    $('#manage-department-modal').modal('show');
                },
                editDepartment: function (index) {
                    let department = typeof this.departments[index] !== 'undefined' ? this.departments[index] : null;
                    if (department === null) {
                        return;
                    }
                    this.department = department;
                    $('#manage-department-modal').modal('show');
                },
                deleteDepartment: function (index) {
                    let departments = typeof this.departments !== 'undefined' ? this.departments : null;
                    let department = typeof this.departments[index] !== 'undefined' ? this.departments[index] : null;
                    if (department === null) {
                        return;
                    }
                    ///department.is_default = department.is_default ? 1 : 0;
                    this.department = department;
                    let e_count = typeof department.counts.employees !== 'undefined' ? department.counts.employees : 0;
                    let context = this;
                    if (e_count<1) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You are about to delete department " + context.department.name,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            showLoaderOnConfirm: true,
                            preConfirm: (departments_delete) => {
                            return axios.delete("/mpe/people-departments/" + context.department.id)
                                .then(function (response) {
                                    //console.log(response);
                                    context.departments.splice(index, 1);
                                    return swal("Deleted!", "The department was successfully deleted.", "success");
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
                                    return swal("Delete Failed", message, "warning");
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })
                    /*.then(function() {
                        //Swal.fire('Ajax request finished!')
                    })*/
                    } else {
                        Swal.fire({
                            title: "Unable to Delete!",
                            text: "The department \"" + department.name + "\" has " + e_count + " employee(s). Remove them first and the retry deleting.",
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
                createDepartment: function () {
                    vm.createDepartment();
                }
            }
        })
    </script>
@endsection