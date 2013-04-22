<?php require_once('/Connections/TCC.php');?>
<?php 
require_once('calendar/classes/tc_calendar.php');

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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmSurvey")) {
  $insertSQL = sprintf("INSERT INTO tbquestoes (numquestao, corpo_questao, fkID_questionario) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['NumQues'], "int"),
                       GetSQLValueString($_POST['Questao'], "text"),
                       GetSQLValueString($_POST['txtSurveyID'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($insertSQL, $TCC) or die(mysql_error());
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

$colname_rsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsUser = sprintf("SELECT UserID FROM tbusers WHERE UserID = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $TCC) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);

$maxRows_rstQuestoes = 10;
$pageNum_rstQuestoes = 0;
if (isset($_GET['pageNum_rstQuestoes'])) {
  $pageNum_rstQuestoes = $_GET['pageNum_rstQuestoes'];
}
$startRow_rstQuestoes = $pageNum_rstQuestoes * $maxRows_rstQuestoes;

$colname_rstQuestoes = "-1";
if (isset($_GET['recordID'])) {
  $colname_rstQuestoes = $_GET['recordID'];
}
mysql_select_db($database_TCC, $TCC);
$query_rstQuestoes = sprintf("SELECT Id_questao, numquestao, corpo_questao, fkID_questionario FROM tbquestoes WHERE fkID_questionario = %s ORDER BY numquestao ASC", GetSQLValueString($colname_rstQuestoes, "int"));
$query_limit_rstQuestoes = sprintf("%s LIMIT %d, %d", $query_rstQuestoes, $startRow_rstQuestoes, $maxRows_rstQuestoes);
$rstQuestoes = mysql_query($query_limit_rstQuestoes, $TCC) or die(mysql_error());
$row_rstQuestoes = mysql_fetch_assoc($rstQuestoes);

if (isset($_GET['totalRows_rstQuestoes'])) {
  $totalRows_rstQuestoes = $_GET['totalRows_rstQuestoes'];
} else {
  $all_rstQuestoes = mysql_query($query_rstQuestoes);
  $totalRows_rstQuestoes = mysql_num_rows($all_rstQuestoes);
}
$totalPages_rstQuestoes = ceil($totalRows_rstQuestoes/$maxRows_rstQuestoes)-1;

$queryString_rstQuestoes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstQuestoes") == false && 
        stristr($param, "totalRows_rstQuestoes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstQuestoes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstQuestoes = sprintf("&totalRows_rstQuestoes=%d%s", $totalRows_rstQuestoes, $queryString_rstQuestoes);

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
	<script language="javascript" src="calendar/calendar.js"></script>
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
				<a href="javascript:history.go(-1)"><img src="/SIEAD/images/Back.png" alt="Voltar" width="34" height="27" border="0"></a>
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
												<th style="vertical-align: central; size:24px; text-align: center; "><strong>Cadastro de Questões</strong></th>
											</tr>
										</tbody>
								</table>
											<p>
											<hr>
											<hr>
											<p>	
								<div id="formWrap">
									<form action="<?php echo $editFormAction; ?>" method="post" name="frmSurvey" id="frmSurvey">
											<div id="form">
													<div class="">
															<div class="">
																<table width="512" border="0" cellspacing="0">	
																	<thead>
																		<tr>
																			<th>#</th>
																			<th>Quest&atilde;o</th>
																		</tr>
																	</thead>
																	<td colspan="1"><p>														
																	<span class="context2">
																	<input name="NumQues" type="text" class="detail" id="Numquest" value="" size="5" maxlength="2" style="border:solid thin; height:24px;text-align:center;"><p><span class="context2">=========</span></p>
																	</td>
																	<td>
																		<textarea name="Questao" cols="53" rows="2" class="detail" id="Questao" style="border:solid thin; height:48px;text-align:center;"></textarea>
																		<p><span class="context2">Máx. 100 caracteres</span></p>
																	</td>
																</tr>
															<tr>																				
														</tr>
														<tfoot>
															<tr>
																<td colspan="2"><p>
																<input type="submit" id="submit" name="submit" style="float:inherit" value="Cadastrar Questão">
																<input name="UserID" type="hidden" id="UserID" style="background:#455a79" value="<?php echo $row_rsUser['UserID']; ?>" readonly>
																<label for="user"></label>
																<input name="Survey" type="hidden" id="Survey" style="background:#455a79" value="" readonly>
																<input name="txtSurveyID" type="hidden" id="txtSurveyID" value="<?php echo $_GET['recordID']; ?>">
																</p>
																<p>&nbsp;</p>
																<p>
																<label for="UserID"></label>
																<label for="Survey"></label>
																</p>
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
												<p>
												<hr>
												<hr>
												<p>	
											</div>Quest&otilde;es Cadastradas
										<input type="hidden" name="MM_insert" value="frmSurvey">
									</form>
								</div>
								<td><table border="0" cellspacing="0">
									<thead>
										<tr>
											<th>#</th>
											<th>Quest&atilde;o</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
										<?php do { ?>
										<tr>
										<td><a href="PagResp.php?recordID=<?php echo $row_rstQuestoes['Id_questao']; ?>"><?php echo $row_rstQuestoes['numquestao']; ?></td>
										<td><a href="PagResp.php?recordID=<?php echo $row_rstQuestoes['Id_questao']; ?>"><?php echo $row_rstQuestoes['corpo_questao']; ?></td>
										<td width="12"><a href="delQuestoes.php?recordID=<?php echo $row_rstQuestoes['fkID_questionario']; ?>&amp;Id_questao=<?php echo $row_rstQuestoes['Id_questao']; ?>"><img src="/SIEAD/images/x_p.png" border="0"></a></td>
									</tr>
								<?php } while ($row_rstQuestoes = mysql_fetch_assoc($rstQuestoes)); ?>
							</table>
							<p>												
						<table border="0">
							<tr>
								<td><?php if ($pageNum_rstQuestoes > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstQuestoes=%d%s", $currentPage, 0, $queryString_rstQuestoes); ?>">Primeiro</a><?php } // Show if not first page ?></td>
								<td><?php if ($pageNum_rstQuestoes > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstQuestoes=%d%s", $currentPage, max(0, $pageNum_rstQuestoes - 1), $queryString_rstQuestoes); ?>">Anterior</a><?php } // Show if not first page ?></td>
								<td><?php if ($pageNum_rstQuestoes < $totalPages_rstQuestoes) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstQuestoes=%d%s", $currentPage, min($totalPages_rstQuestoes, $pageNum_rstQuestoes + 1), $queryString_rstQuestoes); ?>">Pr&oacute;ximo</a><?php } // Show if not last page ?></td>
								<td><?php if ($pageNum_rstQuestoes < $totalPages_rstQuestoes) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstQuestoes=%d%s", $currentPage, $totalPages_rstQuestoes, $queryString_rstQuestoes); ?>">&Uacute;ltimo</a><?php } // Show if not last page ?></td>
							</tr>
							</table>
							</p>
							</td>
							<td>&nbsp;</td>
							</tr>
						</table>
						<tr>
						<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						</b>
					</div>
				</div>
			</div>
  </div>
	</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rsUser);

mysql_free_result($rstQuestoes);
?>
