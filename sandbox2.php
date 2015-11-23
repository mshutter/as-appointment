<!doctype html>
<head>
	<?php
	require_once 'models/Department.php';
	$dept = Department::GetByDepartmentID(0);
	$dept->GetCurriculums();
	?>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
	<link rel="stylesheet" href="styles/jquery-ui.css">
	<link rel="stylesheet" href="styles/filters.css">
</head>

<body>

<!-- Input: Department Tour -->
	<div class="container">

		<button onclick="addDeptFilter(0)">Bruh</button>

		<div id="dept-filters">
			
		</div>
	</div>

<script>
//Bind UI change to Department Tour selection

/*====================
	Code Necessary for DeptJSON
  ==================== */


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
					toggleValidationUI(
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
		    html += '<input type="hidden" name="departmentID" value="'+dept.departmentID+'" />';
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
					html += '<input id="option-curr-'+curr.curriculumID+'" type="checkbox" name="curriculumID" value="'+curr.curriculumID+'" />';
					html += '&nbsp;'+curr.title;
					html += '</label>';
				});

				html += '</div><div class="row-space"></div>';
				html += '</div>';

		return html;
	}






	function removeElement (element, runBefore) {
		if ( jQuery.isFunction( runBefore ) ) {
			runBefore();
		}

		$(element).remove();
		return false;
	}

	

	window.onload = function () {

		initDropdowns();

		
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