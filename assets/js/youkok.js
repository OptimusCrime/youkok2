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
});