<?php require_once('/Connections/TCC.php'); ?>
<?php

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "/SIEAD/index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
//função para redirecionamento de URL..
function redireciona($link){
if ($link==-1){
echo" <script>history.go(-1);</script>";
}else{
echo" <script>document.location.href='$link'</script>";
}
}

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO tbmatprof (fk_userProf, fk_idMaterias) VALUES (%s, %s)",
                       GetSQLValueString($_POST['lstProf'], "text"),
                       GetSQLValueString($_POST['lstMaterias'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($insertSQL, $TCC) or die(mysql_error());

  $insertGoTo = "/SIEAD/sucesso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmCadMat")) {
  $insertSQL = sprintf("INSERT INTO tbmaterias (codMat, DescMat) VALUES (%s, %s)",
                       GetSQLValueString($_POST['codMat'], "text"),
                       GetSQLValueString($_POST['descMat'], "text"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($insertSQL, $TCC) or die(mysql_error());

  $insertGoTo = "/SIEAD/sucesso.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  /*header(sprintf("Location: %s", $insertGoTo));*/
$link = $insertGoTo; // especifica o endereço
redireciona($link); // chama a função
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsMatProf = 10;
$pageNum_rsMatProf = 0;
if (isset($_GET['pageNum_rsMatProf'])) {
  $pageNum_rsMatProf = $_GET['pageNum_rsMatProf'];
}
$startRow_rsMatProf = $pageNum_rsMatProf * $maxRows_rsMatProf;

mysql_select_db($database_TCC, $TCC);
$query_rsMatProf = "SELECT DescMat FROM tbmaterias";
$query_limit_rsMatProf = sprintf("%s LIMIT %d, %d", $query_rsMatProf, $startRow_rsMatProf, $maxRows_rsMatProf);
$rsMatProf = mysql_query($query_limit_rsMatProf, $TCC) or die(mysql_error());
$row_rsMatProf = mysql_fetch_assoc($rsMatProf);

if (isset($_GET['totalRows_rsMatProf'])) {
  $totalRows_rsMatProf = $_GET['totalRows_rsMatProf'];
} else {
  $all_rsMatProf = mysql_query($query_rsMatProf);
  $totalRows_rsMatProf = mysql_num_rows($all_rsMatProf);
}
$totalPages_rsMatProf = ceil($totalRows_rsMatProf/$maxRows_rsMatProf)-1;

$maxRows_rstMaterias =13;
$pageNum_rstMaterias = 0;
if (isset($_GET['pageNum_rstMaterias'])) {
  $pageNum_rstMaterias = $_GET['pageNum_rstMaterias'];
}
$startRow_rstMaterias = $pageNum_rstMaterias * $maxRows_rstMaterias;

mysql_select_db($database_TCC, $TCC);
$query_rstMaterias = "SELECT * FROM viewmats";
$query_limit_rstMaterias = sprintf("%s LIMIT %d, %d", $query_rstMaterias, $startRow_rstMaterias, $maxRows_rstMaterias);
$rstMaterias = mysql_query($query_limit_rstMaterias, $TCC) or die(mysql_error());
$row_rstMaterias = mysql_fetch_assoc($rstMaterias);

if (isset($_GET['totalRows_rstMaterias'])) {
  $totalRows_rstMaterias = $_GET['totalRows_rstMaterias'];
} else {
  $all_rstMaterias = mysql_query($query_rstMaterias);
  $totalRows_rstMaterias = mysql_num_rows($all_rstMaterias);
}
$totalPages_rstMaterias = ceil($totalRows_rstMaterias/$maxRows_rstMaterias)-1;

mysql_select_db($database_TCC, $TCC);
$query_rstProfs = "SELECT * FROM viewprofs";
$rstProfs = mysql_query($query_rstProfs, $TCC) or die(mysql_error());
$row_rstProfs = mysql_fetch_assoc($rstProfs);
$totalRows_rstProfs = mysql_num_rows($rstProfs);

$queryString_rsMatProf = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsMatProf") == false && 
        stristr($param, "totalRows_rsMatProf") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsMatProf = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsMatProf = sprintf("&totalRows_rsMatProf=%d%s", $totalRows_rsMatProf, $queryString_rsMatProf);
?>
<!doctype html>
<html>
  <head>
  <meta charset="iso-8859-1">
  
  <link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
  <title>Registrar Mat&eacute;rias / Professor</title>
  </head>
  <body>
	<div id="container">
		<div id="headerpage">
					<div style="left: 5px; width: 733px; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: red;">SIEAD</span><br>
						Sistema Integrado de Ensino Superior a Distancia </div>
			</div>
		<div id="menubar">
					<ul>
						<li><a href="/SIEAD/HomeP.php">Inicio</a></li>
						<li><a href="#">Gerenciar Aulas</a></li>
						<li><a href="#">Gerenciar Videos</a></li>
						<li><a href="#">Gerenciar Questionários</a></li>
						<li><a href="#">Material de Apoio</a></li>
						<li><a href="#">Configurações</a></li>
						<li><a href="<?php echo $logoutAction ?>">Logoff</a></li>
				</ul>
			</div>
		<div id="leftpic">
					<div id="leftsidebar">
						<h2 align="center">Mat&eacute;rias Ministradas</h2>
						<p></p>
						<div id="leftsidebartext">
								<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<br>
									<br>
							</div>
				</div>
					<form id="frmNovaMat" name="frmNovaMat" method="post" action="/SIEAD/CadMaterias.php">
					</form>
					<div id="bottomLeftForm"> </div>
			</div>
		<div id="rightpic">
					<div id="rightsidebar">
						<h2>Visitas recentes<br>
							</h2>
						<div id="rightsidebartext">
									<ul>
										<li>Aluno 01<br>
											</li>
										<li>Aluno 02<br>
											</li>
										<li><a href="#">Aluno 03<br>
												</a></li>
										<li><a href="#">Aluno 04<br>
												</a></li>
								</ul>
							</div>
						<h3>Videos mais acessados<br>
							</h3>
						<div id="rightsidebartext1">
									<ul>
										<li>Video 01<br>
											</li>
										<li>Video 02<br>
											</li>
										<li><a href="#">Video 03<br>
												</a></li>
										<li><a href="#">Video 04<br>
												</a></li>
										<li><a href="#">Video 05<br>
												</a></li>
										<li><a href="#">Video 06<br>
												</a></li>
										<li><a href="#">Video 07<br>
												</a></li>
										<li><a href="#">Video 08<br>
												</a></li>
								</ul>
							</div>
				</div>
			</div>
		<div id="content">
					<div id="middlepic">
						<div id="contenttxt">
						<form id="form1" name="form1" method="post" action="<?php echo $editFormAction; ?>">
									<table border="1" align="center" cellpadding="0" cellspacing="0" style="text-align: left; height: 40px;">
										<tbody>
													<tr>
														<td style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white;"><strong>Associar Mat&eacute;ria / Professor</strong></td>
												</tr>
											</tbody>
								</table>
									<h2>&nbsp;</h2>
							
									<table width="512px" border="1" cellspacing="5" height="253">
											<tr>
													<th style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white; width:400; border:hidden" scope="col"><strong>Mat&eacute;rias cadastradas</th>
												</tr>
											<tr>
													<td><select name="lstMaterias" size="13" id="lstMaterias" style="overflow:auto; width:500px; border:hidden; max-width:512px; padding:10px">
															<?php
do {  
?>
															<option value="<?php echo $row_rstMaterias['idMaterias']?>"><?php echo $row_rstMaterias['Materia']?></option>
															<?php
} while ($row_rstMaterias = mysql_fetch_assoc($rstMaterias));
  $rows = mysql_num_rows($rstMaterias);
  if($rows > 0) {
      mysql_data_seek($rstMaterias, 0);
	  $row_rstMaterias = mysql_fetch_assoc($rstMaterias);
  }
?>
													</select></td>
												</tr>
									</table>
									
									<p>&nbsp;</p>
									<table width="512px" height="50" border="1" align="center" cellspacing="5">
											<tr>
													<th style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white; border:hidden" scope="col"><strong>Professores Cadastrados</span></th>
													<th rowspan="2" style="vertical-align: central; size:24px; text-align: center; background-color: #fff;" scope="col"><input name="btnCadMatProf" type="submit" id="btnCadMatProf" value="Associar Mat&eacute;ria ao Professor" style="float:left; height:30px; padding: 5px"></th>
											</tr>
											<tr>
													<td><select name="lstProf" size="5" id="lstProf" style="border:hidden; padding-left:10px; padding-right:10px; text-align:center; width:300px">
															<?php
do {  
?>
															<option value="<?php echo $row_rstProfs['userProf']?>"><?php echo $row_rstProfs['Professor']?></option>
															<?php
} while ($row_rstProfs = mysql_fetch_assoc($rstProfs));
  $rows = mysql_num_rows($rstProfs);
  if($rows > 0) {
      mysql_data_seek($rstProfs, 0);
	  $row_rstProfs = mysql_fetch_assoc($rstProfs);
  }
?>
													</select></td>
												</tr>
									</table>
									<input type="hidden" name="MM_insert" value="form1">
								</form>
								<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>											<br>
									</p>
									</b></div>
				</div>
			</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rstProfs);
?>
