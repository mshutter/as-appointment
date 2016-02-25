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
$title = "Step Two";
include 'partials/header.php';

?>

<div class='appt-container'>
	<div class="appt-header">
		<h2><?php echo date( 'F Y', $_SESSION['date'] ) ?></h2>

		<nav id='appt-nav-days' style='overflow:auto;'>
			<!-- AJAX will populate this with navigation items for each day -->
		</nav>
	</div>

	<a href="/as-webdev/as-appt-0.3/api/monthOfAppts.php?d=2016-04-01">Month Of Appts</a>

	<!-- Example Alert -->
	<div class='alert alert-warning alert-dismissable'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong>Campus Tour @ 12:00pm</strong> could not be added because it conflicts with <strong>Admissions @ 12:00pm</strong>.
	</div>


	<div class='appt-content'>
		<!-- AJAX will populate this with appointments that match filters -->

		<p class='alert alert-danger' role='alert'>
			JavaScript is required to use this application. Please
			<a class="alert-link" href="http://www.enable-javascript.com/" target="_blank">enable JavaScript in
			your browser's settings</a> and refresh or try using a different browser.
		</p>
	
		<?php
			/* DEBUG */
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
</div>



<script>
	var schedApptData = {
		//will hold data about all scheduled appointments displayed on page 
	};

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

		//Previous button
		navHtml += "<span class='col-xs-1'>";
		navHtml += "<a href class='glyphicon glyphicon-triangle-left'" +
			"onclick='return refreshUI({date:\""+days[1].fullDate+"\"});' />";
		navHtml += "</span>";

		//Date buttons
		for (var i = 0; i < days.length; i++) {
			navHtml += "<span class='col-xs-2'>";
			navHtml += "<a href='#' onclick='return refreshUI({date:\""+days[i].fullDate+"\"});'>";
			navHtml += days[i].dayAbbrev+" "+days[i].dayOfMonth+"</a>";
			navHtml += "</span>";
		}

		//Next button
		navHtml += "<span class='col-xs-1'>";
		navHtml += "<a href class='glyphicon glyphicon-triangle-right'" +
			"onclick='return refreshUI({date:\""+days[3].fullDate+"\"});' />";
		navHtml += "</span>";

		//update navigation of days
		$('#appt-nav-days').html( navHtml );

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
					uri.push('da=' + args.date);
				}

				//apptTypeID
				if ( args.apptTypeID ) {
					if ( Array.isArray( args.apptTypeID ) ) {
						uri.push('ty[]=' + args.apptTypeID.join('&amp;ty[]='));
					}
					else {
						uri.push('ty=' + args.apptTypeID);
					}
				}

				//apptTypeID
				if ( args.curriculumID ) {
					if ( Array.isArray( args.curriculumID ) ) {
						uri.push('cu[]=' + args.curriculumID.join('&amp;cu[]='));
					}
					else {
						uri.push('cu=' + args.curriculumID);
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

		console.log(schedApptData);

	//construct view of each appointment returned
		var html = '';

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
			     + (grp.apptType.apptTypeID == 3 ? '<th>Programs</th>' : ' ')
			     + '<th /></tr>';

			//For each appt within the group
			grp.apptList.forEach(function (appt, i) {

				//Add row with..
				html += '<tr>'
				     + '<td>'+appt.timeStart+" - "+appt.timeEnd+'</td>'     //time (start - end)
				     + '<td>'+(appt.title ? appt.title : ' ')+'</td>'       //program title
				     + '<td style="text-align:right;">'
				     + '<label class="btn btn-primary">'                    //button (UI)
				     + '<input class="hidden" type="checkbox" '             //form input
				     + 'name="schedApptID[]" value="'+appt.schedApptID+'" '
				     + 'id="input-addAppt-'+appt.schedApptID+'" />'         //unique id
				     + '<span>Add</span>&nbsp;'
				     + '<i class="glyphicon glyphicon-plus" />'             //glyphicon
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
		$('[id^="input-addAppt-"]').change(function (event) {
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

		return false;
	}



	function checkForTimeConflict ( schedApptID ) {
		//pass in schedApptID from id attribute of selected input

		var toBeAdded = $.grep(schedApptData, function (obj) {
			return ( obj.schedApptID == schedApptID );
		});
	}


	function addToItinerary ( schedApptID ) {
		//
	}
</script>
<script src='js/appt.js'></script>

<?php
include 'partials/footer.php'
?>