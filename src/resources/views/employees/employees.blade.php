@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="row row-cards row-deck" id="employees-list">
            @if (!empty($employees) && $employees->count() > 0)
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="true"
                           data-show-refresh="true"
                           data-unique-id="id"
                           data-id-field="id"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-search-on-enter-key="true"
                            id="employees-table"
                       v-on:click="clicked($event)">
                        <thead>
                        <tr>
                            <th class="w-1" data-field="avatar">&nbsp;</th>
                            <th data-field="name">Full Name</th>
                            <th data-field="staff_code">Staff Code</th>
                            <th data-field="department.data.name">Department</th>
                            <th data-field="job_title">Job Title</th>
                            <th data-field="gender">Gender</th>
                            <th data-field="created_at">Added On</th>
                            <th data-field="menu">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)

                            <tr>
                                <td class="w-1"><span class="avatar" style="background-image: url({{ $employee->photo }})"></span></td>
                                <td>{{ implode(' ', [$employee->firstname, $employee->lastname]) }}</td>
                                <td>{{ $employee->staff_code }}</td>
                                <td>{{ !empty($employee->department) ? $employee->department['data']['name'] : '-' }}</td>
                                <td>{{ $employee->job_title }}</td>
                                <td>{{ title_case($employee->gender) }}</td>
                                <td class="text-nowrap">{{ Carbon\Carbon::parse($employee->created_at)->format('jS F, Y') }}</td>
                                <td class="w-1">
                                    <a title="View Employee" href="{{ route('people-employees-view', [$employee->id]) }}" class="icon" data-action="view"><i class="fe fe-eye"></i></a> &bull;
<!--                                     <a title="Edit Employee" href="#" class="icon" data-action="edit" data-id="{{ $employee->id }}" data-name="{{ $employee->firstname . ' ' . $employee->lastname }}"><i class="fe fe-edit"></i></a> &bull; -->
                                    <a title="Delete Employee" href="#" class="icon" data-action="delete" data-id="{{ $employee->id }}" data-name="{{ $employee->firstname . ' ' . $employee->lastname }}"><i class="fe fe-trash"></i></a>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="col s12">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Employees
                    @endslot
                    You can add one or more employees by clicking the Add Employee button
                    @slot('buttons')
                        <a href="{{ route('people-employees-new') }}" class="btn btn-primary btn-sm">Add Employee</a>
                    @endslot
                @endcomponent
            </div>
            @endif
        </div>

    </div>

</div>

@endsection

@section('body_js')
<script>
    app.currentUser = {!! json_encode($dorcasUser) !!};
    let vmPage = new Vue({
        el: '#employees-list',
        data: {
            caccounts: [],
            request_id: '{{ !empty($requestId) ? $requestId : '' }}',
            caccount: {},
            employees: {!! json_encode($employees) !!},
            employees_count: {{ $employees->count() }},
        },
        computed: {
            showEmptyState: function () {
                return this.employees_count === 0;
            },
            showTable: function () {
                return this.employees_count > 0;
            }
        },
        mounted: function () {
           this.triggerEdit();
       },
       methods: {
        titleCase: function (string) {
            return v.titleCase(string);
        },
        clicked: function ($event) {
            let target = $event.target;
            if (!target.hasAttribute('data-action')) {
                target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
            }
            //console.log(target, target.getAttribute('data-action'));
            let action = target.getAttribute('data-action').toLowerCase();
            let name = target.getAttribute('data-name');
            let id = target.getAttribute('data-id');
            // let index = parseInt(target.getAttribute('data-index'), 10);
            // if (isNaN(index)) {
            //     console.log('Index is not set.');
            //     return;
            // }
            if (action === 'view') {
                return true;
            } else if (action === 'delete') {
                this.deleteItem(id,name);
            } else {
                return true;
            }
        },
        deleteItem: function (id, name) {
            console.log("Vals are "+id+", "+name);
            //var name = attributes['data-name'] || '';
            //var id = attributes['data-id'] || null;
            //console.log(name);
            /*if (index === null) {
                return false;
            }*/
            context = this;
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to delete " + name + " from your employees.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete!",
                showLoaderOnConfirm: true,
                preConfirm: (delete_customer) => {
                this.deleting = true;
                return axios.delete("/mpe/people-employees/" + id)
                    .then(function (response) {
                        console.log(response);
                    window.location = '{{ url()->current() }}';
                    return swal("Deleted!", "The employee was successfully deleted", "success");
                    })
                    .catch(function (error) {
                        var message = '';
                        console.log(error);
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
            });

        },
            triggerEdit: function () {
                if (this.request_id.length === 0 || this.caccounts.length === 0) {
                    return '';
                }
                let indexOf = -1;
                let totalCount = this.caccounts.length;
                for (let i = 0; i < totalCount; i++) {
                    if (this.caccount[i].id !== this.request_id) {
                        continue;
                    }
                    indexOf = i;
                    break;
                }
                if (indexOf === -1) {
                    return '';
                }
                this.editItem(indexOf);
            }
        }

    });

    /*function processRecords(response) {
        console.log(response);
        vmPage.caccounts = response.rows;
        vmPage.triggerEdit();
        return response;
    }

    function processRows(row, index) {
            //row.buttons = '<a class="waves-effect btn-flat btn-small grey-text text-darken-3 view" href="/apps/crm/customers/' + row.id + '">View</a>'+
            //'<a class="waves-effect btn-flat remove red-text btn-small" href="#" data-id="'+row.id+'" data-name="'+row.name+'">DELETE</a>';

            //let user = typeof row.user !== 'undefined' && row.user.data !== null ? row.user.data : null;
            if (row.id !== null) {
                row.avatar = '<div class="avatar d-block" style="background-image: url(' + row.photo + ')">';
                if (row.photo === null) {
                    row.avatar += row.firstname.substr(0, 1) + row.lastname.substr(0, 1);
                }
                row.avatar+= '</div>';
                row.name = row.firstname + ' ' + row.lastname;
                //row.email = row.email;
                //row.phone = row.phone;
            }
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');
            if (app.currentUser.uuid !== row.id) {
                row.menu = '<div style="font-size: 1.2rem; margin-left: 10px;">';
                row.menu += '<a href="customers-customers/' + row.id + '" class="btn btn-icon"><i data-action="view" data-index="' + index + '"  class="fe fe-eye"></i></a>';
                row.menu += '<a href="#" class="btn btn-icon customer_delete" data-id="'+row.id+'" data-name="'+row.name+'"><i  data-action="customer_delete" data-index="' + index + '" data-name="'+row.name+'" data-id="'+row.id+'" class="fe fe-trash-2"></i></a>';
                row.menu += '</div>';
            }
            return row;
    }*/
</script>
@endsection