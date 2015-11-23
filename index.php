<?php

// ========== Controller ========== //

//Get Faculty/Staff appointment types
require_once 'models/AppointmentType.php';
$apptTypes = AppointmentType::ListAppointmentTypes(1);


// ========== View ========== //

// Pre-header (DOCTYPE, open html/head, meta, etc..)
include 'partials/preHeader.php';
?>

<link rel="stylesheet" type="text/css" href="styles/filters.css" />

<?php
// Append page title
$title = "Visit";

// Header (title, close head, open body/div.container, div.header)
include 'partials/header.php';
?>

<h2>Pay Us a Visit</h2>

<form class="appt-filters" action="step-two.php" method="POST">

	<div id='error-display'>
		<!-- Validation errors will be displayed here -->
	</div>

	<?php require_once 'partials/filters.php' ?>

<!-- Submit -->
	<div>
		<input type="submit" class="btn btn-warning" value='Get Started' />	
	</div>
	
</form>



<?php
	include 'partials/footer.php';
?>
<script src='js/appt.js'></script>
<script>
	window.onload = function () {
		initDatepicker();
		initFilterButtons();
		initDropdowns();
	}
</script>

</body>
</html>