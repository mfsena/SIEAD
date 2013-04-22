<?php

 $arquivo = $_FILES['arquivo'];
 if ($arquivo['type'] == "image/jpeg" || $arquivo['type']== "image/pjpeg")
 {
   if ($arquivo['size']>500000)
   {
	   	echo '<script type="text/javascript">alert("Arquivo muito grande. Tamanho máximo permitido 500kb.")</script>';
     	exit;
	 	echo '<meta http-equiv="refresh" content="1; url=CadVideo.php" />'; 
   }
   
   $novonome = md5(mt_rand(1,10000).$arquivo['name']).'.jpg';
   $dir = "videos/";
   if (!file_exists($dir))
   {
     mkdir($dir, 0755);  
   }
   $caminho = $dir.$novonome;
   move_uploaded_file($arquivo['tmp_name'],$caminho);
   echo '<script type="text/javascript">alert("Arquivo enviado!")</script>';
   echo '<meta http-equiv="refresh" content="1; url=CadVideo.php" />';  
 } else{
   echo '<script type="text/javascript">alert(""Arquivo inválido. É permitido somente imagem com extensão .jpg."")</script>';
 }

 ?>