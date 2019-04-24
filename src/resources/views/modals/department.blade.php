<div class="modal fade" id="manage-department-modal" tabindex="-1" role="dialog" aria-labelledby="manage-department-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manage-department-modalLabel">@{{ typeof department.id !== 'undefined' ? 'Edit Department' : 'Create Department' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form action="{{ route('people-departments-post') }}" id="form-people-department-post" method="post">
          {{ csrf_field() }}
          <fieldset class="form-fieldset">
            <div class="form-group">
              <label class="form-label" for="department_name">Department Name</label><!--v-bind:class="{'active': department.name.length > 0}"-->
              <input class="form-control" id="department_name" type="text" name="name" maxlength="80" v-model="department.name">
            </div>
            <div class="form-group">
              <label class="form-label" for="description">Description (Optional)</label><!-- v-bind:class="{'active': department.description.length > 0}"-->
              <textarea class="form-control" id="description" name="description" v-model="department.description"></textarea>
            </div>
          </fieldset>
          <input type="hidden" name="department_id" id="department_id" v-model="department.id" v-if="showDepartmentId" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="save_department" form="form-people-department-post" class="btn btn-primary">@{{ typeof department.id !== 'undefined' ? 'Update Department' : 'Create Department' }}</button>
      </div>
    </div>
  </div>
</div>