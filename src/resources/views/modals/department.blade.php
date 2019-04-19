<div class="modal fade" id="manage-department-modal" tabindex="-1" role="dialog" aria-labelledby="manage-department-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manage-department-modalLabel">@{{ typeof department.id !== 'undefined' ? 'Edit Department' : 'Create Department' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form action="{{ route('people-departments-post') }}" id="form-customers-department-update" method="post">
            {{ csrf_field() }}
            <fieldset class="form-fieldset">
              <div class="form-group">
                <label class="form-label" for="grp-name" v-bind:class="{'active': department.name.length > 0}">Department Name</label>
                <input class="form-control" id="grp-name" type="text" name="name" maxlength="80" v-model="department.name">
              </div>
              <div class="form-group">
                <label class="form-label" for="description" v-bind:class="{'active': department.description.length > 0}">Department Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" v-model="department.description"></textarea>
              </div>
            </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="hidden" name="group_id" id="grp-department-id" :value="department.id" v-if="typeof department.id !== 'undefined'" />
        <button type="submit" name="save_group" form="form-customers-department-post" class="btn btn-primary">@{{ typeof department.id !== 'undefined' ? 'Update Department' : 'Create Department' }}</button>
      </div>
    </div>
  </div>
</div>