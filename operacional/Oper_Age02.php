<?php require_once('../Connections/rec01.php'); ?>
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
$MM_authorizedUsers = "1,3";
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

$MM_restrictGoTo = "../erro/Acesso_Negado.php";
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

mysql_select_db($database_rec01, $rec01);
$query_cs_medico = "SELECT id_med, nome_med FROM medico ORDER BY nome_med ASC";
$cs_medico = mysql_query($query_cs_medico, $rec01) or die(mysql_error());
$row_cs_medico = mysql_fetch_assoc($cs_medico);
$totalRows_cs_medico = mysql_num_rows($cs_medico);

$colname_cs_agenda = "-1";
if (isset($_POST['medico'])) {
  $colname_cs_agenda = $_POST['medico'];
}

$today = date("n");

mysql_select_db($database_rec01, $rec01);
$query_cs_agenda = sprintf("SELECT id_age, data_age, clin_id_age, med_id_age, tipo_id, paciente_id, situa_id, tp_pagamento_id, conv_id_age, proc_id_age, valor_age, obs_age, cadastrador_age, id_clin, nome_clin, id_pac, nome_pac, id_tp_age, tipo_age, id_pag, nome_pag 
FROM agenda, clinica, paciente, agenda_tipo, tpo_pagamento 
WHERE med_id_age = %s 
AND clin_id_age=id_clin 
AND paciente_id=id_pac 
AND tipo_id=id_tp_age 
AND tp_pagamento_id=id_pag 
AND Month(data_age)>= '$today'
ORDER BY data_age ASC", GetSQLValueString($colname_cs_agenda, "int"));
$cs_agenda = mysql_query($query_cs_agenda, $rec01) or die(mysql_error());
$row_cs_agenda = mysql_fetch_assoc($cs_agenda);
$totalRows_cs_agenda = mysql_num_rows($cs_agenda);
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
             <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
					<a class="navbar-brand logo-st" href="#">Operacional</a>
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li>
						<a href="./principal_Oper01.php">Inicio</a>
						</li>
						<!---->										
						<li>	
						<a href="<?php echo $logoutAction ?>">Sair</a>
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
					</ul>
					</div><!-- /.navbar-collapse -->
					<div class="clearfix"></div>
				  </div><!-- /.container-fluid -->
			  </nav>
          </div>
			
	</div>
		<!-- Identifica Usuario Logado -->		
               <?php include('../restrito/identifica01.php');?>	
				<?php include('Menu_Operacional.php');?>        
					<div class="col-md-8 ser-fet">
						<h3>:..Agenda - Consulta..:</h3>
						<span class="line"></span>
						<div class="features">					
						<div class="w3-container">                                             
                        <div class="w3-container w3-hide-medium">
                         <form name="form1" method="post" action="">
                           <table width="100%" border="0" cellspacing="10" cellpadding="10">
                             <tr>
                               <th width="9%" scope="row">Médico :</th>
                               <td width="91%"><label for="medico"></label>
                                 <select name="medico" id="medico">
                                 <option value="0">Selecione...</option>
                                   <?php
									do {  
									?>
									<option value="<?php echo $row_cs_medico['id_med']?>"><?php echo $row_cs_medico['nome_med']?></option>
									<?php
									} while ($row_cs_medico = mysql_fetch_assoc($cs_medico));
									  $rows = mysql_num_rows($cs_medico);
									  if($rows > 0) {
										  mysql_data_seek($cs_medico, 0);
										  $row_cs_medico = mysql_fetch_assoc($cs_medico);
									  }
									?>
                                 </select>
                               </td>
                             </tr>
                             <tr>
                               <th scope="row">&nbsp;</th>
                               <td>&nbsp;<input type="submit" name="ok" id="ok" value="Pesquisar"></td>
                             </tr>
                           </table>
                         </form>
                         <br>
                         <h4>Resultado:</h4>
                         <br>
                         <?php
							 $res=$totalRows_cs_agenda;									      
							 if($res>0)
						 {?>
                         <table width="100%" border="0" cellspacing="10" cellpadding="10">
                           <tr>
                             <th bgcolor="#CCCCCC" scope="row"><strong>Data</strong></th>
                             <td bgcolor="#CCCCCC"><strong>Clínica</strong></td>
                             <td bgcolor="#CCCCCC"><strong>Paciente</strong></td>
                             <td bgcolor="#CCCCCC"><strong>Tipo</strong></td>
                             <td bgcolor="#CCCCCC"><strong>Pagamento</strong></td>
                           </tr>
                           <?php do { ?>
                           <tr> 
                             <th scope="row"><?php echo $row_cs_agenda['data_age']; ?></th>
                             <td><?php echo $row_cs_agenda['nome_clin']; ?></td>
                             <td><?php echo $row_cs_agenda['nome_pac']; ?></td>
                             <td><?php echo $row_cs_agenda['tipo_age']; ?></td>
                             <td><?php echo $row_cs_agenda['nome_pag']; ?></td>                               
                           </tr>
                           <?php } while ($row_cs_agenda = mysql_fetch_assoc($cs_agenda)); ?>
                         </table>
                         <?php } else {			   
										 
								echo "Não foi encontrado nenhum registro.";
									 }
						 ?>	
                        </div>
                                    
                        </div>									  
						<br>                                                 
						<!-- Fim do Formulario de Pesquisa-->														
					  </div>
					</div>
                    <div class="clearfix"></div>
				</div>
			</div>		
            </div>

		<!-- Rodapé -->
		
		<?php include('../restrito/rodape01.html');?>
        
</body>
</html>
<?php
mysql_free_result($cs_medico);

mysql_free_result($cs_agenda);
?>
