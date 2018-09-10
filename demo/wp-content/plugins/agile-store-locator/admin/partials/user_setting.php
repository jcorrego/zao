<!-- Container -->
<div class="asl-p-cont">
    <div>
        <?php if(!$all_configs['api_key']): ?>
        <h3  class="alert alert-danger" style="font-size: 14px">Google API KEY is missing, Map will not appear OR the Map Search and Direction will not work without it, Please add Google API KEY. <a href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank">How to Add API Key?</a></h3>
        <?php endif; ?>
        <div class="dump-message asl-dumper"></div>
        <form class="form-horizontal" id="frm-usersetting">
        <div class="row" id="message_complete"></div>
        <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title"><?php echo __('Agile Locator Setting','asl_admin') ?></h3></div>
                        <div class="panel-body">

                            <div class="form-group s-option no-0">
                                <label class="col-sm-3 control-label" for="txt_Cluster"><?php echo __('Cluster','asl_admin') ?></label>
                                 <select  id="asl-cluster" name="data[cluster]"   class="form-control">
                                    <option value="0">OFF</option>	
                                    <option value="1">ON</option>
                                </select>
                            </div>
                            <div class="form-group s-option no-1">
                                <label class="col-sm-3 control-label" for="map_type"><?php echo __('Default Map','asl_admin') ?></label>
                                 <select   id="asl-map_type"   name="data[map_type]" class="form-control">
                                    <option value="hybrid"><?php echo __('HYBRID','asl_admin') ?></option>
                                        <option value="roadmap"><?php echo __('ROADMAP','asl_admin') ?></option>
                                        <option value="satellite"><?php echo __('SATELLITE','asl_admin') ?></option>
                                        <option value="terrain"><?php echo __('TERRAIN','asl_admin') ?></option>
                                </select>
                            </div>
                            <div class="form-group s-option no-2">
                                <label class="col-sm-3 control-label" for="txt_time_format"><?php echo __('Time Format','asl_admin') ?></label>
                                 <select  id="asl-time_format" name="data[time_format]" class="form-control">
                                    <option value="0">12 Hours</option>	
                                    <option value="1">24 Hours</option>
                                </select>
                            </div>
                            <div class="form-group s-option no-5">
                                <label class="col-sm-3 control-label" for="distance_unit"><?php echo __('Distance Unit','asl_admin') ?></label>
                                 <select  id="asl-distance_unit"  name="data[distance_unit]" class="form-control">
                                        <option value="KM">KM</option>  
                                        <option value="Miles">Miles</option>
                                </select>
                            </div>
                            <div class="form-group s-option no-6">
                                <label class="col-sm-3 control-label" for="asl-zoom"><?php echo __('Default Zoom','asl_admin') ?></label>
                                 <select  id="asl-zoom" name="data[zoom]" class="form-control">
                                        <?php for($index = 2;$index <= 20;$index++):?>
                                            <option value="<?php echo $index ?>"><?php echo $index ?></option>
                                        <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group s-option no-3">
                                <label class="col-sm-3 control-label" for="display_list"><?php echo __('Display List','asl_admin') ?></label>
                                <select id="asl-display_list" disabled="disabled"  name="data[display_list]" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="Template"><?php echo __('Map Region','asl_admin') ?></label>
                                 <select  id="asl-map_region" name="data[map_region]" class="form-control">
                                    <option value="">None</option>  
                                    <?php foreach($countries as $country): ?>
                                        <option value="<?php echo $country->code ?>"><?php echo $country->country ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>         

                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label"><?php echo __('Map Language','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input type="text" class="form-control" maxlength="2" name="data[map_language]" id="asl-map_language" placeholder="Example: US">
                                </div>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Enter the Language Code, 2 character.','asl_admin') ?> <a href="https://agilestorelocator.com/wiki/display-maps-different-language/" target="_blank" rel="nofollow">Get Code</a>)</p>
                            </div>

                            <br clear="both">
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label"><?php echo __('Header Title','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input  type="text" class="form-control validate[required]" name="data[head_title]" id="asl-head_title" placeholder="<?php echo __('Head title','asl_admin') ?>">
                                </div>
                            </div>
                            <br clear="both">

                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label"><?php echo __('Category Title','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input  type="text"   class="form-control validate[required]" name="data[category_title]" id="asl-category_title" placeholder="<?php echo __('Category Title','asl_admin') ?>">
                                </div>
                            </div>
                            <br clear="both">

                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label"><?php echo __('No Item Text','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input  type="text" class="form-control validate[required]" name="data[no_item_text]" id="asl-no_item_text" placeholder="<?php echo __('No Item Text','asl_admin') ?>">
                                </div>
                            </div>
                            <br clear="both">

                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label"><?php echo __('Google API KEY','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input  type="text" class="form-control" name="data[api_key]" id="asl-api_key" placeholder="API KEY">
                                </div>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Generate Key from  google console if required','asl_admin') ?>)</p>
                            </div>
                            <br clear="both">
                            <div class="form-group lng-lat">
                                <label class="col-sm-3 control-label"><?php echo __('Default Lat/Lng','asl_admin') ?></label>
                                <div class="col-sm-4">
                                    <input  type="text"  class="form-control validate[required]" name="data[default_lat]" id="asl-default_lat" placeholder="Lat">
                                </div>
                                <div class="col-sm-4">
                                <input  type="text"   class="form-control validate[required]" name="data[default_lng]"  id="asl-default_lng" placeholder="Lng">
                                </div>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Default coordinates for map to load','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group ralign">
                                <button type="button" class="btn btn-primary mrg-r-10" data-loading-text="<?php echo __('Saving...','asl_admin') ?>" data-completed-text="Settings Updated" id="btn-asl-user_setting"><?php echo __('Save Settings','asl_admin') ?></button>
                            </div>

                            <br clear="both">
                            <h3 class="alert alert-info" style="display: block;clear: both;font-size: 1.2em"><?php echo __('ADVANCED PRO VERSION FEATURES','asl_admin') ?></h3>

                            <div class="form-group s-option no-6">
                                <label class="col-sm-3 control-label" for="geo_button"><?php echo __('Field Button Type','asl_admin') ?></label>
                                 <select disabled="disabled"  id="asl-geo_button" name="data[geo_button]" class="form-control">
                                    <option value="1"><?php echo __('Geo-Location','asl_admin') ?></option>
                                    <option value="0"><?php echo __('Search Location','asl_admin') ?></option>
                                </select>
                            </div>

                            <div class="form-group s-option no-6">
                                <label class="col-sm-3 control-label" for="week_hours"><?php echo __('Display Hours','asl_admin') ?></label>
                                 <select  disabled="disabled" id="asl-week_hours" name="data[week_hours]" class="form-control">
                                    <option value="0"><?php echo __('Today','asl_admin') ?></option>
                                    <option value="1"><?php echo __('7 Days','asl_admin') ?></option>
                                </select>
                            </div>

                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="distance_control"><?php echo __('Distance Control','asl_admin') ?></label>
                                 <select  id="asl-distance_control" disabled="disabled" name="data[distance_control]" class="form-control">
                                        <option value="0"><?php echo __('Slider','asl_admin') ?></option>   
                                        <option value="1"><?php echo __('Dropdown','asl_admin') ?></option>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9 red"><?php echo __('Choose Either Slider or Dropdown for Radius Miles/KM','asl_admin') ?><br>
                                <a target="_blank" class="red" href="https://agilestorelocator.com/wiki/set-radius-value-distance-range-slider/"><?php echo __('To Set Maximum value for Search Range Slider','asl_admin') ?></a></p>
                            </div>
                            <div class="row form-group no-of-shop">
                                <label class="col-sm-3 control-label"><?php echo __('Search Dropdown Options','asl_admin') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" disabled="disabled" name="data[dropdown_range]" id="asl-dropdown_range" placeholder="Example: 10,20,30">
                                </div>
                                <p class="help-p col-sm-offset-3 col-sm-9 red"><?php echo __('Enter the Search dropdown Options number, Comma Separated. Add default value with * symbol.','asl_admin') ?>
                                <?php echo __('Default Value Example: 10*,20,30 . Here 10 is default value','asl_admin') ?></p>
                            </div>
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="prompt_location"><?php echo __('Prompt Location','asl_admin') ?></label>
                                 <select  id="asl-prompt_location" disabled="disabled"  name="data[prompt_location]" class="form-control">
                                        <option value="0"><?php echo __('OFF','asl_admin') ?></option>	
                                        <option value="1"><?php echo __('ON','asl_admin') ?></option>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9">(<?php echo __('Prompt a dailog box for Current Location','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="sort_by"><?php echo __('Sort List','asl_admin') ?></label>
                                 <select  disabled="disabled" id="asl-sort_by" name="data[sort_by]" class="form-control">
                                        <option value=""><?php echo __('Default (Distance)','asl_admin') ?></option>
                                        <option value="title"><?php echo __('Store ID','asl_admin') ?></option> 
                                        <option value="title"><?php echo __('Title','asl_admin') ?></option>
                                        <option value="ordr"><?php echo __('Order Field','asl_admin') ?></option>   
                                        <option value="city"><?php echo __('City','asl_admin') ?></option>  
                                        <option value="state"><?php echo __('State','asl_admin') ?></option>    
                                        <option value="logo_id"><?php echo __('Logo ID','asl_admin') ?></option>    
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Sort your listing based on fields, default is Distance','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="stores_limit"><?php echo __('Stores Limit (Limited Stores)','asl_admin') ?></label>
                                 <input  disabled="disabled" type="number" class="form-control validate[integer]" name="data[stores_limit]" id="asl-stores_limit">
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Default is NULL, Option will display given Store count only, for only mobile use attribute mobile_stores_limit="10" in shortcode','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-4 full">
                                <label class="col-sm-3 control-label" for="search_destin"><?php echo __('Search Result','asl_admin') ?></label>
                                 <select  id="asl-search_destin" disabled="disabled" name="data[search_destin]" class="form-control">
                                        <option value="1"><?php echo __('Show My Nearest Location From Search','asl_admin') ?></option> 
                                        <option value="0"><?php echo __('Default','asl_admin') ?></option>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('In search address point to my nearest markers','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label" for="asl-zoom_li"><?php echo __('Clicked Zoom','asl_admin') ?></label>
                                 <select  id="asl-zoom_li" name="data[zoom_li]" disabled="disabled"  class="form-control">
                                        <option selected="selected" value="">12</option>
                                        <?php for($index = 2;$index <= 20;$index++):?>
                                            <option value="<?php echo $index ?>"><?php echo $index ?></option>
                                        <?php endfor; ?>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9">(<?php echo __('Zoom Value when List Item is Clicked','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label" for="search_type"><?php echo __('Search Type','asl_admin') ?></label>
                                 <select  name="data[search_type]" disabled="disabled" id="asl-search_type" class="form-control">
                                        <option value="1"><?php echo __('Search By Store Name','asl_admin') ?></option>
                                        <option value="0"><?php echo __('Search By Address','asl_admin') ?></option>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Search by Either Address or Store Name, use search_type="1" in ShortCode','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label" for="asl-search_zoom"><?php echo __('Search Zoom','asl_admin') ?></label>
                                 <select disabled="disabled"  id="asl-search_zoom" name="data[search_zoom]" class="form-control">
                                        <option selected="selected" value="">12</option>
                                        <?php for($index = 2;$index <= 20;$index++):?>
                                            <option value="<?php echo $index ?>"><?php echo $index ?></option>
                                        <?php endfor; ?>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9 red">(<?php echo __('Zoom value when Search is performed','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label" for="load_all"><?php echo __('Marker Load','asl_admin') ?></label>
                                 <select  name="data[load_all]" disabled="disabled"  id="asl-load_all" class="form-control">
                                        <option value="0"><?php echo __('Load on Bound','asl_admin') ?></option>
                                        <option value="1"><?php echo __('Load All','asl_admin') ?></option>
                                </select>
                                <p class="help-p col-sm-offset-3 col-sm-9">(<?php echo __('Use Load on Bound in case of 1000 plus Markers','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label"><?php echo __('Search By','asl_admin') ?></label>
                                 <select  name="data[google_search_type]" disabled="disabled" id="asl-google_search_type" class="form-control">
                                        <option value="cities"><?php echo __('Cities (Postal Code, Cities','asl_admin') ?>)</option>
                                        <option value=""><?php echo __('All','asl_admin') ?></option>
                                        <option value="regions"><?php echo __('Regions (Locality, City, State)','asl_admin') ?></option>
                                </select>
                            </div>
                            <div class="row form-group s-option no-7 full">
                                <label class="col-sm-3 control-label"><?php echo __('Restrict Search','asl_admin') ?></label>
                                <div class="col-sm-8" style="padding-left: 0px">
                                    <input  type="text" disabled="disabled" class="form-control validate[minSize[2]]" maxlength="2" name="data[country_restrict]" id="asl-country_restrict" placeholder="Example: US">
                                </div>
                                <p class="help-p col-sm-offset-3 col-sm-9">(<?php echo __('Enter 2 alphabet country','asl_admin') ?> <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank" rel="nofollow">Code</a>)</p>
                            </div>
                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label" for="single_cat_select"><?php echo __('Category Select','asl_admin') ?></label>
                                 <select  name="data[single_cat_select]" disabled="disabled" id="asl-single_cat_select" class="form-control">
                                        <option value="0"><?php echo __('Multiple Category Selection','asl_admin') ?></option>
                                        <option value="1"><?php echo __('Single Category Selection','asl_admin') ?></option>
                                </select>
                            </div>

                            <div class="form-group s-option no-7 full">
                                <label class="col-sm-3 control-label"><?php echo __('Full Height','asl_admin') ?></label>
                                 <select  name="data[full_height]" disabled="disabled" id="asl-full_height" class="form-control">
                                        <option value=""><?php echo __('Full Screen','asl_admin') ?></option>
                                        <option value="full-height"><?php echo __('Full Height (Not Fixed)','asl_admin') ?></option>
                                        <option value="full-height asl-fixed"><?php echo __('Full Height (Fixed)','asl_admin') ?></option>
                                </select>
                            </div>                 


                            <div class="form-group c-option no-0 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-show_categories"><?php echo __('Show Categories','asl_admin') ?></label>
                                <input type="checkbox" value="1"  disabled="disabled"  id="asl-show_categories" name="data[show_categories]">
                            </div>
                            <div class="form-group c-option no-1 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-time_switch"><?php echo __('Time Switch','asl_admin') ?></label>
                                <input type="checkbox" disabled="disabled"  value="1" id="asl-time_switch" name="data[time_switch]">
                            </div>
                            <!--
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="asl-direction">Direction Service</label>
                                <input type="checkbox" value="1" id="asl-direction" name="data[direction]">
                            </div>
                            -->
                            <div class="form-group c-option no-2 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-additional_info"><?php echo __('Additional Info','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"  id="asl-additional_info" name="data[additional_info]">
                            </div>
                            <div class="form-group c-option no-3 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-advance_filter"><?php echo __('Advance Filter','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"   id="asl-advance_filter" name="data[advance_filter]">
                            </div>
                            <div class="form-group c-option no-4 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-category_marker"><?php echo __('Category Marker','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"   id="asl-category_marker" name="data[category_marker]">
                                <p class="help-p">(<?php echo __('Single Category Selection','asl_admin') ?>)</p>
                            </div>
                            <div class="form-group c-option no-5 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-distance_slider"><?php echo __('Distance Slider','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"   id="asl-distance_slider" name="data[distance_slider]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-full_width"><?php echo __('Full Width','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"  id="asl-full_width" name="data[full_width]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-analytics"><?php echo __('Analytics','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"  id="asl-analytics" name="data[analytics]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-scroll_wheel"><?php echo __('Mouse Scroll','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled"  id="asl-scroll_wheel" name="data[scroll_wheel]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-sort_by_bound"><?php echo __('Sort By Bound','asl_admin') ?></label>
                                <input type="checkbox" value="1" disabled="disabled" id="asl-sort_by_bound" name="data[sort_by_bound]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-target_blank"><?php echo __('Open Link New Tab','asl_admin') ?></label>
                                <input type="checkbox" value="1" id="asl-target_blank" disabled="disabled" name="data[target_blank]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-user_center"><?php echo __('Default Location Marker','asl_admin') ?></label>
                                <input type="checkbox" value="1" id="asl-user_center"  disabled="disabled" name="data[user_center]">
                            </div>
                            <div class="form-group c-option no-6 thirdpart">
                                <label class="col-sm-3 control-label" for="asl-smooth_pan"><?php echo __('Smooth Marker Pan','asl_admin') ?></label>
                                <input type="checkbox" value="1" id="asl-smooth_pan" disabled="disabled" name="data[asl-smooth_pan]">
                            </div>
                            <br clear="both">
                            <div class="form-group page_layout">
                                <label class="col-sm-3 control-label" ><?php echo __('Layout','asl_admin') ?></label>
                                <div id="radio">
                                    <p style=" line-height: 28px;"><?php echo __('Available in','asl_admin') ?> <a href="https://agilestorelocator.com/demos/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('Full Version','asl_admin') ?></a></p>
                                </div>
                            </div>
                            
                            
                            <br clear="both">

                            <div class="form-group map_layout">
                                <label class="col-sm-3 control-label" ><?php echo __('Map Layout','asl_admin') ?></label>
                                <div id="radio">
                                    <p style=" line-height: 28px;"><?php echo __('Available in','asl_admin') ?> <a href="https://agilestorelocator.com/demos/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('Full Version','asl_admin') ?></a></p>
                                </div>
                            </div>


                            <div class="form-group layout">
                                <label class="col-sm-3 control-label"><?php echo __('Template','asl_admin') ?></label>
                                <div id="radio">
                                    <p style=" line-height: 28px;"><?php echo __('Available in','asl_admin') ?> <a href="https://agilestorelocator.com/demos/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('Full Version','asl_admin') ?></a></p>
                                </div>
                            </div>


                            <div class="template-box box_layout_0 hide">
                                <div class="row form-group color_scheme">
                                    <label class="col-sm-3 control-label"><?php echo __('Color Scheme','asl_admin') ?></label>
                                    <div id="radio" >
                                        <span>
                                            <input type="radio" id="asl-color_scheme-0" value="0" name="data[color_scheme]">
                                            <label class="color-box color-0" for="asl-color_scheme-0"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-1" value="1" name="data[color_scheme]">
                                            <label class="color-box color-1" for="asl-color_scheme-1"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-2" value="2" name="data[color_scheme]">
                                            <label class="color-box color-2" for="asl-color_scheme-2"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-3" value="3" name="data[color_scheme]">
                                            <label class="color-box color-3" for="asl-color_scheme-3"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-4" value="4" name="data[color_scheme]">
                                            <label class="color-box color-4" for="asl-color_scheme-4"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-5" value="5" name="data[color_scheme]">
                                            <label class="color-box color-5" for="asl-color_scheme-5"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-6" value="6" name="data[color_scheme]">
                                            <label class="color-box color-6" for="asl-color_scheme-6"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-7" value="7" name="data[color_scheme]">
                                            <label class="color-box color-7" for="asl-color_scheme-7"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-8" value="8" name="data[color_scheme]">
                                            <label class="color-box color-8" for="asl-color_scheme-8"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme-9" value="9" name="data[color_scheme]">
                                            <label class="color-box color-9" for="asl-color_scheme-9"></label>
                                        </span>
                                    </div>
                                </div>
                                <div class="row form-group Font_color">
                                    <label class="col-sm-3 control-label"><?php echo __('Font Colors','asl_admin') ?></label>
                                    <div id="radio">
                                        <span>
                                            <input type="radio" id="asl-font_color_scheme-0" value="0" name="data[font_color_scheme]">
                                            <label class="font-color-box color-0" for="asl-font_color_scheme-0"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-font_color_scheme-1" value="1" name="data[font_color_scheme]">
                                            <label class="font-color-box color-1" for="asl-font_color_scheme-1"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-font_color_scheme-2" value="2" name="data[font_color_scheme]">
                                            <label class="font-color-box color-2" for="asl-font_color_scheme-2"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-font_color_scheme-3" value="3" name="data[font_color_scheme]">
                                            <label class="font-color-box color-3" for="asl-font_color_scheme-3"></label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-font_color_scheme-4" value="4" name="data[font_color_scheme]">
                                            <label class="font-color-box color-4" for="asl-font_color_scheme-4"></label>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group infobox_layout">
                                    <label class="col-sm-3 control-label" ><?php echo __('Infobox Layout','asl_admin') ?></label>
                                    <div id="radio">
                                        <p style=" line-height: 28px;"><?php echo __('Available in','asl_admin') ?> <a href="https://agilestorelocator.com/demos/?v=<?php echo AGILESTORELOCATOR_CVERSION ?>" target="_blank"><?php echo __('Full Version','asl_admin') ?></a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="template-box box_layout_1 hide">
                                <div class="row form-group color_scheme layout_2">
                                    <label class="col-sm-3 control-label"><?php echo __('Color Scheme','asl_admin') ?></label>
                                    <div id="radio" >
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-0" value="0" name="data[color_scheme_1]">
                                            <label class="color-box color-0" for="asl-color_scheme_1-0">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-1" value="1" name="data[color_scheme_1]">
                                            <label class="color-box color-1" for="asl-color_scheme_1-1">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-2" value="2" name="data[color_scheme_1]">
                                            <label class="color-box color-2" for="asl-color_scheme_1-2">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-3" value="3" name="data[color_scheme_1]">
                                            <label class="color-box color-3" for="asl-color_scheme_1-3">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-4" value="4" name="data[color_scheme_1]">
                                            <label class="color-box color-4" for="asl-color_scheme_1-4">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-5" value="5" name="data[color_scheme_1]">
                                            <label class="color-box color-5" for="asl-color_scheme_1-5">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-6" value="6" name="data[color_scheme_1]">
                                            <label class="color-box color-6" for="asl-color_scheme_1-6">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-7" value="7" name="data[color_scheme_1]">
                                            <label class="color-box color-7" for="asl-color_scheme_1-7">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-8" value="8" name="data[color_scheme_1]">
                                            <label class="color-box color-8" for="asl-color_scheme_1-8">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                        <span>
                                            <input type="radio" id="asl-color_scheme_1-9" value="9" name="data[color_scheme_1]">
                                            <label class="color-box color-9" for="asl-color_scheme_1-9">
                                                <span class="co_1"></span>
                                                <span class="co_2"></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>



        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
	var ASL_Instance = {
		url: '<?php echo AGILESTORELOCATOR_URL_PATH ?>'
	},
	asl_configs =  <?php echo json_encode($all_configs); ?>;
	asl_engine.pages.user_setting(asl_configs);
</script>