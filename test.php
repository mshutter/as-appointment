<?php

require_once 'models/ScheduledAppointment.php';

print_r( json_encode( ScheduledAppointment::listByApptType( 2, '2015-11-01' ) ) );

?>