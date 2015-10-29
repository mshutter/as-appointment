<?php
	/**
	 * TO-DO:
	 * - Error handling for AJAX functions
	 */
	session_start();

// If apptType has not been selected or is negative (default value)
if ( ( !isset( $_POST['apptType'] ) || intval( $_POST['apptType'] ) < 0 ) && !isset( $_SESSION['apptType'] ) ) {

	// Redirect user back to index
	header('Location: .');
}


// Update session variables to preserve user progress
else {
	if ( isset( $_POST['apptType'] ) )
		$_SESSION['apptType'] = $_POST['apptType'];

	if ( isset( $_POST['datePref'] ) )
		$_SESSION['datePref'] = strtotime( $_POST['datePref'] );
	else 
		$_SESSION['datePref'] = time();
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

?>


<!-- Page content-->
<div id='appt-container'>
	<div class="appt-header">
		<h2><?php echo date( 'F Y', $_SESSION['datePref'] ) ?></h2>
		<button onclick='refreshUI({date:"2015-10-06"})'>Click me</button>

		<nav id='appt-nav-days'>
			<!-- AJAX will populate this with navigation items for each day -->
		</nav>

	</div>
</div>


<script>
	function refreshUI (args) {
		
		if ( args.date ) {
			refreshDateNav(args.date);
		}
	}


	//get json for date nav and pass it to render function
	function refreshDateNav (date) {
		$.ajax({
			url: 'api/dateNav.php?d=' + date,
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

		$('#appt-nav-days').html( navHtml );

		return false;
	}

</script>

<?php
include 'partials/footer.php'
?>