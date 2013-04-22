<?php virtual('/SIEAD/Connections/TCC.php'); ?>
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
  <link rel="stylesheet" type="text/css" href="css/styles.css" media="screen">
  <style type="text/css">
h1 {
	font-size: 9px;
}
body {
	background-color: #FFFFFF;
}
#apDiv1 {
	position: absolute;
	width: 264px;
	height: 48px;
	z-index: 1;
	left: 386px;
	top: 495px;
	overflow: hidden;
}
body, td, th {
	color: #000000;
}
#container #content #middlepic #contenttxt h2 {
	color: #F00;
}
	</style>
  <link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
  <title>Home Professor</title>
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
						<li><a href="#">Sair</a></li>
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
									<table border="1" cellpadding="0" cellspacing="0" style="text-align: left; height: 40px;">
										<tbody>
													<tr>
														<td style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white;"><strong>Cadastrar  nova mat&eacute;ria</strong></td>
												</tr>
											</tbody>
								</table>
									<h2>&nbsp;</h2>
									<div id="formWrap"></div>
									<br>
									<h2>Opera&ccedil;&atilde;o realizada com sucesso!<br>
								</h2>
									<p><br>
											</b></p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><img src="/SIEAD/images/Back.png" width="77" height="59" border="0"></a></p>
						</div>
				</div>
			</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);

mysql_free_result($rstMaterias);
?>
