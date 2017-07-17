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
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1,2,3,4,5";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../erro/Acesso_Negado.html";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
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

$data_atual=date('Y/m/d');

mysql_select_db($database_rec01, $rec01);
$query_cs_contas = "SELECT data_ct_pag, clin_id_ct_pag, conta_id_pag, dtvenc_ct_pag, valor_ct_pag, dtpag_ct_pag, id_clin, nome_clin, id_plan_ct, nome_plan_ct, usu_id, login, id_usu, clin_id
FROM contas_pagar, clinica, plano_contas, acesso, usuario
WHERE clin_id_ct_pag=id_clin 
AND conta_id_pag=id_plan_ct
AND login='$usuario'
AND usu_id=id_usu
AND dtpag_ct_pag is NULL
AND id_clin=clin_id
ORDER BY dtvenc_ct_pag ASC";
$cs_contas = mysql_query($query_cs_contas, $rec01) or die(mysql_error());
$row_cs_contas = mysql_fetch_assoc($cs_contas);
$totalRows_cs_contas = mysql_num_rows($cs_contas);
?>
<!doctype html>
<html>
<!--Cabeçalho -->
<head>
		<title>:..Recebiveis..:</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Clinica, Hospital, Laboratorio, Medicos, Plano de Saude, Faturamento, Credenciamento" />
		<script type="application/x-javascript"> addEventListener("load", function() {setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
		<!--fonts-->
			<link href='http://fonts.googleapis.com/css?family=Monda:400,700' rel='stylesheet' type='text/css'>
			<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,300,100,700' rel='stylesheet' type='text/css'>
		<!--fonts-->
		<!--owlcss-->
		<link href="../css/owl.carousel.css" rel="stylesheet">
		<!--bootstrap-->
			<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<!--coustom css-->
			<link href="../css/style.css" rel="stylesheet" type="text/css"/>
		<!--default-js-->
			<script src="../js/jquery-2.1.4.min.js"></script>
		<!--bootstrap-js-->
			<script src="../js/bootstrap.min.js"></script>
		<!--script-->
			<script type="text/javascript" src="../js/move-top.js"></script>
			<script type="text/javascript" src="../js/easing.js"></script>
		<!--script-->
	</head>
  <!--Fim do Cabeçalho-->
	<body>
		<div class="header" id="home">
			<div class="header-top">
				<div class="container">
					
				</div>
			</div>
			<div class="header_nav" id="home">
				<nav class="navbar navbar-default chn-gd">
					<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand logo-st" href="../restrito/index.php">Operacional</a>
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li>
						<a href="../restrito/principal.php">
                        <br>
						Inicio
						</a>
						</li>
						<!---->
						<li>	
						<a href="<?php echo $logoutAction ?>"><br>Fechar</a>
						</li>
						<!--script-->
						<script type="text/javascript">
						jQuery(document).ready(function($) {
						$(".scroll").click(function(event){		
						event.preventDefault();
						$('html,body').animate({scrollTop:$(this.hash).offset().top},900);
						});
						});
						</script>
						<!--script-->
					</ul>
					</div><!-- /.navbar-collapse -->
					<div class="clearfix"></div>
					</div><!-- /.container-fluid -->
				</nav>
			</div>
            </div>
                    <!-- Identifica Usuario Logado -->		
                    <!--<?php include('../restrito/identifica01.php');?>-->					
                    <!--<?php include('./Menu_Operacional.php');?>-->                                  
                    <!--Inicio Area de Conteudo -->
					<div class="col-md-8 ser-fet">
						<h3>:..Quadro de Avisos..:</h3>
						<span class="line"></span>                           
			             <div class="w3-container">
                         <br>
                         <?php
							$res=$totalRows_cs_contas;									      
							if($res>0)
						 {?>
                         <h4 class="w3-grey">Clínica:<?php echo $row_cs_contas['nome_clin']; ?> </h4>
                         <br>
                         <table width="100%" border="0" cellpadding="10" cellspacing="10">
                           <tr>                                                           
                             <td bgcolor="#999999"><strong>conta_id_pag</strong></td>                             
                             <td bgcolor="#999999"><strong>dtvenc_ct_pag</strong></td>
                             <td bgcolor="#999999"><strong>valor_ct_pag</strong></td>
                             <td bgcolor="#999999"><strong>Mensagem</strong></td>                             
                           </tr>
                           <?php do { ?>
                             <tr>                              
                               <td><?php echo $row_cs_contas['nome_plan_ct']; ?></td>                               
                               <td><?php echo $row_cs_contas['dtvenc_ct_pag']; ?></td>
                               <td><?php echo $row_cs_contas['valor_ct_pag']; ?></td> 
                               <td><?php 
							        $data_atual;
									$vencimento = $row_cs_contas['dtvenc_ct_pag'];
									
									// Comparando as Datas
									if(strtotime($data_atual) > strtotime($vencimento))
									{
									echo "<img src=../images/negado04.png></img>";
									}
									elseif(strtotime($data_atual) == strtotime($vencimento))
									{
									echo "<img src=../images/aprovado01.png></img>";
									}
									else
									{
									echo "<img src=../images/aprovado01.png></img>";
									}
							    ?></td>                               
                             </tr>
                             <?php } while ($row_cs_contas = mysql_fetch_assoc($cs_contas)); ?>
                         </table>
                         <br>                          
                          <?php } else {			   
								
							 echo "<img src=../images/ok.jpg width=120 height=120 title=OK></img><br>";			 
									}
						?>		               
                         </div>
                            </div>
							<div class="clearfix"></div>
								</div>
							</div>							
						</div>
					</div>					
				</div>
			</div>
		
</div>
         <!--Final Area de Conteudo -->
         
		<!-- Rodapé -->
		
		<!--<?php include('../restrito/rodape01.html');?>-->    

</body>
</html>
<?php
 mysql_free_result($cs_usuario);

mysql_free_result($cs_contas);
?>