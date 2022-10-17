<div class="modal fade" id="manage-task-modal" tabindex="-1" role="dialog" aria-labelledby="manage-task-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="manage-team-modalLabel">@{{ typeof team.id !== 'undefined' ? 'Edit Team' : 'Create Team' }}</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form action="{{ route('people-teams-post') }}" id="form-people-teams-post" method="post">
           {{ csrf_field() }}
           <fieldset class="form-fieldset">
             <div class="form-group">
               <label class="form-label" for="team_name">Team Name</label><!--v-bind:class="{'active': team.name.length > 0}"-->
               <input class="form-control" id="team_name" type="text" name="name" maxlength="80" v-model="team.name">
             </div>
             <div class="form-group">
               <label class="form-label" for="description">Description (Optional)</label><!-- v-bind:class="{'active': team.description.length > 0}"-->
               <textarea class="form-control" id="description" name="description" v-model="team.description"></textarea>
             </div>
           </fieldset>
           <input type="hidden" name="team_id" id="team_id" v-model="team.id" v-if="showTeamId" />
         </form>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         <button type="submit" name="save_team" form="form-people-teams-post" class="btn btn-primary">@{{ typeof team.id !== 'undefined' ? 'Update Team' : 'Create Team' }}</button>
       </div>
     </div>
   </div>
 </div>