<?php session_start();
if ( !( isset($_POST['schedApptID']) || isset($_SESSION['schedApptID']) ) ) {
	header('Location: browse-appts.php');
	exit;
}
if ( isset($_POST['schedApptID']) )
	$_SESSION['schedApptID'] = $_POST['schedApptID'];

//get Scheduled Appointment objects
require_once 'models/ScheduledAppointment.php';
require_once 'models/Curriculum.php';
require_once 'models/AppointmentType.php';
require_once 'models/Building.php';
$schedAppts = [];
foreach ( $_SESSION['schedApptID'] as $id ) {
	$tmp = ScheduledAppointment::GetBySchedApptID($id);
	$tmp->AppointmentType = AppointmentType::GetByApptTypeID($tmp->apptTypeID);
	$tmp->Building = Building::GetByBuildingAbbrev( $tmp->building );
	if ( $tmp->apptTypeID == 2 ) {
		$tmp->Curriculum = Curriculum::GetByCurriculumID($tmp->curriculumID);
	}
	array_push($schedAppts, $tmp);
}

//sort by time
usort($schedAppts, 'timeCompare');
function timeCompare ($a, $b) {
	$a = strtotime($a->timeStart);
	$b = strtotime($b->timeStart);
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}

require_once "partials/preHeader.php";
echo '<link rel="stylesheet" href="styles/appt.css" />';
$title = "Review Itinerary";
require_once "partials/header.php";

?>

<h2>Complete Your Registration</h2>
<p>
	Below is a chronological list of appointments you've selected. Review the list
	and complete the form below to finalize your registration.
</p>

<hr />
<h4><?php echo date('l, F d, Y', strtotime($schedAppts[0]->timeStart)); ?></h4>

<form action="finished.php" method="POST">
<?php
$i = 0;
foreach ( $schedAppts as $appt ) : ?>
	<!-- hidden schedApptID input -->
	<?php
		echo '<input class="hidden" name="schedApptID[]" value="';
		echo $appt->schedApptID;
		echo '" />';
	?>

	<!-- time -->
	<?php
		echo date('g:i a', strtotime($appt->timeStart));
		echo ' - ';
		echo date('g:i a', strtotime($appt->timeEnd));
	?><br />

	<!-- title -->
	<?php
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<strong>'.$appt->AppointmentType->title.'</strong>';
		if ( $appt->apptTypeID == 2 )
			echo ' - '.$appt->Curriculum->title;
	?><br />

	<!-- location -->
	<?php
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<em>&commat; '.$appt->Building->buildingName;
		echo ', Rm. '.$appt->room.'</em>';
	?><br />

	<?php if (++$i != count($schedAppts)) echo '<br />';
endforeach;
?>
<hr />

<div class="form-group">
	<label>Name <span class="red">*</span></label><br />
	<input class='col-xs-5' type="text" name="firstName" placeholder="First Name" required />
	<input class='col-xs-2' type="text" name="middleInitial" maxlength='1' placeholder="M.I." />
	<input class='col-xs-5' type="text" name="lastName" placeholder="Last Name" required />
</div>
<div class="row-space"></div>

<div class="form-group">
	<label>Email <span class="red">*</span></label><br />
	<input class="col-xs-12" type="text" name="email" placeholder="Email" required />
</div>
<div class="row-space"></div>
<div class="row-space"></div>
<div class="row-space"></div>

<div class="form-group">
	<label>Address <span class="red">*</span></label>
	<input class="col-xs-12" type="text" name="address" placeholder="Street" required />
</div>
<div class="row-space"></div>

<div class="form-group">
	<label class='col-xs-8' style='padding-left:0;'>City <span class="red">*</span></label>
	<label class='col-xs-4' style='padding-left:0;'>State <span class="red">*</span></label>
	<input class='col-xs-8' type="text" name='city' placeholder='City' required />
	<input class='col-xs-4' type="text" name='state' maxlength='2' placeholder='State (e.g. "NY")' required />
</div>
<div class="row-space"></div>

<div class="form-group">
	<label>Zip <span class="red">*</span></label>
	<input class="col-xs-12" type="text" name='zip' placeholder='Zip' required />
</div>
<div class="row-space"></div>
<div class="row-space"></div>
<div class="row-space"></div>

<div class="form-group">
	<label>Phone</label>
	<div class="row"></div>
	<input class="col-xs-6" type="text" name='primaryPhone' placeholder='Primary' />
	<input class="col-xs-6" type="text" name='secondaryPhone' placeholder='Secondary' />
</div>
<div class="row-space"></div>

<div class="form-group">
	<label>Highschool</label>
	<input class="col-xs-12" type="text" name='highschool' placeholder='Highschool' />
</div>
<div class='row-space'></div>

<div class="form-group">
	<label>Birth Date</label><br />
	<input class="col-xs-12" type="text" name="birthDate" placeholder="Birth Date (MM/DD/YYYY)" />
</div>
<div class="row-space"></div>

<p class='red col-xs-12'>
	* Required field
</p>
<div class="row"></div>
<hr />

<div class='col-xs-6' align='left'>
	<a class='btn btn-primary' href="browse-appts.php">Browse Appointments</a>
</div>
<div class="col-xs-6" align='right'>
	<input class="btn btn-warning" type="submit" value="Finish" />
</div>
<div class="row-space"></div>

</form>

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

<?php
require_once "partials/footer.php";
?>