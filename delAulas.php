<?php require_once('/Connections/TCC.php');?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if ((isset($_GET['recordID'])) && ($_GET['recordID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbcadaula WHERE idtbCadAula=%s",
                       GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($deleteSQL, $TCC) or die(mysql_error());

}

$deleteGoTo = "/SIEAD/CadAulas.php";
if (isset($_SERVER['QUERY_STRING'])) {
	$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
	$deleteGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $deleteGoTo));

mysql_select_db($database_TCC, $TCC);
$query_rstAulas = "SELECT * FROM tbcadaula";
$rstAulas = mysql_query($query_rstAulas, $TCC) or die(mysql_error());
$row_rstAulas = mysql_fetch_assoc($rstAulas);
$totalRows_rstAulas = mysql_num_rows($rstAulas);
?>
<?php
mysql_free_result($rstAulas);
?>
