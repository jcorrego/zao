jQuery(document).ready(function($){
	plugsN = $('.eos-dp-plugin-name').length;
	eos_dpBody = $('body');
	right = eos_dp_js.is_rtl ? 'left' : 'right';
	psiButtons = $('.eos-dp-psi-preview');
	eos_dp_fit_table();
	if(plugsN < 15){
		eos_dpBody.addClass('eos-dp-less-than-15-plugins');
	}
	else{
		eos_dpBody.addClass('eos-dp-more-than-15-plugins');
		localStorage.setItem('eos_dp_orientation','eos-dp-vertical');
	}
	var	orientation = localStorage.getItem('eos_dp_orientation');
	if(orientation && 'eos-dp-horizontal' === orientation){
		eos_dpBody.addClass('eos-dp-horizontal');
		eos_dp_set_horizontal_cell_width();
	}
	if('eos_dp_admin' === eos_dp_js.page){
		var main_menu_items = document.getElementsByClassName('eos-dp-admin-main-menu-link'),
			n = 0;
		if(main_menu_items){
			var main_menu_itemsLen = main_menu_items.length;
			if(main_menu_items.length > 0){
				var menu_topFirsts = document.getElementsByClassName('menu-top-first'),
					menu_topFirstsLen = menu_topFirsts.length;
				for(n;n < menu_topFirstsLen;++n){
					var link = menu_topFirsts[n].getElementsByTagName('a')[0],
						k = 0;
					if('undefined' !== typeof(link) && 'undefined' !== typeof(link.href)){
						for(k;k < main_menu_itemsLen;++k){
							if(link.href.length > 4 && 'undefined' !== typeof(main_menu_items[k].href) && link.href === main_menu_items[k].href && 'undefined' !== link.text){
								main_menu_items[k].getElementsByTagName('h4')[0].innerText = link.text;
							}
						}
					}
				}
			}
		}
	}
	$('.eos-dp-duplicated-url a.eos-dp-title').each(function(){
		var href = this.href;
		$('a.eos-dp-title[href="' + href + '"]').each(function(){
			$(this).closest('tr').find('td').on('click',function(){
				var source_row = $(this).closest('tr');
				setTimeout(function(){
					$('a.eos-dp-title[href="' + href + '"]').each(function(){
						eos_dp_clone_row_options(source_row,$(this).closest('tr'));
					});
				},1000);
			});
		});
	});	
	$('.eos-dp-title,#eos-dp-setts th span').hover(function(){
		$(this).css('transform','scale(1.05)').css('transform-origin','0 0').css('display','inline-block');
	});
	$('.eos-dp-title,#eos-dp-setts th span').on('mouseleave',function(){
		$(this).css('transform','scale(1)');
	});
	$(window).resize(function(){
		eos_dp_fit_table();
	});
	$('#eos-dp-orientation-icon').on('click',function(){
		if($(window).width() < 1300){
			eos_dpBody.removeClass('eos-dp-horizontal');
			$('.eos-dp-td-chk-wrp,.eos-dp-plugin-name').css('width','30px');
			return;
		}
		eos_dpBody.toggleClass('eos-dp-horizontal');
		if(eos_dpBody.hasClass('eos-dp-horizontal')){
			localStorage.setItem('eos_dp_orientation','eos-dp-horizontal');
			eos_dp_set_horizontal_cell_width();
		}
		else{
			localStorage.setItem('eos_dp_orientation','eos-dp-vertical');
			$('.eos-dp-td-chk-wrp,.eos-dp-plugin-name').css('width','30px');
		}
	});
	$('.eos-dp-copy').on('click',function(){
		var row = $(this).closest('tr');
		window.eos_dp_last_copied_row = eos_dp_row2setts(row);
		localStorage.setItem('eos_dp_last_copied_row',JSON.stringify(window.eos_dp_last_copied_row));
		return false;
	});
	$('.eos-dp-paste').on('click',function(){
		var row = $(this).closest('tr');
		eos_dp_paste_last_copied_setts(row);
		return false;
	});
	$('.eos-dp-preview').on('click',function(event){
		var a = this,
		plugin_path = '',
		theme = $(a).closest('td').find('.eos-dp-themes-list').val(),
		row_class = $(a).hasClass('eos-dp-archive-preview') ? '.eos-dp-archive-row' : '.eos-dp-post-row',
		row = $(this).closest(row_class),colN = 0,
		chk;
		row.find('.eos-dp-td-chk-wrp input[type=checkbox]').each(function(){
			chk = $(this);
			if(chk.is(':checked') && !chk.hasClass('eos-dp-global-chk-row')){
				colN = $(this).index();
				plugin_path += ';pn:' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path');
			}
			else{
				plugin_path += ';pn:';
			}
		});
		var button = $(this),
			page_speed_insights = button.attr('data-page_speed_insights');
		a.href = 'true' === page_speed_insights ? a.href.split('&theme=')[0].split('%26theme%3D')[0] + '%26theme%3D' + theme : a.href.split('&theme=')[0].split('%26theme%3D')[0] + '&theme=' + theme;;
		row.find('.eos-dp-post-name-wrp').addClass('eos-dp-progress');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_setts").val() || $("#eos_dp_arch_setts").val(),
				"post_type": button.closest('.eos-dp-archive-row').attr('data-post-type'),
				"tax": button.closest('.eos-dp-archive-row').attr('data-tax'),
				"post_id" : button.closest('.eos-dp-actions').attr('data-post-id'),
				"plugin_path" : plugin_path,
				"page_speed_insights" : page_speed_insights,
				"action" : 'eos_dp_preview'
			},
			success : function (response) {
				if (parseInt(response) == 1) {
					row.find('.eos-dp-post-name-wrp').removeClass('eos-dp-progress');
					window.open(a.href,'_blank');
					return true;
				}
				else{
					row.find('.eos-dp-post-name-wrp').removeClass('eos-dp-progress');
					alert( 'Something went wrong' );
				}
			}
		});
		return false;
	});
	$('.eos-dp-post-name-wrp').on('mouseover',function(){
		$(this).removeClass('eos-dp-not-hover');
	});
	$('.eos-dp-themes-list').on('click',function(){
		$(this).closest('td').addClass('eos-dp-hover').removeClass('eos-dp-not-hover');
		return false;
	});
	$('.eos-dp-close-actions,.eos-dp-actions a').not('.eos-dp-themes-list').on('click',function(){
		$(this).closest('td').removeClass('eos-dp-hover').addClass('eos-dp-not-hover');
		if(-1 !== this.className.indexOf('eos-dp-close-actions')) return false;
	});
	$('#eos-dp-setts input[type=checkbox]').on('mouseenter',function(){
		$(this).eos_dp_shiftSelectable();
	});
	$('#eos-dp-setts').delegate('input[type=checkbox]','click',function(){
		$(this).closest('td').toggleClass('eos-dp-active');
		window.eos_dp_grouped = false;
		window.eos_dp_last_modified_row = $(this).closest('tr');
	});
	$('.eos-dp-priority-post-type').on('click',function(){
		var chk = $(this);
		if(chk.hasClass('eos-dp-priority-post-type')){
			if(!chk.is(':checked')){
				chk.closest('.eos-dp-priority-post-type-wrp').addClass('eos-dp-priority-active');
			}
			else{
				chk.closest('.eos-dp-priority-post-type-wrp').removeClass('eos-dp-priority-active');
			}
			return;
		}
	});
	$('.eos-dp-global-chk-col').on('click',function(){
		var chk = $(this),
			checked = chk.is(':checked'),
			data_col = chk.attr('data-col'),
			col_class = '.eos-dp-col-' + data_col;
		if('theme' === data_col){
			col_class = '.eos-dp-row-theme';
		}
		eos_dp_update_chk_wrp(chk,checked);
		$(col_class).attr('checked',checked);
		eos_dp_update_chks($(col_class));
	});
	$('.eos-dp-lock-post').on('click',function(){
		$(this).closest('tr').toggleClass('eos-post-locked');
	});
	$('#eos-dp-setts').delegate('.eos-dp-global-chk-row','click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		var chks = chk.closest('.eos-dp-post-row').find('input[type=checkbox]').not('.eos-dp-default-post-type,.eos-dp-lock-post');
		chks.attr('checked',checked);
		eos_dp_update_chks(chks);
		eos_dp_update_chk_wrp(chk,checked);
	});
	$('.eos-dp-reset-col').on('click',function(){
		$('.eos-dp-col-' + $(this).attr('data-col')).each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
		$(this).closest('.eos-dp-global-chk-col-wrp').find('.eos-dp-global-chk-col').attr('checked',false);
		eos_dp_update_chk_wrp($(this),checked);
	});
	$('.eos-dp-reset-row').on('click',function(){
		$(this).closest('.eos-dp-post-row').find('input[type=checkbox]').each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
		$(this).closest('td').find('.eos-dp-global-chk-row').attr('checked',false);
		eos_dp_update_chk_wrp($(this),checked);
	});
	$('.eos-dp-global-chk-post_type').on('click',function(){
		var chk = $(this);
		var checked = chk.is(':checked');
		$('.eos-dp-post-' + chk.attr('data-post_type')).find('input[type=checkbox]').attr('checked',checked);
		eos_dp_update_chks($('.eos-dp-post-' + chk.attr('data-post_type')).find('input[type=checkbox]'));
		eos_dp_update_chk_wrp(chk,checked);
	});
	$('.eos-dp-reset-post_type').on('click',function(){
		$('.eos-dp-post-' + $(this).attr('data-post_type') + ' input[type=checkbox]').each(function(){
			var checked = $(this).attr('data-checked') === 'checked' ? true : false;
			$(this).attr('checked',checked);
			eos_dp_update_chks($(this));
		});
	});
	$('.eos-dp-plugin-name span a').each(function(){
		var name_wrp = $(this);
		if(name_wrp.text().length > 27){
			name_wrp.text(name_wrp.text().substring(0,24) + ' ...');
		}
	});
	$('.eos-dp-title').each(function(){
		var name_wrp = $(this);
		if(name_wrp.text().length > 60){
			name_wrp.text(name_wrp.text().substring(0,57) + ' ...');
		}
	});
	$("#eos-dp-add-url").on("click", function () {
		var last_row = $('.eos-dp-url.eos-hidden');
		last_row.clone().insertAfter(last_row);
		last_row.removeClass('eos-hidden');
		return false;
	});
	$('#eos-dp-setts').delegate('.eos-dp-delete-url','click',function(){
		$(this).closest('tr').remove();
	});
	$(".eos-dp-default-post-type-wrp").on("click", function () {
		$(this).find('input').trigger('click');
	});
	$(".eos-dp-setts-menu-item").on("click", function () {
		$(".eos-dp-setts-menu-item").removeClass('eos-active');
		$(this).addClass('eos-active');
		$('.eos-dp-section').fadeOut(2000);
	});
	$(".eos-dp-save-eos_dp_by_post_type").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var ajax_loader = $(this).next(".ajax-loader-img");
		var data_post_type = {};
		var post_types = document.getElementsByClassName('eos-dp-post-type');
		for(var n = 0;n < post_types.length;++n){
			var plugins = [];
			var chks = post_types[n].getElementsByClassName('eos-dp-td-post-type-chk-wrp');
			for(k = 0;k < chks.length;++k){
				if(!$(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active')){
					plugins[k] = document.getElementById('eos-dp-plugin-name-' + (k + 1)).getAttribute('data-path');
				}
			}
			var flg = $(post_types[n]).find('.eos-dp-priority-post-type-wrp').hasClass('eos-dp-priority-active') ? '1' : '0';
			var def = $(post_types[n]).find('.eos-dp-default-post-type').is(':checked') ? '1' : '0';
			data_post_type[post_types[n].getAttribute('data-post-type')] = [flg,plugins.join(','),def];
		};
		var data = JSON.stringify(data_post_type);
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_pt_setts").val(),
				"eos_dp_pt_setts" : data,
				"action" : 'eos_dp_save_post_type_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_url").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var ajax_loader = $(this).next(".ajax-loader-img");
		var data_url = {};
		var urls = document.getElementsByClassName('eos-dp-url'),url = '';
		for(var n = 0;n < urls.length - 1;++n){
			var plugins = [];
			var chks = urls[n].getElementsByClassName('eos-dp-td-url-chk-wrp');
			for(k = 0;k < chks.length;++k){
				if(!$(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active')){
					plugins[k] = document.getElementById('eos-dp-plugin-name-' + (k + 1)).getAttribute('data-path');
				}
			}
			url = $(urls[n]).find('.eos-dp-url-input').val();
			data_url[n] = {};
			data_url[n]['url'] = url;
			data_url[n]['plugins'] = plugins.join(',');
		};
		var data = JSON.stringify(data_url);
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_url_setts").val(),
				"eos_dp_url_setts" : data,
				"action" : 'eos_dp_save_url_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_menu").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var data_checked = 'not checked',
			actual_checked = '',
			chk,
			post_id = '',
			data = {},
			ids_locked = [],
			ids_unlocked = [],
			str = '',
			modified = [],
			bit = 0,
			row;
		$('.eos-dp-post-row').each(function(){
			row = $(this);
			modified = [];
			str = '';
			post_id = row.attr('data-post-id');
			if('undefined' !== typeof(post_id)){
				if(row.hasClass('eos-post-locked')){
					ids_locked.push(post_id);
				}
				else{
					ids_unlocked.push(post_id);
				}
				row.find('input[type=checkbox]').filter(':visible').not('.eos-dp-global-chk-row').each(function(){
					chk = $(this);
					data_checked = chk.attr('data-checked');
					actual_checked = chk.is(':checked') ? 'checked' : 'not-checked';
					bit = actual_checked !== data_checked ? '1' : '0';
					modified.push(bit);
					str += actual_checked === 'checked' ? ',' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path') : ',';
				});
				if(modified.indexOf('1') > -1){
					data['post_id_' + post_id] = str.substring(1,(str.length));
				}
			}
		});
		data['ids_locked'] = ids_locked;
		data['ids_unlocked'] = ids_unlocked;
		data['post_type'] = $('#eos-dp-setts').attr('data-post_type');
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_setts").val(),
				"eos_dp_setts" : data,
				"action" : 'eos_dp_save_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
					var checked = '';
					$('#eos-dp-setts input[type=checkbox]').each(function(){
						checked = $(this).is(':checked') ? 'checked' : 'not-checked';
						$(this).attr('data-checked',checked);
					});
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_by_archive,.eos-dp-save-eos_dp_by_term_archive").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var data_checked = 'not checked',
		actual_checked = '',
		chk,
		post_id = '',
		dataArchives = {},
		data = {},
		str = '',
		modified = [],
		bit = 0,
		archiveRow,
		row;
		$('#eos-dp-by-archive-section .eos-dp-archive-row').each(function(){
			str = '';
			row = $(this);
			row.find('input[type=checkbox]').not('.eos-dp-global-chk-row').each(function(){
				chk = $(this);
				str += chk.is(':checked') ? ',' + $('#eos-dp-plugin-name-' + chk.closest('td').index()).attr('data-path') : ',';
			});
			dataArchives[row.find('.eos-dp-view').attr('data-href')] = str;
		});
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_arch_setts").val(),
				"eos_dp_setts" : data,
				"eos_dp_setts_archives" : dataArchives,
				"action" : 'eos_dp_save_archives_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
					var checked = '';
					$('#eos-dp-setts input[type=checkbox]').each(function(){
						checked = $(this).is(':checked') ? 'checked' : 'not-checked';
						$(this).attr('data-checked',checked);
					});
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_mobile").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var chk,str = '';
		$('.eos-dp-mobile').each(function(){
			chk = $(this);
			str += !chk.is(':checked') ? ',' + $(this).attr('data-path') : ',';
		});
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_mobile_setts").val(),
				"eos_dp_mobile" : str,
				"action" : 'eos_dp_save_mobile_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_search").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var chk,str = '';
		$('.eos-dp-search').each(function(){
			chk = $(this);
			str += !chk.is(':checked') ? ',' + $(this).attr('data-path') : ',';
		});
		var ajax_loader = $(this).next(".ajax-loader-img");
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_search_setts").val(),
				"eos_dp_search" : str,
				"action" : 'eos_dp_save_search_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_admin_url").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var ajax_loader = $(this).next(".ajax-loader-img"),
			data_url = {},
			urls = document.getElementsByClassName('eos-dp-url'),
			url = '',
			theme_activation = {};
		for(var n = 0;n < urls.length - 1;++n){
			var plugins = [];
			var chks = urls[n].getElementsByClassName('eos-dp-td-url-chk-wrp');
			for(k = 0;k < chks.length - 1;++k){
				if(!$(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active')){
					plugins[k] = document.getElementById('eos-dp-plugin-name-' + (k + 1)).getAttribute('data-path');
				}
			}
			url = $(urls[n]).find('.eos-dp-url-input').val();
			theme_activation[url] = $(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active');
			data_url[n] = {};
			data_url[n]['url'] = url;
			data_url[n]['plugins'] = plugins.join(',');
		};
		var data = JSON.stringify(data_url),
			theme_activation = JSON.stringify(theme_activation);
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_admin_url_setts").val(),
				"eos_dp_admin_url_setts" : data,
				"theme_activation" : theme_activation,
				"action" : 'eos_dp_save_admin_url_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$(".eos-dp-save-eos_dp_admin").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var ajax_loader = $(this).next(".ajax-loader-img"),
			data_admin = {},
			theme_activation = {},
			eos_dp_admin = document.getElementsByClassName('eos-dp-admin-row');
		for(var n = 0;n < eos_dp_admin.length;++n){
			var plugins = [],
			chks = eos_dp_admin[n].getElementsByClassName('eos-dp-td-admin-chk-wrp');
			for(var k = 0;k < chks.length - 1;++k){
				if(!$(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active')){
					plugins[k] = $('#eos-dp-plugin-name-' + (k + 1)).attr('data-path');
				}
			}
			var key = eos_dp_admin[n].getAttribute('data-admin');
			theme_activation[key] = $(chks[k].getElementsByTagName('input')).closest('td').hasClass('eos-dp-active');
			data_admin[key] = plugins.join(',');
		}
		var data = JSON.stringify(data_admin),admin_menus = {};
		$('#adminmenu a').each(function(){
			var menu_item = $(this);
			admin_menus[menu_item.closest('li').attr('id')] = [menu_item.find('.wp-menu-name').clone().children().remove().end().text(),menu_item.attr('href')];
		});
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_admin_setts").val(),
				"eos_dp_admin_setts" : data,
				"menu_in_topbar" : $('#menu_in_topbar').is(':checked'),
				"admin_menus" : eos_dp_admin_pages,
				"theme_activation" : JSON.stringify(theme_activation),
				"action" : 'eos_dp_save_admin_settings'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1 || '' === response) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0'){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});		
	$(".eos-dp-save-eos_dp_firing_order").on("click", function () {
		$('.eos-dp-opts-msg').addClass('eos-hidden');
		var plugins = [],ajax_loader = $(this).next(".ajax-loader-img");
		$('.eos-dp-plugin.ui-sortable-handle').each(function(){
			plugins.push($(this).attr('data-path'));
		});
		ajax_loader.removeClass('eos-not-visible');
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : {
				"nonce" : $("#eos_dp_firing_order_setts").val(),
				"eos_dp_plugins" : plugins,
				"action" : 'eos_dp_save_firing_order'
			},
			success : function (response) {
				ajax_loader.addClass('eos-not-visible');
				if (parseInt(response) == 1) {
					$('.eos-dp-opts-msg_success').removeClass('eos-hidden');
				} else {
					if(response !== '0' && response !== ''){
						$('.eos-dp-opts-msg_warning').text(response);
						$('.eos-dp-opts-msg_warning').removeClass('eos-hidden');
					}
					else{
						$('.eos-dp-opts-msg_failed').removeClass('eos-hidden');
					}
				}
			}
		});
		return false;
	});
	$('#current-page-selector').keypress(function(e){
		if(e.which == 13) {
			if(parseInt(this.value) - this.value !== 0) return false;
			window.location.href = $(this).attr('data-url') + '&eos_page=' + this.value;
		}
	});
	$('#eos-dp-setts').delegate('.eos-dp-td-chk-wrp','hover',function(){
		var extra_class = $(this).parent().hasClass('eos-dp-active') ? ' eos-dp-plugin-active' : ' eos-dp-plugin-not-active';
		var idxX = $(this).parent().index();
		var idxY = $(this).closest('tr').index();
		$('.eos-dp-name-th').eq(idxX - 1).addClass('eos-dp-plugin-hover' + extra_class);
		$('.eos-dp-post-row').eq(idxY - 1).find('.eos-dp-post-name-wrp').addClass('eos-dp-row-hover');
	});
	$('#eos-dp-setts').delegate('.eos-dp-td-chk-wrp','mouseleave',function(){
		var idxX = $(this).parent().index();
		var idxY = $(this).closest('tr').index();
		$('.eos-dp-name-th').eq(idxX - 1).removeClass('eos-dp-plugin-hover').removeClass('eos-dp-plugin-active').removeClass('eos-dp-plugin-not-active');
		$('.eos-dp-post-row').eq(idxY - 1).find('.eos-dp-post-name-wrp').removeClass('eos-dp-row-hover');
	});
	$('#eos-dp-posts-per-page,#eos-dp-orderby-sel,#eos-dp-order-sel,#eos-dp-device').on('change',function(){
		var post_type = $('.eos-dp-pt-active').attr('data-post-type');
		var href = eos_dp_posts_href(this,post_type);
		document.getElementById('eos-dp-order-refresh').href = href;
		return false;
	});
	$('#eos-dp-toggle-search').on('click',function(){
		$('.eos-dp-search-wrp').toggleClass('eos-hidden');
	});
	$('#eos-dp-post-search-submit').on('click',function(){
		window.location.href = $(this).attr('data-url') + '&eos_post_title=' + encodeURI($(this).prev().val()) + '&posts_per_page=' + document.getElementById('eos-dp-posts-per-page').value;
		return false;
	});
	$('#eos-dp-by-cat-search-submit').on('click',function(){
		window.location.href = $(this).attr('data-url') + '&eos_cat=' + $('#eos-dp-by-cat-search select').val() + '&posts_per_page=' + document.getElementById('eos-dp-posts-per-page').value;
		return false;
	});
	var eos_dp_plugins_comparison = $("#eos-dp-plugins-comparison");
	if(eos_dp_plugins_comparison.length > 0){
		$('#eos-dp-show-comparison').on('click',function(){
			$([document.documentElement, document.body]).animate({
				scrollTop: $("#eos-dp-plugins-comparison").offset().top
			},1000);
		});
	}
	else{
		$('#eos-dp-show-comparison').remove();
	}
	$('#eos-dp-go-to-top').on('click',function(){
		$([document.documentElement, document.body]).animate({
			scrollTop: 0
		},500);
	});	
	$('#eos-dp-collapse-all').on('click',function(){
		$('.eos-dp-plugin-info-section').removeClass('open').addClass('close');
	});
	$('#eos-dp-expand-all').on('click',function(){
		$('.eos-dp-plugin-info-section').removeClass('close').addClass('open');
	});
	$('.eos-dp-toggle-div').on('click',function(){
		var div = $(this).closest('.eos-dp-plugin-info-section'),
			is_open = div.hasClass('open');
		$('.eos-dp-plugin-info-section').removeClass('open').addClass('close');
		if(is_open){
			div.removeClass('open').addClass('close');
		}
		else{
			div.addClass('open').removeClass('close');
		}
	});
	$('#eos-dp-fit-to-screen .dashicons').on('click',function(){
		$('body').toggleClass('eos-dp-no-zoom');
	});
	$("#wp-admin-bar-eos-dp-menu li>a").on("click",function(e){
		e.stopPropagation();
		e.stopImmediatePropagation();
		if($("#eos-dp-get-screen").hasClass('eos-dp-active')){
			var href = this.href;
			if(href && href.length > 4){
				$('#eos-dp-setts a').each(function(){
					if(this.href === href){
						var ofs = $('#eos-dp-setts').hasClass('fixed') ? $('#eos-dp-table-head').height() : 2*$('#eos-dp-table-head').height();
						$([document.documentElement,document.body]).animate({
							scrollTop: $(this).closest('tr').offset().top - ofs - $('#wpadminbar').height() - 130
						},2000);
						$("#eos-dp-get-screen").removeClass('eos-dp-active');
						return false;
					}
				});
			}
			return false;
		}
	});	
	$("#eos-dp-get-screen").on("click", function () {
		$(this).toggleClass('eos-dp-active');
		if($(this).hasClass('eos-dp-active')){
			$('#wp-admin-bar-eos-dp-menu').addClass('hover');
		}
		else{
			$('#wp-admin-bar-eos-dp-menu').addClass('hover');
		}
	});	
	if('undefined' !== typeof(eos_dp_js.page)){
		$('.eos-dp-urls').sortable({
			axis : "y",
			containment : "parent",
			items: ".eos-dp-url"
		});
		$('.eos-dp-firing-order').sortable({
			axis : "y",
			containment : "parent",
			items: ".eos-dp-plugin"
		});	
		$('.eos-ui-sortable').disableSelection();
	}
	if(psiButtons && psiButtons.length > 0){
		setInterval(function(){
			$.ajax({
				type : "POST",
				url : ajaxurl,
				data : {
					"nonce" : $("#eos_dp_key").val(),
					"action" : 'eos_dp_updated_key_for_preview'
				},
				success : function (response) {
					if('' !== response){
						var href = '';
						psiButtons.each(function(){
							href = this.href.split('$26eos_dp_preview%3D')[0];
							this.href = href + '$26eos_dp_preview%3D' + response;
						});
					}
				}
			});			
		},2000);
	}
	window.onscroll = eos_move_table_head;
	window.onbeforeunload = function(event){
		window.scrollTo(0,0);
		$('html, body').css({
			overflow: 'hidden',
			height: '100%'
		});
	};
});
jQuery.fn.eos_dp_shiftSelectable = function() {
    var lastChecked,
		boxesClasses = jQuery(this).attr('class').split(' ');
    try{ 
		$boxes = jQuery('.' + boxesClasses[0] + ',.' + boxesClasses[1]);
	}
	catch(err){
		throw '.' + boxesClasses[0] + ',.' + boxesClasses[1] + ' is not a CSS selector';
		return;
	}
    $boxes.click(function(evt) {
        if(evt.shiftKey) {
			if(!lastChecked) {
				lastChecked = this;
				return;
			}
			var classes = jQuery(this).attr('class').split(' '),
				lastClasses = jQuery(lastChecked).attr('class'),
				diffClass = '',
				sameClasses = [],
				n = 0;
			for(n;n < classes.length;++n){
				if(lastClasses.indexOf(classes[n]) < 0 ){
					diffClass = classes[n];
				}
				else{
					sameClasses.push(classes[n]);
				}
			}
			sameClass = '.' + sameClasses.join(',');
			if('.' !== sameClasses && !window.eos_dp_grouped){
				var lastCheckedWrp = jQuery(lastChecked).parent('.eos-dp-td-chk-wrp'),
					lastCheckedClass = lastCheckedWrp.parent().attr('class'),
					dataChecked,
					start = jQuery(sameClass).parent('.eos-dp-td-chk-wrp').parent().index(lastCheckedWrp.parent()),
					end = jQuery(sameClass).parent('.eos-dp-td-chk-wrp').parent().index(jQuery(this).parent('.eos-dp-td-chk-wrp').parent()),
					group =  jQuery(sameClass).slice(Math.max(0,Math.min(start,end)),Math.max(start,end) + 1);
				if(lastCheckedClass.indexOf('eos-dp-active') > 0){
					var checked = false;
				}
				else{
					var checked = true;
				}
				group
					.attr('checked',checked)
					.trigger('change');

				group.parent('.eos-dp-td-chk-wrp').parent().attr('class',lastCheckedClass);
				window.eos_dp_grouped = true;
			}
			else{
				lastChecked = null;
				$boxes = null;
			}
        }
    });
	$boxes.mouseleave(function(evt) {
		if(!evt.shiftKey) {
			lastChecked = null;
			$boxes = null;
		}
	});
};
jQuery.fn.eos_dp_isInViewport = function() {
	var elementTop = jQuery(this).offset().top;
	var elementBottom = elementTop + jQuery(this).outerHeight();
	var viewportTop = jQuery(window).scrollTop();
	var viewportBottom = viewportTop + jQuery(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};
function eos_dp_update_chks(chk){
	if(!chk.is(':checked')){
		chk.closest('td').addClass('eos-dp-active');
	}
	else{
		chk.closest('td').removeClass('eos-dp-active');
	}
}
function eos_move_table_head(){
	var table = document.getElementById('eos-dp-setts');
	if(table){
		if(!jQuery('.eos-pre-nav').first().eos_dp_isInViewport()){
			if(!jQuery('.eos-dp-post-row').last().eos_dp_isInViewport()){
				var first_col = jQuery('.eos-dp-post-row td'),
					ofs = first_col.outerWidth();
				ofs = eos_dp_js.is_rtl !== '1' ? ofs : - ofs;
				jQuery('#eos-dp-table-head').css('transform','translateX(' + ofs + 'px)');
				jQuery('.eos-dp-post-name-wrp').css('width',first_col.width() + 'px');
				table.className = 'fixed';
			}
		}
		else{
			table.className = '';
			jQuery('#eos-dp-table-head').css('transform','none');
		}
	}
}
function eos_dp_update_chk_wrp(chk,checked){
	if(true === checked){
		chk.parent().removeClass('eos-dp-active-wrp').addClass('eos-dp-not-active-wrp');
	}
	else{
		chk.parent().addClass('eos-dp-active-wrp').removeClass('eos-dp-not-active-wrp');
	}
}
function eos_dp_go_to_post_type(post_type){
	var tableHead = jQuery('#eos-dp-setts.fixed');
	var offs = tableHead.length < 1 ? parseInt(jQuery('#eos-dp-setts').offset().top) : 0;
	jQuery('.eos-dp-post-name').each(function(){
		if(jQuery(this).text().toLowerCase().split(' ').join('-')  === post_type.toLowerCase().split(' ').join('-') ){
			var el = jQuery(this).closest('.eos-dp-filters-table');
			if('undefined' !== typeof(el)){
				jQuery('html,body').animate({
					scrollTop: parseInt(el.offset().top) - offs - 40
				},500);
			}
			return false;
		}
	});
}
function eos_dp_fit_table(){
	if(jQuery(window).width() < 1300){
		eos_dpBody.removeClass('eos-dp-horizontal');
		jQuery('.eos-dp-td-chk-wrp,.eos-dp-plugin-name').css('width','30px');
	}	
	var table = jQuery('#eos-dp-setts');
	table.css('zoom','1');
	table.removeClass('eos-dp-zoom');
	var scale = (jQuery('#wpbody-content').outerWidth() - 40)/table.prop('scrollWidth');
	if(scale < 1){
		table.css('zoom',scale);
		table.addClass('eos-dp-zoom');
		table.attr('data-zoom',scale);
	}
}
function eos_dp_posts_href(el,post_type){
	var device = '',
		device_sel = document.getElementById('eos-dp-device');
	if(device_sel){
		device = '&device=' + device_sel.value;
	}
	return window.location.href.split('?')[0] + '?page=eos_dp_menu&eos_dp_post_type=' + post_type + '&orderby=' + document.getElementById('eos-dp-orderby-sel').value + '&order=' + document.getElementById('eos-dp-order-sel').value + '&posts_per_page=' + document.getElementById('eos-dp-posts-per-page').value + device;
}
function eos_dp_set_horizontal_cell_width(){
	var	w = (jQuery('.eos-dp-section').width() - jQuery('.eos-dp-post-name-wrp').outerWidth())/plugsN - 10;
	jQuery('.eos-dp-horizontal .eos-dp-td-chk-wrp,.eos-dp-horizontal .eos-dp-plugin-name').css('width',w + 'px');
}
function eos_dp_clone_row_options(source_row,destination_row){
	var setts = eos_dp_row2setts(source_row);
	eos_dp_paste_setts(setts,destination_row);
}
function eos_dp_row2setts(row){
	var setts = [];
	row.find('.eos-dp-td-chk-wrp').closest('td').each(function(){
		setts.push(!jQuery(this).hasClass('eos-dp-active'));
	});
	return setts;
}
function eos_dp_paste_setts(setts,row){
	var chks = row.find('.eos-dp-td-chk-wrp input');
	if(setts.length !== chks.length) return;
	chks.each(function(idx,el){
		var td = jQuery(el).closest('td'),chk = jQuery(el);
		if(!setts[idx]){
			td.addClass('eos-dp-active');
			chk.attr('checked',false);
		}
		else{
			td.removeClass('eos-dp-active');
			chk.attr('checked',true);
		}
	});
}
function eos_dp_paste_last_copied_setts(row){
	if('undefined' !== typeof(window.eos_dp_last_copied_row)){
		var setts = window.eos_dp_last_copied_row
	}
	else{
		var setts = localStorage.getItem('eos_dp_last_copied_row');
		if(setts && '' !== setts){
			setts = JSON.parse(setts);
		}
	}
	if(setts){
		eos_dp_paste_setts(setts,row);
	}
}