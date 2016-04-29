<?php
require_once 'api/_registerStudent.php';
require_once 'partials/preHeader.php';
$title = "Registration Complete";
require_once 'partials/header.php';
?>
	<p>
		<?php echo $student->firstName; ?>,
		<br /><br />
		Thank you for bearing with us through the registration process,
		we look forward to seeing you on campus! An email has been
		sent to you (<strong><?php echo $student->email; ?></strong>) with details
		regarding your visit.
		<br /><br />

		<?php
		$student = Student::GetByStudentID($student->studentID, true);
		$student->Agenda = StudentAgenda::GetByAgendaID($studentAgenda->agendaID);
		$student->Agenda->GetAgendaItems(true);

//--- construct itinerary
		echo "<pre>";
		var_dump($student);
		echo '</pre>';

		ob_start();
		?>
		<h1>
			Visit Alfred State College
		</h1>
		<h4>
			Itinerary
			-
			<?php
				echo date('F j, Y', strtotime($student->Agenda->agendaItems[0]->timeStart));
			?>
		</h4>
		<p>
			<?php
				echo $student->firstName.' ';
				if (isset($student->middleInitial) && $student->middleInitial != '')
					echo $student->middleInitial.' ';
				echo $student->lastName;
			?>
		</p>
		<hr />
		<?php
		$i = 0; //$i++ to indicate last record
		foreach ( $student->Agenda->agendaItems as $appt ) {

			// time
			echo date('g:i a', strtotime($appt->timeStart));
			echo ' - ';
			echo date('g:i a', strtotime($appt->timeEnd));
			echo '<br />';

			// title
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<strong>'.$appt->AppointmentType->title.'</strong>';
			if ( $appt->apptTypeID == 2 )
				echo ' - '.$appt->Curriculum->title;
			echo '<br />';

			// location
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<em>&commat; '.$appt->Building->buildingName;
			echo ', Rm. '.$appt->room.'</em>';
			echo '<br />';

			// extra line break in-between itinerary items
			if (++$i != count($student->Agenda->agendaItems)) echo '<br />';
		}
		?>
		<hr />
		<p>
			For additional questions or concerns, call our admissions department at 607-587-4215 or 1-800-4-ALFRED.
		</p>
		<?php
		require_once('includes/mpdf/mpdf.php');
		$mpdf = new mPDF();
		$mpdf->WriteHTML(ob_get_clean());
//--- end itinerary

		/*

		create email
		attach pdf ($mpdf)
		send

		*/

		?>

	</p>
<?php
require_once 'partials/footer.php';
?>