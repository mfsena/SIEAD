<?php require_once('/Connections/TCC.php');?>
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

?>
<?php
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

$colname_rsMatProf = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsMatProf = $_SESSION['MM_Username'];
}
mysql_select_db($database_TCC, $TCC);
$query_rsMatProf = sprintf("SELECT DescMat FROM viewprofmats WHERE userProf = %s", GetSQLValueString($colname_rsMatProf, "text"));
$rsMatProf = mysql_query($query_rsMatProf, $TCC) or die(mysql_error());
$row_rsMatProf = mysql_fetch_assoc($rsMatProf);
$totalRows_rsMatProf = mysql_num_rows($rsMatProf);
?>
<!doctype html>
<html>
<head>
<meta charset="iso-8859-1">
<link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
<title>Transmissão Ao Vivo</title>
<META HTTP-EQUIV="Content-Script-Type" CONTENT="text/javascript">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">

<SCRIPT language="JavaScript" src="js/vlc.js"></SCRIPT>

<style type="text/css">
</style>

</script>
 
 <SCRIPT LANGUAGE="JavaScript">
function getSelectedButton(buttonGroup){
    for (var i = 0; i < buttonGroup.length; i++) {
        if (buttonGroup[i].checked) {
            return i;
        }
    }
    return 0;
}
function SelCamera(form) {
    var i = getSelectedButton(form.camera);
	var vlc = document.getElementById("vlc");
	//vlc.playlist.clear();
	vlc.setAttribute("target",form.camera[i].value);
	//alert(vlc.getAttribute("target"));
   	var options = ":sout=#duplicate{dst=display,dst=std{access=file,mux=mp4,dst=c:\\temp\\output.mp4}}";
    var id = vlc.playlist.add(form.camera[i].value, "TESTE", options);
	vlc.playlist.playItem(id);
	//vlc.playlist.play();

}

</SCRIPT>
   
</head>
<body leftmargin="10" topmargin="10">

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
						
						<table width="30" border="1">
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
							</p>
							<p>&nbsp;</p>
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
			  <div id="Video">
					<div id="contenttxt">
						<h2><strong>Streaming</strong></h2>
					</div>
					
					<!-- player -->
					
					
					
						<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab#Version=0,8,6,0"   width="500" height="400" id="vlc" [url]events[/url]="True">
						   <param name="MRL" value= "" />
						   <param name="ShowDisplay" value="True" />
						   <param name="AutoLoop" value="False" /> 
						   <param name="AutoPlay" value="False" />
						   <embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org" progid="VideoLAN.VLCPlugin.2" width="500" height="400" loop="no" hidden="no" target="" />
						   </object>	
						   				
											   
					
						<form name="endereco">
<INPUT size="90" id="targetTextField" value="" style="visibility: hidden">
<INPUT type=submit value="Gravar" onClick="doGo(document.getElementById('targetTextField').value);">
<!-- player -->	
				
				</form>
					<div id="contenttxt">
					<FORM>
					<h2>Camera:<INPUT TYPE="radio" NAME="camera" VALUE="rtsp://192.168.25.14:554/video.mp4" onClick="SelCamera(this.form)">1 (Marcelo)
					<INPUT TYPE="radio" NAME="camera" VALUE="rtsp://laboratorioatcom.no-ip:554/h264" onClick="SelCamera(this.form)">2 (Tiago)
					<INPUT TYPE="radio" NAME="camera" VALUE="rtsp://149.5.40.144:8001/live_h264_1.sdp" onClick="SelCamera(this.form)">3 (EUA)
					<INPUT TYPE="radio" NAME="camera" VALUE="rtsp://149.5.42.145:11062/live_mpeg4.sdp" onClick="SelCamera(this.form)">4 (EUA 2)
					</form>
					</h2>
					</div>
				</div>
			</div>

		</div>
</body>
</html>
<?php
mysql_free_result($rsMatProf);
?>