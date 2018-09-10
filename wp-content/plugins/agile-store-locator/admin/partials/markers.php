<!-- Container -->
<div class="asl-p-cont">
  <div>
      <h3 class="alert alert-info head-1"><?php echo __('Manage Markers','asl_admin') ?></h3>

      <div class="row">
        <div class="col-md-12 ralign">
          <button type="button"  onclick="asl_lock()" style="margin-bottom:20px"  class="btn disabled btn-primary mrg-r-10"><?php echo __('New Marker','asl_admin') ?></button>
        </div>
      </div>
        <table id="tbl_markers" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th align="center"><input type="text" data-id="id"  placeholder="<?php echo __('Search ID','asl_admin') ?>"  /></th>
              <th align="center"><input type="text" data-id="marker_name"  placeholder="<?php echo __('Search Name','asl_admin') ?>"  /></th>
              <th align="center"><input type="text" data-id="is_active"  placeholder="<?php echo __('Active','asl_admin') ?>"  /></th>
              <th align="center">&nbsp;</th>
              <th align="center">&nbsp;</th>
            </tr>
            <tr>
              <th align="center"><?php echo __('Marker ID','asl_admin') ?></th>
              <th align="center"><?php echo __('Name','asl_admin') ?></th>
              <th align="center"><?php echo __('Active','asl_admin') ?></th>
              <th align="center"><?php echo __('Icon','asl_admin') ?></th>
              <th align="center"><?php echo __('Action','asl_admin') ?>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          <tr>
          <td colspan="5"><span class="calign" style="width: 100%;display: block;"><?php echo __('No Marker is Uploaded','asl_admin') ?></span></td>
          </tr>
          </tbody>
        </table>
	   <div class="dump-message asl-dumper"></div>

  </div>
</div>


<!-- asl-cont end-->
</div>


<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};
</script>
