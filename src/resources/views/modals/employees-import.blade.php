<div class="modal fade" id="employees-import-modal" tabindex="-1" role="dialog" aria-labelledby="employees-import-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="employees-import-modalLabel">{{ $importEntriesModal or 'Import Employee Data' }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="" id="form-employees-import" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<fieldset class="form-fieldset">
	                    <div class="form-group col-md-6">
	                        <div class="form-label">Select Bulk Employee Import CSV</div>
	                        <div class="custom-file">
	                            <input type="file" name="employee_import_file" id="employee_import_file" ref="employee_import_file" accept="text/csv" class="custom-file-input" v-on:change="fileUploadCheck('employee_import_file','employee_import_label','employee_import_message', 'employee_import_submit',5120)">
	                            <label class="custom-file-label" id="employee_import_label">Choose File</label>
	                        </div>
	                        <small id="employee_import_message">Any attachment must not exceed 5MB in size</small>
	                    </div>
					</fieldset>
				</form>
                <p>
                	Feel free to <a href="{{ cdn('samples/people-employees.csv') }}" class="btn btn-primary btn-sm" target="_blank">Download</a> our <strong>CSV Template</strong>: <em>Add your employee data and then upload</em>.
                </p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" id="employee_import_submit" form="form-employees-import" class="btn btn-primary" name="action"
                    value="import_employees">Upload Employees</button>
			</div>
		</div>
	</div>
</div>

