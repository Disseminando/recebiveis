<?php

require_once('../Connections/rec01.php'); 

 
$estado = mysql_real_escape_string($_POST['uf_pac']);
 
$sql = "SELECT * FROM tb_cidades WHERE uf = '$estado' ORDER BY nome ASC";
$qr = mysql_query($sql) or die(mysql_error());
 
if(mysql_num_rows($qr) == 0){
   echo  '<option value="0">'.htmlentities('..Escolha um Estado..').'</option>';
    
}else{
   while($ln = mysql_fetch_assoc($qr)){
      echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
   }
}
 
?>
