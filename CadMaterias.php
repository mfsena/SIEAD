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
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "2";
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

$queryString_rstMaterias = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstMaterias") == false && 
        stristr($param, "totalRows_rstMaterias") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstMaterias = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstMaterias = sprintf("&totalRows_rstMaterias=%d%s", $totalRows_rstMaterias, $queryString_rstMaterias);

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
  <title>Cadastro de Matérias</title>
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
						<li><a href="/SIEAD/CadAulas.php">Gerenciar Aulas</a></li>
						<li><a href="#">Gerenciar Videos</a></li>
						<li><a href="/SIEAD/CadSurvey.php">Gerenciar Questionários</a></li>
						<li><a href="#">Material de Apoio</a></li>
						<li><a href="#">Configurações</a></li>
						<li><a href="<?php echo $logoutAction ?>">Logoff</a></li>
				</ul>
			</div>
		<div id="leftpic">
					<div id="leftsidebar">
						<h2 align="center">Mat&eacute;rias Ministradas</h2>
						<p></p>
						<table width="179" border="1">
									<?php do { ?>
											<tr>
												<td align="center" valign="middle" bgcolor="#FFFFFF"><?php echo $row_rsMatProf['DescMat']; ?></td>
										</tr>
											<?php } while ($row_rsMatProf = mysql_fetch_assoc($rsMatProf)); ?>
							</table>
						<div id="leftsidebartext">
								<table border="0">
										<tr>
													<td><?php if ($pageNum_rsMatProf > 0) { // Show if not first page ?>
																	<a href="<?php printf("%s?pageNum_rsMatProf=%d%s", $currentPage, 0, $queryString_rsMatProf); ?>"><img src="/SIEAD/images/First.gif"></a>
												<?php } // Show if not first page ?></td>
													<td><?php if ($pageNum_rsMatProf > 0) { // Show if not first page ?>
																	<a href="<?php printf("%s?pageNum_rsMatProf=%d%s", $currentPage, max(0, $pageNum_rsMatProf - 1), $queryString_rsMatProf); ?>"><img src="/SIEAD/images/Previous.gif"></a>
												<?php } // Show if not first page ?></td>
													<td><?php if ($pageNum_rsMatProf < $totalPages_rsMatProf) { // Show if not last page ?>
																	<a href="<?php printf("%s?pageNum_rsMatProf=%d%s", $currentPage, min($totalPages_rsMatProf, $pageNum_rsMatProf + 1), $queryString_rsMatProf); ?>"><img src="/SIEAD/images/Next.gif"></a>
												<?php } // Show if not last page ?></td>
													<td><?php if ($pageNum_rsMatProf < $totalPages_rsMatProf) { // Show if not last page ?>
																	<a href="<?php printf("%s?pageNum_rsMatProf=%d%s", $currentPage, $totalPages_rsMatProf, $queryString_rsMatProf); ?>"><img src="/SIEAD/images/Last.gif"></a>
												<?php } // Show if not last page ?></td>
										</tr>
								</table>
									</p>
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
									<table border="1" cellpadding="0" cellspacing="0" style="text-align: left; height: 40px;">
										<tbody>
													<tr>
														<td style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white;"><strong>Cadastrar  nova mat&eacute;ria</strong></td>
												</tr>
											</tbody>
								</table>
									<h2>&nbsp;</h2>
									<div id="formWrap">
										<form action="<?php echo $editFormAction; ?>" method="post" name="frmCadMat">
													<div id="form">
														<div class="row">
																	<div class="label">Código da Mat&eacute;ria</div>
																	<div class="input"></div>
																	<input name="codMat" type="text" class="detail" id="codMat" value="" size="6" maxlength="6" align="">
																	<div class="context">Máx. 6 caracteres</div>
															</div>
														<div class="row">
																	<div class="label">Descrição da Mat&eacute;ria</div>
																	<div class="input"></div>
																	<input name="descMat" type="text" class="detail" id="descMat" value="" size="45" maxlength="45">
															</div>
														<div class="context">Máx. 45 caracteres</div>
														<div class="submit">
																	<p>
																		<input type="submit" id="submit" name="submit" value="Confirmar Cadastro">
																</p>
															</div>
												</div>
													<input type="hidden" name="MM_insert" value="frmCadMat">
											</form>
								</div>
									<br>
									<h2>Mat&eacute;rias cadastradas<br>
								</h2>
									<table style="text-align: left; width: 511px; height: 44px;" border="1" cellpadding="2" cellspacing="2">
										<tbody>
													<tr>
														<td style="vertical-align: top; width: 100px; text-align: center; background-color: rgb(40,88,133);"><span style="font-weight: bold; color: white;">Código</span><br></td>
														<td style="vertical-align: top; width: 411px; text-align: center; background-color: rgb(40,88,133);"><span style="font-weight: bold; color: white;">Descrição</span><br></td>
												</tr>
											</tbody>
										<?php do { ?>
										<tr>
												<td style="vertical-align:central; width="100px;"><?php echo $row_rstMaterias['CodMat']; ?></td>
												<td style="vertical-align:central; width="411px;"><?php echo $row_rstMaterias['DescMat']; ?></td>
										</tr>
										<?php } while ($row_rstMaterias = mysql_fetch_assoc($rstMaterias)); ?>
								</table>
									<table border="0">
											<tr>
													<td><?php if ($pageNum_rstMaterias > 0) { // Show if not first page ?>
																	<a href="<?php printf("%s?pageNum_rstMaterias=%d%s", $currentPage, 0, $queryString_rstMaterias); ?>"><img src="/SIEAD/images/First.gif"></a>
													<?php } // Show if not first page ?></td>
													<td><?php if ($pageNum_rstMaterias > 0) { // Show if not first page ?>
																	<a href="<?php printf("%s?pageNum_rstMaterias=%d%s", $currentPage, max(0, $pageNum_rstMaterias - 1), $queryString_rstMaterias); ?>"><img src="/SIEAD/images/Previous.gif"></a>
													<?php } // Show if not first page ?></td>
													<td><?php if ($pageNum_rstMaterias < $totalPages_rstMaterias) { // Show if not last page ?>
																	<a href="<?php printf("%s?pageNum_rstMaterias=%d%s", $currentPage, min($totalPages_rstMaterias, $pageNum_rstMaterias + 1), $queryString_rstMaterias); ?>"><img src="/SIEAD/images/Next.gif"></a>
													<?php } // Show if not last page ?></td>
													<td><?php if ($pageNum_rstMaterias < $totalPages_rstMaterias) { // Show if not last page ?>
																	<a href="<?php printf("%s?pageNum_rstMaterias=%d%s", $currentPage, $totalPages_rstMaterias, $queryString_rstMaterias); ?>"><img src="/SIEAD/images/Last.gif"></a>
													<?php } // Show if not last page ?></td>
											</tr>
									</table>
<br>
									</b></div>
				</div>
			</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);
?>
