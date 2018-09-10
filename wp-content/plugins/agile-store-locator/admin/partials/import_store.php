<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  	<div class="row asl-inner-cont">
  	<div class="col-md-12">
		<div class="dump-message asl-dumper"></div>
		<div class="row" id="message_complete">
		</div>
		<h3  class="alert alert-info head-1"><?php echo __('Import Stores','asl_admin') ?></h3>
		<?php if(!class_exists('ZipArchive')): ?>
  			<h3  class="alert alert-danger" style="font-size: 14px"><?php echo __('ZipArchive is not found in your PHP, Contact your server admin OR login to your cpanel and enable it. Import/Export will Not work without this library.','asl_admin') ?></h3>	
  		<?php endif; ?>
		<div class="well">
			<div class="row">
				<div class="col-md-4">
					<div style="display: block">
						<input type="checkbox" class="inline-checkbox" checked="checked" id="asl-create-category" style="display:inline-block;margin: 0px !important;">
						<label style="display: inline-block;line-height: 15px;margin-top: 5px"><?php echo __('Create Category If NOT Exist','asl_admin') ?></label>
					</div>
					<div style="display: block">
						<input type="checkbox" class="inline-checkbox" id="asl-use-key" style="display: inline-block;margin: 0px !important">
						<label  style="display: inline-block;line-height: 15px;margin-top: 5px"><?php echo __('USE API KEY','asl_admin') ?></label>		
					</div>
					<div style="display: block">
						<input type="checkbox" class="inline-checkbox" id="asl-export-ids" style="display: inline-block;margin: 0px !important">
						<label  style="display: inline-block;line-height: 15px;margin-top: 5px"><?php echo __('Export with Store IDs (Only for Update)','asl_admin') ?></label>		
					</div>
				</div>
				<div class="col-md-3">
					<button type="button" class="btn disabled btn-success" data-toggle="smodal" data-target="#import_store_file_emodel"><?php echo __('Upload','asl_admin') ?></button>
					<button type="button" class="btn disabled btn-success" id="export_store_file_"><?php echo __('Export All','asl_admin') ?></button>
					<br>
					<a id="btn-fetch-miss-coords" style="margin-top: 10px" class="btn disabled btn-success"><?php echo __('Fetch Missing Coordinates','asl_admin') ?></a>
				</div>
				<div class="col-md-3">
					<button type="button" class="btn disabled btn-danger" data-loading-text="<?php echo __('Deleting...','asl_admin') ?>" id="asl-delete-stores"><?php echo __('Delete All Stores & Logo','asl_admin') ?></button>
				</div>
				<div class="col-md-1">
					<a target="_blank" class="btn disabled">Template.xlsx</a>
				</div>
			</div>
		</div>
		<table id="tbl_stores" class="table table-bordered">
	      <thead>
	      	<tr>        
	          <th align="center"><?php echo __('File Name','asl_admin') ?></th>
	          <th align="center"><?php echo __('Date','asl_admin') ?></th>
	          <th align="center"><?php echo __('View','asl_admin') ?></th>
	          <th align="center"><?php echo __('Import','asl_admin') ?></th>
	          <th align="center"><?php echo __('Delete','asl_admin') ?></th>
	        </tr>
	      </thead>
	      <tbody>
	     	<tr>
	     	<td colspan="5"><span class="calign" style="width: 100%;display: block;"><?php echo __('No Import File is Uploaded','asl_admin') ?></span></td>
	     	</tr>
	      </tbody>
	    </table>


	    <div class="smodal fade" id="import_store_file_emodel" role="dialog">
	        <div class="smodal-dialog">
	        
	          <!-- Modal content-->
	          <div class="smodal-content">
	            <div class="smodal-header">
	              <button type="button" class="close" data-dismiss="smodal">&times;</button>
	              <h4 class="smodal-title"><?php echo __('Upload Excel File','asl_admin') ?></h4>
	            </div>
	            <div class="smodal-body">
	            	<form id="import_store_file" name="import_store_file" >
			        	<div class="form-group">
				        	<div class="input-group col-sm-offset-3 col-sm-9" id="drop-zone">
						      <input type="text" class="form-control file-name" placeholder="<?php echo __('File Path...','asl_admin') ?>">
						      <input type="file" class="btn disabled btn-default" accept=".xlsx,.xls" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
						      <span class="input-group-btn">
						        <button class="btn disabled btn-default" onclick="jQuery('#drop-zone input[type=file]').trigger('click')" style="padding:3px 12px" type="button"><?php echo __('Browse','asl_admin') ?></button>
						      </span>
						    </div>
				        </div>
					    <div class="form-group ralign">
							<button class="btn disabled btn-default btn-start mrg-r-15" type="button" data-loading-text="Submitting ..."><?php echo __('Upload File','asl_admin') ?></button>
						</div>
						<div class="form-group">
							<div class="progress hideelement" style="display:none" id="progress_bar_">
					          <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
					            <span style="position:relative" class="sr-only">0% Complete</span>
					          </div>
					        </div>
						</div>
						<ul></ul>
						<p id="message_upload" class="alert alert-warning hide"></p>
					</form>
	            </div>
	            <div class="smodal-footer">
	              <button type="button" class="btn disabled btn-default" data-dismiss="smodal"><?php echo __('Close','asl_admin') ?></button>
	            </div>
	          </div>
	          
	        </div>
	    </div>
			
		<div class="smodal fade asl-p-cont" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="smodal-dialog">
		        <div class="smodal-content">
		            <div class="smodal-header">
		                <h4><?php echo __('Delete All Stores','asl_admin') ?></h4>
		            </div>
		            <div class="smodal-body"><?php echo __('Are you sure you want to Delete ALL stores?','asl_admin') ?></div>
		            <div class="smodal-footer">
		                <button type="button" class="btn disabled btn-default" data-dismiss="smodal"><?php echo __('Cancel','asl_admin') ?></button>
		                <a class="btn disabled btn-danger btn-ok" id="btn-delete-stores"><?php echo __('Delete','asl_admin') ?></a>
		            </div>
		        </div>
		    </div>
		</div>
	</div>
	</div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
</script>