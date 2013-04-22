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

if ((isset($_GET['Id_questao'])) && ($_GET['Id_questao'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbquestoes WHERE Id_questao=%s",
                       GetSQLValueString($_GET['Id_questao'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($deleteSQL, $TCC) or die(mysql_error());

}

$deleteGoTo = "/SIEAD/CadQuest.php?recordID=" . $row_rstQuestoes['fkID_questionario'] . "";
if (isset($_SERVER['QUERY_STRING'])) {
	$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
	$deleteGoTo .= $_SERVER['QUERY_STRING'];
}
header(sprintf("Location: %s", $deleteGoTo));

$colname_rstQuestoes = "-1";
if (isset($_GET['recordID'])) {
  $colname_rstQuestoes = $_GET['recordID'];
}
mysql_select_db($database_TCC, $TCC);
$query_rstQuestoes = sprintf("SELECT * FROM tbquestoes WHERE fkID_questionario = %s", GetSQLValueString($colname_rstQuestoes, "int"));
$rstQuestoes = mysql_query($query_rstQuestoes, $TCC) or die(mysql_error());
$row_rstQuestoes = mysql_fetch_assoc($rstQuestoes);
$totalRows_rstQuestoes = mysql_num_rows($rstQuestoes);
?>
<?php
mysql_free_result($rstQuestoes);
?>
