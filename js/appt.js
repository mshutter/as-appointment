// =========================
// Initialization
// =========================


// ========== Initialize 'filters' UI ========== //
function initFilters() {
		initDatepicker();
		initDialog();
		initFilterButtons();
		initDropdowns();
}


	// Initialize appt filter date selector
	function initDatepicker () {
		$('#input-date').datepicker({
			dateFormat: "m/d/yy",
			minDate: 0
		});

		$('.ui-datepicker-next').removeClass('ui-datepicker-next');
		$('.ui-datepicker-prev').removeClass('ui-datepicker-prev');

		//Focus hidden date input on button click (opens datepicker)
		$('#btn-date').click(function () {
			$('#input-date').toggleClass('hidden',false).focus().toggleClass('hidden',true);
		});
	}


	// Initialize appt filter "add department" dialog
	function initDialog () {

		//1. Move #dept-list to bottom of body (outside container)

			var dialogHTML = $('#dept-list').html(); //Get html of dept list
			$('#dept-list').remove();                //Remove existing element
			//Create HTML elements for underlay and dialog box
			$('body').append('<div class="appt-dialog-underlay"></div>')
			$('body').append('<div id="dept-dialog"></div>');
			//Populate #dept-dialog with dialog and add class for UI
			$('#dept-dialog').html(dialogHTML).addClass('appt-dialog');

		//2. Configure open/close dialog UI changes

			//Open dialog
			$('#add-dept').click(function () {
				$('body').toggleClass('dialogIsOpen', true);
			});

			//Close Dialog (underlay and close button)
			$('.appt-dialog-underlay, .appt-dialog-close').click(function () {
				$('body').toggleClass('dialogIsOpen', false);
			});

		//3. Configure addDeptFilter onclick UI

			$('.select-dept').click(function () {
				var deptID = $(this).attr('data-dept');
				if ( !$("#dept-"+deptID).length ) {
					addDeptFilter(deptID);
					$('body').toggleClass('dialogIsOpen', false);
				}
			});
	}


	// Initialize appt filter button UI
	function initFilterButtons () {

	 //Bind UI change to Date selection
		$('#input-date').change(function (event) {
			validationUIHandler(
				//Context for UI change
				$('#btn-date'),

				//validation: if input is a valid date
				function () {
					//Check if JavaScript is able to interpret the input as a date
					var input = $('#input-date').val();
					var check = new Date( input );

					//If the input matches JavaScript's interpretation of itself
					if ( input === check.toLocaleDateString("en-US") ) {
						return true;
					}
					else return false;
				},

				//UI execution
				function ( context, state ) {
					context
						.toggleClass('btn-primary', !state)
						.toggleClass('btn-success', state);
					if (state) {
						context.children('*[data-role="display-value"]')
							.html( $('#input-date').val() );
					}
				}
			);
		});


	 //Bind UI change to Campus Tour selection
		$('#input-campusTour').change(function () {
			validationUIHandler(
				//Context for UI change
				$('#btn-campusTour'),

				//validation
				$(this).prop('checked'),

				//UI execution
				function ( context, state ) {
					context
						.toggleClass('btn-primary', !state)
						.toggleClass('btn-success', state);
					//console.log(state); //DEBUG
				}
			);
		});


		//Bind UI change to Faculty/Staff Appt selection
		$('input[id^="option-facStaff"]').change(function () {
			validationUIHandler(
				//Context for UI change
				$('#btn-facStaffAppt'),

				//validation: if any any faculty/staff appointments were selected
				function () {
					var state = false
					$('input[id^="option-facStaff"]').each(function () {
						if ( $(this).prop('checked') ) {
							state = true;
							return;
						}
					});
					return state;
				},

				//UI execution
				function ( context, state ) {
					context
						.toggleClass('btn-primary', !state)
						.toggleClass('btn-success', state);
					//console.log(state); //DEBUG
				}
			);
		});
	}


// Initialize dropdowns
function initDropdowns() {
  $('*[data-role="dropdown-toggle"]').click(function () {
    toggleDropdown( $(this).attr('data-dropdown') );
  });
}



// =========================
// Basic UI Changes
// =========================


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
	//   <label id="btn-facStaffAppt" data-role='dropdown-toggle' data-dropdown="dropdown-facStaffAppts">	
	//     ...
	//   </label>
	//   <div id="dropdown-facStaffAppts" data-role='dropdown-container'>
	//     ...
	//   </div>

	// Binding example:
	//   $('#btn-selector').click(function () {
	//     toggleDropdown( $(this).attr('data-dropdown') );
	//   });



// =========================
// Handler for validation UI changes
// =========================

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



// =========================
// Department Filters
// =========================

function addDeptFilter ( deptID ) {
	//If this department has already been added..
	if ( $('#dept-'+deptID).length ) {

		//Error handling code here...
		//escape the function.
		return false;
	}

	//Get dept JSON
	$.ajax({
		url: "api/dept.php?deptID="+deptID,
		method: "GET",
		success: function (data, status) {

			//Pass the JSON to be rendered
			var html = renderDeptFilter(data);

			//Append element to container
			$('#dept-filters').append(html);


			//Attach dropdown toggle to button
			$('#btn-dept-'+deptID).click(function () {
				toggleDropdown( $(this).attr('data-dropdown') );
			});


			//Configure button UI changes
			$('#dept-'+deptID+' input[id^="option-curr"]').change(function () {
				validationUIHandler(
					//Context for UI change
					$('#btn-dept-'+deptID),

					//Validation - true if any curriculums have been selected
					function () {
						var state = false
						$('#dept-'+deptID+' input[id^="option-curr"]').each(function () {
							if ( $(this).prop('checked') ) {
								state = true;
								return;
							}
						});
						return state;
					},

					//UI execution
					function ( context, state ) {
						context
							.toggleClass('btn-primary', !state)
							.toggleClass('btn-success', state);
						//console.log(state); //DEBUG
					}
				);
			});


			//Attach 'remove element' functionality to red x button
			$('#rmv-dept-'+deptID).click(function () {
				removeElement('#dept-'+deptID);
			});


			//Open dropdopwn
			toggleDropdown("dropdown-dept-"+deptID);
		}

	});
}


function renderDeptFilter ( deptJSON ) {
	var dept   = JSON.parse(deptJSON);
	var htmlID = 'dept-'+dept.departmentID;

//Generate html element from JSON
	var html = '<div id="'+htmlID+'">';

		// Button
	    html += '<label id="btn-'+htmlID+'" class="btn btn-primary col-xs-11" data-role="dropdown-toggle" data-dropdown="dropdown-'+htmlID+'">';
	    html += '<input type="hidden" name="apptTypeID[]" value="3" />';
	    html += '<input type="hidden" name="departmentID[]" value="'+dept.departmentID+'" />';
	    html += dept.title;
	    html += '&nbsp;<i class="glyphicon glyphicon-menu-down"></i></label>';

	    html += '<label id="rmv-'+htmlID+'" class="btn btn-danger no-side-pad col-xs-1">';
			html += '<i class="glyphicon glyphicon-remove"></i>';
			html += '</label>';

			html += '<div class="row"></div>';

		//Dropdown
			html += '<div id="dropdown-'+htmlID+'" data-role="dropdown-container">';

			dept.Curriculums.forEach(function (curr, i, arr) {
				html += '<label class="btn btn-default appt-dropdown-item">';
				html += '<input id="option-curr-'+curr.curriculumID+'" type="checkbox" name="curriculumID[]" value="'+curr.curriculumID+'" />';
				html += '&nbsp;'+curr.title;
				html += '</label>';
			});

			html += '</div><div class="row-space"></div>';
			html += '</div>';

	return html;
}