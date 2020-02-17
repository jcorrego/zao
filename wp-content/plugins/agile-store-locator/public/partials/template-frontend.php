
<div id="asl-storelocator" class="container no-pad storelocator-main asl-p-cont asl-bg-0 asl-text-0">
  <div class="row">
      <div class="col-md-12" id="filter-options">
          <div class="inner-filter"></div>
      </div>
  </div>
  <div class="row">
  	<div class="col-sm-4 col-xs-12 asl-panel">
      <div class="col-xs-12 inside search_filter">
        <p><?php echo __( 'Search Location', 'asl_locator') ?></p>
        <div class="asl-store-search">
            <input type="text" id="auto-complete-search" class="form-control" placeholder="<?php echo __('Type your Address', 'asl_locator') ?>">
            <span><i class="glyphicon icon-direction-outline" title="Current Location"></i></span>
        </div>
        <div class="Num_of_store">
          <span><?php echo $all_configs['head_title'] ?>: <span class="count-result">0</span></span>
        </div>    
      </div>
      <!--  Panel Listing -->
      <div id="panel" class="storelocator-panel asl-store-locator-panel">
      	<div class="asl-overlay" id="map-loading">
          <div class="white"></div>
          <div class="loading"><img style="margin-right: 10px;" class="loader" src="<?php echo AGILESTORELOCATOR_URL_PATH ?>public/Logo/loading.gif"><?php echo __('Loading...', 'asl_locator') ?></div>
        </div>
        <div class="panel-cont">
            <div class="panel-inner">
              <div class="col-md-12">
                    <ul id="p-statelist" class="accordion" role="tablist" aria-multiselectable="true">
                  </ul>
              </div>
            </div>
        </div>
        <div class="directions-cont hide">
          <div class="agile-modal-header">
            <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
            <h4><?php echo __('Directions', 'asl_locator') ?></h4>
          </div>
          <div class="rendered-directions"></div>
        </div>
      </div>
  	</div> 
  	<div class="col-sm-8 col-xs-12 asl-map">
      <div class="store-locator">
        <div id="asl-map-canv"></div>
        <!-- agile-modal -->
        <div id="agile-modal-direction" class="agile-modal fade">
          <div class="agile-modal-backdrop-in"></div>
          <div class="agile-modal-dialog in">
            <div class="agile-modal-content">
              <div class="agile-modal-header">
                <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4><?php echo __('Get Your Directions', 'asl_locator') ?></h4>
              </div>
              <div class="form-group">
                <label for="frm-lbl"><?php echo __('From', 'asl_locator') ?>:</label>
                <input type="text" class="form-control frm-place" id="frm-lbl" placeholder="Enter a Location">
              </div>
              <div class="form-group">
                <label for="frm-lbl"><?php echo __('To', 'asl_locator') ?>:</label>
                <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="Prepopulated Destination Address">
              </div>
              <div class="form-group">
                <span for="frm-lbl"><?php echo __('Show Distance In', 'asl_locator') ?></span>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type" checked id="rbtn-km" value="0"> KM
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type" id="rbtn-mile" value="1"> Mile
                </label>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-default btn-submit"><?php echo __('GET DIRECTIONS', 'asl_locator') ?></button>
              </div>
            </div>
          </div>
        </div>

      </div>
  	</div>
  </div>
  <div class="asl-footer"><a href="https://wordpress.org/plugins/agile-store-locator/" rel="nofollow">Powered by Agile Store Locator for WordPress | WordPress.org</a></div>
</div>

<script id="tmpl_list_item" type="text/x-jsrender">
	<div class="item" data-id="{{:id}}">
    <div class="addr-sec">
    <p class="p-title">{{:title}}</p>
    </div>
  	<div class="clear"></div>
  	<div class="col-md-12 col-xs-12 addr-sec">
    	{{if description}}
      <p class="p-area" style="margin-bottom: 5px;font-style: italic;">{{:description}}</p>
      {{/if}}
    	<p class="p-area"><span class="glyphicon icon-location"></span>{{:address}}</p>
      {{if phone}}
    	<p class="p-area"><span class="glyphicon icon-phone-outline"></span> <?php echo __( 'Phone','asl_locator') ?>: {{:phone}}</p>
      {{/if}}
      {{if email}}
      <p class="p-area"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></p>
      {{/if}}
      {{if fax}}
        <p class="p-area"><span class="glyphicon icon-fax"></span> Fax:{{:fax}}</p>
      {{/if}}  
      {{if c_names}}
      <p class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</p>
      {{/if}}
      {{if days_str}}
      <p class="p-time"><span class="glyphicon icon-calendar"></span> {{:days_str}}</p>
      {{/if}}
      {{if open_hours}}
      <p class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</p>
      {{/if}}
  	</div>
    <div class="col-xs-12 distance">
        <div class="col-xs-6">
          <p class="p-direction"><span class="s-direction"><?php echo __('Directions', 'asl_locator') ?></span></p>
        </div>
        {{if dist_str}}
        <div class="col-xs-6">
            <a class="s-distance"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</a>
        </div>
        {{/if}}
    </div>
  	<div class="clear"/>
  </div>
</script>



<script id="asl_too_tip" type="text/x-jsrender">
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      {{if description}}
      <div class="address" style="margin-bottom: 10px">{{:description}}</div>
      {{/if}}
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b>Phone: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{if email}}
      <div class="phone"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></div>
      {{/if}}
      {{if c_names}}
      <div class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</div>
      {{/if}}
      {{if open_hours}}
      <div class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</div>
      {{/if}}
      {{if dist_str}}
        <div class="row">
          <div class="col-xs-12">
            <a class="s-distance pull-right" style="margin-right: 15px"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</a>
          </div>
        </div>
      {{/if}}
    </div>
  <div class="asl-buttons"></div>
</div><div class="arrow-down"></div>
</script>

