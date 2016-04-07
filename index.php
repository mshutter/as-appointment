<?php
include 'partials/preHeader.php';
?>

<link rel="stylesheet" type="text/css" href="styles/appt.css" />

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

		//on key-up (with delay) within #input-search field
		//filter the contents of the program list accordingly
		$('#input-search').keyup(debounce(function () {
				var exp = new RegExp(this.value, 'i');
				var match = false;

				$('.appt-dropdown-item').each(function (i, e) {

					match = e.textContent.match(exp) ? true : false;
					e.classList.toggle('hidden', !match);
				});	
			}, 200));

		//prevent form from being submitted on ENTER
		$(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });
	}  
</script>

</body>
</html>