<?php session_start();

//Redirect user back to index if date and apptTypeID has not been selected
if ( !isset( $_POST['apptTypeID'] ) && !isset( $_SESSION['apptTypeID'] ) ) {
	header('Location: .');
}
if ( !isset( $_POST['date'] ) && !isset( $_SESSION['date'] ) ) {
	header('Location: .');
}

//Assign request values to $_SESSION
include_once 'api/updateSession.php';

// Begin HTML
include 'partials/preHeader.php';
$title = "Browse Appointments";
?>

<link rel="stylesheet" href="styles/browse-appts.css" />
<link rel="stylesheet" href="styles/appt.css" />

<?php
include 'partials/header.php';
?>

<div class='appt-container'>
	<div class="appt-header">
		<h2><?php echo date( 'F Y', $_SESSION['date'] ) ?></h2>

		<nav id='appt-nav-days' style='overflow:auto;'>
			<!-- AJAX will populate this with navigation items for each day -->
		</nav>
	</div>
	<hr />

	<form action="review-itinerary.php" method="POST">
		<div class='appt-content'>
			<!-- AJAX will populate this with appointments that match filters -->

			<p class='alert alert-danger' role='alert'>
				JavaScript is required to use this application. Please
				<a class="alert-link" href="http://www.enable-javascript.com/" target="_blank">enable JavaScript in
				your browser's settings</a> and refresh or try using a different browser.
			</p>
			<p class="alert alert-danger" role="alert">
				If JavaScript is already enabled on your browser, try again in a moment or call 1-800-4-ALFRED to
				plan your visit with our admissions department directly.
			</p>
		
			<?php
				/* DEBUG * /
				echo '<strong>Request</strong>';
				echo '<pre>';
				print_r($_REQUEST);
				echo '</pre>';

				echo '<strong>Session</strong>';
				echo '<pre>';
				print_r($_SESSION);
				echo '</pre>';
				// */
			?>
		</div>
		<hr />

		<div class='col-xs-6' align='left'>
			<a class='btn btn-primary' href="index.php">Edit Filters</a>
		</div>
		<div class="col-xs-6" align='right'>
			<input class="btn btn-warning" type="submit" value="Review Itinerary" />
		</div>
		<div class="row-space"></div>
		<div class="row-space"></div>
	</form>
</div>



<script>
	var schedApptData = {
		//will hold data about all scheduled appointments displayed on page 
	};

	window.onload = function () {

		//initial creation of navigation of days
		refreshUI();
		setupAddButtons();
	};



	function refreshUI (args) {
		
		//if date has changed..
		if ( args && args.date ) {

			//refresh the navigation to match
			refreshDateNav(args.date);
		}

		//or else call for a date refresh with no parameters 
		else refreshDateNav();

		//Pass on arguments to refresh page content
		refreshContent(args);

		return false;
	}



	//get json for date nav and pass it to renderDateNav() 
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

		//Get string of date selected
		var selectedDate = days[5];

		//Get date string for prev week
		var prevWeekDate = new Date(days[5] + "T12:00:00");
		prevWeekDate.setDate(prevWeekDate.getDate() - 7);
		prevWeekDate = prevWeekDate.getFullYear()+'-'+(prevWeekDate.getMonth()+1)+'-'+prevWeekDate.getDate();

		//Get date string for next week
		var nextWeekDate = new Date(days[5] + "T12:00:00");
		nextWeekDate.setDate(nextWeekDate.getDate() + 7);
		nextWeekDate = nextWeekDate.getFullYear()+'-'+(nextWeekDate.getMonth()+1)+'-'+nextWeekDate.getDate();

		//Previous button
		navHtml += "<span class='col-xs-1'>";
		navHtml += "<a href class='glyphicon glyphicon-triangle-left'" +
			"onclick='return refreshUI({date:\""+prevWeekDate+"\"});' />";
		navHtml += "</span>";

		//Date buttons
		for (var i = 0; i < 5; i++) {
			navHtml += "<span class='col-xs-2' ";
			navHtml += "id='nav-"+days[i].fullDate+"'>"; //to apply "selected" styles
			navHtml += "<a href='#' onclick='return refreshUI({date:\""+days[i].fullDate+"\"});'>";
			navHtml += days[i].dayAbbrev+" "+days[i].dayOfMonth+"</a>";
			navHtml += "</span>";
		}

		//Next button
		navHtml += "<span class='col-xs-1'>";
		navHtml += "<a href class='glyphicon glyphicon-triangle-right'" +
			"onclick='return refreshUI({date:\""+nextWeekDate+"\"});' />";
		navHtml += "</span>";

		//update navigation of days
		$('#appt-nav-days').html( navHtml );

		//apply styles to selected nav element
		$('#nav-'+days[5]).addClass('datenav-selected');

		return false;
	}



	function refreshContent (args) {
		//if arguments have been passed, build a new api request
		if (args) {

			//Create array to hold url query segments
			var uri = [];

			//Build each segment of the url
				//date
				if ( args.date ) {
					uri.push('date=' + args.date);
				}

				//apptTypeID
				if ( args.apptTypeID ) {
					if ( Array.isArray( args.apptTypeID ) ) {
						uri.push('apptTypeID[]=' + args.apptTypeID.join('&amp;apptTypeID[]='));
					}
					else {
						uri.push('apptTypeID=' + args.apptTypeID);
					}
				}

				//apptTypeID
				if ( args.curriculumID ) {
					if ( Array.isArray( args.curriculumID ) ) {
						uri.push('curriculumID[]=' + args.curriculumID.join('&amp;curriculumID[]='));
					}
					else {
						uri.push('curriculumID=' + args.curriculumID);
					}
				}

			//build uri chain string
			uriStr = (uri[0]) ? '?' : '';
			for (var i = 0; i < uri.length; i++) {
				uriStr += uri[i];
				uriStr += (uri[i+1]) ? '&amp;' : '';
			}

			console.log("http://test.com/" + uriStr);
		}

		//if no arguments have been passed, uri string will be empty
		else uriStr = '';

		//make ajax call to api
		$.ajax({
			url: 'api/browseAppts.php'+uriStr,
			method: 'GET'
		})
		.done(renderContent);

		return false;
	}

	function renderContent (json) {
		var data = JSON.parse(json);
		

	//add each schedAppt in json to schedApptData object.
	//this JavaScript object will be used to detect time conflicts while adding appts to itinerary.
		data.forEach(function (val, i, arr) {
			val.apptList.forEach(function (val, i, arr) {
				schedApptData[val.schedApptID] = val;
			})
		});

	//construct view of each appointment returned
		var html = '';
		console.log(data);

		//if the option to sign up for campus tour or admissions is available
		//let the student know they are eligible for complementary lunch
		var addLunchAlert = false;
		data.forEach(function (grp, i) {
			if ( grp.apptType.apptTypeID  == 1 || grp.apptType.apptTypeID  == 3 )
				addLunchAlert = true;
		});
		if (addLunchAlert) {
			html += "<div class='alert alert-info' role='alert'>"
			     + "<strong>Heads up!</strong> If you register to <strong>tour our campus</strong> "
			     + "and/or <strong>meet with admissions</strong>, leave a space in your "
			     + "schedule anywhere between 11:00am and 2:00pm to enjoy lunch on us!</div>";
		}

		//for each appointment group in data
		data.forEach(function (grp, i) {
			html += '<div class="appt-group">'
			
			//Title
			     + '<h3>' + grp.apptType.title + ': '
			     + grp.apptList.length + '</h3>'

			//Description
			     + '<p>' + grp.apptType.description + '</p>'

			//Table
			     + '<table style="width:100%;"><tr>'
			     + '<th>Times</th>'
			     + (grp.apptType.apptTypeID == 2 ? '<th>Programs</th>' : ' ')
			     + '<th /></tr>';

			//For each appt within the group
			grp.apptList.forEach(function (appt, i) {

				//Add row with..
				html += '<tr>'
				     + '<td>'+appt.timeStart+" - "+appt.timeEnd+'</td>' //time (start - end)
				     + '<td>'+(appt.title ? appt.title : ' ')+'</td>'   //program title
				     + '<td style="text-align:right;">'
				     + '<label class="btn btn-primary">'                //button (UI)
				     + '<input class="hidden input-schedApptID" type="checkbox"' //form input
				     + 'name="schedApptID[]" value="'+appt.schedApptID+'" '
				     + 'id="input-addAppt-'+appt.schedApptID+'" />'     //unique id
				     + '<span>Add</span>&nbsp;'
				     + '<i class="glyphicon glyphicon-plus" />'         //glyphicon
				     + '</label></td></tr>';
			});
			     
			html += '</table></div>';
		});

		//If there were no results
		if ( html == '' ) {
			html = "There are no visits scheduled on this day that match your search criteria.";
		}

		//place generated HTML into .appt-content container
		$('.appt-content').html( html );

		//initialize addAppt button UI
		setupAddButtons();

		return false;
	}



	function setupAddButtons () {
		$('.input-schedApptID').change(function () {

			if ( $(this).is(':checked') ) {
				//1. Check if there is a time conflict
				var conflict = false;
				var schedApptID = $(this).val();
				var apptTypeID = schedApptData[schedApptID].apptTypeID;
				var timeStart = schedApptData[schedApptID].timeStart;
				var timeEnd = schedApptData[schedApptID].timeEnd;

				var conflictTime;
				var conflictType;

				$('.input-schedApptID').each(function (i, e) {
					if ( $(this).is(':checked') ) {
						if ( e.value != schedApptID && !checkForTimeConflict(
						      	timeStart,
						      	timeEnd,
						      	schedApptData[e.value].timeStart,
						      	schedApptData[e.value].timeEnd) ) {
							conflict = true;
							conflictTime = schedApptData[e.value].timeStart;
							conflictType = schedApptData[e.value].apptTypeID;
						}
					}
				});

				//2. Uncheck if there is a time conflict...
				if ( conflict ) {
					$(this).prop('checked', false);

				//3. and display conflict alert.
					var strAppt;
					switch (apptTypeID) {
						case 1:
							strAppt = "Campus Tour @ "+timeStart;
							break;
						case 2:
							strAppt = "Department Tour @ "+timeStart;
							break;
						case 3:
							strAppt = "Admissions @ "+timeStart;
							break;
						case 4:
							strAppt = "Financial Aid @ "+timeStart;
							break;
						default: break;
					}

					var conflictAppt;
					switch (conflictType) {
						case 1:
							conflictAppt = "Campus Tour @ "+conflictTime;
							break;
						case 2:
							conflictAppt = "Department Tour @ "+conflictTime;
							break;
						case 3:
							conflictAppt = "Admissions @ "+conflictTime;
							break;
						case 4:
							conflictAppt = "Financial Aid @ "+conflictTime;
							break;
						default: break;
					}


					var alert = "<div class='alert alert-warning alert-dismissable'>"
					          + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"
					          + "<span aria-hidden='true'>&times;</span></button>";
					alert += "<strong>"+strAppt+"</strong> could not be added because it conflicts with <strong>"+conflictAppt+"</strong>.</div>"
					$(this).parents('table').after(alert);
				}
			}


			//"Add" to "Added" UI change
			validationUIHandler(
				$(this).parent(),
				this.checked,
				function (context, state) {
					//set btn style
					context
						.toggleClass('btn-primary', !state)
						.toggleClass('btn-success', state)

					//set icon
					context.children('i')
						//plus icon if not checked
						.toggleClass('glyphicon-plus', !state)

						//check icon if checked
						.toggleClass('glyphicon-ok', state)

					//set btn text
					context.children('span')
						.html( (state) ? 'Added' : 'Add' );
				}
			)
		});
	}


	function checkForTimeConflict (x1, x2, y1, y2) {
		//change time strings into comparable date objects
		x1 = new Date('1/1/2011 ' + x1);
		x2 = new Date('1/1/2011 ' + x2);
		y1 = new Date('1/1/2011 ' + y1);
		y2 = new Date('1/1/2011 ' + y2);

		//return false if there is a conflict
		return !(x1 < y2 && y1 < x2);
	}
</script>
<script src='js/appt.js'></script>

<?php
include 'partials/footer.php'
?>