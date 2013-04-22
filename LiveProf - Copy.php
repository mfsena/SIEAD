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
<SCRIPT language="JavaScript" src="js/localization.js"></SCRIPT>
<SCRIPT language="JavaScript" src="js/commfunc.js"></SCRIPT>
<SCRIPT language="JavaScript" src="js/axobjdef.js"></SCRIPT>
<script language="JavaScript" src="js/slider.js"></script>
<SCRIPT language="JavaScript" src="js/vlc.js"></SCRIPT>
<SCRIPT language="Javascript" src="js/plugin_detect.js"></SCRIPT>

<script type="text/JavaScript">
<!--
function RGB(r,g,b){
    return (b*65536+g*256+r);
}
function Viewer()
{
    document.open();
    document.write("<OBJECT NAME='AxMC'");
    document.write(" CLASSID='CLSID:B4CB8358-ABDB-47EE-BC2D-437B5DEBABCB' data='data:application/x-oleobject'");
    document.write(" CODEBASE='AxViewer/AxMediaControl.cab#version=2,0,0,1' width=500 height=400>");
    
    document.write("	<PARAM name='BkColor' VALUE=" + RGB(40,88,133) + ">");
   	document.write("	<PARAM name='TextColor' VALUE=" + RGB(90,90,90) + ">");
    document.write("	<PARAM name='ButtonColor' VALUE=" + RGB(110,110,110) + ">");
    document.write("	<PARAM name='HoverColor' VALUE=" + RGB(18,204,214) + ">");
    
    document.write("	<PARAM name='UIMode' VALUE='none'>");
    document.write("	<PARAM name='ShowStatusBar' VALUE='0'>");
    document.write("	<PARAM name='ShowToolBar' VALUE='1'>");
   	//document.write("	<PARAM name='CaptionText' VALUE='Transmissão Ao Vivo'>");
   	document.write("	<PARAM name='EnableContextMenu' VALUE='1'>");
	document.write("	<PARAM name='VideoEffectMode' VALUE='0'>");
   
  	document.write("  <PARAM name='AutoStart' VALUE='1'>");
	document.write("  <PARAM name='AudioReceiveStart' VALUE='1'>");
    document.write("	<PARAM name='MediaDelay' VALUE='0'>");
    document.write("  <PARAM name='ShowToolTip' VALUE='0'>");
    document.write("  <PARAM name='PTZMouseCtl' VALUE='0'>");
    
    document.write("  <PARAM name='StretchToFit' VALUE='1'>");
    document.write("  <PARAM name='MaintainAspectRatio' VALUE='1'>");

    document.write("	<PARAM name='ToolBarConfiguration' VALUE='stream+rec+mic+zoom+time+rotate'>");
    document.write("	<PARAM name='HostIP' VALUE='192.168.25.14'>");
    document.write("	<PARAM name='HttpPort' VALUE='554'>");
	//document.write("	<PARAM name='MediaURL' VALUE='http://homesena.no-ip.org/'>");
    document.write("	<PARAM name='MediaUsername' VALUE='siead'>");
    document.write("	<PARAM name='MediaPassword' VALUE='tcc'>");
	document.write("	<PARAM name='MediaProtocol' VALUE='3'>");
	document.write("	<PARAM name='MediaType' VALUE='255'>");
    document.write("	<PARAM name='MediaChannel' VALUE='0'>");
	document.write("	<PARAM name='MediaDelay' VALUE='0'>");
    document.write("	<PARAM name='RecordSuffix' VALUE= '1'>");
	//document.write("	<PARAM name='RecordFormat' VALUE=1>");
	//document.write("	<PARAM name='RecordPath' VALUE= 'C:\\xampp\\htdocs\\SIEAD\\Videos'>");
	//document.write("	<PARAM name='RecordName' VALUE= 'Video2.avi'>");
	
	document.write("</OBJECT>");
    document.close();
}
function onunload(){
    //AxMC.AVConnect(0);
}
function Play()
{
    AxMC.Play(1);
    //AxMC.SetMD(0, 0,10, 10, 50, 50, 40, 50);
}
function Playback(value)
{
    AxMC.MediaFile = value;
}
function RecordStart()
{
var recForm = document.rec_form;

AxMC.RecordPath = recForm.recPath.value;

AxMC.RecordName = recForm.recName.value;

AxMC.RecordSuffix = recForm.recSuffix[0].value;
AxMC.RecordFormat = recForm.recFormat[0].value;

var recFlags = 0;
if(recForm.recVideo.checked)
recFlags+=Number(recForm.recVideo.value);
if(recForm.recAudio.checked)
recFlags+=Number(recForm.recAudio.value);
AxMC.StartRecord(recFlags, AxMC.RecordFormat);
}

function RecordStop()
{
	AxMC.StopRecord();
}
//-->
</script>

</head>
<body leftmargin="10" topmargin="10" onUnload="onunload()">
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
						<div id="contenttxt">
						  <table border="1">
                            <tr>
                              <td colspan="3" align="center" valign="middle"><script>Viewer()</script></td>
                            </tr>
                            <tr>
                              <td align="center" valign="middle"><input type="button" name="Submit" value="FullScreen(1)" onClick="AxMC.FullScreen(1);">
                              </td>
                              <td align="center" valign="middle"><input type="button" name="Submit2" value="Play" onClick="AxMC.Play(1);">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3" align="center" valign="middle"><form name="rec_form">
                                  <table width="100%" border="1" style="table-layout:auto">
                                    <tr>
                                      <td>Record</td>
                                      <td><input type="button" name="recStart" value="Start" onClick="RecordStart();"></td>
                                      <td><input type="button" name="recStop" value="Stop" onClick="RecordStop();"></td>
                                      <td>Path</td>
                                      <td colspan="2"><input type="text" name="recPath" value="C:\temp\"></td>
                                    </tr>
                                    <td colspan="6">&nbsp;</td>
                                    <tr>
                                      <td>Name</td>
                                      <td colspan="2"><input type="text" name="recName" value=""></td>
                                      <td>Method</td>
                                      <td colspan="0"><input type="checkbox" name="recVideo" value="8" checked="checked">
                                        Video</td>
                                      <td colspan="0"><input type="checkbox" name="recAudio" value="1" checked="checked">
                                        Audio</td>
                                    </tr>
                                    <tr>
                                      <td>Suffix</td>
                                      <td colspan="2"><input name="recSuffix" type="radio" value="0">
                                        None</td>
                                      <td colspan="2"><input name="recSuffix" type="radio" value="1" checked="checked">
                                        Date/Time</td>
                                      <td colspan="2"><input name="recSuffix" type="radio" value="2">
                                        Sequence</td>
                                    </tr>
                                    <tr>
                                      <td>Video Format</td>
                                      <td colspan="2"><input name="recFormat" type="radio" value="0">
                                        H264 </td>
                                      <td colspan="2"><input name="recFormat" type="radio" value="1" checked="checked">
                                        MPEG4</td>
                                      <td colspan="2"><input name="recFormat" type="radio" value="2">
                                        MJPEG</td>
                                    </tr>
                                  </table>
                              </form></td>
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
?>
