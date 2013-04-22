<?php require_once('/Connections/TCC.php'); ?><?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['usuario'])) {
  $loginUsername=$_POST['usuario'];
  $password=$_POST['senha'];
  $MM_fldUserAuthorization = "UserRole";
  $MM_redirectLoginSuccess = "/SIEAD/HomeP.php";
  $MM_redirectLoginFailed = "/SIEAD/index2.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_TCC, $TCC);
  	
  $LoginRS__query=sprintf("SELECT userID, senha, UserRole FROM tbusers WHERE userID='%s' AND senha='%s'",
  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $TCC) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'UserRole');
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?><!doctype html>
<html><head>
  <meta charset="iso-8859-1">
  
<!-- Oculta erros no código php  
  
  <?php

$default = '/SIEAD/css/styles.css'; // Variavel que define o estilo css no primeiro acesso a página.
$blue = ($_POST["blue"]); // Postagem da variavel que recebe o valor do botão Preto
$red = ($_POST["red"]); // Postagem da variavel que recebe o valor do botão Azul
$brown = ($_POST["brown"]); // Postagem da variavel que recebe o valor do botão Azul
$theme = ''; // Variavel que define, o valor da css caso o nenhum botão seja clicado.

if ($theme = '' or $blue){ // Se variavel $thema for igual a vazio ou variavel $black.
	$theme = '/SIEAD/css/styles.css'; // Faça $thema igual a referencia css.
} elseif ($theme = $red){ // Se variavel $thema for igual a variavel $blue.
	$theme = '/SIEAD/css/styles_red.css'; // Faça $thema igual a referencia css.
} elseif ($theme = $brown){ // Se variavel $thema for igual a variavel $blue.
$theme = '/SIEAD/css/styles_brown.css'; // Faça $thema igual a referencia css.
} elseif ($theme = $default){ // Se variavel $thema for igual a variavel $default.
	$theme = $default; // Faça $thema igual a variavel $default. <link href="/SIEAD/css/styles.css" rel="stylesheet" type="text/css">
}

?>
 -->	 
  <link rel="stylesheet" type="text/css" href="<?php echo $theme // Recebe valor de $theme já calculado no script. ?>" />
  
   
  <title>Página de Login</title>
</head>
  <body>
  	   
	<div id="container">
		<div id="headerpage">
					<div style="left: 5px; width: 733px; text-align: left; margin-left: 160px; top: 25px; height: 72px;" id="logo">BEM VINDO AO <span style="color: yellow;">SIEAD</span><br>
						Sistema Integrado de Ensino Superior a Distancia </div>
    
	  </div>
		<div id="menubar"><ul></ul>
	  </div>
		<div id="leftpic">
					<div id="leftsidebar">
						<h2 align="center">&nbsp;</h2>
						<p></p>
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
				 <div id="general" align="right">
					<form method="post" action="index.php">
					<input type="submit" name="blue" id="blue" value="A" style="background-color:blue; color:blue" />
					<input type="submit" name="red" id="red" value="V" style="background-color:red; color:red"/>
					<input type="submit" name="brown" id="brown" value="M" style="background-color:brown; color:brown"/>
					</form>
				</div>
	  </div>
		<div id="content">
		<div id="middlepic">
		<div id="contenttxt">
		<h2>ACESSO AO SISTEMA<br>
		</h2>
		<div id="box">
		
			<form id="frmLogin" name="frmLogin" method="POST" action="<?php echo $loginFormAction; ?>">
			<br>
			
			<span>Usuário:</span>
			<input name="usuario" type="text" id="usuario">
			
			<span>Senha:</span>
			<input name="senha" type="password" id="senha">
			
			<input type="submit" name="logar" id="logar" value="Entrar" class="btn">
			
		  </form>
		</div>
		
		</div>
		</div>
		</div>
</div>
</body>
</html>