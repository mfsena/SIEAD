<?php
// Este arquivo conecta um banco de dados MySQL - Servidor = localhost 
//echo "teste";
print_r($_POST);
$id = $_POST[usuario_id];

$servidor="localhost";
$dbname="siead"; // Indique o nome do banco de dados que será aberto
$usuario="root"; // Indique o nome do usuário que tem acesso
$password=""; // Indique a senha do usuário

$conexao = mysql_connect($servidor, $usuario, $password) or die ("Não foi possivel conectar ao servidor MySQL");

$db_selected = mysql_select_db($dbname, $conexao);
if (!$db_selected) {
    die ('Can\'t use $manager : ' . mysql_error());
}

?>
 