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
<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Modern UI CSS">
    <meta name="author" content="Sergey Pimenov">
    <meta name="keywords" content="windows 8, modern style, modern ui, style, modern, css, framework">

    <link href="css/modern.css" rel="stylesheet">
    <link href="css/theme-dark.css" rel="stylesheet">

    <script src="js/jquery-1.8.2.min.js"></script>
    <script src="js/google-analytics.js"></script>
    <script src="js/jquery.mousewheel.min.js"></script>
    <script src="js/github.info.js"></script>
    <script src="js/tile-image-slider.js"></script>

    <title>SIEAD</title>

    <style>
        body {
            background: #1d1d1d;
        }
    </style>

    <script>
        function Resize(){
            var tiles_area = 0;
            $(".tile-group").each(function(){
                tiles_area += $(this).outerWidth() + 80;

            });

            $(".tiles").css("width", 120 + tiles_area + 20);

            $(".page").css({
                height: $(document).height() - 20,
                width: $(document).width()
            });
        }

        function AddMouseWheel(){
            $("body").mousewheel(function(event, delta){
                var scroll_value = delta * 50;
                if (!jQuery.browser.chrome) {
                    document.documentElement.scrollLeft -= scroll_value;
                } else {
                    this.scrollLeft -= scroll_value;
                }
                return false;
            });
        }

        $(function(){

            Resize();
            AddMouseWheel();

        })


    </script>
</head>

<body class="modern-ui" onresize="Resize()">
<div class="page secondary fixed-header">
    <div class="page-header ">
        <div class="page-header-content">
            <div class="user-login">
                <a href="#">
                    <div class="name">
                        <span class="first-name">Sergey</span>
                        <span class="last-name">Pimenov</span>
                    </div>
                    <div class="avatar">
                        <img src="images/myface.jpg"/>
                    </div>
                </a>
            </div>

            <h1>SIEAD</h1>
			<br>
			<h2>Sistema Integrado de Ensino Superior a Distancia</h2>
        </div>
    </div>

  
  <body>
	<div class="horizontal-menu">
        <ul>
            <li><a href="#">Inicio</a></li>
			<li><a href="/SIEAD/CadAulas.php">Gerenciar Aulas</a></li>
			<li><a href="/SIEAD/CadVideo.php">Gerenciar Videos</a></li>
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
							<p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
									<input type="submit" name="btnCadMat" id="btnCadMat" value="Nova Mat&eacute;ria - CRIAR AMBIENTE ADMIN">
							</p>
							<p>
									<input name="UserID" type="hidden" id="UserID" value="<?php echo $row_rsUser['UserID']; ?>">
							</p>
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
									<table border="1" cellpadding="0" cellspacing="0" style="text-align: left; height: 30px;">
										<th style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white;"><strong>Aulas Agendadas</strong></th>
									</table>
									<table width="512px">
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
									<br>
								<table border="1" cellpadding="0" cellspacing="0" style="text-align: left; height: 30px;">
									<th style="vertical-align: central; size:24px; text-align: center; background-color: rgb(40,88,133); color: white;"><strong>Question&aacute;rios Dispon&iacute;veis</strong></th>
									</table>
								
								<table width="512px">
										<tr>
											<thead>
												<th style="text-align:left">#</th>
												<th style="text-align:left">Data</th>
												<th style="text-align:left">Question&aacute;rio</th>
										</thead>
										
		
		<?php do { ?>
		<tr>
				<td width="12px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"> <?php echo $row_rstSurveys['Id_questionario']; ?></a></td>
				<td width="30px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"><?php echo $row_rstSurveys['Data']; ?></td>
				<td width="120px"><a href="CadQuest.php?recordID=<?php echo $row_rstSurveys['Id_questionario']; ?>"><?php echo $row_rstSurveys['Nome_questionario']; ?></td>
				<?php } while ($row_rstSurveys = mysql_fetch_assoc($rstSurveys)); ?>
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

mysql_free_result($rsUser);

mysql_free_result($rstProxAulas);

mysql_free_result($rstSurveys);
?>
