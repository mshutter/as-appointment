<?php

//Eliminate the space between the * and / at the end of any heading
//to begin debugging. (* / => */)

/* DEBUG : ScheduledAppointment * /
require_once 'models/ScheduledAppointment.php';
$appt = ScheduledAppointment::GetBySchedApptID('748459df59');
$appt->GetStudentsAttending( true );
$appt->GetCurriculumDetails();
$appt->GetAppointmentTypeDetails();
var_dump($appt);
// ========================= */


/* DEBUG : AppointmentType * /
require_once 'models/AppointmentType.php';
$type = AppointmentType::GetByApptTypeID(2);
var_dump($type);
// ========================= */


/* DEBUG : Student * /
require_once 'models/Student.php';
$stdt = Student::GetByStudentID('09j23df98n', true);
var_dump($stdt);
// ========================= */


/* DEBUG : StudentAgendaItem * /
require_once 'models/StudentAgendaItem.php';
$item = StudentAgendaItem::ListByAgendaID( 'j3VO2g9RBl', true );
var_dump($item);
// ========================= */


/* DEBUG : Department * /
require_once 'models/Department.php';
$dept = Department::ListAllDepartments();
foreach ( $dept as &$d ) {
	$d->GetCurriculums();
}
var_dump($dept);
// ========================= */


/* DEBUG : Curriculum * /
require_once 'models/Curriculum.php';
$curr = Curriculum::ListAllCurriculums();
foreach( $curr as &$c ) {
	$c->GetDepartmentDetails();
}
var_dump($curr);
// ========================= */ 


/* DEBUG : Building * /
require_once 'models/Building.php';
$bldg = Building::ListBuildings();
foreach( $bldg as &$b ) {
	$b->GetRooms();
}
var_dump($bldg);
// ========================= */


/* DEBUG : Room * /
require_once 'models/Room.php';
$room = Room::ListByBuildingAbbrev('SET');
var_dump($room);
// ========================= */


/* DEBUG : StudentAgenda * /
require_once 'models/StudentAgenda.php';
$agda = StudentAgenda::ListByStudentID('09j23df98n');
foreach ( $agda as &$a ) {
	$a->GetAgendaItems();
}
var_dump($agda);
// ========================= */

?>