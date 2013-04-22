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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frmResp")) {
  $updateSQL = sprintf("UPDATE tbresposta SET correcao_resposta=%s WHERE Id_resposta=%s",
                       GetSQLValueString($_POST['rdCorrection'], "int"),
                       GetSQLValueString($_POST['IdResp'], "int"));

  mysql_select_db($database_TCC, $TCC);
  $Result1 = mysql_query($updateSQL, $TCC) or die(mysql_error());
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

$maxRows_rstRespostas = 10;
$pageNum_rstRespostas = 0;
if (isset($_GET['pageNum_rstRespostas'])) {
  $pageNum_rstRespostas = $_GET['pageNum_rstRespostas'];
}
$startRow_rstRespostas = $pageNum_rstRespostas * $maxRows_rstRespostas;

mysql_select_db($database_TCC, $TCC);
$query_rstRespostas = "SELECT * FROM viewresposta ORDER BY Id_resposta ASC";
$query_limit_rstRespostas = sprintf("%s LIMIT %d, %d", $query_rstRespostas, $startRow_rstRespostas, $maxRows_rstRespostas);
$rstRespostas = mysql_query($query_limit_rstRespostas, $TCC) or die(mysql_error());
$row_rstRespostas = mysql_fetch_assoc($rstRespostas);

if (isset($_GET['totalRows_rstRespostas'])) {
  $totalRows_rstRespostas = $_GET['totalRows_rstRespostas'];
} else {
  $all_rstRespostas = mysql_query($query_rstRespostas);
  $totalRows_rstRespostas = mysql_num_rows($all_rstRespostas);
}
$totalPages_rstRespostas = ceil($totalRows_rstRespostas/$maxRows_rstRespostas)-1;

$queryString_rstRespostas = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstRespostas") == false && 
        stristr($param, "totalRows_rstRespostas") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstRespostas = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstRespostas = sprintf("&totalRows_rstRespostas=%d%s", $totalRows_rstRespostas, $queryString_rstRespostas);

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
 
  <title>Corre&ccedil;&atilde;o de respostas de Alunos</title>
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
														<th style="vertical-align: central; size:24px; text-align: center; "><strong>Correção de Respostas</strong></th>
												</tr>
											</tbody>
								</table>
									<blockquote>
											<h2>&nbsp;</h2>
									</blockquote>
										<div id="formWrap">
											<form action="<?php echo $editFormAction; ?>" method="POST" name="frmResp" id="frmResp">
													<div id="form">
															<div class="">
																<table width="512" border="0" cellspacing="0">
																<thead>
																	<tr>
																		<th style="text-align:center" width="55">#</th>
																		<th style="text-align:left" width="429">Ação</th>
																	</tr>
																	<td colspan="1"><p>														
																		<span class="context2">
																		<input name="IdResp" type="text" class="detail" id="IdResp" value="" size="7" maxlength="5" style="border:solid thin; height:24px;text-align:center;">
																	</td>
																	<td>
																		<p>
																			<label>
																				<input type="radio" name="rdCorrection" value='0' id="rdCorrection_0">Resposta Incorreta
																			</label>
																			<br>
																			<label>
																				<input type="radio" name="rdCorrection" value='1' id="rdCorrection_1">Resposta Correta
																			</label>
																			<br>
																		</p>
																	</td>
																	</thead>
																	<td width="18">																																										
																	</tr>
																	<tfoot>
																		<tr>
																			<td><p>
																					<label for="user"></label>
																			<input name="UserID" type="hidden" id="UserID" style="background:#455a79" value="<?php echo $row_rsUser['UserID']; ?>" readonly>
																			<input name="Survey" type="hidden" id="Survey" style="background:#455a79" value="" readonly>
																			<input name="txtSurveyID" type="hidden" id="txtSurveyID" value="<?php echo $_GET['recordID']; ?>">
																			</p>
																		</td>
																			<td><input type="submit" id="submit" name="submit" style="float:inherit" value="Enviar Resposta"></td>
																		</tr>
																	</tfoot>
															</table>
															<label for="user"></label>
															<div class="context"></div>
														</div>
														<div class="row">
														<div class="label"></div>
														<div class="input"></div>
														<div class="context"></div>
													</div>															
												<div class="submit"></div>
											</div>
Respostas Cadastradas
											<input type="hidden" name="MM_update" value="frmResp">
											</form>
								</div>
								<td><table border="1" cellspacing="0">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th scope="col">Resposta</th>																	
										<th scope="col">Correção</th>
										<th scope="col">Aluno</th>
										<th scope="col">&nbsp;</th>
									</tr>
								</thead>
								<?php do { ?>
								<tr>
									<td><?php echo $row_rstRespostas['Id_resposta']; ?></td>
									<td><?php echo $row_rstRespostas['corpo_resposta']; ?></td>
									<td><?php echo $row_rstRespostas['status']; ?></td>  
									<td><?php echo $row_rstRespostas['fkuserID']; ?></td>
									<td width="12"><a href="delRespostas.php?recordID=<?php echo $row_rstRespostas['fkId_questao']; ?>&amp;Id_questao=<?php echo $row_rstRespostas['Id_resposta']; ?>"><img src="/SIEAD/images/x_p.png" border="0"></a></td>
									</tr>
									<?php } while ($row_rstRespostas = mysql_fetch_assoc($rstRespostas)); ?>
									</table>
									<p>&nbsp;												
									<table border="0">
										<tr>
											<td><?php if ($pageNum_rstRespostas > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstRespostas=%d%s", $currentPage, 0, $queryString_rstRespostas); ?>">Primeiro</a><?php } // Show if not first page ?></td>
											<td><?php if ($pageNum_rstRespostas > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstRespostas=%d%s", $currentPage, max(0, $pageNum_rstRespostas - 1), $queryString_rstRespostas); ?>">Anterior</a><?php } // Show if not first page ?></td>
											<td><?php if ($pageNum_rstRespostas < $totalPages_rstRespostas) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstRespostas=%d%s", $currentPage, min($totalPages_rstRespostas, $pageNum_rstRespostas + 1), $queryString_rstRespostas); ?>">Pr&oacute;ximo</a><?php } // Show if not last page ?></td>
											<td><?php if ($pageNum_rstRespostas < $totalPages_rstRespostas) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstRespostas=%d%s", $currentPage, $totalPages_rstRespostas, $queryString_rstRespostas); ?>">&Uacute;ltimo</a><?php } // Show if not last page ?></td>
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
				</b></div>
				</div>
			</div>
		</div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rsUser);

mysql_free_result($rstRespostas);
?>
