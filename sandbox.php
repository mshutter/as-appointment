<?php

//Prototype for the prototype for the index page

require_once 'models/AppointmentType.php';
$facStaffAppts = AppointmentType::ListAppointmentTypes(true);

require_once 'models/Department.php';
$dept = Department::GetByDepartmentID(0);
$dept->GetCurriculums();
?>

<!--
	Form Elements Test
-->

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
	<link rel="stylesheet" href="styles/jquery-ui.css">
	<link rel="stylesheet" href="styles/filters.css">
</head>
<body>

<div class="btn btn-primary appt-chk-btn" style="font-size:1.3em;">
	<i class='glyphicon glyphicon-record'></i>
</div>

<div class="container">
	<h1>Pay Us a Visit</h1>
	<hr />

<!-- Input: Date -->
	<label id="btn-date" class='btn btn-primary col-xs-12'>
		<input id='input-date' class='hidden' type="text" name="date" value="" />
		<i class="glyphicon glyphicon-calendar"></i>
		<span data-role="display-value">Approximate Visit Date</span>
	</label>
	<div class="row-space"></div>


<!-- Input: Campus Tour -->	
	<label id="btn-campusTour" class="btn btn-primary col-xs-12">
		<input class="hidden" id='input-campusTour' type="checkbox" name="apptType" value="2" />
		Tour Our Campus
	</label>
	<div class='row-space'></div>


<!-- Input: Faculty/Staff -->
	<label id="btn-facStaffAppt" class="btn btn-primary col-xs-12" data-role='dropdown-toggle' data-dropdown="dropdown-facStaffAppts">	
		Meet With Faculty/Staff
		<i class="glyphicon glyphicon-menu-down"></i>
	</label>
	<div class="row"></div>

	<div id="dropdown-facStaffAppts" data-role='dropdown-container'>
		<?php foreach ( $facStaffAppts as $type ) : ?>		

		<label class="btn btn-default appt-dropdown-item">
			<input id="option-facStaff-<?php echo $type->apptTypeID; ?>" type="checkbox" name="apptType" value="<?php echo $type->apptTypeID; ?>" />
			<?php echo $type->title; ?>
		</label>

		<?php endforeach; ?>
	</div>
	<div class="row"></div>

	<hr />

<!-- Input: Department Tour -->
	<div>
		<!-- Ajax populates this with Department Filter form elements -->
		<div id="dept1">

			<label id="btn-dept1" class="btn btn-primary col-xs-11" data-role='dropdown-toggle' data-dropdown="dropdown-dept1">	
				<input type="hidden" name="departmentID" value="<?php echo $dept->departmentID; ?>" />
				<?php echo $dept->title; ?>
				<i class="glyphicon glyphicon-menu-down"></i>
			</label>
			<label class='btn btn-danger no-side-pad col-xs-1' onclick="removeElement('#dept1');">
				<i class="glyphicon glyphicon-remove"></i>
			</label>
			<div class="row"></div>

			<div id="dropdown-dept1" data-role='dropdown-container'>
				<?php foreach ($dept->Curriculums as $curr) : ?>

				<label class="btn btn-default appt-dropdown-item">
					<input id="option-curr-<?php echo $curr->curriculumID; ?>" type="checkbox" name="curriculumID" value="<?php echo $curr->curriculumID; ?>" />
					<?php echo $curr->title; ?>
				</label>

				<?php endforeach; ?>
			</div>
		</div>
		<div class='row-space'></div>
		
	</div>
	
	<a href="#" style="padding-left:12px;">
		<i class="glyphicon glyphicon-plus"></i>
		Explore a Program
	</a>

</div>

	
<script>
	window.onload = function () {
		initDropdowns();
		initButtons();

		$('#input-date').datepicker({
			dateFormat: "m/d/yy",
			minDate: 0
		});

		//Focus hidden date input on button click (opens datepicker)
		$('#btn-date').click(function () {
			$('#input-date').toggleClass('hidden',false).focus().toggleClass('hidden',true);
		});
		
	}

// ========== General UI Functions ========== //

	//Remove an element from the document
	function removeElement (element, runBefore) {
		if ( jQuery.isFunction( runBefore ) ) {
			runBefore();
		}

		$(element).remove();
		return false;
	}


// ========== Validation UI ========== //

	//Binds UI changes to all buttons on this page
	function initButtons () {

		//Bind UI change to Date selection
		$('#input-date').change(function () {
			toggleValidationUI(
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
			toggleValidationUI(
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
			toggleValidationUI(
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


		//Bind UI change to Department Tour selection
		$('#dept1 input[id^="option-curr"]').change(function () {
			console.log($(this));
			toggleValidationUI(
				//Context for UI change
				$('#btn-dept1'),

				//validation: if any curriculums have been selected
				function () {
					var state = false
					$('#dept1 input[id^="option-curr"]').each(function () {
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


	//Encapsulation used for validation-based and UI-based document changes (for binding, e.g. onclick)
	function toggleValidationUI (context, validate, execute) {
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


// ========== Dropdown UI ========== //
	function initDropdowns () {
		$('*[data-role="dropdown-toggle"').click(function () {
			toggleDropdown( $(this).attr('data-dropdown') );
		});
	}

	function toggleDropdown ( dropdownId ) {
		var dropdown = $('#'+dropdownId);

		if ( dropdown.css('display') == 'none' ) 
			dropdown.slideDown();
		else
			dropdown.slideUp();

		return false;
	}
</script>

<?php require_once 'partials/footer.php'; ?>

</body>
</html>










<?php

/**
 * GENERATE UID FUNCTION
 */

function UniqueID () {
	//String containing all characters that may be used in unique ID
	$chars = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

	//First character must be alphabetical
	$uid = substr( $chars, mt_rand(0,51), 1 );

	//Shuffle numbers into $chars
	$chars = str_shuffle($chars . '0123456789');

	//Add 9 random alphanumeric characters to $uid
	for ($i = 0; $i < 9; $i++) {
		$x = mt_rand(0,61);
		$uid .= substr( $chars, $x, 1 );
	}

	return $uid;
}

?>