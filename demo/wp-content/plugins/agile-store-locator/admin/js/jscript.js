var asl_engine = {};

function asl_lock(){

	aswal({
		title: "AGILE STORE LOCATOR",
		customClass: 'asl-aswal',
		html: 'THANK YOU FOR USING AGILE STORE LOCATOR, MANY OTHER FEATURES INCLUDING THIS ONE IS INCLUDED IN <a target="_blank" href="https://agilestorelocator.com/demos/?v=lite">FULL VERSION</a>.'
	});
}

(function( $,app_engine ) {
	'use strict';
		function codeAddress(_address,_callback) {

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': _address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				_callback(results[0].geometry);
			} 
			else {
				displayMessage(ASL_REMOTE.LANG.geocode_fail + status,$(".dump-message"),'alert alert-danger static',true);
			}
		});
	};

	
	function isEmpty(obj) {

		if (obj == null) return true;
	    if(typeof(obj) == 'string' && obj == '')return true;
	    return Object.keys(obj).length === 0;
	};

	// Asynchronous load
    var map,
    map_object = {
        is_loaded: true,
        marker: null,
        changed :false,
		store_location: null,
		map_marker : null,
        intialize: function(_callback) {
            
        	console.log(asl_configs.api_key);
        	var API_KEY = '';
            if(asl_configs && asl_configs.api_key) {
            	API_KEY = '&key='+asl_configs.api_key;
            }

            var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = '//maps.googleapis.com/maps/api/js?libraries=places,drawing&'
                 +'callback=asl_map_intialized'+API_KEY;
                 //+'callback=asl_map_intialized';
                document.body.appendChild(script);
                this.cb = _callback;
        },
        render_a_map: function(_lat,_lng) {

        	var hdlr 	= this,
            map_div 	= document.getElementById('map_canvas'),
            _draggable 	= true;

            hdlr.store_location = (_lat && _lng)?[parseFloat(_lat),parseFloat(_lng)]:[-37.815,144.965];

            var latlng = new google.maps.LatLng(hdlr.store_location[0],hdlr.store_location[1]);

            if(!map_div)return false;

			var mapOptions = {
				zoom : 14,
                minZoom : 8,
                center:latlng,
                //maxZoom: 10,
                mapTypeId : google.maps.MapTypeId.ROADMAP,
                styles:[{"stylers":[{"saturation":-100},{"gamma":1}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.place_of_worship","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"poi.place_of_worship","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"water","stylers":[{"visibility":"on"},{"saturation":50},{"gamma":0},{"hue":"#50a5d1"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#333333"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"weight":0.5},{"color":"#333333"}]},{"featureType":"transit.station","elementType":"labels.icon","stylers":[{"gamma":1},{"saturation":50}]}]
			};
			
			hdlr.map_instance = map = new google.maps.Map(map_div, mapOptions);
			
			// && navigator.geolocation && _draggable
			if((!hdlr.store_location || isEmpty(hdlr.store_location[0]))) {
			
				/*navigator.geolocation.getCurrentPosition(function(position){
					
					hdlr.changed = true;
					hdlr.store_location = [position.coords.latitude,position.coords.longitude];
					var loc = new google.maps.LatLng(position.coords.latitude,  position.coords.longitude);
					hdlr.add_marker(loc);
					map.panTo(loc);
				});*/

				hdlr.add_marker(latlng);
			}
			else if(hdlr.store_location) {
				if(isNaN(hdlr.store_location[0]) || isNaN(hdlr.store_location[1]))return;
				//var loc = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);
				hdlr.add_marker(latlng);
				map.panTo(latlng);
			}
        },
        add_marker: function(_loc) {
			
			var hdlr   = this;	
			
			hdlr.map_marker = new google.maps.Marker({
				draggable:true,
				position:_loc,
				map:map
			});

			var marker_icon = new google.maps.MarkerImage(ASL_Instance.url+'admin/images/pin1.png');
			marker_icon.size = new google.maps.Size(24,39);
			marker_icon.anchor = new google.maps.Point(24,39);
			hdlr.map_marker.setIcon(marker_icon);
			hdlr.map_instance.panTo(_loc);
			
			google.maps.event.addListener(
			hdlr.map_marker,
			'dragend',
			function() {
				hdlr.store_location =  [hdlr.map_marker.position.lat(),hdlr.map_marker.position.lng()];
				hdlr.changed = true;
				var loc = new google.maps.LatLng(hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng());
				//map.setPosition(loc);
				map.panTo(loc);

				app_engine.pages.store_changed(hdlr.store_location);
			});
			
		}
    };

	//add the uploader
	app_engine.uploader = function($form,_URL,_done/*,_submit_callback*/){

		function formatFileSize(bytes) {
			if (typeof bytes !== 'number') {
				return ''
			}
			if (bytes >= 1000000000) {
				return (bytes / 1000000000).toFixed(2) + ' GB'
			}
			if (bytes >= 1000000) {
				return (bytes / 1000000).toFixed(2) + ' MB'
			}
			return (bytes / 1000).toFixed(2) + ' KB'
		};

		var ul = $form.find('ul');
		$form[0].reset();

		
		$form.fileupload({
		        url: _URL,
		        dataType: 'json',
		        //multipart: false,
		        done: function(e,data){

		        	ul.empty();
		        	_done(e,data);

		        	$form.find('.progress-bar').css('width','0%');
		        	$form.find('.progress').hide();

		        	//reset form if success
		        	if(data.result.success) {
		        	}

		        },
		        add : function (e, data) {

					ul.empty();
					

					var tpl = $('<li class="working"><p></p><span></span></li>');
					tpl.find('p').text(data.files[0].name.substr(0,50)).append('<i>' + formatFileSize(data.files[0].size) + '</i>');
					data.context = tpl.appendTo(ul);

					var jqXHR = null;
					$form.find('.btn-start').unbind().bind('click', function () {
						
						/*if(_submit_callback){
							if(!_submit_callback())return false;
						}*/

						jqXHR = data.submit();
						
						$form.find('.progress').show()
					});

					
					$form.find('.file-name').val(data.files[0].name);
				},
		        progress : function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$form.find('.progress-bar').css('width',progress+'%');
					$form.find('.sr-only').html(progress+'%');

					if (progress == 100) {
						data.context.removeClass('working');
					}
				},
				fail : function (e, data) {
					data.context.addClass('error');
					$form.find('.upload-status-box').html('Upload Failed! Please try again.').addClass('bg-warning alert')
				}
		        /*
		        formData: function(_form) {

		        	var formData = [{
		        		name: 'data[action]',
		        		value: 'asl_add_store'
		        	}]

		        	//	console.log(formData);
		        	return formData;
		        }*/
		    })
			.bind('fileuploadsubmit', function (e, data) {
			
		        data.formData = $form.serializeObject();
		    })
			.prop('disabled', !$.support.fileInput)
		    .parent().addClass($.support.fileInput ? undefined : 'disabled');
	};

	//http://harvesthq.github.io/chosen/options.html
	app_engine['pages'] = {
		store_changed:function(_position){

			$('#asl_txt_lat').val(_position[0]);
			$('#asl_txt_lng').val(_position[1]);
		},
		manage_categories: function(){

			var table = null;		

			//prompt the category box
			$('#btn-asl-new-c').bind('click',function(){
				$('#asl-new-cat-box').smodal('show');	
			});


			var asInitVals = {};				
			table = $('#tbl_categories').dataTable({
				"bProcessing": true,
		        "bFilter":false,
		        "bServerSide" : true,
		        //"scrollX": true,
		        /*"aoColumnDefs": [
		          { 'bSortable': false, 'aTargets': [ 1 ] }
		        ],*/
		        "bAutoWidth" : true,
		        "columnDefs": [
		            { 'bSortable': false,"width": "75px", "targets": 0 },
		            { "width": "75px", "targets": 1 },
		            { "width": "200px", "targets": 2 },
		            { "width": "100px", "targets": 3 },
		            { "width": "100px", "targets": 4 },
		            { "width": "150px", "targets": 5 },
		            { "width": "150px", "targets": 6 },
		            { 'bSortable': false, 'aTargets': [ 0,6 ] }
		        ],
		        "iDisplayLength": 10,
				"sAjaxSource": ASL_REMOTE.URL+"?action=asl_get_categories",
				"columns": [
					{ "data": "check" },
					{ "data": "id" },
					{ "data": "category_name" },
					{ "data": "is_active" },
					{ "data": "icon" },
					{ "data": "created_on" },
					{ "data": "action" }
				],
				'fnServerData' : function (sSource, aoData, fnCallback) {
	              
	              	$.get(sSource, aoData, function (json) {
	                	
	                	fnCallback(json);

	              	}, 'json');

	            },
				"fnServerParams" : function (aoData) {

					$("thead input").each( function (i) {
						
						if ( this.value != "" ) {	
							aoData.push({
				                "name" : 'filter['+$(this).attr('data-id')+']',
				                "value" :this.value
				            });
						}
					});
		        },
				"order": [[1, 'desc']]
			});
			

			//Select all button
			$('.table .select-all').bind('click',function(e){

				$('.asl-p-cont .table input').attr('checked','checked');
				
			});

			//Delete Selected Categories:: bulk
			$('#btn-asl-delete-all').bind('click',function(e){

				var $tmp_categories = $('.asl-p-cont .table input:checked');

				if($tmp_categories.length == 0) {
					displayMessage('No Category selected',$(".dump-message"),'alert alert-danger static',true);
					return;
				}

				var item_ids = [];
				$('.asl-p-cont .table input:checked').each(function(i){

					item_ids.push($(this).attr('data-id'));
				});

				
			    aswal({
			        title: ASL_REMOTE.LANG.delete_categories,
			        text: ASL_REMOTE.LANG.warn_question+' '+ ASL_REMOTE.LANG.delete_categories + '?',
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.delete_it
			        }).then(function() {

						ServerCall(ASL_REMOTE.URL+"?action=asl_delete_category",{item_ids: item_ids, multiple: true},function(_response){

			        		if(_response.success) {
			        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
			        			table.fnDraw();
			        			return;
			        		}
			        		else {
			        			displayMessage((_response.error || ASL_REMOTE.LANG.error_try_again),$(".dump-message"),'alert alert-danger static',true);
			        			return;
			        		}

						},'json');
			        }
			    );
			});


			//TO ADD NEW Categories
			var url_to_upload = ASL_REMOTE.URL,
			$form = $('#frm-addcategory');

			app_engine.uploader($form,url_to_upload+'?action=asl_add_categories',function (e, data) {

				var data = data.result;

				if(!data.success) {
					
					$('#message_upload').html(data.msg).removeClass('hide');
	           	}
	           	else {
						
						$('#message_upload').text(data.msg);
						//reset form
						$('#asl-new-cat-box').smodal('hide');
						$('#frm-addcategory').find('input:text, input:file').val('');
						$('#message_upload').empty().addClass('hide');
						$('#progress_bar').hide();
						//show table value
						table.fnDraw();
	            }
		    });

			//Validate
			$('#btn-asl-add-categories').bind('click',function(e) {

				if($('#frm-addcategory ul li').length == 0) {

					displayMessage('Please Upload Category Icon',$(".dump-message"),'alert alert-danger static',true);

					e.preventDefault();
					return;
				}
			});

		  	//show edit category model
		    $('#tbl_categories tbody').on('click','.edit_category',function(e){

		    	$('#updatecategory_image').show();
		    	$('#updatecategory_editimage').hide();
		    	$('#confirm-update').smodal('show');	
		    	$('#update_category_id').text($(this).attr("data-id"));
		    	$('#update_category_id_input').val($(this).attr("data-id"));
		    	$('#message_update').html("");

		    	ServerCall(ASL_REMOTE.URL+"?action=asl_get_category_byid",{category_id:$(this).attr("data-id")},function(_response){

	        		if(_response.success) {

	        			$("#update_category_name").val(_response.list[0]['category_name']);
	        			$("#update_category_icon").attr("src", ASL_Instance.url+"public/icon/"+_response.list[0]['icon']);
	        		}
	        		else {

	        			$('#message_update').html(_response.error).attr('class','alert alert-danger');return;
	        		}
				},'json');	
			});

		    //show edit category upload image
			$('#change_image').click(function(){

				$("#update_category_icon").attr("data-id","")
				$('#updatecategory_image').hide();
				$('#message_update').html("");
				$('#updatecategory_editimage').show();
			});	

			//update category without icon
			$('#btn-asl-update-categories').click(function(){

				if($("#update_category_icon").attr("data-id") == "same" ) {

					ServerCall(ASL_REMOTE.URL+"?action=asl_update_category",
						{data:{category_id:$("#update_category_id").text(),action:"same",category_name:$("#update_category_name").val()}},
						function(_response){

	        		if(_response.success) {

	        			$('#message_update').html(_response.msg).attr('class','alert alert-success');

	        			table.fnDraw();
	        			
	        			return;
	        		}
	        		else if(_response.error) {
	        			$('#message_update').html(_response.msg).attr('class','alert alert-danger');
	        			return;	
	        		}
				},'json');

				}

			});	

			//update category with icon

			var url_to_upload = ASL_REMOTE.URL,
			$form 			  = $('#frm-updatecategory');
			
          	$form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

			app_engine.uploader($form,url_to_upload+'?action=asl_update_category',function (e, data) {

				var data = data.result;

				if(data.success) {
					
					$('#message_update').html(data.msg).attr('class','alert alert-success');
					$('#confirm-update').smodal('hide');
					$('#frm-updatecategory').find('input:text, input:file').val('');
					$('#progress_bar_').hide();
					table.fnDraw();
	           	}
	           	else
					$('#message_update').html(data.msg).attr('class','alert alert-danger');
		    });

			//show delete category model
			$('#tbl_categories tbody').on('click','.delete_category',function(e){

				var _category_id = $(this).attr("data-id");

				aswal({
			        title: ASL_REMOTE.LANG.delete_category,
			        text: ASL_REMOTE.LANG.warn_question +' '+ ASL_REMOTE.LANG.delete_category+ ' '+_category_id+" ?",
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.delete_it,
			        }).then(
			        function() {

						ServerCall(ASL_REMOTE.URL+"?action=asl_delete_category",{category_id:_category_id},function(_response){

			        		if(_response.success) {
			        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
			        			table.fnDraw();
			        			return;
			        		}
			        		else {
			        			displayMessage((_response.error || ASL_REMOTE.LANG.error_try_again),$(".dump-message"),'alert alert-danger static',true);
			        			return;
			        		}
					        		
						},'json');

			        }
			    );
			});


		
			$("thead input").keyup( function (e) {
				
				if(e.keyCode == 13) {
					table.fnDraw();
				}
			});
		},
		manage_stores: function(){

			var table = null,
			row_duplicate_id = null;


			/*DUPLICATE STORES*/
			var duplicate_store = function(_id) {

				ServerCall(ASL_REMOTE.URL+"?action=asl_duplicate_store",{store_id:_id},function(_response){

	        		if(_response.success) {
	        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
	        			table.fnDraw();
	        			return;
	        		}
	        		else if(_response.error) {
	        			displayMessage(_response.error,$(".dump-message"),'alert alert-danger static',true);
	        			return;	
	        		}
	        		else {
	        			displayMessage(ASL_REMOTE.LANG.error_try_again,$(".dump-message"),'alert alert-danger static',true);
	        		}
				},'json');
			};

			//Prompt the DUPLICATE alert
			$('#tbl_stores').on('click','.glyphicon-duplicate',function(){
				
				row_duplicate_id = $(this).data('id');
				
				aswal({
			        title: ASL_REMOTE.LANG.duplicate_stores,
			        text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.duplicate_stores + "?",
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.duplicate_it,
			        })
			    .then(
			        function() {

						duplicate_store(row_duplicate_id);
			        }
			    );

			});


			/*Delete Stores*/
			var _delete_all_stores = function(){

				var $this = $('#asl-delete-stores');
				$this.bootButton('loading');

				ServerCall(ASL_REMOTE.URL+'?action=asl_delete_all_stores',{},function(_response){
					
					$this.bootButton('reset');
					displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',_response.success);
					table.fnDraw();
				},'json');
			};

			/*Delete All stores*/
			$('#asl-delete-stores').bind('click',function(e){

				aswal({
			        title: ASL_REMOTE.LANG.delete_all_stores,
			        text: ASL_REMOTE.LANG.warn_question +' '+ ASL_REMOTE.LANG.delete_all_stores +"?",
			        type: "error",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.delete_all
			        }).then(
			        function() {

						_delete_all_stores();
			        }
			    );
			});

			//duplicate the alert
			/*
			$('#btn-duplicate-store').bind('click',function(){

				$('#confirm-duplicate').smodal('hide');	
				duplicate_store(row_duplicate_id);
			});
			*/


			var asInitVals = {};				
			table = $('#tbl_stores').dataTable({
				"bProcessing": true,
		        "bFilter":false,
		        "bServerSide" : true,
		        "scrollX": true,
		        /*"aoColumnDefs": [
		          { 'bSortable': false, 'aTargets': [ 1 ] }
		        ],*/
		        "bAutoWidth" : false,
		        "columnDefs": [
		            { "width": "2000px", "targets": 0 },
		            { "width": "250px", "targets": 1 },
		            { "width": "100px", "targets": 2 },
		            { "width": "200px", "targets": 3 },
		            { "width": "300px", "targets": 4 },
		            { "width": "300px", "targets": 5 },
		            { "width": "300px", "targets": 6 },
		            { "width": "300px", "targets": 7 },
		            { "width": "150px", "targets": 8 },
		            { "width": "150px", "targets": 9 },
		            { "width": "150px", "targets": 10 },
		            { "width": "150px", "targets": 11 },
		            { "width": "150px", "targets": 12 },
		            { "width": "150px", "targets": 13 },
		            { "width": "50px", "targets": 14 },
		            { "width": "350px", "targets": 15 },
		            { "width": "50px", "targets": 16 },
		            { "width": "50px", "targets": 17 },
		            { "width": "150px", "targets": 18 },
		            { 'bSortable': false, 'aTargets': [ 0,15,1 ] }
		        ],
		        "iDisplayLength": 10,
				"sAjaxSource": ASL_REMOTE.URL+"?action=asl_get_store_list",
				"columns": [
					{ "data": "check" },
					{ "data": "action" },
					{ "data": "id" },
					{ "data": "title" },
					{ "data": "description" },
					{ "data": "lat" },
					{ "data": "lng" },
					{ "data": "street" },
					{ "data": "state" },
					{ "data": "city" },
					{ "data": "phone" },
					{ "data": "email" },
					{ "data": "website" },
					{ "data": "postal_code" },
					{ "data": "is_disabled" },
					{ "data": "categories" },
					{ "data": "marker_id" },
					{ "data": "logo_id" },
					{ "data": "created_on" }
				],
				"fnServerParams" : function (aoData) {

					$("#tbl_stores_wrapper .dataTables_scrollHead thead input").each( function (i) {

						if ( this.value != "" ) {	
							aoData.push({
				                "name" : 'filter['+$(this).attr('data-id')+']',
				                "value" :this.value

				            });
						}
					});

		        },
				"order": [[2, 'desc']]
			});

			//oTable.fnSort( [ [10,'desc'] ] );

			//Select all button
			$('.table .select-all').bind('click',function(e){

				$('.asl-p-cont .table input').attr('checked','checked');
			});

			//Delete Selected Stores:: bulk
			$('#btn-asl-delete-all').bind('click',function(e){

				var $tmp_stores = $('.asl-p-cont .table input:checked');

				if($tmp_stores.length == 0) {
					displayMessage('No Store selected',$(".dump-message"),'alert alert-danger static',true);
					return;
				}

				var item_ids = [];
				$('.asl-p-cont .table input:checked').each(function(i){

					item_ids.push($(this).attr('data-id'));
				});


			    aswal({
			        title: ASL_REMOTE.LANG.delete_stores,
			        text: ASL_REMOTE.LANG.warn_question+" "+ASL_REMOTE.LANG.delete_stores+"?",
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.delete_it,
			        })
			    .then(
			        function() {

						ServerCall(ASL_REMOTE.URL+"?action=asl_delete_store",{item_ids: item_ids, multiple: true},function(_response){

			        		if(_response.success) {
			        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
			        			table.fnDraw();
			        			return;
			        		}
			        		else {
			        			displayMessage((_response.error || ASL_REMOTE.LANG.error_try_again),$(".dump-message"),'alert alert-danger static',true);
			        			return;
			        		}

						},'json');
			        }
			    );
			});

			//Change the Status
			$('#btn-change-status').bind('click',function(e){

				var $tmp_stores = $('.asl-p-cont .table input:checked');

				if($tmp_stores.length == 0) {
					displayMessage('No Store Selected',$(".dump-message"),'alert alert-danger static',true);
					return;
				}

				var item_ids = [];
				$('.asl-p-cont .table input:checked').each(function(i){

					item_ids.push($(this).attr('data-id'));
				});


			    ServerCall(ASL_REMOTE.URL+"?action=asl_store_status",{item_ids: item_ids, multiple: true,status:$('#asl-ddl-status').val()},function(_response){

	        		if(_response.success) {
	        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
	        			table.fnDraw();
	        			return;
	        		}
	        		else {
	        			displayMessage((_response.error || ASL_REMOTE.LANG.error_try_again),$(".dump-message"),'alert alert-danger static',true);
	        			return;
	        		}

				},'json');
			});

			//show delete store model
			$('#tbl_stores tbody').on('click','.glyphicon-trash',function(e){

				var _store_id = $(this).attr("data-id");

				aswal({
			        title: ASL_REMOTE.LANG.delete_store,
			        text: ASL_REMOTE.LANG.warn_question+" "+ASL_REMOTE.LANG.delete_store+" "+_store_id+"?",
			        type: "warning",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: ASL_REMOTE.LANG.delete_it,
			        }).then(function() {

						ServerCall(ASL_REMOTE.URL+"?action=asl_delete_store",{store_id:_store_id},function(_response){

			        		if(_response.success) {
			        			displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
			        			table.fnDraw();
			        			return;
			        		}
			        		else {
			        			displayMessage((_response.error || ASL_REMOTE.LANG.error_try_again),$(".dump-message"),'alert alert-danger static',true);
			        			return;
			        		}
					        		
						},'json');

			        }
			    );
			});


			$("thead input").keyup( function (e) {
				
				if(e.keyCode == 13) {
					table.fnDraw();
				}
			});

		},
		customize_map: function(){

		

        	//Trafic Layer
		    $('.asl-p-cont .map-option-bottom #asl-trafic_layer').bind('click',function(e){

		    	asl_lock();

		    });

		    //Transit Layer
		    $('.asl-p-cont .map-option-bottom #asl-transit_layer').bind('click',function(e){

		    	asl_lock();
		    });

		    //Bike Layer
		    $('.asl-p-cont .map-option-bottom #asl-bike_layer').bind('click',function(e){
		    	
		    	asl_lock();

		    });

		    //Marker Animate
		    $('.asl-p-cont .map-option-bottom #asl-marker_animations').bind('click',function(e){

		    	asl_lock();
		    });


		    //Save the Map Customization
		    $('#asl-save-map').bind('click',function(e){

		    	asl_lock();
		    });

		},
		
		edit_store: function(_store){

			this.add_store(true,_store);
		},
		add_store: function(_is_edit,_store) {

			var $form = $('#frm-addstore'),
				hdlr  = this;

			var current_time = new Date();
		    current_time.setHours(7);
		    current_time.setMinutes(0);
		    
		    var start_time = current_time.toLocaleTimeString(navigator.language, {hour: '2-digit', minute:'2-digit'});
		    current_time.setHours(current_time.getHours()+ 12);
		    
		    var end_time = current_time.toLocaleTimeString(navigator.language, {hour: '2-digit', minute:'2-digit'});

		    /*Time Picker for Duration*/
			$('.start_time').timepicker({
				defaultTime: (_store)?null:start_time,
				appendWidgetTo: '.asl-p-cont'
			});

			$('.end_time').timepicker({
				defaultTime: (_store)?null:end_time,
				appendWidgetTo: '.asl-p-cont'
			});


			var current_date = new Date();

			//Add/Remove DateTime Picker
			$('.asl-time-details tbody').on('click','.add-k-add',function(e){

				var $new_slot   = $('<div class="form-group">\
										<div class="input-append input-group bootstrap-timepicker">\
								          <input type="text" value="9:30 AM" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">\
								          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>\
								        </div>\
								        <div class="input-append input-group bootstrap-timepicker">\
								          <input type="text" value="6:30 PM" class="form-control timepicker validate[required]" placeholder="End Time">\
								          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>\
								        </div>\
								        <span class="add-k-delete glyphicon glyphicon-trash"></span>\
								    </div>');

				var $cur_slot   = $(this).parent().prev().find('.asl-all-day-times .asl-closed-lbl');
				$cur_slot.before($new_slot);
				

				$new_slot.find('input.timepicker').removeAttr('id').attr('class','form-control timepicker validate[required]').val('').timepicker({
					defaultTime: current_date,
					//orientation: 'auto',
					appendWidgetTo: '.asl-p-cont'
				});
			});

			$('.asl-time-details tbody').on('click','.glyphicon-trash',function(e){

				var $this_tr = $(this).parent().remove();
			});

			//$('.asl-p-cont .asl_dates').datepicker();

			$('.asl-p-cont .asl-time-details .timepicker').timepicker({
				//defaultTime: current_date,
				//orientation: 'auto',
				appendWidgetTo: '.asl-p-cont'
			});

			//Convert the time for validation
			function asl_timeConvert(_str) {
		    
			    var time 	= $.trim(_str).toUpperCase();
			    var regex 	= /(1[012]|[1-9]):[0-5][0-9][ ]?(AM|PM)/;
			    	
			    if(!regex.test(time))
			    	return 0;

			    var hours   = Number(time.match(/^(\d+)/)[1]);
			    var minutes = Number(time.match(/:(\d+)/)[1]);
			    var AMPM    = (time.indexOf('PM') != -1)?'PM':'AM';

			    if (AMPM == "PM" && hours < 12) hours  = hours + 12;
				if (AMPM == "AM" && hours == 12) hours = hours - 12;

			    return hours+(minutes / 100);
			};

			//Match the Date :: validation
			
			//Match the time :: validation
			window['ASLmatchTime'] = function(field, rules, i, options) {

				var field_2 	= field.parent().next().children(0),
					time_val_1 	= asl_timeConvert(field.val()),
					time_val_2 	= asl_timeConvert(field_2.val());
				
				if(time_val_1 >= time_val_2)
					return "* Invalid";
			};
			
			//init the maps
			$(function(){


				if(!(window['google'] && google.maps)) {
			    	map_object.intialize();
			    }
			    else
			    	asl_map_intialized();
			});

			
		    window['asl_map_intialized'] = function(){
		    	if(_store)
		        	map_object.render_a_map(_store.lat,_store.lng);
		       	else
		       		map_object.render_a_map(parseFloat(asl_configs.default_lat),parseFloat(asl_configs.default_lng));
		    };

		    //the category ddl
			$('#ddl_categories').chosen({
				width:"100%"
				/*
				no_results_text:'Oops, nothing found!',
				allow_single_deselect:true,
				disable_search_threshold:10*/
			});

			/*Form Submit*/
			$form.validationEngine({
				binded: true,
				scroll: false
			});

			//To get Lat/lng
			$('#txt_city,#txt_state,#txt_postal_code').bind('blur',function(e){

				if(!isEmpty($form[0].elements["data[city]"].value) && !isEmpty($form[0].elements["data[postal_code]"].value)) {
					
					var address   = [$form[0].elements["data[street]"].value,$form[0].elements["data[city]"].value,$form[0].elements["data[postal_code]"].value,$form[0].elements["data[state]"].value];
				
					var q_address = [];

					for(var i = 0; i < address.length ; i++ ) {
		
						if(address[i])
							q_address.push(address[i]);						
					}

					var _country = jQuery('#txt_country option:selected').text();

					//Add country if available
					if(_country && _country != "Select Country") {
						q_address.push(_country);	
					}

					address = q_address.join(', ');

					codeAddress(address,function(_geometry) {

						var s_location =  [_geometry.location.lat(),_geometry.location.lng()];
						var loc = new google.maps.LatLng(s_location[0],s_location[1]);
						map_object.map_marker.setPosition(_geometry.location);
						map.panTo(_geometry.location);
						app_engine.pages.store_changed(s_location);

					});
				}
			});


			//Coordinates Fixes
			var _coords = {
				lat: '',
				lng: ''
			};

			$('#lnk-edit-coord').bind('click',function(e){

				_coords.lat = $('#asl_txt_lat').val();
				_coords.lng = $('#asl_txt_lng').val();

				$('#asl_txt_lat,#asl_txt_lng').val('').removeAttr('readonly');
			});

			var $coord = $('#asl_txt_lat,#asl_txt_lng');
			$coord.bind('change',function(e){

				if($coord[0].value &&  $coord[1].value && !isNaN($coord[0].value) && !isNaN($coord[1].value)) {

					var loc = new google.maps.LatLng(parseFloat($('#asl_txt_lat').val()),parseFloat($('#asl_txt_lng').val()));
					map_object.map_marker.setPosition(loc);
					map.panTo(loc);
				}
			});

			//Get Working Hours
			function getOpenHours() {

				var open_hours = {};

				$('.asl-time-details .asl-all-day-times').each(function(e){

					var $day      = $(this),
						day_index = String($day.data('day')); 
					open_hours[day_index] = null;

					if($day.find('.form-group').length > 0) {

						open_hours[day_index] = [];
					}
					else {

						open_hours[day_index] = ($day.find('.asl-closed-lbl input')[0].checked)?'1':'0';
					}

					$day.find('.form-group').each(function(){

						var $hours = $(this).find('input');
						open_hours[day_index].push($hours.eq(0).val()+' - '+$hours.eq(1).val());
					});

				});

				return JSON.stringify(open_hours);
			}

			//Add store button
			$('#btn-asl-add').bind('click',function(e) {
					
				if(!$form.validationEngine('validate'))return;

				var $btn = $(this),
				formData = $form.serializeObject();
				
				formData['action']   = (_is_edit)?'asl_edit_store':'asl_add_store';
				formData['category'] = $('#ddl_categories').val();

				if(_is_edit){formData['updateid'] = $('#update_id').val();}

				formData['data[marker_id]'] = '1';
				formData['data[logo_id]']   = '1';
				

				formData['data[open_hours]'] = getOpenHours();



				$btn.bootButton('loading');
				ServerCall(ASL_REMOTE.URL,formData,function(_response){

					$btn.bootButton('reset');
	        		if(_response.success) {
	        			
	        			$form[0].reset();
	        			$btn.bootButton('completed');
		    			
	        			_response.msg += " Redirect...";
		        		window.location.replace(ASL_REMOTE.URL.replace('-ajax','')+"?page=manage-agile-store");

	    				displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
	        			return;
	        		}
	        		else if(_response.error) {
	        			displayMessage(_response.error,$(".dump-message"),'alert alert-danger static',true);
	        			return;	
	        		}
	        		else {
	        			displayMessage('Error Occured, Please try again.',$(".dump-message"),'alert alert-danger static',true);
	        		}
				},'json');
			});


		},//user setting
		user_setting: function(_configs) {

				var $form = $('#frm-usersetting');

				var _keys = Object.keys(_configs);

				for(var i in _keys) {

					var $elem = $form.find('#asl-'+_keys[i]);
					$elem.val(_configs[_keys[i]]);
				}

				
				
				$('#btn-asl-user_setting').bind('click',function(e){

					if(!$form.validationEngine('validate'))return;

					var all_data = {
						
					};

					var data = $form.serializeObject();

					all_data = $.extend(all_data,data);
					
					ServerCall(ASL_REMOTE.URL+'?action=asl_save_setting',all_data,function(_response) {
					
						if(_response.success) {
							displayMessage(_response.msg,$(".dump-message"),'alert alert-success static',true);
							return;
						}
						else if(_response.error) {
							
							displayMessage(_response.msg,$(".dump-message"),'alert alert-danger static',true);
							return;
						}
						else {
							displayMessage('Error Occurred.',$(".dump-message"),'alert alert-danger static',true);
							return;
							
						}
					},'json');
				});
		}
	};

	//<p class="message alert alert-danger static" style="display: block;">Legal Location not found<button data-dismiss="alert" class="close" type="button"> ×</button><span class="block-arrow bottom"><span></span></span></p>
	//if jquery is defined
	if($)
		$('.asl-p-cont').append('<div class="loading site hide">Working ...</div><div class="asl-dumper dump-message"></div>');

})( jQuery,asl_engine );