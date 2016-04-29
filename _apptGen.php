<?php
require_once 'partials/preHeader.php';
echo '<title>Appointment Generator</title>';
require_once 'partials/header.php';

//include dependent models
require_once 'models/ScheduledAppointment.php';
require_once 'models/AppointmentType.php';
require_once 'models/Curriculum.php';
require_once 'models/Building.php';

//fetch dependent models as objects
$curr = Curriculum::ListAllCurriculums();
$type = AppointmentType::ListAppointmentTypes();
$bldg = Building::ListBuildings();
foreach ($bldg as &$b) {
	$b->GetRooms();
}

/* DEBUG * /
echo "Buildings:";
var_dump($bldg[2]);
echo "Types:";
var_dump($type);
echo "Curriculums:";
var_dump($curr);
// ========== */
?>

<h1>Scheduled Appointment Generator</h1>
<p>Generates mock scheduled appointments for debugging</p>
<hr />

<form method="post" action="api/_addAppt.php">
	<label for="input-date">Date:</label>
	<input id='input-date' type="text" name="date" value="4/29/2016" />
	<br />

	<select id="input-type" name='type'>
		<option value="">Select appt. type</option>
		<?php foreach ($type as $t) : ?>
		<option value="<?php echo $t->apptTypeID; ?>">
			<?php echo $t->title; ?>
		</option>
		<?php endforeach; ?>
	</select>
	<br />

	<select id="input-curr" name='curr'>
		<option value="">Select curriculum</option>
		<?php foreach ($curr as $c) : ?>
		<option value="<?php echo $c->curriculumID; ?>">
			<?php echo $c->title; ?>
		</option>
		<?php endforeach; ?>
	</select>
	<span style="color:#f00">*</span>
	<br />

	<label for="input-time-start">Time start:</label>
	<input type="time" id='input-time-start' name='time-start' />
	<br />

	<label for="input-time-end">Time end:</label>
	<input type="time" id='input-time-end' name='time-end' />
	<br />

	<select id="input-bldg" name='building'>
		<option value="">Select room</option>
		<?php for ($i=0; $i<count($bldg); $i++) : ?>
		<optgroup label="<?php echo $bldg[$i]->buildingName; ?>">

			<?php foreach ($bldg[$i]->rooms as $room) : ?>
			<option value="<?php echo $bldg[$i]->buildingAbbrev.'_'.$room->roomNum; ?>">
				<?php echo $bldg[$i]->buildingAbbrev." ".$room->roomNum ?>
			</option>
			<?php endforeach; ?>
			

		</optgroup>
		<?php endfor; ?>
	</select>
	<br /><br />

	<input type="submit" value='Create Appointment' />
</form>
<hr />

<p style="color:#f00">*Only required if apptType == "Department Tour"</p>

<script>
	window.onload = function () {
		$('#input-date').datepicker({
			dateFormat: "m/d/yy",
			minDate: 0
		});

		$('.ui-datepicker-next').removeClass('ui-datepicker-next');
		$('.ui-datepicker-prev').removeClass('ui-datepicker-prev');
	};
</script>

<?php
require_once 'partials/footer.php';
?>