//
// Moment.js
//

moment.lang('en', {
    relativeTime : {
        future: "Om %s",
        past:   "%s siden",
        s:  "Noen få sekunder",
        m:  "Ett minutt",
        mm: "%d minutter",
        h:  "En time",
        hh: "%d timer",
        d:  "en dag",
        dd: "%d dager",
        M:  "En måned",
        MM: "%d måneder",
        y:  "Et år",
        yy: "%d år"
    }
});

//
// Bloodhound
//

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

	var archive_id = 0;
	var $archive_right_click = null;
	var $archive_context_menu = $('#archive-context-menu');
	$('body').on('contextmenu', '.archive-item', function(e) {
		// Pointer
		var $that = $(this);
		$archive_right_click = $that;

		// Set id
		archive_id = $that.data('id');

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

		// Favorite
		if ($that.data('favorite') == null) {
			$('#archive-context-star', $archive_context_menu).hide();
		}
		else {
			$('#archive-context-star', $archive_context_menu).show();

			if ($that.data('favorite') == 1) {
				$('#archive-context-star-inside', $archive_context_menu).html('Fjern favoritt');
			}
			else {
				$('#archive-context-star-inside', $archive_context_menu).html('Legg til favoritt');
			}
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
	$('#archive-context-flags,#archive-context-info,#archive-context-report').on('click', function() {
		// Hide context menu
		$archive_context_menu.hide();
	});

	//
	// Flags
	//

	function load_flags() {
		$.ajax({
			cache: false,
			type: "post",
			url: "processor/flag/get",
			data: { id: archive_id },
			success: function(json) {
				if (json.code == 200) {
					// Good to go
					$('#flags-panel').html(json.html);
				}
				else {
					// Something went wrong
					$('#modal-flags').modal('hide');
					alert('Noe gikk galt under lastingen av flagg.');
				}
			}
		})
	}

	$('#flags-panel').on('click', '.flag-button', function () {
		$.ajax({
			cache: false,
			type: "post",
			url: "processor/flag/vote",
			data: { id: archive_id, flag: $(this).data('flag'), value: $(this).data('value') },
			success: function(json) {
				if (json.code == 200) {
					// Good to go
					load_flags();
				}
				else {
					// Something went wrong
					alert('Noe gikk galt under stemmingen. Prøv igjen!');
				}
			}
		})
	});

	//
	// Modals
	//

	$('#archive-context-flags').on('click', function(e) {
		// Prevent # href
		e.preventDefault();

		// Show modal
		$('#modal-flags').modal('show');

		// Ajax request
		load_flags();
	});

	$('#archive-context-info').on('click', function(e) {
		// Prevent # href
		e.preventDefault();

		// Show modal
		$('#modal-info').modal('show');
	});

	$('#archive-context-report').on('click', function(e) {
		// Prevent # href
		e.preventDefault();

		// Show modal
		$('#modal-report').modal('show');
	});

	//
	// Favorite
	//

	$('#archive-heading-star').on('click', function () {
		// Store
		var $that = $(this);

		// Check which way to favorite
		var favorite_type = 'add';
		if ($that.hasClass('archive-heading-star-1')) {
			favorite_type = 'remove';
		}

		// Gogogo request
		$.ajax({
			cache: false,
			type: "post",
			url: "processor/favorite/" + favorite_type,
			data: { id: $that.data('archive-id') },
			success: function(json) {
				if (json.code == 200) {
					// Everything went better than expected :)
					if (json.status) {
						$that.removeClass('archive-heading-star-0').addClass('archive-heading-star-1');
					}
					else {
						$that.removeClass('archive-heading-star-1').addClass('archive-heading-star-0');
					}
				}
				else {
					// Something went wrong
					alert('Noe gikk visst galt her. Ups!');
				}
			}
		});
	});

	$('#archive-context-star-inside').on('click', function(e) {
		// Derp
		e.preventDefault();

		if ($archive_right_click != null) {
			var favorite_status = $archive_right_click.data('favorite');
			if (favorite_status == 0 || favorite_status == 1) {
				var favorite_type = 'add';
				if (favorite_status == 1) {
					favorite_type = 'remove';
				}

				$.ajax({
					cache: false,
					type: "post",
					url: "processor/favorite/" + favorite_type,
					data: { id: $archive_right_click.data('id') },
					success: function(json) {
						if (json.code == 200) {
							// Everything went better than expected :)
							if (json.status) {
								$archive_right_click.data('favorite', 1);
							}
							else {
								$archive_right_click.data('favorite', 0);
							}

							// Hide menu
							$archive_context_menu.hide();
						}
						else {
							// Something went wrong
							alert('Noe gikk visst galt her. Ups!');
						}
					}
				})
			}
		}
	});

	//
	// Moment.js
	//

	$('.moment-timestamp').each(function () {
		$that = $(this);
        $that.html(moment($(this).data('ts')).fromNow());
	});

	//
	// Home
	//

	$('#home-most-popular-dropdown li').on('click', function (e) {
		// Default
		e.preventDefault();

		// Swap content
		$('#home-most-popular-selected').html($(this).html());

		// Swap disabled
		$('#home-most-popular-dropdown li').removeClass('disabled');
		$(this).addClass('disabled');

		// Ajax!
		$.ajax({
			cache: false,
			type: "post",
			url: "processor/popular/update",
			data: { delta: $('a', $(this)).data('delta') },
			success: function(json) {
				if (json.code == 200) {
					$('#home-most-popular').slideUp(400, function () {
						$(this).html(json.html).slideDown(400);

					});
				}
				else {
					// Something went wrong
					alert('Noe gikk visst galt her. Ups!');
				}
			}
		})
	});
});