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
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
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

mysql_select_db($database_rec01, $rec01);
$query_cs_clinica = "SELECT id_clin, nome_clin FROM clinica ORDER BY nome_clin ASC";
$cs_clinica = mysql_query($query_cs_clinica, $rec01) or die(mysql_error());
$row_cs_clinica = mysql_fetch_assoc($cs_clinica);
$totalRows_cs_clinica = mysql_num_rows($cs_clinica);

$colname_cs_baixa = "-1";
if (isset($_POST['clinica'])) {
  $colname_cs_baixa = $_POST['clinica'];
}
mysql_select_db($database_rec01, $rec01);
$query_cs_baixa = sprintf("SELECT id_bax, data_bax, clin_id_bax, cod_lanc_bax, med_id_bax, valor_bax, imp_bax, cadastrador_bax, nome_clin FROM baixa, clinica WHERE clin_id_bax = %s AND clin_id_bax=id_clin ORDER BY data_bax ASC", GetSQLValueString($colname_cs_baixa, "int"));
$cs_baixa = mysql_query($query_cs_baixa, $rec01) or die(mysql_error());
$row_cs_baixa = mysql_fetch_assoc($cs_baixa);
$totalRows_cs_baixa = mysql_num_rows($cs_baixa);

mysql_select_db($database_rec01, $rec01);
$query_cs_total = "SELECT sum(valor_bax), sum(imp_bax) FROM baixa WHERE clin_id_bax='$colname_cs_baixa'";
$cs_total = mysql_query($query_cs_total, $rec01) or die(mysql_error());
$row_cs_total = mysql_fetch_assoc($cs_total);
$totalRows_cs_total = mysql_num_rows($cs_total);
?>
<!doctype html>
<html>

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
        <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
        <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
	</head>
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
					<a class="navbar-brand logo-st" href="#">Administrativo</a>
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li>
						<a href="./principal_Adm01.php">Inicio</a>
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
		
			<!--<?php include('../restrito/Menu_lateral.php');?>-->
                        <div class="col-md-8 ser-fet">
						<h3>:..Consulta Baixa pela Clínica..:</h3>
						<span class="line"></span>
						<div class="features">
							<div>
								<div>
									<div>										
									</div>
									<div>
                                       <br>
                                      <form name="form1" method="post" action="">
                                        <table width="100%" border="0" cellspacing="10" cellpadding="10">
                                          <tr>
                                            <th width="10%" align="left" scope="row">Clínica:</th>
                                          <td width="17%"><span id="spryselect1">                                              
                                              <select name="clinica" id="clinica">
                                                <option value="0">Selecione...</option>
                                                <?php
												do {  
												?>
												<option value="<?php echo $row_cs_clinica['id_clin']?>">
												<?php echo $row_cs_clinica['nome_clin']?></option>											
												<?php
												} while ($row_cs_clinica = mysql_fetch_assoc($cs_clinica));
												  $rows = mysql_num_rows($cs_clinica);
												  if($rows > 0) {
													  mysql_data_seek($cs_clinica, 0);
													  $row_cs_clinica = mysql_fetch_assoc($cs_clinica);
												  }
												?>
                                              </select>
                                           <span class="selectInvalidMsg">Invalido.</span><span class="selectRequiredMsg">.</span></span></td>
                                            <td width="73%"><input type="submit" name="Consultar" id="Consultar" value="Consultar"></td>
                                          </tr>
                                        </table>
                                      </form>
                                      <br>
                             <h4>Resultado..:</h4>
                             <br>
                             <p>Nome Selecionado: <?php echo $row_cs_baixa['nome_clin'];?></p>
                             <table width="100%" border="1" cellpadding="10" cellspacing="10">
                               <tr>
                                 <td>Data Baixa</td>
                                 <td>Valor R$</td>
                                 <td>Imposto</td>
                                 <td>Liquido</td>
                               </tr>
                               <?php do { ?>
                                 <tr>
                                   <td><?php echo $row_cs_baixa['data_bax']; ?></td>
                                   <td><?php echo $row_cs_baixa['valor_bax']; ?></td>
                                   <td><?php echo $row_cs_baixa['imp_bax']; ?></td>
                                   <td><?php 
									                    $v1=$row_cs_baixa['valor_bax'];;
														$imp=$row_cs_baixa['imp_bax'];
														$total=($v1-(($v1*$imp)/100));
									                    echo number_format($total,2);										 
								    ?></td>
                                 </tr>
                                 <?php } while ($row_cs_baixa = mysql_fetch_assoc($cs_baixa)); ?>
                             </table>
                                    <br>
                                 <table width="100%" border="0" cellspacing="10" cellpadding="10">
                                   <tr>
                                     <th width="53%" scope="row">&nbsp;</th>
                                     <td width="18%">Valor Total R$</td>
                                     <td width="29%" align="center"><?php 
									                    $v1=$row_cs_total['sum(valor_bax)'];
														$imp=$row_cs_total['sum(imp_bax)'];
														$total=($v1-($v1*$imp)/100);
									                    echo number_format($total,2);										 
													 ?></td>
                                   </tr>
                                 </table>
                                 <br>
                                    </div>
								</div>
								<!-- Fim do Login -->
							</div>							
						</div>
					</div>
				</div>
			</div>
		
</div>

		<!-- Rodapé -->
		
		<!--<?php include('../restrito/rodape01.html');?>-->
        


</body>
</html>
<?php
mysql_free_result($cs_clinica);

mysql_free_result($cs_baixa);

mysql_free_result($cs_total);
?>
