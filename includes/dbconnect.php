<?php
// Este arquivo conecta um banco de dados MySQL - Servidor = localhost 
//echo "teste";
print_r($_POST);
$id = $_POST[usuario_id];

$servidor="localhost";
$dbname="siead"; // Indique o nome do banco de dados que ser� aberto
$usuario="root"; // Indique o nome do usu�rio que tem acesso
$password=""; // Indique a senha do usu�rio

$conexao = mysql_connect($servidor, $usuario, $password) or die ("N�o foi possivel conectar ao servidor MySQL");

$db_selected = mysql_select_db($dbname, $conexao);
if (!$db_selected) {
    die ('Can\'t use $manager : ' . mysql_error());
}

?>
 