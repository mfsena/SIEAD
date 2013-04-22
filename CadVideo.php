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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmVideos")) {
  $insertSQL = sprintf("INSERT INTO tbcadvideo (DataVideo, HorarioIniVideo, HorarioFimVideo, NomeVideo, TemaVideo, UserID) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['cal1Date'], "text"),
                       GetSQLValueString($_POST['horaIni'], "text"),
                       GetSQLValueString($_POST['horaFim'], "text"),
                       GetSQLValueString($_POST['nomeVideo'], "text"),
                       GetSQLValueString($_POST['temaVideo'], "text"),
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

$maxRows_rstVideos = 10;
$pageNum_rstVideos = 0;
if (isset($_GET['pageNum_rstVideos'])) {
  $pageNum_rstVideos = $_GET['pageNum_rstVideos'];
}
$startRow_rstVideos = $pageNum_rstVideos * $maxRows_rstVideos;

$colname_rstVideos = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rstVideos = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rstVideos = sprintf("SELECT * FROM viewvideos WHERE UserProf = %s ORDER BY `Data` ASC", GetSQLValueString($colname_rstVideos, "text"));
$query_limit_rstVideos = sprintf("%s LIMIT %d, %d", $query_rstVideos, $startRow_rstVideos, $maxRows_rstVideos);
$rstVideos = mysql_query($query_limit_rstVideos, $TCC) or die(mysql_error());
$row_rstVideos = mysql_fetch_assoc($rstVideos);

if (isset($_GET['totalRows_rstVideos'])) {
  $totalRows_rstVideos = $_GET['totalRows_rstVideos'];
} else {
  $all_rstVideos = mysql_query($query_rstVideos);
  $totalRows_rstVideos = mysql_num_rows($all_rstVideos);
}
$totalPages_rstVideos = ceil($totalRows_rstVideos/$maxRows_rstVideos)-1;

$colname_rsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsUser = sprintf("SELECT UserID FROM tbusers WHERE UserID = %s", GetSQLValueString($colname_rsUser, "text"));
$rsUser = mysql_query($query_rsUser, $TCC) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);

$queryString_rstVideos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rstVideos") == false && 
        stristr($param, "totalRows_rstVideos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rstVideos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rstVideos = sprintf("&totalRows_rstVideos=%d%s", $totalRows_rstVideos, $queryString_rstVideos);

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
  <title>Gerenciar Videos</title>
  
  <script language="javascript">

function play(tgt) {
    var uri = "rtsp://192.168.25.14:554/video.mp4"; // rtsp://sieadcam.no-ip.org:554/video.mp4 ou rtsp://192.168.25.14:554/video.mp4 ou http://177.99.244.125
    if (document.all) tgt += "_IE"
    var tgt = document.getElementById(tgt);
   // alert(tgt);
    if (document.all) tgt.playlist.add(uri,uri, new Array());
    else     tgt.playlist.add(uri,uri, "");
    tgt.playlist.play(); 
}
function reload() {
    document.body.innerHTML="";
    setTimeout("document.location.reload();", 500);
}
</script>
   	
<!-- 1. jquery library -->
<script
  src="/SIEAD/js/jquery.min.js">
</script>
 
<!-- 2. flowplayer -->
<script src="/SIEAD/js/flowplayer.min.js"></script>
 
<!-- 3. skin -->
<link rel="stylesheet" type="text/css"
   href="/SIEAD/skin/minimalist.css" />

	
</head>
  <body>
	<div id="container">
		<div id="headerpage">
			<div style="left: 5px; width: 733px; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: red;">SIEAD</span><br>
			Sistema Integrado de Ensino Superior a Distancia
			</div>
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
			<h2>Visitas recentes<br></h2>
			<div id="rightsidebartext">
				<ul>
					<li>Aluno 01<br></li>
					<li>Aluno 02<br></li>
					<li><a href="#">Aluno 03<br></a></li>
					<li><a href="#">Aluno 04<br></a></li>
				</ul>
			</div>
			<h3>Videos mais acessados<br></h3>
			<div id="rightsidebartext1">
				<ul>
					<li>Video 01<br></li>
					<li>Video 02<br></li>
					<li><a href="#">Video 03<br></a></li>
					<li><a href="#">Video 04<br></a></li>
					<li><a href="#">Video 05<br></a></li>
					<li><a href="#">Video 06<br></a></li>
					<li><a href="#">Video 07<br></a></li>
					<li><a href="#">Video 08<br></a></li>
				</ul>
			</div>
		</div>
	</div>
  <div id="content">
	<div id="middlepic">
			<div id="Video">
			
				<div id="contenttxt">
						<h2><strong>Videos armazenados</strong></h2>
					</div>
					
					<?  
						$arquivo    = $_FILES["arquivo"];  
						  
						// Recupera o nome do arquivo  
						$arquivo_nome = $arquivo['name'];  
						  
						// Recupera o caminho temporario do arquivo no servidor  
						$arquivo_caminho = $arquivo['tmp_name'];  
						  
						echo $arquivo_caminho;  
					?>  
					
					<!-- player -->
					
					<video width="500" height="400" controls poster="images/logo.png" >
					  <source src="./videos/trailer.mp4" type="video/mp4">
					  <source src="videos/video.ogg" type="video/ogg">
					  <source src="videos/video.webm" type="video/webm">
					  	<object data="flowplayer/flowplayer.swf">
						  <param name="flashvars" value="./videos/video.mp4">

						</object>
					</video>	 
							
				<p>&nbsp;    </p>
				
				<div class="example">
				<input type="file" id="arquivo" name="arquivo" onClick="" />
				<output id="file_list"></output>
							
				</div>
		</div>
	</div>
	</div>
</div>
	
</div>
		
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);

mysql_free_result($rstVideos);

mysql_free_result($rsUser);
?>
