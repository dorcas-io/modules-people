
<div class="modal fade" id="manage-update-project-modal" tabindex="-1" role="dialog" aria-labelledby="manage-task-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manage-team-modalLabel">@{{ typeof project.id !== 'undefined' ? 'Edit Project' : 'Create Project' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form action="{{ route('project-update') }}"  id="form-people-project-update" method="post">
                    {{ csrf_field() }}
                    <fieldset class="form-fieldset">
                        <div class="form-group">
                            <label class="form-label" for="team_name">Project Name</label><!--v-bind:class="{'active': task.length > 0}"-->
                            <input class="form-control" id="task_name" type="text" name="project" maxlength="80" v-model="project.name">
                            <input class="form-control" id="task_name" type="hidden" name="project_id"  v-model="project.id">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Description (Optional)</label><!-- v-bind:class="{'active': task.description.length > 0}"-->
                            <textarea class="form-control" id="description" name="project_description" v-model="project.description"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Priority</label>
                            <select  class="form-control" name="priority">
                                <option>Select Priority</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        {{-- <div class="form-group">
                          <label class="form-label" for="description">Project Status</label>
                          <select  class="form-control" name="priority">
                            <option>Select Project Status</option>
                            <option value="completed">Completed</option>
                            <option value="in-progress">In-progress</option>
                            <option value="overdue">Overdue</option>
                            <option value="backlog">BackLog</option>
                          </select>
                        </div> --}}
                        <div class="form-group">
                            <label class="form-label" for="description">Start date</label>
                            <input class="form-control" id="task_name" type="date" name="start_date" v-model="project.start_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">End date</label>
                            <input class="form-control" id="task_name" type="date" name="end_date" v-model="project.end_date">
                        </div>
                    </fieldset>
                    {{-- <input type="hidden" name="team_id" id="team_id" v-model="team.id" v-if="showTeamId" /> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="save_team" form="form-people-project-update" class="btn btn-primary">@{{ typeof project.id !== 'undefined' ? 'Update Project' : 'Create Project' }}</button>
            </div>
        </div>
    </div>
</div>