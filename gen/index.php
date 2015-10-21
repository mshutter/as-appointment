<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Accessor Generator</title>
</head>
<body>
	<p>Type in the variable name to generate an accessor</p>
	<form action=".">
		<input type='text' name='var' value='' />
		<input type='submit' value='Generate' />
	</form>
	<br />
	<?php
		function gen ($var) {
			$Var = substr_replace($var, strtoupper( $var{0} ), 0, 1);
			$str = "function ";
			$str .= "get" . $Var . "() {";
			$str .= "<br />";

			$str .= "&nbsp;&nbsp;&nbsp;&nbsp;return \$this->" . $var . "; }";
			$str .= "<br />";

			$str .= "function set" . $Var . "(\$value) {";
			$str .= "<br />";

			$str .= "&nbsp;&nbsp;&nbsp;&nbsp;\$this->" . $var . " = \$value; }";

			return $str;

			// function getFirstName() {
			// 	return $this->firstName; }
			// function setFirstName($value) {
			// 	$this->firstName = $value; }
			// }
		}

		if ( isset( $_REQUEST['var'] ) ) {
			print_r( gen( $_REQUEST['var'] ) );
		}
	?>
	<script>
		window.onload = function () {
			var res = document.getElementById('result');

			res.focus();
			res.innerHTML.select();
		}
	</script>
</body>
</html>