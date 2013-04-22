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
<!doctype html>
<html>
<head>
  <meta charset="iso-8859-1">
  <link rel="stylesheet" type="text/css" href="/SIEAD/css/styles.css" media="screen">
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
	</style>
 <link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
 
  <title>Transmissão Ao Vivo</title>
  <META HTTP-EQUIV="Content-Script-Type" CONTENT="text/javascript">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
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
    document.write(" CODEBASE='AxViewer/AxMediaControl.cab#version=2,0,0,1' width=512 height=400>");
    
    document.write("	<PARAM name='BkColor' VALUE=" + RGB(69,90,121) + ">");
    document.write("	<PARAM name='TextColor' VALUE=" + RGB(90,90,90) + ">");
    document.write("	<PARAM name='ButtonColor' VALUE=" + RGB(110,110,110) + ">");
    document.write("	<PARAM name='HoverColor' VALUE=" + RGB(18,204,214) + ">");
    
    document.write("	<PARAM name='UIMode' VALUE='none'>");
    document.write("	<PARAM name='ShowStatusBar' VALUE='0'>");
    document.write("	<PARAM name='ShowToolBar' VALUE='1'>");
   // document.write("	<PARAM name='CaptionText' VALUE='Transmissão Ao Vivo'>");
   document.write("	<PARAM name='EnableContextMenu' VALUE='1'>");
   
  	document.write("  <PARAM name='AutoStart' VALUE='1'>");
	document.write("  <PARAM name='AudioReceiveStart' VALUE='1'>");
    document.write("	<PARAM name='MediaDelay' VALUE='0'>");
    document.write("  <PARAM name='ShowToolTip' VALUE='0'>");
    document.write("  <PARAM name='PTZMouseCtl' VALUE='0'>");
    
    document.write("  <PARAM name='StretchToFit' VALUE='1'>");
    document.write("  <PARAM name='MaintainAspectRatio' VALUE='1'>");

    document.write("	<PARAM name='ToolBarConfiguration' VALUE='stream+volume+mute+mic'>");
    document.write("	<PARAM name='HostIP' VALUE='192.168.25.7'>");
    document.write("	<PARAM name='HttpPort' VALUE='1191'>");
    document.write("	<PARAM name='MediaProtocol' VALUE='3'>");
    document.write("	<PARAM name='MediaChannel' VALUE='1'>");
    document.write("	<PARAM name='MediaUsername' VALUE='tccfsa'>");
    document.write("	<PARAM name='MediaPassword' VALUE='tcc'>");
	
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
//-->
</script>
</head>
  <body>
	<div id="container">
		<div id="headerpage">
					<div style="left: 5px; width: 733px; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: red;">SIEAD</span><br>
						Sistema Integrado de Ensino Superior a Distancia </div>
			</div>
		<div id="menubar">
					<ul>
						<li><a href="/SIEAD/HomeA.php">Inicio</a></li>
						<li><a href="/SIEAD/AgAulas.php">Agendar Aulas</a></li>
						<li><a href="#">Assistir Videos</a></li>
						<li><a href="#">Acessar Questionários</a></li>
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
								<tr>
										<td align="center" valign="middle" bgcolor="#FFFFFF"></td>
								</tr>
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
							<p>
									<input name="UserID" type="text" id="UserID" value="<?php echo $row_rsUser['UserID']; ?>">
							Usuário</p>
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
										<tbody>
													<tr>
															<td style="vertical-align: central; size:24px; text-align: center; background-color: #455a79; color: #FFF;"><strong>Transmiss&atilde;o Ao Vivo</strong></td>
													</tr>
											</tbody>
								</table>
								<h2>&nbsp;</h2>
								<table border="1">
										<tr>
										<td colspan="3" align="center" valign="middle"><script>Viewer()</script></td>
									  </tr>
									  <tr>
										<td align="center" valign="middle">
										  <input type="button" name="Submit" value="Tela Cheia" onClick="AxMC.FullScreen(1);">    </td>
									  </tr>
							</table>
										</form>	</td>
									  </tr>
									</table>
								<br>
									</b></div>
				</div>
			</div>
</div>
</body>
</html>