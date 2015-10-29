<?php session_start();

//pass uri variables to session scope
if ( isset( $_GET['da'] ) )
	$_SESSION['datePref'] = strtotime( $_GET['da'] );

if ( isset( $_GET['ty'] ) )
	$_SESSION['apptType'] = $_GET['ty'];

if ( isset( $_GET['de'] ) )
	$_SESSION['deptID'] = $_GET['de'];

if ( isset( $_GET['cu'] ) )
	$_SESSION['currID'] = $_GET['cu'];


if ( false ) { //debug
	echo 'datePref: '.date('Y-m-d', $_SESSION['datePref']).'<br />';
	echo 'apptType: '.$_SESSION['apptType'].'<br />';
	echo 'deptID: '  .$_SESSION['deptID'].'<br />';
	echo 'currID: '  .$_SESSION['currID'].'<br />';
}




?>