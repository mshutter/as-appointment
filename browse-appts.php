<?php session_start();
	/**
	 * TO-DO:
	 * - Error handling for AJAX functions
	 */


//Redirect user back to index if date and apptType has not been selected
if ( !isset( $_POST['apptType'] ) && !isset( $_SESSION['apptType'] ) ) {
	header('Location: .');
}
if ( !isset( $_POST['date'] ) && !isset( $_SESSION['date'] ) ) {
	header('Location: .');
}

//Assign form values to $_SESSION
//apptType
if ( isset( $_POST['apptType'] ) )
	$_SESSION['apptType'] = $_POST['apptType'];

//date
if ( isset( $_POST['date'] ) )
	$_SESSION['date'] = strtotime( $_POST['date'] );
if ( !isset( $_SESSION['date'] ) )
	$_SESSION['date'] = time();

//departmentID
if ( isset( $_POST['departmentID'] ) ) 
	$_SESSION['departmentID'] = $_POST['departmentID'];

//curriculumID
if ( isset( $_POST['curriculumID'] ) )
	$_SESSION['curriculumID'] = $_POST['curriculumID'];



// Begin HTML
include 'partials/preHeader.php';
$title = "Step Two";
include 'partials/header.php';

?>

<div id='appt-container'>
	<div class="appt-header">
		<h2><?php echo date( 'F Y', $_SESSION['date'] ) ?></h2>

		<nav id='appt-nav-days'>
			<!-- AJAX will populate this with navigation items for each day -->
		</nav>
	</div>

	<div class='appt-content'>
		<!-- AJAX will populate this with appointments that match filters -->
	
		<?php
			/* DEBUG * /
			echo '<strong>Request</strong>';
			echo '<pre>';
			print_r($_REQUEST);
			echo '</pre>';

			echo '<strong>Request</strong>';
			echo '<pre>';
			print_r($_SESSION);
			echo '</pre>';
			// */
		?>
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

	//renders each nav item onto page
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
			method: 'GET'
		})
		.done(renderContent);

		return false;
	}

	function renderContent (json) {
		if ( json == 'null' ) {
			$('.appt-content').html( "There are no visits scheduled on this day that match your search criteria." );
			return false;
		}

		var contentHTML = '';
		var data = JSON.parse(json);

		//for each record in @data
		for (var i = 0; i < data.length; i++) {
			contentHTML += '<div class="appt-sched-item" id="'+data[i].SchedApptID+'">';
			contentHTML += data[i].SchedApptID;
			contentHTML += '</div>';
		}

		//place generated HTML into .appt-content container
		$('.appt-content').html( contentHTML );

		//DEBUG
		$('.appt-content').append( 'Incoming JSON:<br />'+json );

		return false;
	}
</script>

<?php
include 'partials/footer.php'
?>