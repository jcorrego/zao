<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
  <div class="col-md-12">
    <h3  class="alert alert-info head-1"><?php echo __('Agile Store Locator Dashboard','asl_admin') ?></h3>
    <div class="alert alert-info" role="alert">
      <?php echo __('Simply add <b>[ASL_STORELOCATOR]</b> shortcode on your','asl_admin') ?> <a href="post-new.php?post_type=page">page</a> or <a href="post-new.php">post</a> <?php echo __('to display Store Locator, Support Forum for Bugs or Troubleshooting','asl_admin') ?> <a target="_blank" href="https://wordpress.org/support/plugin/agile-store-locator"><?php echo __('Create a Ticket','asl_admin') ?></a> 
    </div>
    <div class="alert alert-info" role="alert">
      <?php echo __('Upgrade to PRO VERSION, No need to re-enter Stores Information again in Pro version or setting the configuration, please visit the demo page to explore all options of','asl_admin') ?> <a target="_blank" href="https://agilestorelocator.com/demos/store-locator-demo-3/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>">Agile Store Locator WordPress</a> 
    </div>
    <?php if(!$all_configs['api_key']): ?>
        <h3  class="alert alert-danger" style="font-size: 14px"><?php echo __('Your Google API KEY is missing, the Map Search and Direction will not work without key, Please add Google MAPS API KEY in ASL Settings.','asl_admin') ?> <a href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('How to Add API Key?','asl_admin') ?></a></h3>
      <?php endif; ?>
    <div class="dashboard-area">
      <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3 stats-store">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-shopping-cart"></span></div>
                    <div class="stats-b"><?php echo $all_stats['stores'] ?><br><span><?php echo __('Stores','asl_admin') ?></span></div>
                </div>
              </div>
              <div class="col-md-3 stats-category">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-tag"></span></div>
                    <div class="stats-b"><?php echo $all_stats['categories'] ?><br><span><?php echo __('Categories','asl_admin') ?></span></div>
                </div>
              </div>
              <div class="col-md-3 stats-marker">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-map-marker"></span></div>
                    <div class="stats-b"><?php echo $all_stats['markers'] ?><br><span><?php echo __('Markers','asl_admin') ?></span></div>
                </div>
              </div>
              <div class="col-md-3 stats-searches">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-search"></span></div>
                    <div class="stats-b"><?php echo $all_stats['searches'] ?><br><span><?php echo __('Searches','asl_admin') ?></span></div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <div class="row"></div>
       <div class="tab-pane fade in active" role="tabpanel" id="amaps-analytics" aria-labelledby="amaps-analytics">
        <div class="row">
          <div class="col-md-12">
            <p class="alert alert-info" style="margin-top: 30px"><?php echo __('Analytics with Bar Chart, Top Views and Top Stores is added in','asl_admin') ?> <a href="https://agilestorelocator.com/demos/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('PRO version','asl_admin') ?></a>.</p>
            <img src="<?php echo AGILESTORELOCATOR_URL_PATH.'admin/images/searches.png' ?>" style="max-width:100%;margin-top: 0px">
          </div>
        </div>
      </div>
    </div>

    <div class="dump-message asl-dumper"></div>
  </div>  
  </div>
</div>
<!-- asl-cont end-->








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
};

var ASL_upload = '<?php echo AGILESTORELOCATOR_PLUGIN_PATH ?>';
//asl_engine.pages.dashboard();
</script>