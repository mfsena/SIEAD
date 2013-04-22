<?php require_once('/Connections/TCC.php');?>
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
<?php
$DataHoje=date("Y/m/d");
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

$currentPage = $_SERVER["PHP_SELF"];

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmSurvey")) {
  $insertSQL = sprintf("INSERT INTO tbquestionario (Nome_questionario, userID, Data_questionario) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['NmSurvey'], "text"),
                       GetSQLValueString($_POST['UserID'], "text"),
                       GetSQLValueString($_POST['DataSurvey'], "text"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($insertSQL, $TCC) or die(mysql_error());
}

if ((isset($_POST['recordID'])) && ($_POST['recordID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbquestionario WHERE Id_questionario=%s",
                       GetSQLValueString($_POST['recordID'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($deleteSQL, $TCC) or die(mysql_error());
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
$totalPages_rsMatProf = ceil($totalRows_rsMatProf/$maxRows_rsMatProf)-1;$maxRows_rsMatProf = 10;
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
$query_rsMatProf = sprintf("SELECT DescMat FROM viewprofmats WHERE userProf = %s", GetSQLValueString($colname_rsMatProf, "text"));
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

mysql_select_db($database_TCC, $TCC);
$query_rstMaterias = "SELECT * FROM tbmaterias";
$rstMaterias = mysql_query($query_rstMaterias, $TCC) or die(mysql_error());
$row_rstMaterias = mysql_fetch_assoc($rstMaterias);
$totalRows_rstMaterias = mysql_num_rows($rstMaterias);

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

$colname_rsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsUser = sprintf("SELECT UserID FROM tbusers WHERE UserID = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $TCC) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);

$maxRows_rstSurveyLink = 7;
$pageNum_rstSurveyLink = 0;
if (isset($_GET['pageNum_rstSurveyLink'])) {
  $pageNum_rstSurveyLink = $_GET['pageNum_rstSurveyLink'];
}
$startRow_rstSurveyLink = $pageNum_rstSurveyLink * $maxRows_rstSurveyLink;

mysql_select_db($database_TCC, $TCC);
$query_rstSurveyLink = "SELECT * FROM viewsurveys";
$query_limit_rstSurveyLink = sprintf("%s LIMIT %d, %d", $query_rstSurveyLink, $startRow_rstSurveyLink, $maxRows_rstSurveyLink);
$rstSurveyLink = mysql_query($query_limit_rstSurveyLink, $TCC) or die(mysql_error());
$row_rstSurveyLink = mysql_fetch_assoc($rstSurveyLink);

if (isset($_GET['totalRows_rstSurveyLink'])) {
  $totalRows_rstSurveyLink = $_GET['totalRows_rstSurveyLink'];
} else {
  $all_rstSurveyLink = mysql_query($query_rstSurveyLink);
  $totalRows_rstSurveyLink = mysql_num_rows($all_rstSurveyLink);
}
$totalPages_rstSurveyLink = ceil($totalRows_rstSurveyLink/$maxRows_rstSurveyLink)-1;

$queryString_rstSurveyLink = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstSurveyLink") == false && 
        stristr($param, "totalRows_rstSurveyLink") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstSurveyLink = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstSurveyLink = sprintf("&totalRows_rstSurveyLink=%d%s", $totalRows_rstSurveyLink, $queryString_rstSurveyLink);

$queryString_rstSurveyLink = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstSurveyLink") == false && 
        stristr($param, "totalRows_rstSurveyLink") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstSurveyLink = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstSurveyLink = sprintf("&totalRows_rstSurveyLink=%d%s", $totalRows_rstSurveyLink, $queryString_rstSurveyLink);

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
  <title>Cadastrar Question&aacute;rio</title>
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
								<h2><strong>Cadastro de Questionários</strong></h2>
								</div>			
									
								<div id="formWrap">
										<form action="<?php echo $editFormAction; ?>" method="post" name="frmSurvey" id="frmSurvey">
												<div id="form">
														<div class="">
																<div class="label">
																	<table border="0" cellspacing="0">
																	<thead>
																		<tr>
																			<th>Nome do Question&aacute;rio</th>
																		</tr>
																	</thead>
																	<td colspan="2"><p>														
																	<span class="context2">
																	<input name="NmSurvey" type="text" class="detail" id="tema" value="" size="85" maxlength="45" style="border:solid thin; height:24px;text-align:center;">
																	</span>																					
																	<p>
																	<span class="context2">M&aacute;x. 45 caracteres</span></td>
																	</p>
																	<tfoot>
																		<tr>
																		<td height="20" colspan="2">
																			
																			<label for="user"></label>
																			<label for="UserID"></label>
																			<input type="submit" id="submit" name="submit" style="float:inherit; padding-left:6px; padding-right:6px" value="Cadastrar Question&aacute;rio">
																			<input name="UserID" type="hidden" id="UserID" style="background:#455a79" value="<?php echo $row_rsUser['UserID']; ?>" readonly>
																			<label for="DataSurvey"></label>
																			<input name="DataSurvey" type="hidden" id="DataSurvey" style="background:#455a79" value="<?php echo $DataHoje ?>" readonly>
																			<label for="SurveyID"></label>
	<input name="SurveyID" type="hidden" id="SurveyID" style="background:#455a79" value="" readonly>
																		</p>
																	</td>
																</tr>
															</tfoot>
														</table>
													<label for="user"></label>
													</div>
													<div class="context"></div>
													</div>
													<div class="row">
													<div class="label"></div>
													<div class="input"></div>
													<div class="context"></div>
													</div>
													<div class="submit"></div>
										  </div>
													<p>
													<hr>
													<hr>
													<p>	
 													<blockquote>
 													<p><strong>Question&aacute;rios Cadastrados</strong>
 													<input type="hidden" name="MM_insert" value="frmSurvey">
													</p>
												</blockquote>
										</form>
								</div>
								<table width="512px">
										<thead>
											<tr>
												<th style="text-align:left">#</th>
												<th style="text-align:left">Questionario</th>
												<th style="text-align:left">Data</th>
												<th>&nbsp;</th>
											</tr>
										</thead>
										<?php do { ?>
										<tr>
										<td><a href="CadQuest.php?recordID=<?php echo $row_rstSurveyLink['Id_questionario']; ?>"> <?php echo $row_rstSurveyLink['Id_questionario']; ?></a></td>
										<td><a href="CadQuest.php?recordID=<?php echo $row_rstSurveyLink['Id_questionario']; ?>"><?php echo $row_rstSurveyLink['Nome_questionario']; ?></a></td>
																														<td><a href="CadQuest.php?recordID=<?php echo $row_rstSurveyLink['Id_questionario']; ?>"><?php echo $row_rstSurveyLink['Data']; ?></a></td>
										
<td width="12"><a href="delSurvey.php?recordID=<?php echo $row_rstSurveyLink['Id_questionario']; ?>"><img src="/SIEAD/images/x_p.png" border="0"></a></td>
								</tr>
								<?php } while ($row_rstSurveyLink = mysql_fetch_assoc($rstSurveyLink)); ?>
						  </table>
							    <table border="0" align="left">
								<tr>
									<td><?php if ($pageNum_rstSurveyLink > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstSurveyLink=%d%s", $currentPage, 0, $queryString_rstSurveyLink); ?>">Primeiro</a><?php } // Show if not first page ?></td>
									<td><?php if ($pageNum_rstSurveyLink > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstSurveyLink=%d%s", $currentPage, max(0, $pageNum_rstSurveyLink - 1), $queryString_rstSurveyLink); ?>">Anterior</a><?php } // Show if not first page ?></td>
									<td><?php if ($pageNum_rstSurveyLink < $totalPages_rstSurveyLink) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstSurveyLink=%d%s", $currentPage, min($totalPages_rstSurveyLink, $pageNum_rstSurveyLink + 1), $queryString_rstSurveyLink); ?>">Pr&oacute;ximo</a><?php } // Show if not last page ?></td>
									<td><?php if ($pageNum_rstSurveyLink < $totalPages_rstSurveyLink) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstSurveyLink=%d%s", $currentPage, $totalPages_rstSurveyLink, $queryString_rstSurveyLink); ?>">&Uacute;ltimo</a><?php } // Show if not last page ?></td>
							  </tr>
						</table>
						<blockquote>
							<p>
							</p>
							</p>
						</blockquote>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rstSurveys);

mysql_free_result($rsUser);

mysql_free_result($rstSurveyLink);
?>
