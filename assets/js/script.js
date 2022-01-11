jQuery( function( $ ) {
	$('.tw-datepicker').each(function( index ) {
	  	$(this).datepicker({
	  		dateFormat: 'yy-mm-dd'
	  	});
	});

	$('.tw-contact-form').each(function( index ) {
		const form = $(this);

		form.submit(function( event ) {
			event.preventDefault();

			const firstName = form.find('input[name="first_name"]').removeClass('tw-error').val().trim();
			const lastName = form.find('input[name="last_name"]').removeClass('tw-error').val().trim();
			const email = form.find('input[name="email"]').removeClass('tw-error').val().trim();
			const colors = form.find('select[name="colors"]').removeClass('tw-error').val();
			const date = form.find('input[name="date"]').removeClass('tw-error').val().trim();
			const fileData = form.find('input[name="file"]').prop('files')[0];

			const formData = new FormData();

			formData.append('action', 'submit_form');
			formData.append('first_name', firstName);
			formData.append('last_name', lastName);
			formData.append('email', email);
			formData.append('colors', colors);
			formData.append('date', date);
			formData.append('file_data', fileData);

			let errors = '';

			form.find('.tw-error-container').text('').hide();
			form.find('.tw-success-container').text('').hide();

			const emailRegexp = new RegExp(
				/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			);

			if ( firstName === '' ) {
				errors += "First name can't be empty. ";
				form.find('input[name="first_name"]').addClass('tw-error');
			}

			if ( lastName === '' ) {
				errors += "Last name can't be empty. ";
				form.find('input[name="last_name"]').addClass('tw-error');
			}

			if ( email === '' ) {
				errors += "Email can't be empty. ";
				form.find('input[name="email"]').addClass('tw-error');
			} else if ( ! emailRegexp.test(email) ) {
				errors += 'Invalid email ';
				form.find('input[name="email"]').addClass('tw-error');
			}

			if ( errors.length > 0 ) {
				form.find('.tw-error-container').text(errors).show();

				return;
			} else {
				form.find('.tw-btn').text('Loading...').prop('disabled', true);

		        $.ajax({
			        url: vars.ajax_url,
			        cache: false,
			        contentType: false,
			        processData: false,
			        data: formData,                         
			        type: 'post',
			        success: function(response) {
			        	form.find('.tw-btn').text('Submit').prop('disabled', false);

		                if ( response.success ) {
		                	form.find('.tw-success-container').text(response.data).show();

		                	form.find('input[name="first_name"]').val('');
		                	form.find('input[name="last_name"]').val('');
		                	form.find('input[name="email"]').val('');
		                	form.find('select[name="colors"]').val('');
		                	form.find('input[name="date"]').val('');
		                	form.find('input[name="file"]').val('');
		                }

		                console.log(response);
			        },
			        error: function(xhr, status, error) {
			        	form.find('.tw-btn').text('Submit').prop('disabled', false);
	            		form.find('.tw-error-container').text(xhr.responseJSON.data).show();

	            		console.log(xhr);
		                console.log(status);
		                console.log(error);
			        }
			    });
			}
		})
	});
} );