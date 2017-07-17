<?php require_once('../Connections/rec01.php'); ?>
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
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
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

$usuario=$_SESSION['MM_Username'];

mysql_select_db($database_rec01, $rec01);
$query_cs_usuario = "SELECT id_ace, usu_data, usu_id, login, senha, perfil_id, situacao_ace, cadastrador_ace, id_pf, nome_pf FROM acesso, perfil WHERE perfil_id=id_pf AND login='$usuario'";
$cs_usuario = mysql_query($query_cs_usuario, $rec01) or die(mysql_error());
$row_cs_usuario = mysql_fetch_assoc($cs_usuario);
$totalRows_cs_usuario = mysql_num_rows($cs_usuario);

$perfil=$row_cs_usuario['nome_pf'];
?>
<html>
	<!--Cabeçalho -->
	<?php include('head01.html');?>
	<body>		
		<div class="style-label">
			<div class="container">
				<ul class="box-shadow effect2">
					<li class="col-md-3">						
						<div class="label-text">
						<h3><font color="white" size="+2"><?php  echo "Usuário:&nbsp".$_SESSION['MM_Username'];?></font></h3><br>
                        <h3><font color="white" size="+2"><?php  echo "Perfil:&nbsp".$perfil=$row_cs_usuario['nome_pf'];?></font></h3><br>
                        <h3><font color="white" size="+2"><?php  date_default_timezone_set('America/Sao_Paulo');
																	$date = date('d-m-Y -- H:i');
																	echo $date;
														            ?></font></h3>
						
						</div>
					</li>
				  <div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</body>
</html>