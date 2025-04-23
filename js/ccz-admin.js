jQuery(function($) {
    function loadZoneDays(zoneId) {
        if (!zoneId) {
            $('#ccz_day_checkboxes').hide();
            return;
        }

        $.post(cczAdmin.ajaxUrl, {
            action: 'ccz_get_zone_days',
            zone_id: zoneId,
            nonce: cczAdmin.nonce
        }, function(response) {
            if (response.success) {
                $('.ccz-day').prop('checked', false);
				$('#ccz_save_msg').css('color', '').text('');
                response.data.forEach(day => {
                    $('.ccz-day[value="' + day + '"]').prop('checked', true);
                });
                $('#ccz_day_checkboxes').fadeIn();
            }
        });
    }

    // On dropdown change
    $('#ccz_zone_selector').on('change', function() {
        const zoneId = $(this).val();
        loadZoneDays(zoneId);
    });

    // ✅ PAGE LOAD: Auto-load first zone's days
    const defaultZoneId = $('#ccz_zone_selector').val();

    if (defaultZoneId) {
        loadZoneDays(defaultZoneId);
    }

    // Save button
    $('#ccz_save_days_btn').on('click', function() {
        let zoneId = $('#ccz_zone_selector').val();
        let selectedDays = [];

        $('.ccz-day:checked').each(function() {
            selectedDays.push($(this).val());
        });

        $('#ccz_save_msg').text('Saving...');

        $.post(cczAdmin.ajaxUrl, {
            action: 'ccz_save_zone_days',
            zone_id: zoneId,
            days: selectedDays,
            nonce: cczAdmin.nonce
        }, function(response) {
            if (response.success) {
                $('#ccz_save_msg').css('color', 'green').text('Days saved successfully.');
            } else {
                $('#ccz_save_msg').css('color', 'red').text('Error saving days.');
            }
        });
    });
});
