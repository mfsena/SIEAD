<?php require_once('/Connections/TCC.php'); ?>
<?php 
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

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

$colname_rsMatProf = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsMatProf = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsMatProf = sprintf("SELECT DescMat FROM viewprofmats WHERE fk_userProf = %s", GetSQLValueString($colname_rsMatProf, "text"));
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

$maxRows_rstMaterias = 7;
$pageNum_rstMaterias = 0;
if (isset($_GET['pageNum_rstMaterias'])) {
  $pageNum_rstMaterias = $_GET['pageNum_rstMaterias'];
}
$startRow_rstMaterias = $pageNum_rstMaterias * $maxRows_rstMaterias;

mysql_select_db($database_TCC, $TCC);
$query_rstMaterias = "SELECT * FROM tbmaterias";
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
<?php
$colname_rsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsUser = sprintf("SELECT UserID FROM tbusers WHERE UserID = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $TCC) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);

$maxRows_rstProxAulas = 8;
$pageNum_rstProxAulas = 0;
if (isset($_GET['pageNum_rstProxAulas'])) {
  $pageNum_rstProxAulas = $_GET['pageNum_rstProxAulas'];
}
$startRow_rstProxAulas = $pageNum_rstProxAulas * $maxRows_rstProxAulas;

$colname_rstProxAulas = "-1";
if (isset($_SERVER['getDate()'])) {
  $colname_rstProxAulas = $_SERVER['getDate()'];
}
mysql_select_db($database_TCC, $TCC);
$query_rstProxAulas = sprintf("SELECT idtbCadAula, HorarioIniAula, HorarioFimAula, TemaAula, `Data` FROM viewproxaulas WHERE `Data` >= %s", GetSQLValueString($colname_rstProxAulas, "text"));
$query_limit_rstProxAulas = sprintf("%s LIMIT %d, %d", $query_rstProxAulas, $startRow_rstProxAulas, $maxRows_rstProxAulas);
$rstProxAulas = mysql_query($query_limit_rstProxAulas, $TCC) or die(mysql_error());
$row_rstProxAulas = mysql_fetch_assoc($rstProxAulas);

if (isset($_GET['totalRows_rstProxAulas'])) {
  $totalRows_rstProxAulas = $_GET['totalRows_rstProxAulas'];
} else {
  $all_rstProxAulas = mysql_query($query_rstProxAulas);
  $totalRows_rstProxAulas = mysql_num_rows($all_rstProxAulas);
}
$totalPages_rstProxAulas = ceil($totalRows_rstProxAulas/$maxRows_rstProxAulas)-1;

$maxRows_rstSurveys = 10;
$pageNum_rstSurveys = 0;
if (isset($_GET['pageNum_rstSurveys'])) {
  $pageNum_rstSurveys = $_GET['pageNum_rstSurveys'];
}
$startRow_rstSurveys = $pageNum_rstSurveys * $maxRows_rstSurveys;

$colname_rstSurveys = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rstSurveys = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rstSurveys = sprintf("SELECT Id_questionario, Nome_questionario, `Data` FROM viewsurveys WHERE fkUserProf = %s ORDER BY `Data` ASC", GetSQLValueString($colname_rstSurveys, "text"));
$query_limit_rstSurveys = sprintf("%s LIMIT %d, %d", $query_rstSurveys, $startRow_rstSurveys, $maxRows_rstSurveys);
$rstSurveys = mysql_query($query_limit_rstSurveys, $TCC) or die(mysql_error());
$row_rstSurveys = mysql_fetch_assoc($rstSurveys);

if (isset($_GET['totalRows_rstSurveys'])) {
  $totalRows_rstSurveys = $_GET['totalRows_rstSurveys'];
} else {
  $all_rstSurveys = mysql_query($query_rstSurveys);
  $totalRows_rstSurveys = mysql_num_rows($all_rstSurveys);
}
$totalPages_rstSurveys = ceil($totalRows_rstSurveys/$maxRows_rstSurveys)-1;

$queryString_rstSurveys = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstSurveys") == false && 
        stristr($param, "totalRows_rstSurveys") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstSurveys = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstSurveys = sprintf("&totalRows_rstSurveys=%d%s", $totalRows_rstSurveys, $queryString_rstSurveys);

$queryString_rstProxAulas = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstProxAulas") == false && 
        stristr($param, "totalRows_rstProxAulas") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstProxAulas = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstProxAulas = sprintf("&totalRows_rstProxAulas=%d%s", $totalRows_rstProxAulas, $queryString_rstProxAulas);
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "2,1";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "/SIEAD/index3.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<!doctype html>
<html>
<head>
  	<meta charset="iso-8859-1">
  	<link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
  
  	<title>Gerenciar Videos</title>
<meta http-equiv="Content-Type" content="text/html; charset="></head>
  <body>
	<div id="container">
		<div id="headerpage">
					<div style="left: 5px; width: 98%; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: red;">SIEAD</span><br>
						Sistema Integrado de Ensino Superior a Distancia </div>
	  </div>
		<div id="menubar">
			<ul>
				<li><a href="/SIEAD/HomeP.php">Inicio</a></li>
				<li><a href="/SIEAD/Cadaulas.php">Agendar Aulas</a></li>
				<li><a href="/SIEAD/CadVideo.php">Gerenciar Videos</a></li>
				<li><a href="/SIEAD/CadSurvey.php">Gerenciar Questionários</a></li>
				<li><a href="http://localhost/forum/index.php">Material de Apoio</a></li>
				<li><a href="#">Configurações</a></li>
				<li><a href="<?php echo $logoutAction ?>">Logoff</a></li>
			</ul>
	  </div>
		<div id="leftpic">
			<div id="leftsidebar">
				<h2 align="center">Mat&eacute;rias Ministradas</h2>
				<p></p>
				<table width="179" border="1" class="fixo">
					<?php do { ?>
						<tr>
							<td align="center" valign="middle" bgcolor="#FFFFFF"><?php echo $row_rsMatProf['DescMat']; ?></td>
						</tr>
						<?php } while ($row_rsMatProf = mysql_fetch_assoc($rsMatProf)); ?>
				</table>
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
		</div>
		
		<div id="rightpic">
			<div id="rightsidebar">
			<h2>Visitas recentes</h2>
				<div id="rightsidebartext">
					<ul>
						<li>Aluno 01<br></li>
						<li>Aluno 02<br></li>
						<li>Aluno 03<br></li>
						<li>Aluno 04<br></li>
					</ul>
				</div>					
			</div>
		</div>
	  
		<div id="content">
					<div id="middlepic">
						
						<div id="contenttxt">
							<h2><strong>Aulas Agendadas</strong></h2>
						</div>

						<table>
							<thead>
								<tr>
									<th>Data</th>
									<th>Inicio</th>
									<th>Fim</th>
									<th>Tema</th>
								</tr>
							</thead>
								<?php do { ?>
								<tr>
									<td width="80px"><a href="LiveProf.php?recordID=<?php echo $row_rstProxAulas['idtbCadAula']; ?>&amp;Data=<?php echo $row_rstProxAulas['Data']; ?>"><?php echo $row_rstProxAulas['Data']; ?></a></td>
									<td style="width:60px;border-right: solid thin"><?php echo $row_rstProxAulas['HorarioIniAula']; ?></td>
									<td style="width:60px; border-right: solid thin"><?php echo $row_rstProxAulas['HorarioFimAula']; ?></td>
									<td style="width:300px; border-right: solid thin"><?php echo $row_rstProxAulas['TemaAula']; ?></td>
								</tr>
								<?php } while ($row_rstProxAulas = mysql_fetch_assoc($rstProxAulas)); ?>
						</table>
					<tr>
											<td><?php if ($pageNum_rstProxAulas > 0) { // Show if not first page ?>
															<a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, 0, $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/First.gif"></a>
							<?php } // Show if not first page ?></td>
											<td><?php if ($pageNum_rstProxAulas > 0) { // Show if not first page ?>
															<a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, max(0, $pageNum_rstProxAulas - 1), $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Previous.gif"></a>
							<?php } // Show if not first page ?></td>
											<td><?php if ($pageNum_rstProxAulas < $totalPages_rstProxAulas) { // Show if not last page ?>
															<a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, min($totalPages_rstProxAulas, $pageNum_rstProxAulas + 1), $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Next.gif"></a>
							<?php } // Show if not last page ?></td>
											<td><?php if ($pageNum_rstProxAulas < $totalPages_rstProxAulas) { // Show if not last page ?>
															<a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, $totalPages_rstProxAulas, $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Last.gif"></a>
							<?php } // Show if not last page ?></td>
			  </tr>
							</table>
							</h2>
							<div id="contenttxt">
								<h2><strong>Question&aacute;rios Dispon&iacute;veis</strong></h2>
						
																				
						<table width="512px">
								<tr>
						  			<thead>
						 			 	<th style="text-align:left">#</th>
										<th style="text-align:left">Data</th>
										<th style="text-align:left">Question&aacute;rio</th>
									</thead>
								</tr>

							<?php do { ?>
							<tr>
								<td width="12px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"> <?php echo $row_rstSurveys['Id_questionario']; ?></a></td>
								<td width="30px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"><?php echo $row_rstSurveys['Data']; ?></a></td>
								<td width="120px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"><?php echo $row_rstSurveys['Nome_questionario']; ?></a></td>
								<?php } while ($row_rstSurveys = mysql_fetch_assoc($rstSurveys)); ?>
							</tr>
						</table>
						<br>
					</b>			
				</div>
			</div>
  		</div>
	</div>

	

</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rsUser);

mysql_free_result($rstProxAulas);

mysql_free_result($rstSurveys);
?>
