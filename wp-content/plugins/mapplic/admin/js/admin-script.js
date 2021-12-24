jQuery(document).ready(function($) {
	// Sortable lists
	$('.sortable-list').sortable({
		placeholder: 'list-item-placeholder',
		forcePlaceholderSize: true,
		handle: '.list-item-handle'
	});

	$(document).on('keyup', '.title-input', function(e) {
		var text = $(this).val();
		if (text === '') text = 'undefined';

		$(this).closest('.list-item').find('.menu-item-title').text(text);
	});

	$(document).on('click', '.menu-item-toggle', function(e) {
		e.preventDefault();
		$(this).closest('.list-item').children('.list-item-settings').slideToggle(200);
		$(this).toggleClass('opened');
	});

	// New map type select
	$('#mapplic-new-type').on('change', function() {
		if (this.value != 'custom') $('#mapplic-mapfile').hide();
		else $('#mapplic-mapfile').show();
	});

	// Edit mode
	$('#mapplic-editmode').click(function() {
		$('.mapplic-rawedit').toggle();
		$('#mapplic-admin-map').toggle();
		$(this).val(function(i, text) { return text === mapplic_localization.raw ? mapplic_localization.map : mapplic_localization.raw; });
	});

	// Indentation
	$('#mapplic-indent').change(function() {
		var ischecked = $(this).is(':checked'),
			object = JSON.parse($('#mapplic-mapdata').val());
		if (ischecked) $('#mapplic-mapdata').val(JSON.stringify(object, null, 4));
		else $('#mapplic-mapdata').val(JSON.stringify(object));
	});

	// Import select
	$("#mapplic-new-type").change(function() {
		if ($(this).val() == 'import') $('#mapplic-import').show();
		else $('#mapplic-import').hide();
	});

	// WordPress colorpicker
	$('.mapplic-color-picker').wpColorPicker();
	
	// Media buttons
	$(document).on('click', '.media-button', function(e) {
		e.preventDefault();

		var button = this;

		var media_popup = wp.media({
			title: 'Select or Upload File',
			button: { text: 'Select' },
			multiple: false
		});

		media_popup.on('select', function() {
			var attachment = media_popup.state().get('selection').first().toJSON();
			$(button).closest('div').find('.input-text').val(attachment.url);
		}).open();
	});

	// Item actions
	$(document).on('click', '.item-cancel', function(e) {
		e.preventDefault();
		$(this).closest('.list-item-settings').slideToggle(200);
	});

	$(document).on('click', '.item-delete', function(e) {
		e.preventDefault();
		if (confirm('Are you sure you want to delete the selected item?')) {
			$(this).closest('.list-item').remove();
		}
	});

	// Categories
	$('#new-category').click(function() {
		$('#category-list .new-item').clone().removeClass('new-item').appendTo('#category-list');
	});

	// Floors
	$('#new-floor').click(function() {
		$('#floor-list .new-item').clone().removeClass('new-item').appendTo('#floor-list');
	});

	// Pin switcher
	$('#pins-input > li').click(function() {
		$('#pins-input .selected').removeClass('selected');
		$(this).addClass('selected');

		// Show label field only when it's available
		if ($('.mapplic-pin', this).hasClass('pin-label')) $('#landmark-settings .label-input').show();
		else $('#landmark-settings .label-input').hide();

		var selected = $('.selected-pin');
		if (selected.length) {

			var data = selected.data('landmarkData'),
				pin = $('.mapplic-pin', this).data('pin');

			selected.attr('class', 'mapplic-pin selected-pin ' + pin);
			data.pin = pin;
		}
	});

	// Settings panel
	$(document).on('keyup', '#setting-height', function(e) {
		var text = $(this).val();
		if (text === '') text = 'auto';

		$('#h-attribute').text(text);
	});

	$('.help-toggle').mousedown(function(e) { e.preventDefault(); });
	$('.help-toggle').click(function() { $(this).parent().next('.help-content').toggle(100); });

	// Landmarks
	function MapplicAdmin() {
		
		this.init = function() {
			return this;
		}

		this.newLocation = function(id) {
			// Remove selection if any
			$('.selected-pin').removeClass('selected-pin');
			// Show empty landmark fields
			$('#landmark-settings').show();
			$('#landmark-settings input[type="text"]').val('');
			$('#landmark-settings .mapplic-landmark-field').val('');
			
			if (typeof id !== 'undefined') $('#landmark-settings .id-input').val(id);

			if ($('#wp-descriptioninput-wrap').hasClass('html-active')) $('#descriptioninput').val('');
			else tinyMCE.get('descriptioninput').setContent('');
			
			$('#landmark-settings .category-select').val('false');
			$('#landmark-settings .action-select').val('default');
			$('#landmark-settings .hide-input').prop('checked', false);
			// Change button text
			$('.save-landmark').val(mapplic_localization.add);
			$('.duplicate-landmark').hide();
		}
	}
	var admin = new MapplicAdmin().init();

	$('#new-landmark').click(function() {
		admin.newLocation();
	});

	$('.save-landmark').click(function() {
		var data = null,
			selected = $('.selected-pin');

		// No id specified
		if (!$('#landmark-settings .id-input').val()) {
			alert(mapplic_localization.missing_id);
			return false;
		}
		
		if (selected.length) {
			// Save existing landmark
			data = selected.data('landmarkData');
			saveLandmarkData(data);

			$('.selected-pin').removeClass('selected-pin');
			$('#landmark-settings').hide();
		}
		else {
			// Add new landmark
			data = {};
			saveLandmarkData(data);

			data.x = 0.5;
			data.y = 0.5;

			newLandmark(data);
			$(this).val(mapplic_localization.save);
		}
	});

	$('.delete-landmark').click(function() {
		var data = $('.selected-pin').data('landmarkData');

		// Remove the location and pin
		if (data) {
			data.id = null;
			$('.selected-pin').remove();
		}

		// Hide the settings
		$('#landmark-settings').hide();
	});

	$('.duplicate-landmark').click(function() {
		var original = $('.selected-pin').data('landmarkData'),
			duplicate = jQuery.extend(true, {}, original);

		duplicate.id = prompt('Unique ID of the new landmark:', original.id + '-d');
		$('.selected-pin').removeClass('selected-pin');
		newLandmark(duplicate);
		$('#landmark-settings .id-input').val(duplicate.id);
	});

	var newLandmark = function(data) {
		$.each(mapData.levels, function(index, level) {
			if (level.id == shownLevel) {
				level.locations.push(data);
			}
		});

		// Add new pin to the map
		var pin = $('<a></a>').attr({'href': '#' + data.id, 'title': data.title}).addClass('mapplic-pin selected-pin').addClass(data.pin).css({'top': '50%', 'left': '50%'}).click(function(e) {
			e.preventDefault();
		}).appendTo($('.mapplic-layer:visible'));
		pin.data('landmarkData', data);

		$('.duplicate-landmark').show();
	}

	var saveLandmarkData = function(data) {
		data.id 			= $('#landmark-settings .id-input').val();
		data.title 			= $('#landmark-settings .title-input').val();
		data.description 	= $('#wp-descriptioninput-wrap').hasClass('html-active') ? $('#descriptioninput').val() : tinyMCE.get('descriptioninput').getContent();
		data.lat 			= $('#landmark-settings .landmark-lat').val();
		data.lng 			= $('#landmark-settings .landmark-lng').val();
		data.pin 			= $('#pins-input .selected .mapplic-pin').data('pin');
		data.label 			= $('#landmark-settings .label-input').val();
		data.fill 			= $('#landmark-settings .fill-input').val();
		data.link 			= $('#landmark-settings .link-input').val();
		data.category 		= $('#landmark-settings .category-select').val();
		data.thumbnail 		= $('#landmark-settings .thumbnail-input').val();
		data.image 			= $('#landmark-settings .image-input').val();
		data.action 		= $('#landmark-settings .action-select').val();
		data.zoom 			= $('#landmark-settings .zoom-input').val();
		data.reveal 		= $('#landmark-settings .reveal-input').val();
		data.about 			= $('#landmark-settings .about-input').val();
		data.hide 			= $('#landmark-settings .hide-input').prop('checked');

		// Custom fields
		$('#landmark-settings .mapplic-landmark-field').each(function(){
			var field = $(this).data('field');
			data[field] = $(this).val();
		});
	}

	var getParameter = function(param) {
		var pageURL = window.location.search.substring(1);
		var variables = pageURL.split('&');
		for (var i = 0; i < variables.length; i++) {
			var paramName = variables[i].split('=');
			if (paramName[0] == param) {
				return paramName[1];
			}
		}
	}

	// Load the map
	var adminmap = $('#mapplic-admin-map').mapplic({
		id: getParameter('map'),
		height: 480,
		locations: true,
		sidebar: false,
		search: true,
		minimap: true,
		slide: 0
	}).data('mapplic');
	if (adminmap) adminmap.admin = admin;

	var invalid;
	var errormsg;

	// Form submit
	$('input[type=submit]').click(function(event) {
		if ($('#mapplic-admin-map').is(':visible')) {
			invalid = false;

			var newData = {};

			if (typeof mapData === 'undefined') mapData = {};
			else newData = mapData;

			// Container height
			newData['height'] = $('#setting-height').val();
			if (newData['height'] == '') delete newData['height'];

			// Map File Dimensions
			newData['mapwidth'] 	= $('#setting-mapwidth').val();
			newData['mapheight'] 	= $('#setting-mapheight').val();

			// Features
			newData['minimap'] 		= $('#setting-minimap').is(':checked');
			newData['clearbutton'] 	= $('#setting-clearbutton').is(':checked');
			newData['zoombuttons'] 	= $('#setting-zoombuttons').is(':checked');
			newData['fullscreen'] 	= $('#setting-fullscreen').is(':checked');
			newData['hovertip'] 	= $('#setting-hovertip').is(':checked');
			newData['deeplinking'] 	= $('#setting-deeplinking').is(':checked');
			newData['zoomoutclose'] = $('#setting-zoomoutclose').is(':checked');
			newData['closezoomout'] = $('#setting-closezoomout').is(':checked');

			// Sidebar
			newData['sidebar'] 		= $('#setting-sidebar').is(':checked');
			newData['alphabetic'] 	= $('#setting-alphabetic').is(':checked');
			newData['search'] 		= $('#setting-search').is(':checked');
			newData['thumbholder']  = $('#setting-thumbholder').is(':checked');
			newData['hidenofilter'] = $('#setting-hidenofilter').is(':checked');

			// General Settings
			if ($('#setting-portrait').val()) newData['portrait'] = $('#setting-portrait').val();
			else delete newData['portrait'];

			newData['zoom'] 		= $('#setting-zoom').is(':checked');
			newData['mousewheel'] 	= $('#setting-mousewheel').is(':checked');
			newData['mapfill'] 		= $('#setting-mapfill').is(':checked');
			
			if ($('#setting-zoommargin').val()) newData['zoommargin'] = $('#setting-zoommargin').val();
			else delete newData['zoommargin'];
			
			newData['maxscale'] 	= $('#setting-maxscale').val();
			newData['action'] 		= $('#setting-action').val();
			newData['linknewtab'] 	= $('#setting-linknewtab').is(':checked');
			newData['fillcolor'] 	= $('#setting-fillcolor').val();

			// CSV Support
			newData['csv']			= $('#setting-csv').val();

			// Geolocation
			if (!isNaN($('#geopos > #topLat').val())) newData['topLat'] = $('#geopos > #topLat').val();
			if (!isNaN($('#geopos > #leftLng').val())) newData['leftLng'] = $('#geopos > #leftLng').val();
			if (!isNaN($('#geopos > #bottomLat').val())) newData['bottomLat'] = $('#geopos > #bottomLat').val();
			if (!isNaN($('#geopos > #rightLng').val())) newData['rightLng'] = $('#geopos > #rightLng').val();

			// Fetching data
			newData['categories'] 	= getCategories();
			newData['levels'] 		= getLevels();

			// Trigger event
			$('#mapplic-admin-map').trigger('mapplic-savedata', [newData]);

			// Validation
			if (invalid) {
				alert(errormsg);
				event.preventDefault();
				return false;
			}

			// Saving
			$('#mapplic-mapdata').val(JSON.stringify(newData));
		}
	});

	var getCategories = function() {
		var categories = [];
		$('#category-list .list-item:not(.new-item)').each(function() {
			var category = {};
			
			category['title'] = $('.title-input', this).val();
			category['id'] = $('.id-input', this).val();
			category['about'] = $('.about-input', this).val();
			if ($('.legend-input', this).is(':checked')) category['legend'] = 'true';
			if ($('.hide-input', this).is(':checked')) category['hide'] = 'true';
			if ($('.toggle-input', this).is(':checked')) category['toggle'] = 'true';
			if ($('.switchoff-input', this).is(':checked')) category['switchoff'] = 'true';
			category['color'] = $('.color-input', this).val();
			//if (!$('.expand-input', this).is(':checked')) category['show'] = 'false';

			// Validation
			if (category['id'] == '') {
				if (!invalid) errormsg = 'The category titled "' + category['title'] + '" has no ID.';
				invalid = true;
			}

			categories.push(category);
		});

		return categories;
	}

	var getLevels = function() {
		var levels = [];
		$('#floor-list .list-item:not(.new-item)').each(function() {
			var level = {};

			level['id']        = $('.id-input', this).val();
			level['title']     = $('.title-input', this).val();
			level['map']       = $('.map-input', this).val().replace('https:', '').replace('http:', '');
			level['minimap']   = $('.minimap-input', this).val();
			if ($('.show-input', this).is(':checked')) {
				level['show']  = 'true';
			}
			level['locations'] = getLocations(level['id']);

			// Validation
			if (level['id'] == '') {
				if (!invalid) errormsg = 'The floor titled "' + level['title'] + '" has no ID.';
				invalid = true;
			}

			levels.push(level);
		});

		levels.reverse();

		return levels;
	}

	var getLocations = function(targetLevel) {
		var locations = [];
		
		if (typeof mapData.levels !== 'undefined') {
			$.each(mapData.levels, function(index, level) {
				if (level.id == targetLevel) {
					$.each(level.locations, function(index, location) {
						if (location.id !== null) {
							delete location.el;

							for (var key in location) {
								if (location[key] == '') delete location[key];
							}
							locations.push(location);
						}
					});
				}
			});
		}
		
		return locations;
	}
});