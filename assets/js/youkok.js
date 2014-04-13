//
// Bloodhound
//
//localStorage.clear();

var courses = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('course'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	limit: 10,
	prefetch: 'processor/search/courses.json',
});
courses.initialize();

//
// Searching
//

function check_check_search() {
	// Some variables
	var val = $('#search').val();
	var datums = courses.index.datums;
	var datums_size = datums.length;
	
	// Now loop 'em
	for (var i = 0; i < datums_size; i++) {
		// Store reference
		var current_datum = datums[i];

		// Check if match
		if (current_datum.course == val) {
			// Match!
			window.location = $('#search-base').val() + current_datum.url;
		}
	}
}

//
// jQuery gogo
//

$(document).ready(function () {
	
	//
	// Typeahead
	//

	$('#prefetch .typeahead').typeahead({
		hint: true,
  		highlight: true,
	}, {
		name: 'courses',
		displayKey: 'course',
		source: courses.ttAdapter(),	
	}).on('typeahead:selected', function($e, datum){
		check_check_search();
	});

	//
	// Search
	//

	$('#search').on('keyup', function(e) {
		if (e.keyCode == 13) {
			check_check_search();
		}
	});

	//
	// Archive context menu
	//

	var $archive_context_menu = $('#archive-context-menu');
	$('body').on('contextmenu', '.archive-item', function(e) {
		// Pointer
		var $that = $(this);

		// Set info
		$('#archive-context-menu-id', $archive_context_menu).html($that.data('name'));
		$('#archive-context-menu-size', $archive_context_menu).html(' (' + $that.data('size') + ')');
		$('#archive-context-menu-flags', $archive_context_menu).html($that.data('flags'));
		
		// Get link
		var link = $that.parent().attr('href');

		// Show/hide
		if($that.data('type') == 'dir') {
			$('#archive-context-open', $archive_context_menu).show();
			$('#archive-context-open a', $archive_context_menu).show().attr('href', link);

			$('#archive-context-download', $archive_context_menu).hide();
		}
		else {
			$('#archive-context-download', $archive_context_menu).show();
			$('#archive-context-download a', $archive_context_menu).show().attr('href', link);
			
			$('#archive-context-open', $archive_context_menu).hide();
		}

		// Set location
		$archive_context_menu.css({
			display: 'block',
			left: e.pageX - 30,
			top: e.pageY - 40,
		});

		// Disable default
		return false;
	});
	$('#archive-context-close').on('click', function(e) {
		// Prevent # href
		e.preventDefault();

		// Hide context menu
		$archive_context_menu.hide();
	});
	$('#archive-context-menu-info, #archive-context-menu-flags, #archive-context-menu-report').on('click', function() {
		// Hide context menu TODO does not work...
		$archive_context_menu.hide();
	});
});