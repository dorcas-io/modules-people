<div class="modal fade" id="edit-employee-modal" tabindex="-1" role="dialog" aria-labelledby="manage-employee-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manage-department-modalLabel">@{{ typeof employee.id !== 'undefined' ? 'Edit Profile' : 'Create Profile' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">


    <form id="form-employee-post" action="" method="post" v-on:submit.prevent="create">
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
                        <label class="form-label" for="salary_amount" @if ($errors->has('salary_amount')) data-error="{{ $errors->first('salary_amount') }}" @endif>Salary</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input class="form-control" v-model="employee.staff_code" name="staff_code" type="text" class="validate {{ $errors->has('staff_code') ? ' invalid' : '' }}"
                        id="staff_code" required>
                        <label class="form-label" for="staff_code" @if ($errors->has('staff_code')) data-error="{{ $errors->first('staff_code') }}" @endif>Employee Code</label>
                    </div>
                    <div class="col-md-6 form-group">
                        <input class="form-control" v-model="employee.job_title" name="job_title" type="text" class="validate {{ $errors->has('job_title') ? ' invalid' : '' }}" maxlength="80" id="job_title">
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

            <!-- <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btn-block" type="submit" name="action">
                        Save Profile
                    </button>
                </div>
            </div> -->
        </div>
    </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="save_employee" v-if="!saving" form="form-employee-post" class="btn btn-primary">@{{ typeof employee.id !== 'undefined' ? 'Update Profile' : 'Create Profile' }}</button>
      </div>
    </div>
  </div>
</div>