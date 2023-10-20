( function() {
	function reportFilterEvents() {
		let form, submitButton, dateRange;
		if ( ! document.querySelector( '#form_reports_page' ) ) {
			return;
		}
		form = document.querySelector( 'form' );
		dateRange = form.querySelector( '#frm_stats_date_range' );
		dateRange.addEventListener( 'change', handleDateRangeChange );

		function handleDateRangeChange() {
			maybeToggleDatePickersState( this.value );
			if ( this.value !== 'custom' ) {
				form.submit();
			}
		}

		function maybeToggleDatePickersState( value ) {
			const startDate = form.querySelector( '#frm_stats_start_date' );
			const endDate   = form.querySelector( '#frm_stats_end_date' );
			const submitButton = form.querySelector( 'button' );
			const dateFields = form.getElementsByClassName( 'frm_stats_date_wrapper' );

			for ( let i = 0; i < dateFields.length; i++ ) {
				if ( value === 'all_time' ) {
					dateFields[i].classList.add('frm_invisible');
				} else {
					dateFields[i].classList.remove('frm_invisible');
				}
			}

			if ( value !== 'custom' ) {
				startDate.disabled = true;
				endDate.disabled = true;
				submitButton.style.display = 'none';
			} else {
				startDate.disabled = false;
				endDate.disabled = false;
				submitButton.style.display = 'block';
			}
		}

		maybeToggleDatePickersState( dateRange.value );
	}

    reportFilterEvents();
}() );
