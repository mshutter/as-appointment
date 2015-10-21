<?php
	// Creates a SELECT form element with curriculum items from database
	// $_GET['id'] refers to department ID of curriculums to be displayed as options

	require '../connection.php';

	if ( isset( $_GET['id'] ) && ( $_GET['id'] > -1 ) ) {
		// Get ID from request and prepare SQL
		$id = $_GET['id'];
		$stmt = $db->prepare(
			'SELECT `CurriculumID`, `Title`
			 FROM `Curriculum`
			 WHERE DepartmentID = :DeptID');

		$stmt->bindParam( ':DeptID', $id, PDO::PARAM_STR );
		$stmt->execute();
		$currList = $stmt->fetchAll();
	}
?>

<select name="curriculum" id="">
	<option value="">
		Undecided
	</option>
	
	<?php foreach ($currList as $curr) {
			echo '<option value="' . $curr['CurriculumID'] . '">';
			echo $curr['Title'];
			echo '</option>';
		}
	?>
</select>