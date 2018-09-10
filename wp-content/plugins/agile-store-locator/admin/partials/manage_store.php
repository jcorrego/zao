<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
    <div class="col-md-12">
    <h3  class="alert alert-info head-1"><?php echo __('Manage Stores','asl_admin') ?></h3>

    <div class="row">
      <div class="col-md-3 ralign">
        <select class="form-control" id="asl-ddl-status">
          <option value="1"><?php echo __('Enable','asl_admin') ?></option>
          <option value="0"><?php echo __('Disable','asl_admin') ?></option>
        </select>
      </div>
      <div class="col-md-1 ralign">
        <button class="btn btn-info" id="btn-change-status" type="button"><?php echo __('Change','asl_admin') ?></button>
      </div>
      <div class="col-md-3 ralign">
        <button type="button" id="btn-asl-delete-all" class="btn btn-warning mrg-r-10"><?php echo __('Delete Selected','asl_admin') ?></button>
      </div>
      <div class="col-md-3 pull-right">
        <button type="button" id="asl-delete-stores" class="btn btn-danger pull-right"><?php echo __('Delete ALL STORES','asl_admin') ?></button>
      </div>
    </div>
    <div class="alert alert-info mrg-t-20" role="alert">
      <?php echo __('Store Locator Listing columns can easily be updated by simply add/remove from the template, Please visit the link for more','asl_admin') ?> <a href="https://agilestorelocator.com/blog/customize-google-marker-infowindow-sidebar-store-locator/" target="_blank">"Customize Store Locator"</a>.
    </div>
    <table id="tbl_stores" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th><input type="text" data-id="id"  disabled="disabled" style="opacity: 0" placeholder="<?php echo __('Search ID','asl_admin') ?>"  /></th>
          <th style="position: relative;" class="asl-search-btn">
            <input type="text" data-id="-id" disabled="disabled" style="opacity: 0" placeholder="<?php echo __('Search ID','asl_admin') ?>"  />
          </th>
          <th><input type="text" data-id="id"  placeholder="<?php echo __('Search ID','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="title"  placeholder="<?php echo __('Search Title','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="description"  placeholder="<?php echo __('Search Description','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="lat"  placeholder="<?php echo __('Search Lat','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="lng"  placeholder="<?php echo __('Search Lng','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="street"  placeholder="<?php echo __('Search Street','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="state"  placeholder="<?php echo __('Search State','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="city"  placeholder="<?php echo __('Search City','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="phone"  placeholder="<?php echo __('Search Phone','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="email"  placeholder="<?php echo __('Search Email','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="website"  placeholder="<?php echo __('Search URL','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="postal_code"  placeholder="<?php echo __('Search Zip','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="is_disabled"  placeholder="<?php echo __('Disabled','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="category" disabled="disabled" style="opacity:0"  placeholder="<?php echo __('Categories','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="marker_id"  placeholder="<?php echo __('Marker ID','asl_admin') ?>"  /></th>
          <th><input type="text" data-id="logo_id"  placeholder="<?php echo __('Logo ID','asl_admin') ?>" /></th>
          <th><input type="text" data-id="created_on"  placeholder="<?php echo __('Created On','asl_admin') ?>"  /></th>
        </tr>
        <tr>
          <th><a class="select-all"><?php echo __('Select All','asl_admin') ?></a></th>
          <th><?php echo __('Action','asl_admin') ?>&nbsp;</th>
          <th><?php echo __('Store ID','asl_admin') ?></th>
          <th><?php echo __('Title','asl_admin') ?></th>
          <th><?php echo __('Description','asl_admin') ?></th>
          <th><?php echo __('Lat','asl_admin') ?></th>
          <th><?php echo __('Lng','asl_admin') ?></th>
          <th><?php echo __('Street','asl_admin') ?></th>
          <th><?php echo __('State','asl_admin') ?></th>
          <th><?php echo __('City','asl_admin') ?></th>
          <th><?php echo __('Phone','asl_admin') ?></th>
          <th><?php echo __('Email','asl_admin') ?></th>
          <th><?php echo __('URL','asl_admin') ?></th>
          <th><?php echo __('Postal Code','asl_admin') ?></th>
          <th><?php echo __('Disabled','asl_admin') ?></th>
          <th><?php echo __('Categories','asl_admin') ?></th>
          <th><?php echo __('Marker ID','asl_admin') ?></th>
          <th><?php echo __('Logo ID','asl_admin') ?></th>
          <th><?php echo __('Created On','asl_admin') ?></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <div class="dump-message asl-dumper"></div>
    </div>
  </div>
</div>



<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
  url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};
asl_engine.pages.manage_stores();
</script>
