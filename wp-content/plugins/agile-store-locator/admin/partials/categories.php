<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
    <div class="col-md-12">
      <h3  class="alert alert-info head-1"><?php echo __('Manage Categories','asl_admin') ?></h3>
      <?php if(!is_writable(AGILESTORELOCATOR_PLUGIN_PATH.'public/icon')): ?>
        <h3  class="alert alert-danger" style="font-size: 14px"><?php echo AGILESTORELOCATOR_PLUGIN_PATH.'public/icon' ?> <= <?php echo __('Directory is not writable, Category Image Upload will Fail, Make directory writable.','asl_admin') ?></h3>  
      <?php endif; ?>
      <div class="row">
        <div class="col-md-12 ralign" style="margin-bottom: 15px">
          <button type="button" id="btn-asl-delete-all" class="btn btn-danger mrg-r-10"><?php echo __('Delete Selected','asl_admin') ?></button>
          <button type="button" id="btn-asl-new-c" class="btn btn-primary mrg-r-10"><?php echo __('New Category','asl_admin') ?></button>
        </div>
      </div>
      <div class="alert alert-info" role="alert">
        <?php echo __('Upload SVG File for Categories if you are using Template 2','asl_admin') ?>
      </div>
      <table id="tbl_categories" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th align="center">&nbsp;</th>
              <th align="center"><input type="text" data-id="id"  placeholder="<?php echo __('Search ID','asl_admin') ?>"  /></th>
              <th align="center"><input type="text" data-id="category_name"  placeholder="<?php echo __('Search Name','asl_admin') ?>"  /></th>
              <th align="center"><input type="text" data-id="is_active"  placeholder="<?php echo __('Active','asl_admin') ?>"  /></th>
              <th align="center">&nbsp;</th>
              <th align="center">&nbsp;</th>
              <th align="center">&nbsp;</th>
            </tr>
            <tr>
              <th align="center"><a class="select-all"><?php echo __('Select All','asl_admin') ?></a></th>
              <th align="center"><?php echo __('Category ID','asl_admin') ?></th>
              <th align="center"><?php echo __('Name','asl_admin') ?></th>
              <th align="center"><?php echo __('Active','asl_admin') ?></th>
              <th align="center"><?php echo __('Icon','asl_admin') ?></th>
              <th align="center"><?php echo __('Created On','asl_admin') ?></th>
              <th align="center"><?php echo __('Action','asl_admin') ?>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
      <div class="dump-message asl-dumper"></div>


      <!-- Edit Alert -->
      <div class="smodal fade" id="confirm-update" role="dialog">
          <div class="smodal-dialog">
          <div class="smodal-content">
              <div class="smodal-header">
                  <h4><?php echo __('Update Category','asl_admin') ?></h4>
              </div>
              <div class="smodal-body">
                <form name="frm-updatecategory" id="frm-updatecategory">              
                    <div class="form-group">
                          <label class="control-label"><?php echo __('Category ID','asl_admin') ?>: <span id="update_category_id"> </span></label>
                          <input type="hidden"  name="data[category_id]" id="update_category_id_input">
                    </div>
                    <div class="form-group">
                          <label for="txt_name"  class="control-label"><?php echo __('Name','asl_admin') ?></label>
                          <input type="text" class="form-control validate[required]" name="data[category_name]" id="update_category_name">
                    </div>
                    <div class="form-group" id="updatecategory_image">
                       <img  src="" id="update_category_icon" alt="" data-id="same" height="80px" width="80px"/>
                       <button type="button" class="btn btn-default" id="change_image"><?php echo __('Change','asl_admin') ?></button>
                    </div>

                    <div class="form-group" style="display:none" id="updatecategory_editimage">                  
                        <div class="form-group">
                          <div class="input-group col-sm-offset-3 col-sm-9" id="drop-zone">
                            <input type="text" class="form-control file-name" placeholder="File Path...">
                            <input type="file" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
                            <span class="input-group-btn">
                              <button class="btn btn-default" onclick="jQuery('#drop-zone input[type=file]').trigger('click')" style="padding:3px 12px" type="button"><?php echo __('Browse','asl_admin') ?></button>
                            </span>
                          </div>
                        </div>
                       
                        <div class="form-group">
                          <div class="progress hideelement" style="display:none" id="progress_bar_">
                              <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                  <span style="position:relative" class="sr-only">0% Complete</span>
                              </div>
                          </div>
                        </div>
                        <ul></ul>
                    </div>
                    <p id="message_update"></p>
                    <div class="smodal-footer">
                    <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-update-categories"   type="button" data-loading-text="<?php echo __('Submitting ...','asl_admin') ?>"><?php echo __('Update Category','asl_admin') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="smodal"><?php echo __('Cancel','asl_admin') ?></button>
                    </div>
                 </form> 
             </div>
          </div>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- asl-cont end-->

<div class="smodal fade asl-p-cont" id="asl-new-cat-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="smodal-dialog">
          <div class="smodal-content">
              <div class="smodal-header">
                  <h4><?php echo __('Add New Category','asl_admin') ?></h4>
              </div>
              <div class="smodal-body">
               <form name="frm-upload-logo" id="frm-addcategory">
                  <div class="form-group col-md-12">
                    <label for="txt_name" class="col-sm-2 control-label"><?php echo __('Name','asl_admin') ?></label>
                    <div class="col-sm-10"><input type="text" class="form-control validate[required]" name="data[category_name]"></div>
                  </div>
                  <div class="form-group">
                    <div class="input-group col-sm-offset-2 col-sm-10" id="drop-zone-1">
                      <input type="text" class="form-control file-name" placeholder="File Path...">
                      <input type="file" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
                      <span class="input-group-btn">
                        <button class="btn btn-default" onclick="jQuery('#drop-zone-1 input[type=file]').trigger('click')" style="padding:3px 12px" type="button"><?php echo __('Browse','asl_admin') ?></button>
                      </span>
                    </div>
                  </div>
                 
                  <div class="form-group">
                      <div class="progress hideelement" style="display:none" id="progress_bar">
                        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                          <span style="position:relative" class="sr-only">0% Complete</span>
                        </div>
                      </div>
                  </div>
                  <ul></ul>
                  <p id="message_upload" class="alert alert-warning hide"></p>
             
                  <div class="smodal-footer">
                      <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-add-categories" type="button" data-loading-text="<?php echo __('Submitting ...','asl_admin') ?>"><?php echo __('Add Category','asl_admin') ?></button>
                      <button type="button" class="btn btn-default" data-dismiss="smodal"><?php echo __('Cancel','asl_admin') ?></button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
  url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};

asl_engine.pages.manage_categories();
</script>