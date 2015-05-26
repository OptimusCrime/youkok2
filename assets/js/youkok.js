
//
// Variables
//

var courses;
var site_data;

//
// jQuery gogo
//

$(document).ready(function () {


    //
    // Typeahead
    //
    
    
    



    /*$('body').on('contextmenu', '.archive-item', function(e) {
        ignore_click_outside = true;

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
        if ($that.data('type') == 'dir') {
            $('#archive-context-open', $archive_context_menu).show();
            $('#archive-context-open a', $archive_context_menu).show().attr('href', link);

            $('#archive-context-download,#archive-context-new-flag-delete,#archive-context-new-flag-move', $archive_context_menu).hide();
        }
        else {
            $('#archive-context-download', $archive_context_menu).show();
            $('#archive-context-download a', $archive_context_menu).show().attr('href', link);

            $('#archive-context-download,#archive-context-new-flag-delete,#archive-context-new-flag-move', $archive_context_menu).show();
            $('#archive-context-open', $archive_context_menu).hide();
        }

        // Favorite
        if ($('#archive-online').val() == 1) {
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
        }
        else {
            $('#archive-context-star', $archive_context_menu).hide();
        }
        
        // Set location
        $archive_context_menu.css({
            display: 'block',
            left: e.pageX - $('#archive-list').offset().left + 45,
            top: e.pageY - $('#archive-top').offset().top + 10,
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
    })*/

    //
    // Flags
    //

    function load_flags() {
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/flag/get",
            data: { 
                id: archive_id 
            },
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
        });
    }
    $('#flags-panel').on('click', '.flag-button', function () {
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/flag/vote",
            data: { 
                id: archive_id, 
                flag: $(this).data('flag'), 
                value: $(this).data('value') 
            },
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
    $('#archive-context-report').on('click', function(e) {
        // Prevent # href
        e.preventDefault();

        // Set name
        $('#modal-report .modal-title').html('Rapporter: ' + $archive_right_click.data('name'));

        // Show modal
        $('#modal-report').modal('show');
    });

    //
    // Create directory
    //

    

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
        $('#modal-report-selected').html($('a', this).html());

        // Swap disabled
        $('#modal-report .dropdown li').removeClass('disabled');
        $(this).addClass('disabled');
    });
    $('#modal-report-form').on('submit', function () {
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/report/send",
            data: { 
                id: $archive_right_click.data('id'), 
                text: $('#modal-report-form textarea').val(), 
                category: $('#modal-report-selected').html() 
            },
            success: function(json) {
                if (json.code == 200) {
                    // Refresh
                    $('#modal-report').modal('hide');
                    setTimeout(function () {
                        $('#model-report-text').val('');
                        alert('Din rapportering er sendt. Takk.');
                    }, 200);
                }
                else {
                    // Something went wrong
                    alert('Noe gikk visst galt!');
                }
            }
        });

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
                        url: "processor/register/email",
                        data: { 
                            email: $('#register-form-email').val() 
                        },
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

        if ($('#register-form .has-error').length == 0 && register_email_checked == true && check_email($('#register-form-email').val()) && $('#register-form-password1').val().length > 6 && $('#register-form-ret').is(':checked')) {
            $('#register-form-submit').prop('disabled', false);
        }
        else {
            $('#register-form-submit').prop('disabled', true);
        }
    });
    $('#register-form-ret').on('change', function() {
        $('#register-form-password1').trigger('keyup');
    })

    //
    // Forgotten password stuff
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
            $('#archive-create-link-div').stop().slideUp();
            $('#archive-create-file-div').stop().slideDown();
        }
    });
    $('#archive-create-file-div a').on('click', function(e) {
        if ($(this).attr('href') == '#') {
            e.preventDefault();
            $('#archive-create-file-div').stop().slideUp(400);
        }
    });
    
    var num_of_files = 0;
    var num_of_files_uploaded = 0;
    $('#archive-create-file-form').fileupload({
        url: 'processor/file/create?format=html&parent=' + site_data.archive_id,
        add: function (e, data) {
            // Get file object
            var file_object = data.files[data.files.length - 1];

            // Test for valid filetype
            var found = false;
            var name = file_object.name;
            var mimetype = file_object.type;
            var this_filetype = file_object.name.split('.');
            this_filetype = this_filetype[this_filetype.length - 1].toLowerCase();
    
            // Mimetype was not found, okey then, try fileendings
            for (var i = 0; i < site_data.file_types.length; i++) {
                if (name.endsWith('.' + site_data.file_types[i])) {
                    found = true;
                    break;
                }
            }


            // Check if valid or not
            if (found) {
                // Check if file is a .zip archive
                var inner_warning = '';
                if (this_filetype == 'zip') {
                    inner_warning = '<em>Innholdet i dette .zip-arkivet vil bli evaluert mot de godkjente filtypene, og kan ikke innehold andre .zip-arkiv';
                }
                
                // The container
                var $container = $('<div class="fileupload-file"><strong>' + name + '</strong><p>' + human_file_size(file_object.size, true) + '</p><div class="fileupload-file-remove"><span>Fjern <i class="fa fa-times"></i></span></div>' + inner_warning + '</div>');

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
                var error = [
                    {
                        'text' : '<p>Filtypen .' + this_filetype + ' er ikke godkjent på Youkok2. Dersom du syntes denne filtypen burde godkjennes, kan du ta kontakt. Kontaktinformasjon nederst på siden.</p>',
                        'type' : 'danger',
                    },
                ];
                
                display_message(error);
            }

            // Update number of files going to be uploaded
            num_of_files = $('.fileupload-file').length - 1;
        },
        done: function (e, data) {
            // Update number of finished uploads
            num_of_files_uploaded++;
            
            // Check if all done
            if (num_of_files_uploaded == num_of_files) {
                // Reload page
                window.location.reload();
            }
        },
        progressall: function (e, data) {
            var progress = parseInt(division(data.loaded, data.total) * 100, 10);
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

        // Update submit button
        $('#archive-create-file-form-submit').html('Laster opp....').prop('disabled', true);

        // Avoid submitting the form
        return false;
    });
    $('#fileupload-files').on('click', '.fileupload-file-remove', function () {
        $(this).parent().remove();
    });
    if ($('#archive_accepted_filetypes').length > 0) {
        accepted_filetypes = jQuery.parseJSON($('#archive_accepted_filetypes').html());
        accepted_fileendings = jQuery.parseJSON($('#archive_accepted_fileendings').html());
    }


    //
    // New flags
    //

    $('#archive-context-new-flag-name').on('click', function(e) {
        e.preventDefault();

        // Title
        $('#modal-new-flag .modal-title').html('Endre navn på: ' + $archive_right_click.data('name'));

        // Set filetype
        if ($archive_right_click.data('type') == 'dir') {
            $('#modal-new-flag-name .input-group .input-group-addon').html(' ');
        }
        else {
            var filetype_index = $archive_right_click.data('name').lastIndexOf('.');
            $('#modal-new-flag-name .input-group .input-group-addon').html($archive_right_click.data('name').substr(filetype_index));
        }
        
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
                data: { 
                    id: $archive_right_click.data('id'),
                    name: $('#modal-new-flag-name-name').val(),
                    filetype: $('#modal-new-flag-name .input-group .input-group-addon').html(),
                    comment:  $('#modal-new-flag-name-comment').val()
                },
                success: function(json) {
                    if (json.code == 200) {
                        // Refresh
                        window.location.reload();
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
            data: { 
                id: $archive_right_click.data('id'),
                comment:  $('#modal-new-flag-delete-comment').val()
            },
            success: function(json) {
                if (json.code == 200) {
                    // Refresh
                    window.location.reload();
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
        e.preventDefault();
        e.stopPropagation();
        
        $('#login-dropdown').dropdown('toggle');
        
        setTimeout(function() {
            $('#login-email').focus();
        }, 100);
    });

    //
    // Profile
    //

    var profile_email_value = "";
    $('#profile-edit-info-form input').on('keyup', function () {
        var $that = $(this);
        var $that_parent = $that.parent();
        var element_id = this.id;

        if (this.id == 'register-form-email') {
            if (check_email($that.val())) {
                if ($that_parent.hasClass('has-error')) {
                    $that_parent.removeClass('has-error');
                }
                $('#register-form-email-error1').css('color', '');

                if (profile_email_value != $('#register-form-email').val()) {
                    profile_email_value = $('#register-form-email').val();

                    $.ajax({
                        cache: false,
                        type: "post",
                        url: "processor/register/email",
                        data: { 
                            email: $('#register-form-email').val(), 
                            ignore: true 
                        },
                        success: function(json) {
                            if (json.code == 200) {
                                $('#register-form-email-error2').css('color', '');
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
    });
    
    //
    // Info modal
    //
    
    var popularity_chart = null;
    var popularity_chart_options = null;
    $('#archive-context-info').on('click', function(e) {
        // Prevent # href
        e.preventDefault();
        
        // Set name
        $('#modal-info .modal-title').html('Detaljer for: ' + $archive_right_click.data('name'));

        // Show modal
        $('#modal-info').modal('show');
        
        // Load the data
        $.ajax({
            cache: false,
            type: "post",
            url: "processor/element/info",
            data: { 
                id: archive_id 
            },
            success: function(json) {
                if (json.code == 200) {
                    // Good to go
                    $('#info-panel').html(json.html);
                    
                    // Load moment
                    $('#info-panel .moment-timestamp').html(moment($('#info-panel .moment-timestamp').data('ts')).fromNow());
                    
                    // Clean the series array
                    popularity_chart_options.series[0].data = [];
                    
                    // Kill the chart if it is drawn
                    if (popularity_chart !== null) {
                        popularity_chart.destroy();
                    }
                    
                    // Clean data
                    var data_dirty = jQuery.parseJSON($('#modal-info-graph-data').html());
                    var data_clean = [];
                    
                    // Loop all points and eval the time to get the real timestamps
                    for (var i = 0; i < data_dirty.length; i++) {
                        data_clean.push([eval(data_dirty[i][0]), parseInt(data_dirty[i][1], 10)]);
                    }
                    
                    // Apply the data to the series
                    popularity_chart_options.series[0].data = data_clean;
                    
                    // Create new chart
                    popularity_chart = new Highcharts.Chart(popularity_chart_options);
                }
                else {
                    // Something went wrong
                    $('#modal-info').modal('hide');
                    alert('Noe gikk galt under lastingen av detaljer.');
                }
            }
        });
    });
    
    //
    // Highcharts object
    //
    
    popularity_chart_options =  {
        lang: {
            loading: 'Laster...',
            months: ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'],
            weekdays: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
        },
        chart: {
            renderTo: 'modal-info-graph',
            height: 150,
            width: 558,
        },
        title: {
            text: '',
            style: {
                display: 'none'
            }
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                month: '%e. %b',
                year: '%b'
            },
        },
        yAxis: {
            min: 0,
            title: {
                enabled: false,
            },
        },
        tooltip: {
            headerFormat: '<b>Antall totale nedlastninger</b><br>',
            pointFormat: '{point.x:%e. %b %Y}: {point.y:.0f}'
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            name: 'Populæritet',
            data: [],
        }]
    };

});