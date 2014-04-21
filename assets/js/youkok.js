//
// Method for validating email
//

function check_email(str) {
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	if (filter.test(str))
		return true;
	else
		return false;
};

//
// Function for prettifying filesize (http://stackoverflow.com/a/14919494/921563)
//

function human_file_size(bytes, si) {
	var thresh = si ? 1000 : 1024;

	if(bytes < thresh) {
		return bytes + ' B';
	}

	var units = si ? ['kB','MB','GB','TB','PB','EB','ZB','YB'] : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
	var u = -1;
	
	do {
		bytes /= thresh;
		++u;
	} while(bytes >= thresh);
	
	return bytes.toFixed(1)+' '+units[u];
};

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
	
	if ($('#archive-pizza').val() != 'pizza') {
		$('#archive-context-newflag-outer').hide();
	}

	$('body').on('contextmenu', '.archive-item', function(e) {
		// Pointer
		var $that = $(this);
		$archive_right_click = $that;

		// Set id
		archive_id = $that.data('id');

		// Set info
		$('#archive-context-menu-id', $archive_context_menu).html($that.data('name'));
		$('#archive-context-menu-size', $archive_context_menu).html(' (' + human_file_size($that.data('size'), true) + ')');
		$('#archive-context-menu-flags', $archive_context_menu).html($that.data('flags'));
		
		// Get link
		var link = $that.parent().attr('href');

		// Show/hide
		if($that.data('type') == 'dir') {
			$('#archive-context-open', $archive_context_menu).show();
			$('#archive-context-open a', $archive_context_menu).show().attr('href', link);

			$('#archive-context-download,#archive-context-new-flag-delete,#archive-context-new-flag-move', $archive_context_menu).hide();

			$('.archive-context-type').html('mappen');
		}
		else {
			$('#archive-context-download', $archive_context_menu).show();
			$('#archive-context-download a', $archive_context_menu).show().attr('href', link);
			
			$('#archive-context-download,#archive-context-new-flag-delete,#archive-context-new-flag-move', $archive_context_menu).show();

			$('.archive-context-type').html('fil');
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
	$('#archive-context-newflag').on('click', function(e) {
		e.preventDefault();
	})

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
		});
	});

	//
	// Modals
	//

	$('#archive-context-flags').on('click', function(e) {
		// Prevent # href
		e.preventDefault();

		// Set name
		$('#modal-flags .modal-title').html('Flagg for: ' + $archive_right_click.data('name'));

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

		// Set name
		$('#modal-report .modal-title').html('Rapporter: ' + $archive_right_click.data('name'));

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
		$('#home-most-popular-selected').html($('a', this).html());

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
		});
	});

	//
	// Login
	//

	$('#login-dropdown label, #login-dropdown input').on('click', function(e) {
		e.stopPropagation();
	});

	//
    // Create directory
    //

    $('#archive-create-folder').on('click', function () {
        if ($('#archive-create-folder-div').is(':visible')) {
            $('#archive-create-folder-div').stop().slideUp();
        }
        else {
            $('#archive-create-folder-div').stop().slideDown();
            $('#archive-create-file-div').stop().slideUp();
        }
    });
    $('#archive-create-folder-div a').on('click', function(e) {
    	e.preventDefault();
    	$('#archive-create-folder-div').stop().slideUp(400, function () {
    		$('#archive-create-folder-name').val('');
    	});
    });
    var submitting_archive_create_folder_form = false;
    $('#archive-create-folder-form').on('submit', function () {
    	if ($('#archive-create-folder-name').val().length == 0) {
    		alert('Error: Du har ikke gitt mappen noen navn!');
    	}
    	else {
    		if (!submitting_archive_create_folder_form) {
    			submitting_archive_create_folder_form = true;

    			$.ajax({
					cache: false,
					type: "post",
					url: "processor/create/folder",
					data: { id: $('#archive-id').val(), name: $('#archive-create-folder-name').val() },
					success: function(json) {
						if (json.code == 200) {
							//
						}
						else {
							// Something went wrong
							//
						}
					}
				})
    		}
    	}

    	return false;
    });

    //
    // Report
    //

    $('#modal-report-form > a').on('click', function (e) {
    	e.preventDefault();

    	$('#modal-report').modal('hide');
    });

    $('#modal-report .dropdown li').on('click', function(e) {
		// Default
		e.preventDefault();

		// Swap content
		$('#modal-report-selected').html($(this).html());

		// Swap disabled
		$('#modal-report .dropdown li').removeClass('disabled');
		$(this).addClass('disabled');
    });

    $('#modal-report-form').on('submit', function () {
    	$.ajax({
			cache: false,
			type: "post",
			url: "processor/report/send",
			data: { id: $('#archive-id').val(), text: $('#modal-report-form textarea').val(), category: $('#modal-report-selected').html() },
			success: function(json) {
				if (json.code == 200) {
					// Yey
					$('#modal-report').modal('hide');
					$('#modal-report-form textarea').val('');
				}
				else {
					// Something went wrong
					alert('Noe gikk visst galt!');
				}
			}
		})

    	return false;
    });

    //
    // Register
    //

    var register_email_checked = false;
    var register_email_value = "";
    $('#register-form input').on('keyup', function () {
    	var $that = $(this);
    	var $that_parent = $that.parent();
    	var element_id = this.id;

    	if (this.id == 'register-form-email') {
    		if (check_email($that.val())) {
    			if ($that_parent.hasClass('has-error')) {
    				$that_parent.removeClass('has-error');
    			}
    			$('#register-form-email-error1').css('color', '');

    			if (register_email_value != $('#register-form-email').val()) {
    				register_email_value = $('#register-form-email').val();
    				register_email_checked = false;

	    			$.ajax({
						cache: false,
						type: "post",
						url: "processor/register/check",
						data: { email: $('#register-form-email').val() },
						success: function(json) {
							if (json.code == 200) {
								register_email_checked = true;
								$('#register-form-email-error2').css('color', '');
								$('#register-form-email').trigger('keyup');
							}
							else {
								$('#register-form-email-error2').css('color', 'red');
							}
						}
					});
    			}
    		}
    		else {
    			if (!$that_parent.hasClass('has-error')) {
    				$that_parent.addClass('has-error');
    			}
    			$('#register-form-email-error1').css('color', 'red');
    		}
    	}
    	else if (this.id == 'register-form-password1') {
    		if ($that.val().length < 7) {
    			if (!$that_parent.hasClass('has-error')) {
    				$that_parent.addClass('has-error');
    			}
    			$('#register-form-password-error1').css('color', 'red');
    			$('#register-form-password2').prop('disabled', true).val('').parent().removeClass('has-error');
    			$('#register-form-password-error2').css('color', '');
    		}
    		else {
    			if ($that_parent.hasClass('has-error')) {
    				$that_parent.removeClass('has-error');
    			}
    			$('#register-form-password-error1').css('color', '');
    			$('#register-form-password2').prop('disabled', false);
    		}

    		if ($('#register-form-password2').val().length != 0) {
    			if ($('#register-form-password1').val() != $('#register-form-password2').val()) {
					if (!$('#register-form-password2').parent().hasClass('has-error')) {
	    				$('#register-form-password2').parent().addClass('has-error');
	    			}
	    			$('#register-form-password-error2').css('color', 'red');
				}
				else {
					if ($('#register-form-password2').parent().hasClass('has-error')) {
	    				$('#register-form-password2').parent().removeClass('has-error');
	    			}
					$('#register-form-password-error2').css('color', '');
				}
    		}
    	}
    	else {
			if ($('#register-form-password1').val() != $('#register-form-password2').val()) {
				if (!$that_parent.hasClass('has-error')) {
    				$that_parent.addClass('has-error');
    			}
    			$('#register-form-password-error2').css('color', 'red');
			}
			else {
				if ($that_parent.hasClass('has-error')) {
    				$that_parent.removeClass('has-error');
    			}
				$('#register-form-password-error2').css('color', '');
			}
    	}
    	
    	if ($('#register-form .has-error').length == 0 && register_email_checked == true && check_email($('#register-form-email').val()) && $('#register-form-password1').val().length > 6) {
    		$('#register-form-submit').prop('disabled', false);
    	}
    	else {
    		$('#register-form-submit').prop('disabled', true);
    	}
    });

	//
	// 
	//

	$('#forgotten-password-new-form input').on('keyup', function () {
    	var $that = $(this);
    	var $that_parent = $that.parent();
    	var element_id = this.id;

    	if (this.id == 'forgotten-password-new-form-password1') {
    		if ($that.val().length < 7) {
    			if (!$that_parent.hasClass('has-error')) {
    				$that_parent.addClass('has-error');
    			}
    			$('#forgotten-password-new-form-password-error1').css('color', 'red');
    			$('#forgotten-password-new-form-password2').prop('disabled', true).val('').parent().removeClass('has-error');
    			$('#forgotten-password-new-form-password-error2').css('color', '');
    		}
    		else {
    			if ($that_parent.hasClass('has-error')) {
    				$that_parent.removeClass('has-error');
    			}
    			$('#forgotten-password-new-form-password-error1').css('color', '');
    			$('#forgotten-password-new-form-password2').prop('disabled', false);
    		}

    		if ($('#forgotten-password-new-form-password2').val().length != 0) {
    			if ($('#forgotten-password-new-form-password1').val() != $('#forgotten-password-new-form-password2').val()) {
					if (!$('#forgotten-password-new-form-password2').parent().hasClass('has-error')) {
	    				$('#forgotten-password-new-form-password2').parent().addClass('has-error');
	    			}
	    			$('#forgotten-password-new-form-password-error2').css('color', 'red');
				}
				else {
					if ($('#forgotten-password-new-form-password2').parent().hasClass('has-error')) {
	    				$('#forgotten-password-new-form-password2').parent().removeClass('has-error');
	    			}
					$('#forgotten-password-new-form-password-error2').css('color', '');
				}
    		}
    	}
    	else {
			if ($('#forgotten-password-new-form-password1').val() != $('#forgotten-password-new-form-password2').val()) {
				if (!$that_parent.hasClass('has-error')) {
    				$that_parent.addClass('has-error');
    			}
    			$('#forgotten-password-new-form-password-error2').css('color', 'red');
			}
			else {
				if ($that_parent.hasClass('has-error')) {
    				$that_parent.removeClass('has-error');
    			}
				$('#forgotten-password-new-form-password-error2').css('color', '');
			}
    	}
    	
    	if ($('#forgotten-password-new-form .has-error').length == 0 && $('#forgotten-password-new-form-password1').val().length > 6) {
    		$('#forgotten-password-new-form-submit').prop('disabled', false);
    	}
    	else {
    		$('#forgotten-password-new-form-submit').prop('disabled', true);
    	}
    });

    //
    // Create file
    //

    $('#archive-create-file').on('click', function () {
        if ($('#archive-create-file-div').is(':visible')) {
            $('#archive-create-file-div').stop().slideUp();
        }
        else {
        	$('#archive-create-folder-div').stop().slideUp();
            $('#archive-create-file-div').stop().slideDown();
        }
    });
    $('#archive-create-file-div a').on('click', function(e) {
    	if ($(this).attr('href') == '#') {
    		e.preventDefault();
    		$('#archive-create-file-div').stop().slideUp(400);
    	}
    });
    var accepted_filetypes = [];
    $('#archive-create-file-form').fileupload({
        url: 'processor/create/file',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        add: function (e, data) {
        	// Get file object
        	var file_object = data.files[data.files.length - 1];

        	// Test for valid filetype
        	var found = false;
        	var mimetype = file_object.type;
        	for (var i = 0; i < accepted_filetypes.length; i++) {
        		if (accepted_filetypes[i] == mimetype) {
        			found = true;
        			break;
        		}
        	}

        	// Check if valid or not
        	if (found) {
        		// The container
	        	var $container = $(' \
	        		<div class="fileupload-file"> \
						<strong>' + file_object.name + '</strong> \
						<p>' + human_file_size(file_object.size, true) + '</p> \
						<div class="fileupload-file-remove"> \
							<span>Fjern <i class="fa fa-times"></i></span> \
						</div> \
					</div>');

	        	// The button
	            var $button = $('<button style="display: none;">x</button>');

	            // Add button to container
	            $container.append($button);

	            // Set button as context for the file
	            data.context = $button;

	            // Add listener for the button
	            $button.click(function () {
	                data.submit();
	            });

	            // Add container to html
	            $('#fileupload-files-inner').append($container);
        	}
        	else {
        		// Display error, wrong filetype
        		alert('Ikke godkjent filtype.');
        	}
        },
        done: function (e, data) {
        	// Hide all the containers
            $('#fileupload-files-inner div').each(function () {
            	$(this).delay(500).slideUp(400, function () {
            		$(this).remove();
            	});
            });

            // Display success-message
            $('.fileupload-add p').fadeIn(400).delay(2000).fadeOut(400, function () {
            	$('#progress .bar').css({'width': '0px'});
            });
        },
        progressall: function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        $('#progress .bar').css(
	            'width',
	            progress + '%'
	        );
	    },
    });
    $('#archive-create-file-form').on('submit', function () {
    	// Loop all the buttons
    	$('#fileupload-files-inner button').each(function () {
    		// Trigger click
    		$(this).trigger('click');
    	});

    	// Avoid submitting the form
    	return false;
    });
    $('#fileupload-files').on('click', '.fileupload-file-remove', function () {
    	$(this).parent().remove();
    });
    if ($('#archive_accepted_filetypes').length > 0) {
    	accepted_filetypes = jQuery.parseJSON($('#archive_accepted_filetypes').html());
    }

    //
    // History
    //

    if ($('#archive-history').length > 0) {
    	
    	$.ajax({
			cache: false,
			type: "post",
			url: "processor/history/get",
			data: { id: $('#archive-id').val() },
			success: function(json) {
				$('#archive-history-inside').html(json.html);
			}
		});
    }

    //
    // Alerts
    //

    $('.alert-close').on('click', function () {
    	// Remove
    	$(this).parent().fadeOut(400, function () {
    		$(this).remove();
    	});
    });
    if ($('.alert').length > 0) {
    	setTimeout(function () {
    		// Close all messages
    		$('.alert-close').trigger('click');
    	}, 10000);
    };

    //
    // New flags
    //

    $('#archive-context-new-flag-name').on('click', function(e) {
    	e.preventDefault();

    	// Title
    	$('#modal-new-flag .modal-title').html('Endre navn på: ' + $archive_right_click.data('name'));

    	// Set filetype
    	var filetype_index = $archive_right_click.data('name').lastIndexOf('.');
    	$('#modal-new-flag-name .input-group .input-group-addon').html($archive_right_click.data('name').substr(filetype_index));

    	// Show modal
		$('#modal-new-flag').modal('show');

		// Hide all inners
		$('.modal-new-flag-container').hide();
		$('#modal-new-flag-name').show();
    });
    $('#modal-new-flag-name-form').on('submit', function () {
    	// Check if valid
    	if ($('#modal-new-flag-name-name').val().length == 0) {
    		alert('Du glemte å skrive inn et navn...');
    	}
    	else {
    		$.ajax({
				cache: false,
				type: "post",
				url: "processor/flag/name",
				data: { id: $archive_right_click.data('id'),
					name: $('#modal-new-flag-name-name').val(),
					filetype: $('#modal-new-flag-name .input-group .input-group-addon').html(),
					comment:  $('#modal-new-flag-name-comment').val()},
				success: function(json) {
					if (json.code == 200) {
						$('#modal-new-flag').modal('hide');
						alert('Flagg er sendt');
					}
					else {
						alert('Noe gikk visst galt her!');
					}
				}
			});
    	}

    	return false;
    });

    $('#archive-context-new-flag-delete').on('click', function(e) {
    	e.preventDefault();

    	// Title
    	$('#modal-new-flag .modal-title').html('Sletting av: ' + $archive_right_click.data('name'));

    	// Show modal
		$('#modal-new-flag').modal('show');

		// Hide all inners
		$('.modal-new-flag-container').hide();
		$('#modal-new-flag-delete').show();
    });
    $('#modal-new-flag-delete-form').on('submit', function () {
    	$.ajax({
			cache: false,
			type: "post",
			url: "processor/flag/delete",
			data: { id: $archive_right_click.data('id'),
				comment:  $('#modal-new-flag-delete-comment').val()},
			success: function(json) {
				if (json.code == 200) {
					$('#modal-new-flag').modal('hide');
					alert('Flagg er sendt');
				}
				else {
					alert('Noe gikk visst galt her!');
				}
			}
		});

		return false;
    });

    //
    // Stuff
    //

    $('#dropdown-menu-opener').on('click', function(e) {
    	setTimeout(function() {
    		$('#login-email').focus();
    	}, 100);
    });
    $('.login-opener').on('click', function(e) {
    	e.stopPropagation();
    	$('#login-dropdown').dropdown('toggle');
    	setTimeout(function() {
    		$('#login-email').focus();
    	}, 100);
    });
});