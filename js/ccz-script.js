jQuery(function($) {

    $('.ccz-option').on('click', function() {
        $('.ccz-option').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input').prop('checked', true).trigger('change');
    });

    const deliveryToggle = $('input[name="ccz_delivery_type"]');

    const deliveryFields = $('.ccz-delivery-fields');
    const pickupFields = $('.ccz-pickup-fields');

    const deliveryZip = $('input[name="ccz_delivery_zip"]');
    const pickupZip = $('input[name="ccz_pickup_zip"]');
    const deliveryDate = $('input[name="ccz_delivery_date"]');
    const pickupDate = $('input[name="ccz_pickup_date"]');

    const zipDays = cczData.zipDays;
    const weekdaysMap = {
        'Sunday': 0, 'Monday': 1, 'Tuesday': 2, 'Wednesday': 3,
        'Thursday': 4, 'Friday': 5, 'Saturday': 6
    };
    
    function getEnabledDays(zip) {
        let days = zipDays[zip] || [];
        return days.map(day => weekdaysMap[day]);
    }

    function initDatepicker($field, zip) {
		const allowedDays = getEnabledDays(zip);

		$field.datepicker('destroy'); // Reset in case already applied

		if (allowedDays.length > 0) {
			$field.prop('disabled', false).show().datepicker({
				minDate: 0,
				dateFormat: 'mm/dd/yy',
				beforeShowDay: function(date) {
					return [allowedDays.includes(date.getDay())];
				}
			});

			// Optional: remove any warning message
			$field.next('.ccz-disabled-msg').remove();

		} else {
			$field.val('').prop('disabled', true).show(); // Keep visible but disabled

			// Optional: show message why it's disabled
			if ($field.next('.ccz-disabled-msg').length === 0) {
				$field.after('<p class="ccz-disabled-msg" style="color:red; font-size:13px;">No available delivery days for this ZIP code.</p>');
			}
		}
	}


    const nameField = $('#billing_first_name_field, #billing_last_name_field');
    const phoneField = $('#billing_phone_field');
    const fieldsToHide = $('#billing_country_field,#billing_email_field, #billing_address_1_field, #billing_address_2_field, #billing_city_field, #billing_state_field, #billing_postcode_field');

    function toggleFields() {
        const selected = deliveryToggle.filter(':checked').val();
        if (selected === 'pickup') {
            deliveryFields.hide();
            pickupFields.hide();
            nameField.show();
            phoneField.show();            
            fieldsToHide.hide();
			$('.ccz-pickup-only-fields').show();
			const pickupOnlyDate = $('input[name="ccz_pickup_only_date"]');
			if (!pickupOnlyDate.hasClass('hasDatepicker')) {
				pickupOnlyDate.datepicker({
					minDate: 1,
					dateFormat: 'mm/dd/yy'
				});
			}
			
			const dropoffOnlyDate = $('input[name="ccz_dropoff_only_date"]');
			if (!dropoffOnlyDate.hasClass('hasDatepicker')) {
				dropoffOnlyDate.datepicker({
					minDate: 1,
					dateFormat: 'mm/dd/yy'
				});
			}
			
        } else {
            deliveryFields.show();
            pickupFields.show();
            $('#billing_email_field').show();
            fieldsToHide.show();
			$('.ccz-pickup-only-fields').hide();
        }
    }
    

    deliveryToggle.on('change', toggleFields);
    toggleFields();

    // Bind zip input events to initialize date pickers
    deliveryZip.on('input', function() {
        const zip = $(this).val().trim();
        if (zip) initDatepicker(deliveryDate, zip);
    });

    pickupZip.on('input', function() {
        const zip = $(this).val().trim();
        if (zip) initDatepicker(pickupDate, zip);
		
    });
	
    // Pre-fill if zip already entered (e.g., during reload)
    if (deliveryZip.val().trim()) {
        initDatepicker(deliveryDate, deliveryZip.val().trim());
    }

    if (pickupZip.val().trim()) {
        initDatepicker(pickupDate, pickupZip.val().trim());
    }
    
    function validateZip(zip, type) {
        if (zip.length < 3) return; // Avoid unnecessary requests

        $.post(cczData.ajaxUrl, {
            action: 'ccz_validate_zip',
            zip_code: zip
        }, function(response) {
            let $inputField = $('input[name="ccz_' + type + '_zip"]');
            let errorClass = 'ccz-zip-error-' + type; // Unique class for delivery/pickup
            let existingError = $('.' + errorClass);

            if (response.success) {
                existingError.remove(); // Remove the error message if ZIP is correct
            } else {
                if (existingError.length === 0) { // Prevent duplicate errors
                    let errorMsg = '<p class="' + errorClass + '" style="color:red; font-size:14px; margin-top:5px;">Incorrect ZIP code. Please enter a valid ZIP.</p>';
                    $inputField.after(errorMsg);
                }
            }
        }).fail(function() {
            console.error('ZIP validation request failed.');
        });
    }

    // Bind ZIP validation on input change
    $('input[name="ccz_delivery_zip"]').on('blur', function() {
        validateZip($(this).val().trim(), 'delivery');
    });

    $('input[name="ccz_pickup_zip"]').on('blur', function() {
        validateZip($(this).val().trim(), 'pickup');
    });
    
});
