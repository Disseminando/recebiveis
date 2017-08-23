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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE acesso SET usu_data=%s, usu_id=%s, login=%s, senha=%s, perfil_id=%s, situacao_ace=%s, cadastrador_ace=%s WHERE id_ace=%s",
                       GetSQLValueString($_POST['usu_data'], "date"),
                       GetSQLValueString($_POST['usu_id'], "int"),
                       GetSQLValueString($_POST['login'], "text"),
                       GetSQLValueString($_POST['senha'], "text"),
                       GetSQLValueString($_POST['perfil_id'], "int"),
                       GetSQLValueString($_POST['situacao_ace'], "int"),
                       GetSQLValueString($_POST['cadastrador_ace'], "text"),
                       GetSQLValueString($_POST['id_ace'], "int"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($updateSQL, $rec01) or die(mysql_error());

  $updateGoTo = "principal.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$usuario=$_SESSION['MM_Username'];

mysql_select_db($database_rec01, $rec01);
$query_cs_altera_senha = sprintf("SELECT id_ace, usu_data, usu_id, login, senha, perfil_id, situacao_ace, cadastrador_ace, id_usu, nome_usu, id_pf, nome_pf, id_situa, tipo_situa FROM acesso, usuario, perfil, situacao WHERE login ='$usuario' AND usu_id=id_usu AND perfil_id=id_pf AND situacao_ace=id_situa");
$cs_altera_senha = mysql_query($query_cs_altera_senha, $rec01) or die(mysql_error());
$row_cs_altera_senha = mysql_fetch_assoc($cs_altera_senha);
$totalRows_cs_altera_senha = mysql_num_rows($cs_altera_senha);

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
					<a class="navbar-brand logo-st" href="index.php">Recebíveis</a>
					</div>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">						
						<li>
						<a href="../administrativo/principal_Adm01.php">
                        <br>
						Administrativo
						</a>
						</li>
						<!---->
						<li>
						<a href="../operacional/principal_Oper01.php">
                        <br>
						Operacional
						</a>
						</li>
						<!---->
						<li>
						<a href="#acheive" class="scroll">
                        <br>
						Financeiro
						</a>
						</li>
						<!---->
                        <li>
						<a href="../restrito/seguranca01.php">
                        <br>
						Segurança
						</a>
						</li>
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
                    <?php include('identifica01.php');?>					
<div class="content">
			<div class="service_features" id="features">
				<div class="container">
					<div class="col-md-4 ser-fet">
						<h3>Saiba mais...</h3>
						<span class="line"></span>
						<div class="services">
							<div class="menu-grid">
								<ul class="menu_drop">
									<li class="item1 plus"><a href="#" class="active">Administrativo<span class="caret"></span></a>
										<ul>
											<li class="subitem1">
												<p> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
												Lorem Ipsum is simply dummy text of the printing and typesetting industry</p><br>
												<p>when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
												It has survived not only five centuries, but also the leap into electronic typesetting</p>
											</li>
										</ul>
									</li>
									<li class="item3 plus"><a href="#" class="active">Operacional<span class="caret"></span></a>
										<ul>
											<li class="subitem1">
												<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry,
												Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</p><br>
												<p>when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
												It has survived not only five centuries, but also the leap into electronic typesetting</p>
											</li>
										</ul>
									</li>
									<li class="item4 plus"><a href="#" class="active">Financeiro<span class="caret"></span></a>
										<ul>
											<li class="subitem1">
												<p> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
												Lorem Ipsum is simply dummy text of the printing and typesetting industry</p><br>
												<p>when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
												It has survived not only five centuries, but also the leap into electronic typesetting</p>
											</li>
										</ul>
									</li>	
								</ul>
								<!-- script for tabs -->
								<script type="text/javascript">
									$(function() {
										var menu_ul = $('.menu_drop > li > ul'),
											menu_a  = $('.menu_drop > li > a');
												menu_ul.hide();
													menu_a.click(function(e) {
													e.preventDefault();
													if(!$(this).hasClass('active')) {
													menu_a.removeClass('active');
													menu_ul.filter(':visible').slideUp('normal');
													$(this).addClass('active').next().stop(true,true).slideDown('normal');
													} else {
													$(this).removeClass('active');
												$(this).next().stop(true,true).slideUp('normal');
											}
										});
									});
								</script>
							<!-- script for tabs -->
						</div>
						</div>
					</div>                    
                    <!--Inicio Area de Conteudo -->
					<div class="col-md-8 ser-fet">
						<h3>:..Alterar Senha..:</h3>
						<span class="line"></span>                           
			             <div class="w3-container">
                          <br>
                          <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                            <table width="100%" border="0" align="left" cellpadding="10" cellspacing="10">
                              <tr valign="baseline">
                                <td width="10%" align="right" nowrap>Data:</td>
                                <td width="90%" align="left"><input name="usu_data" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['usu_data'], ENT_COMPAT, 'utf-8'); ?>" size="10" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Usuário:</td>
                                <td align="left"><input name="usu_id" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['nome_usu'], ENT_COMPAT, 'utf-8'); ?>" size="30" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Login:</td>
                                <td align="left"><input name="login" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['login'], ENT_COMPAT, 'utf-8'); ?>" size="10" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Senha:</td>
                                <td align="left"><input name="senha" type="text" value="<?php echo htmlentities($row_cs_altera_senha['senha'], ENT_COMPAT, 'utf-8'); ?>" size="30" maxlength="10"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Perfil:</td>
                                <td align="left"><input name="perfil_id" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['nome_pf'], ENT_COMPAT, 'utf-8'); ?>" size="30" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Situação:</td>
                                <td align="left"><input name="situacao_ace" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['tipo_situa'], ENT_COMPAT, 'utf-8'); ?>" size="10" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Cadastrador:</td>
                                <td align="left"><input name="cadastrador_ace" type="text" class="label-info" value="<?php echo htmlentities($row_cs_altera_senha['cadastrador_ace'], ENT_COMPAT, 'utf-8'); ?>" size="20" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">&nbsp;</td>
                                <td align="left"><input type="submit" class="btn-success" value="Gravar"></td>
                              </tr>
                            </table>
                            <input type="hidden" name="MM_update" value="form1">
                            <input type="hidden" name="id_ace" value="<?php echo $row_cs_altera_senha['id_ace']; ?>">
                          </form>
						  <br> 
                      </div>
					</div>					
				</div>
			</div>
		
</div>
         <!--Final Area de Conteudo -->
         
		<!-- Rodapé -->
		
		<?php include('rodape01.html');?> 

</body>
</html>
<?php
mysql_free_result($cs_altera_senha);
?>
