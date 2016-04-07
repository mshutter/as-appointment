// =========================
// Initialization
// =========================

// ========== Initialize filters.php U
	function initFilters() {
		initDatepicker();
		initDropdowns();

		//add '2' (department tour) to apptTypeID if a curriculum is checked
		$('.option-deptTour').change(function () {
			var checked = false;
			$('.option-deptTour').each(function (i, e) {
				//if any .option-deptTour are checked
				if ( $(this).prop("checked") )
					checked = true;
			});

			//then check the hidden 'apptType = 2' checkbox
			$('#input-departmentTour').prop('checked', checked);
			
			getMonthOfApptTypes();
		});
	}

	// Initialize appt filter date selector
	function initDatepicker () {
		$('#datepicker').datepicker({
			beforeShowDay: $.datepicker.noWeekends,
			inline: true,
	    showOtherMonths: true,
	    dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			dateFormat: "yy-m-d",
			minDate: 0,
			onSelect: function (date) {
				$('#input-date').val(date);
				getMonthOfApptTypes();
			},
			onChangeMonthYear: function (year, month) {
				//update global variable 'dpMonthDate' to match datepicker state
				dpMonthDate= year+'-'+month+'-1';

				//call UI update function with updated date
				getMonthOfApptTypes(dpMonthDate);
			}
		});

		//update calendar ui (dots) each time apptType selection changes
		$('input[name="apptTypeID[]"]').change(function () {
			getMonthOfApptTypes( true );
		});
	}

	// Initialize dropdowns
	function initDropdowns() {
	  $('*[data-role="dropdown-toggle"]').click(function () {
	    toggleDropdown( $(this).attr('data-dropdown') );
	  });
	}
// ==========



// =========================
// Basic UI Changes
// =========================

//facilitates 
function validationUIHandler (context, validate, execute) {
	//context  => jQuery DOM element being targeted by UI change
	//validate => function OR boolean returning true or false based on validation state
	//execute  => function executing UI changes on target element

	//If validate is a function, run it
	if ( jQuery.isFunction( validate ) ) {
		var state = validate();
	}
	else {
		var state = validate;
	}

	execute( context, state );
}

//Remove an element from the document
function removeElement (element, runBefore) {
	if ( $.isFunction( runBefore ) ) {
		runBefore();
	}

	$(element).remove();
	return false;
}


//Configure event to toggle showing/hiding of dropdown menu
function toggleDropdown ( dropdownId ) {
	var dropdown = $('#'+dropdownId);

	if ( dropdown.css('display') == 'none' ) 
		dropdown.slideDown();
	else
		dropdown.slideUp();

	return false;
}
	// Markup example:
	//   <!-- trigger -->
	//   <label id="btn-facStaffAppt" data-role='dropdown-toggle' data-dropdown="dropdown-facStaffAppts">	
	//     ...
	//   </label>
	//
	//   <!-- dropdown -->
	//   <div id="dropdown-facStaffAppts" data-role='dropdown-container'>
	//     ...
	//   </div>

	// Binding example:
	//   $('#btn-selector').click(function () {
	//     toggleDropdown( $(this).attr('data-dropdown') );
	//   });