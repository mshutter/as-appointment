<?php

//Bring date and apptType into local scope.
//Defaults: $date = time() (today) && $apptType = 2 (campus tour)
$date     = array_key_exists( 'd', $_GET ) ? $_GET['d'] : time();
$apptType = array_key_exists( 't', $_GET ) ? $_GET['t'] : 2;

echo $date;

$r = new stdObject()

?>