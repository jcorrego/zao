<!-- Container -->
<div class="asl-p-cont">
  <div>
      <h3 class="alert alert-info head-1">Manage Logos</h3>

      <div class="row">
        <div class="col-md-12 ralign">
          <button type="button"  onclick="asl_lock()" style="margin-bottom:20px"  class="btn disabled btn-primary mrg-r-10">New Logo</button>
        </div>
      </div>
        <table id="tbl_logos" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th align="center"><input type="text" data-id="id"  placeholder="Search ID"  /></th>
              <th align="center"><input type="text" data-id="logo_name"  placeholder="Search Name"  /></th>
              <th align="center"><input type="text" data-id="is_active"  placeholder="Active"  /></th>
              <th align="center">&nbsp;</th>
              <th align="center">&nbsp;</th>
            </tr>
            <tr>
              <th align="center">Logo ID</th>
              <th align="center">Name</th>
              <th align="center">Active</th>
              <th align="center">Image</th>
              <th align="center">Action&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          <tr>
        <td colspan="5"><span class="calign" style="width: 100%;display: block;"><?php echo __('No Logo Image is Uploaded','asl_admin') ?></span></td>
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
