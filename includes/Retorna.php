<table align=�center�>
 <tr><td>Mat�ria</td></tr>

<?
 include(�dbconnect.php�); //Fa�o o include o arquivo de conex�o onde posso acessar suas variaveis

$sql = mysql_query(�SELECT descMat from tbmaterias;�,$db); //Executo a query retornado o resultado da busca

//$linha recebe o resultados da busca atraves da fun��o mysql_fetch_array, que traz os dados no formato de um array
while($linha = mysql_fetch_array($sql))

echo �<tr><td>�.$linha['descMat'].�</td></tr>� //No array $linha informo a cluna da tabela onde foi feita a pesquisa

 ?>
</table>