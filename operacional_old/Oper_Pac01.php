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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="principal_Oper01.php";
  $loginUsername = $_POST['cpf_pac'];
  $LoginRS__query = sprintf("SELECT cpf_pac FROM paciente WHERE cpf_pac=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_rec01, $rec01);
  $LoginRS=mysql_query($LoginRS__query, $rec01) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO paciente (data_pac, nome_pac, cpf_pac, end_pac, bairro_pac, cidade_pac, uf_pac, cep_pac, fone_pac, email_pac, conv_id, situacao_pac, cadastrador_pac) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['data_pac'], "date"),
                       GetSQLValueString($_POST['nome_pac'], "text"),
                       GetSQLValueString($_POST['cpf_pac'], "text"),
                       GetSQLValueString($_POST['end_pac'], "text"),
                       GetSQLValueString($_POST['bairro_pac'], "text"),
                       GetSQLValueString($_POST['cidade_pac'], "int"),
                       GetSQLValueString($_POST['uf_pac'], "int"),
                       GetSQLValueString($_POST['cep_pac'], "text"),
                       GetSQLValueString($_POST['fone_pac'], "text"),
                       GetSQLValueString($_POST['email_pac'], "text"),
                       GetSQLValueString($_POST['conv_id'], "int"),
                       GetSQLValueString($_POST['situacao_pac'], "int"),
                       GetSQLValueString($_POST['cadastrador_pac'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "principal_Oper01.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_rec01, $rec01);
$query_cs_convenio = "SELECT id_conv, nome_conv FROM convenio ORDER BY nome_conv ASC";
$cs_convenio = mysql_query($query_cs_convenio, $rec01) or die(mysql_error());
$row_cs_convenio = mysql_fetch_assoc($cs_convenio);
$totalRows_cs_convenio = mysql_num_rows($cs_convenio);

mysql_select_db($database_rec01, $rec01);
$query_cs_situacao = "SELECT id_situa, tipo_situa FROM situacao";
$cs_situacao = mysql_query($query_cs_situacao, $rec01) or die(mysql_error());
$row_cs_situacao = mysql_fetch_assoc($cs_situacao);
$totalRows_cs_situacao = mysql_num_rows($cs_situacao);

mysql_select_db($database_rec01, $rec01);
$query_cs_estados = "SELECT est_id, est_sigla FROM cad_estados";
$cs_estados = mysql_query($query_cs_estados, $rec01) or die(mysql_error());
$row_cs_estados = mysql_fetch_assoc($cs_estados);
$totalRows_cs_estados = mysql_num_rows($cs_estados);
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
			<script src="http://www.google.com/jsapi"></script>
            <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>				
            <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
            
			<script>
				function buscar_cidades(){
				  var estado = $('#uf_pac').val();
				  if(estado){
					var url = 'ajax_buscar_cidades.php?uf_pac='+estado;
					$.get(url, function(dataReturn) {
					  $('#load_cidades').html(dataReturn);
					});
				  }
				}
        	</script> 
           
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
						<!--script-->
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
						<h3>:..Paciente - Cadastro..:</h3>
						<span class="line"></span>
                         <div class="w3-container">
                          <br>
                          <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                            <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
                              <tr valign="baseline">
                                <td width="10%" align="right" nowrap>Data:</td>
                                <td width="90%" align="left"><input type="text" name="data_pac" value="" size="10"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Nome:</td>
                                <td align="left"><span id="sprytextfield2">
                                  <input name="nome_pac" type="text" value="" size="20" maxlength="255">
                                <span class="textfieldRequiredMsg">Obrigatorio.</span></span></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">CPF:</td>
                                <td align="left"><span id="sprytextfield1">
                                <input name="cpf_pac" type="text" value="" size="10" maxlength="20">
                                <span class="textfieldRequiredMsg">Obrigatorio.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Endereço:</td>
                                <td align="left"><span id="sprytextfield3">
                                  <input name="end_pac" type="text" value="" size="20" maxlength="255">
                                <span class="textfieldRequiredMsg">Obrigatorio.</span></span></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Bairro:</td>
                                <td align="left"><input name="bairro_pac" type="text" value="" size="20" maxlength="80"></td>
                              </tr>                             
                              <tr valign="baseline">
                                <td nowrap align="right">Estado:</td>
                                <td align="left">
                                  <select name="uf_pac" id="uf_pac">
                                    <option value="">--Selecione--</option>
                                    <?php
										do {  
										?>
										<option value="<?php echo $row_cs_estados['est_id']?>"><?php echo $row_cs_estados['est_sigla']?></option>
										<?php
										} while ($row_cs_estados = mysql_fetch_assoc($cs_estados));
										  $rows = mysql_num_rows($cs_estados);
										  if($rows > 0) {
											  mysql_data_seek($cs_estados, 0);
											  $row_cs_estados = mysql_fetch_assoc($cs_estados);
										  }
										?>
                                  </select></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Cidade:</td>
                                <td align="left">                                  
                                  <select name="cidade_pac" id="cidade_pac">
                                  <option value="0">-- Escolha um estado --</option>
                                </select></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Cep:</td>
                                <td align="left"><input type="text" name="cep_pac" value="" size="10"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Telefone:</td>
                                <td align="left"><input type="text" name="fone_pac" value="" size="10"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Email:</td>
                                <td align="left"><input name="email_pac" type="text" value="" size="20" maxlength="255"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Convênio:</td>
                                <td align="left">
                                  <select name="conv_id" id="conv_id">
                                  <option value="">--Selecione--</option>
                                    <?php
									do {  
									?>
									<option value="<?php echo $row_cs_convenio['id_conv']?>"><?php echo $row_cs_convenio['nome_conv']?></option>
									<?php
									} while ($row_cs_convenio = mysql_fetch_assoc($cs_convenio));
									  $rows = mysql_num_rows($cs_convenio);
									  if($rows > 0) {
										  mysql_data_seek($cs_convenio, 0);
										  $row_cs_convenio = mysql_fetch_assoc($cs_convenio);
									  }
									?>
                                </select></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Situação:</td>
                                <td align="left">
                                  <select name="situacao_pac" id="situacao_pac">
                                  <option value="">--Selecione--</option>
                                    <?php
										do {  
										?>
									<option value="<?php echo $row_cs_situacao['id_situa']?>"><?php echo $row_cs_situacao['tipo_situa']?></option>
										<?php
										} while ($row_cs_situacao = mysql_fetch_assoc($cs_situacao));
										  $rows = mysql_num_rows($cs_situacao);
										  if($rows > 0) {
											  mysql_data_seek($cs_situacao, 0);
											  $row_cs_situacao = mysql_fetch_assoc($cs_situacao);
										  }
										?>
                                </select></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">Usuário:</td>
                                <td align="left"><input type="text" name="cadastrador_pac" value="<?php  echo $_SESSION['MM_Username'];?>" 
                                            size="20" maxlength="40" readonly="readonly"></td>
                              </tr>
                              <tr valign="baseline">
                                <td nowrap align="right">&nbsp;</td>
                                <td align="left"><input type="submit" class="btn-success" value="Gravar"></td>
                              </tr>
                            </table>
                            <input type="hidden" name="MM_insert" value="form1">
                          </form>
                          
                         </div>                                					
                        </div>
                        <div class="clearfix"></div>
                        </div>
                        </div>
                        </div>

		<!-- Rodapé -->
		
		<?php include('../restrito/rodape01.html');?>
        

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom", {pattern:"xxx.xxx.xxx-xx", validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
</script>
</body>
</html>
<?php
mysql_free_result($cs_convenio);

mysql_free_result($cs_situacao);

mysql_free_result($cs_estados);
?>
