<!doctype html>
<html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="us" lang="us">
<head>
<title>Calendario</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<meta http-equiv="Content-Language" content="sk" />
<meta name="copyright" content="(c) 2005 separd" />
<link href="/SIEAD/includes/js/php_calendar/style.css" type="text/css" rel="stylesheet" />
<script src="/SIEAD/includes/js/php_calendar/scripts.js" type="text/javascript"></script>
</head>
<body>
<?php

include 'configure.php';

$month = NULL;
if (isset($_GET['month']))
    $month = $_GET['month'];
else
   $month = date('n');

$dFirstDayOfMonth = mktime (0,0,0,$month,1,date('Y'));// timestamp of the first day

$nZeroBasedFirstDayOfWeek = $first_day_of_week - 1; //translate to a (zero based) value correlating to php date(w', [date]) returned value
$nZeroBasedLastDayOfWeek = $nZeroBasedFirstDayOfWeek - 1;
if ($nZeroBasedLastDayOfWeek < 0)
  $nZeroBasedLastDayOfWeek = 6;

$nMonthBeginDayOfWeek = date('w', $dFirstDayOfMonth) + 0; //a value between 0 (sunday) to 6 (saturday)

//$nFirstDayOfCal should be a value from -5 (month begin is last day of week). to 1 (month begin is 1st day of week)
//for example, if $nZeroBasedFirstDayOfWeek = 1 (Monday)
//and $nMonthBeginDayOfWeek = 1 (Mon), then $nFirstDayOfCa = 1
//and $nMonthBeginDayOfWeek = 2 (Tue), then $nFirstDayOfCa = 0
//and $nMonthBeginDayOfWeek = 3 (Wed), then $nFirstDayOfCa = -1
//and $nMonthBeginDayOfWeek = 4 (Thu), then $nFirstDayOfCa = -2
//and $nMonthBeginDayOfWeek = 5 (Fri), then $nFirstDayOfCa = -3
//and $nMonthBeginDayOfWeek = 6 (Sat), then $nFirstDayOfCa = -4
//and $nMonthBeginDayOfWeek = 0 (San), then $nFirstDayOfCa = -5

$nFirstDayOfCal = 1; //good for nZeroBasedFirstDayOfWeek === nMonthBeginDayOfWeek
$aCalIndex = array( 1, 0, -1, -2, -3, -4, -5);
if ($nZeroBasedFirstDayOfWeek > $nMonthBeginDayOfWeek)
{
  $aArrReverse = array_reverse($aCalIndex);
  $nFirstDayOfCal = $aArrReverse[$nZeroBasedFirstDayOfWeek - $nMonthBeginDayOfWeek - 1];
}
else if ($nZeroBasedFirstDayOfWeek < $nMonthBeginDayOfWeek)
    $nFirstDayOfCal = $aCalIndex[$nMonthBeginDayOfWeek - $nZeroBasedFirstDayOfWeek]; //start from end

$nLastDayOfMonth = date('t', $dFirstDayOfMonth);// last day of month
echo '
    <div class="month_title">
      <a href="/SIEAD/includes/js/php_calendar/calendar.php?month='.($month-1).'" class="month_move">&laquo;</a>
      <div class="month_name">'.$month_names[date('n', mktime(0,0,0,$month,1,date('Y')))].' '.date('Y', mktime(0,0,0,$month,1,date('Y'))).'</div>
      <a href="/SIEAD/includes/js/php_calendar/calendar.php?month='.($month+1).'" class="month_move">&raquo;</a>
      <div class="r"></div>
    </div>';
for ($d=0;$d<7;$d++) {
  echo '
    <div class="week_day">'.$day_names[$d].'</div>';
}
echo '
    <div class="r"></div>';
for ($d=$nFirstDayOfCal;$d<=$nLastDayOfMonth;$d++) {
  $dDate = mktime (0,0,0,$month,$d,date('Y'));
  if ($dDate >= $dFirstDayOfMonth) {
    $today = (date('Ymd') == date('Ymd', $dDate))? '_today' : '';
    $minulost = (date('Ymd') >= date('Ymd', $dDate+86400)) && !$allow_past;
    echo '
    <div class="day'.$today.'">'.($minulost? date('j', $dDate) : '<a title="'.date($date_format, $dDate).'" href="javascript:insertdate(\''.date($date_format, $dDate).'\')">'.date('j', $dDate).'</a>').'</div>';
  } else {
    echo '
    <div class="no_day">&nbsp;</div>';
  }
  if (date('w', $dDate) == $nZeroBasedLastDayOfWeek && $dDate >= $dFirstDayOfMonth) {
    echo '
    <div class="r"></div>';
  }
}

?>
</body>
</html>
