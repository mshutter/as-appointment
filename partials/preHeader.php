<?php 
// Report all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'models/errors.class.php';
$errors = new errors();
register_shutdown_function(array($errors,'fatalErrorHandler'));
set_error_handler(array($errors,'errorHandler'));
set_exception_handler(array($errors,'exceptionHandler'));
?>
<!DOCTYPE html>
<html lang="en">
<head>	
	<!-- <link rel="stylesheet" href="styles/jquery-ui.css" /> -->