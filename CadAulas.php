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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmAulas")) {

$data = str_replace("/","-",$_POST['date']);
$insertdate = date('Y-m-d',strtotime($data));

  $insertSQL = sprintf("INSERT INTO tbcadaula (DataAula, HorarioIniAula, HorarioFimAula, TemaAula, userID) VALUES ('$insertdate', %s, %s, %s, %s)",
                       //GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['horaIni'], "text"),
                       GetSQLValueString($_POST['horaFim'], "text"),
                       GetSQLValueString($_POST['tema'], "text"),
                       GetSQLValueString($_POST['UserID'], "text"));

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

$colname_rsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsUser = sprintf("SELECT UserID FROM tbusers WHERE UserID = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $TCC) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);

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
  	<script src="/SIEAD/includes/js/php_calendar/scripts.js" type="text/javascript"></script>
	
<script>
function verifica_hora(){
			  hrs = (document.forms[0].hora.value.substring(0,2));
			  min = (document.forms[0].hora.value.substring(3,5));
				
			  alert('hrs '+ hrs);
			  alert('min '+ min);
				
			  situacao = "";
			  // verifica data e hora
			  if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){
				  situacao = "falsa";
			  }
				
			  if (document.forms[0].hora.value == "") {
				  situacao = "falsa";
			  }
 
			  if (situacao == "falsa") {
				  alert("Hora inválida!");
				  document.forms[0].hora.focus();
			  }
		  }
</script> 
	
	
  	<title>Agendar Aulas</title></head>

 <body>
 <div id="container">
		<div id="headerpage">
		  <div style="left: 5px; width: 733px; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: red;">SIEAD</span><br>
			  Sistema Integrado de Ensino  a Distancia </div>
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
							<h2><strong>Agendamento de aulas</strong></h2>
						</div>									
							<div id="formWrap">
								<form action="<?php echo $editFormAction; ?>" method="post" name="frmAulas" id="frmAulas">
									<div id="form">
										<table>	
											<thead>
												<tr>
													<th style="text-align:Center"><div align="center">Data</div></th>
													<th style="text-align:center"><div align="center">Inicio</div></th>
													<th style="text-align:center"><div align="center">Fim</div></th>
													<th style="text-align:center"><div align="center">Tema</div></th>
												</tr>
											</thead>
											<tr>
												<td><input name="date" type="date" id="date" size="10" style="border:solid thin;text-align:center;height:20px" onClick="javascript:viewcalendar()" maxlength="10" />																			  
												<p align="center"><span class="context2">dd/mm/yyyy</span></p></td>
												<td><input name="horaIni" type="text" class="detail" id="horaIni" value="" maxlength="5" size="10" style="border:solid thin; height:20px; text-align:center;"><p align="center"><span class="context2">hh:mm</span></p></td><td><input name="horaFim" type="text" class="detail" id="horaFim" value="" maxlength="5" size="10" style="border:solid thin; height:20px; text-align:center;"><p align="center"><span class="context2">hh:mm</span></p></td>
												<td><input name="tema" type="text" class="detail" id="tema" maxlength="45" size="40" style="border:solid thin; height:20px;text-align:Left;"><p align="right"><span class="context2">M&aacute;x. 45 caracteres</span></p></td>
											</tr>
											<tfoot>
												<tr>
													<td colspan="4"><p>
														<input type="submit" id="submit" name="submit" style="float:inherit" value="Confirmar Agendamento">
														<label for="user"></label>
														<input name="UserID" type="hidden" id="UserID" style="background:#455a79" value="<?php echo $row_rsUser['UserID']; ?>" readonly>
														</p>
													</td>
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
							<input type="hidden" name="MM_insert" value="frmAulas">
							<p>&nbsp;</p>
							<div id="contenttxt">
								<h2><strong>Aulas agendadas</strong></h2>
							</div>
							<input type="hidden" name="MM_insert" value="frmAulas">
					  	</form>
						<table align="center">
							<thead>	
								<th>Data</th>
								<th>Inicio</th>
								<th>Fim</th>
								<th>Tema</th>
								<th></th>
							</thead>
							<?php do { ?>
							<tr>
								<td width="60px"><div align="center"><a href="LiveProf.php?recordID=<?php echo $row_rstProxAulas['idtbCadAula']; ?>&amp;Data=<?php echo $row_rstProxAulas['Data']; ?>"><?php echo $row_rstProxAulas['Data']; ?></a></td>
								<td width="60px"><div align="center"><?php echo $row_rstProxAulas['HorarioIniAula']; ?></td>
								<td width="60px"><div align="center"><?php echo $row_rstProxAulas['HorarioFimAula']; ?></div></td>
								<td width="560px"><?php echo $row_rstProxAulas['TemaAula']; ?></td>
								<td width="12"><a href="delAulas.php?recordID=<?php echo $row_rstProxAulas['idtbCadAula']; ?>"><img src="/SIEAD/images/x_p.png" border="0"></a></td>
							</tr>
							<?php } while ($row_rstProxAulas = mysql_fetch_assoc($rstProxAulas)); ?>
						
						<tr>
							<td><?php if ($pageNum_rstProxAulas > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, 0, $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/First.gif"></a><?php } // Show if not first page ?></td>
							<td><?php if ($pageNum_rstProxAulas > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, max(0, $pageNum_rstProxAulas - 1), $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Previous.gif"></a><?php } // Show if not first page ?></td>
							<td><?php if ($pageNum_rstProxAulas < $totalPages_rstProxAulas) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, min($totalPages_rstProxAulas, $pageNum_rstProxAulas + 1), $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Next.gif"></a><?php } // Show if not last page ?></td>
							<td><?php if ($pageNum_rstProxAulas < $totalPages_rstProxAulas) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rstProxAulas=%d%s", $currentPage, $totalPages_rstProxAulas, $queryString_rstProxAulas); ?>"><img src="/SIEAD/images/Last.gif"></a><?php } // Show if not last page ?></td>
						 </tr>
					</table>
				</p>
			</b>
		</div>
	</div>
 </div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rstProxAulas);

mysql_free_result($rsUser);
?>
