<?php

if ( mail ( 'nick@velloff.com', 'Test mail from localhost', 'Working Fine.' ) );
echo 'Mail sent';
else
echo 'Error. Please check error log.';


?>