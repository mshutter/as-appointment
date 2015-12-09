<?php session_start();
// Returns JSON of requested date @d, along with two days in both the past and future 

if ( !isset( $_GET['d'] ) || !( $date = strtotime( $_GET['d'] ) ) ) {
	//set $date to equal $_GET['d'] or the current date
	$date = ( isset( $_SESSION['date'] ) ) ? $_SESSION['date'] : time();
}


//create simple item containing information about day
function createDateItem ( $date ) {
	$tmp = new stdClass();
                                           //e.g.
	$tmp->fullDate   = date('Y-m-d', $date); //2015-02-09
	$tmp->month      = date('F', $date);     //February
	$tmp->dayOfMonth = date('d', $date);     //09
	$tmp->dayOfWeek  = date('l', $date);     //Tuesday
	$tmp->dayAbbrev  = date('D', $date);     //Tues

	return $tmp;
}


// List of 5 dates that will make up the navigation
$dateNavList = [];

array_push( $dateNavList, createDateItem( strtotime( '-2 days', $date) ) ); //2 days prior
array_push( $dateNavList, createDateItem( strtotime( '-1 day', $date) ) );  //1 day prior
array_push( $dateNavList, createDateItem( $date ) );                        //day of $date
array_push( $dateNavList, createDateItem( strtotime( '+1 days', $date) ) ); //1 day ahead
array_push( $dateNavList, createDateItem( strtotime( '+2 days', $date) ) ); //2 days ahead


//return json encoded string of these 5 date items
echo json_encode( $dateNavList )

?>