<?php
include 'partials/preHeader.php';
?>

<link rel="stylesheet" type="text/css" href="styles/filters.css" />

<?php
$title = "Visit";
include 'partials/header.php';
?>


<div class="appt-container">
	<div class="appt-header">
		<h2>Pay Us a Visit</h2>
	</div>
	
	<div class="appt-content">
		<form class="appt-filters" action="browse-appts.php" method="POST">
			<div id='error-display'>
				<!-- Display for validation errors -->
			</div>

			<?php require_once 'partials/filters.php' ?>

			<input type="submit" class="btn btn-warning" value='Get Started' />	
		</form>		
	</div>
</div>



<?php
	include 'partials/footer.php';
?>

<script src='js/appt.js'></script>

<script>
	window.onload = function () {
		initFilters();
	}  
</script>

</body>
</html>