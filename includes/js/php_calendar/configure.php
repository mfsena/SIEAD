<?php
$month_names = array(1=>'janeiro', 2=>'fevereiro', 3=>'março', 4=>'abril', 5=>'maio', 6=>'junho', 7=>'julho', 8=>'agosto', 9=>'setembro', 10=>'outubro', 11=>'novembro', 12=>'dezembro');

$day_names = array('SE', 'TE', 'QA', 'QU', 'SE', 'SA', 'DO'); //change this array to start with the first day of the week
$first_day_of_week = 2; //1- sunday, 2 - monday, etc.
//for a week that starts on Sunday use these 2 lines instead:
//$day_names = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');
//$first_day_of_week = 1; 

$allow_past = 0;
$date_format = 'd/m/Y';
?>
