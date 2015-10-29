<?php session_start();
	/**
	 * TO-DO:
	 * - Error handling for AJAX functions
	 */


/**
 * Controller
 *************************/

// If apptType has not been selected or is negative (default value)..
if ( ( !isset( $_POST['apptType'] ) || intval( $_POST['apptType'] ) < 0 ) && !isset( $_SESSION['apptType'] ) ) {

	// Redirect user back to index
	header('Location: .');
}

// If datePref has not been selected..
if ( !isset( $_POST['datePref'] ) && !isset( $_SESSION['datePref'] ) ) {

	// Redirect user back to index
	header('Location: .');
}


// Update session variables to preserve user progress
else {
//apptType
	if ( isset( $_POST['apptType'] ) )
		$_SESSION['apptType'] = $_POST['apptType'];

//datePref
	if ( isset( $_POST['datePref'] ) )
		$_SESSION['datePref'] = strtotime( $_POST['datePref'] );
	else 
		$_SESSION['datePref'] = time();

//deptID
	if ( isset( $_POST['deptID'] ) ) {
		$_SESSION['deptID'] = $_POST['deptID'];
	}

//currID
	if ( isset( $_POST['currID'] ) ) {
		$_SESSION['currID'] = $_POST['currID'];
	}
}


// Begin HTML
include 'partials/preHeader.php';
$title = "Step Two";
include 'partials/header.php';


require_once 'models/StudentAppointment.php';

// If student has already started making an appointment 
if ( isset( $_SESSION['studentApptID'] ) ) {

	// Then load up that appointment
	$appt = new StudentAppointment( $_SESSION['studentApptID'] );
}

else {
	// ...or else, create a new one
	$appt = new StudentAppointment();
}

// Pass appointment ID to session scope
$_SESSION['studentApptID'] = $appt->getApptID();



/**
 * View
 *************************/ ?>

<div id='appt-container'>
	<div class="appt-header">
		<h2><?php echo date( 'F Y', $_SESSION['datePref'] ) ?></h2>

		<nav id='appt-nav-days'>
			<!-- AJAX will populate this with navigation items for each day -->
		</nav>
	</div>

	<div class='appt-content'>
		
	</div>
</div>


<script>
	window.onload = function () {

		//initial creation of navigation of days
		refreshUI();
	};


	function refreshUI (args) {
		
		//if date has changed..
		if ( args && args.date ) {

			//refresh the navigation to match
			refreshDateNav(args.date);
		}

		//or else call for a date refresh with no parameters 
		else refreshDateNav();

		refreshContent(args);

		return false;
	}


	//get json for date nav and pass it to render function
	function refreshDateNav (date) {
		uri = (date) ? '?d='+date : '';
		$.ajax({
			url: 'api/dateNav.php' + uri,
			method: 'GET'
		})
		.done(renderDateNav);

		return false;
	}

	//renders all 
	function renderDateNav (json) {
		var navHtml = "";
		var days = JSON.parse(json); 

		for (var i = 0; i < days.length; i++) {
			navHtml += "<span>";
			navHtml += "<a href='#' onclick='return refreshUI({date:\""+days[i].fullDate+"\"});'>";
			navHtml += days[i].dayAbbrev+" "+days[i].dayOfMonth+"</a>";
			navHtml += "</span>"; 
		}

		//update navigation of days
		$('#appt-nav-days').html( navHtml );

		return false;
	}


	function refreshContent (args) {
		//if arguments have been passed..
		if (args) {

			//put each uri variable into an array
			uri = [];
			(args.date) ? uri.push('da=' + args.date) : null;
			(args.type) ? uri.push('ty=' + args.type) : null;
			(args.dept) ? uri.push('de=' + args.dept) : null;
			(args.curr) ? uri.push('cu=' + args.curr) : null;

			//build uri chain string
			strUri = (uri[0]) ? '?' : '';
			for (var i = 0; i < uri.length; i++) {
				strUri += uri[i];
				strUri += (uri[i+1]) ? '&' : '';
			}
		}

		//if no arguments have been passed, uri string will be empty
		else strUri = '';

		//make ajax call to api
		$.ajax({
			url: 'api/filterAppts.php'+strUri,
			method: 'GET',
			context: $('.appt-content')
		})
		.done(function (data) {
			$(this).html(data);
		});

		return false;
	}

	function renderContent (json) {
		return false;
	}
</script>

<?php
include 'partials/footer.php'
?>