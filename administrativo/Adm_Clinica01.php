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
$MM_authorizedUsers = "1,2,4";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$data = $_POST['data_clin'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];	
	$imposto = $_POST['imposto_clin'];
	$imposto = str_replace(',', '.',$imposto);	
	$desconto = $_POST['desconto_clin'];
	$desconto = str_replace(',', '.',$desconto);
  $insertSQL = sprintf("INSERT INTO clinica (data_clin, nome_clin, cnpj_clin, imposto_clin, desconto_clin, fone_clin, email_clin, situacao_clin, contato_clin, cadastrador_clin) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data, "date"),
					   GetSQLValueString($_POST['nome_clin'], "text"),
                       GetSQLValueString($_POST['cnpj_clin'], "text"),
					   GetSQLValueString($imposto, "double"),
					   GetSQLValueString($desconto, "double"),
                       GetSQLValueString($_POST['fone_clin'], "text"),
                       GetSQLValueString($_POST['email_clin'], "text"),
                       GetSQLValueString($_POST['situacao_clin'], "int"),
                       GetSQLValueString($_POST['contato_clin'], "text"),
                       GetSQLValueString($_POST['cadastrador_clin'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "Principal_Adm01.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_rec01, $rec01);
$query_cs_situacao = "SELECT id_situa, tipo_situa FROM situacao";
$cs_situacao = mysql_query($query_cs_situacao, $rec01) or die(mysql_error());
$row_cs_situacao = mysql_fetch_assoc($cs_situacao);
$totalRows_cs_situacao = mysql_num_rows($cs_situacao);
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
        <link href='http://fonts.googleapis.com/css?family=Monda:400,700' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,300,100,700' rel='stylesheet' type='text/css'>
		<link href="../css/owl.carousel.css" rel="stylesheet">
        <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/move-top.js"></script>
        <script type="text/javascript" src="../js/easing.js"></script>
        <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
        <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
        <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
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
					<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand logo-st" href="#">Recebiveis</a>
					</div>
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
					</div>
					<div class="clearfix"></div>
				  </div>
				</nav>
            </div>			
		</div>	
		        <?php include('../restrito/identifica01.php');?>
			   <?php include('../restrito/Menu_lateral.php');?>
                    <div class="col-md-8 ser-fet">
						<h3>:..Clínica - Cadastro..:</h3>
						<span class="line"></span>                           
			             <div class="w3-container">
                                <br>
                       <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                         <table width="100%" align="center" cellpadding="10" cellspacing="20">
                           <tr valign="baseline">
                             <td width="9%" align="right" nowrap><strong>Data:</strong></td>
                             <td width="91%" align="left"><span id="sprytextfield6">
                             <input name="data_clin" type="text" value="" size="10">
                             <span class="textfieldRequiredMsg">Obrigatorio.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td width="9%" align="right" nowrap><strong>Nome:</strong></td>
                       <td width="91%" align="left"><span id="sprytextfield1">
                               <input name="nome_clin" type="text" value="" size="30" maxlength="255">
                             <span class="textfieldRequiredMsg">Obrigatório.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>CNPJ:</strong></td>
                             <td align="left"><span id="sprytextfield2">
                             <input name="cnpj_clin" type="text" value="" size="20" maxlength="40">
                             <span class="textfieldRequiredMsg">Obrigatório.</span><br>
                             <span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>IMPOSTO %:</strong></td>
                             <td align="left"><span id="sprytextfield7">
                             <input name="imposto_clin" type="text" value="" size="10">
                            <span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>DESCONTO %:</strong></td>
                             <td align="left"><span id="sprytextfield8">
                             <input name="desconto_clin" type="text" value="" size="10">
                            <span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>Telefone:</strong></td>
                             <td align="left"><span id="sprytextfield3">
                             <input name="fone_clin" type="text" value="" size="20" maxlength="20">
                             <span class="textfieldRequiredMsg">Obrigatório.</span><br>
                             <span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>Email:</strong></td>
                             <td align="left"><span id="sprytextfield4">
                             <input name="email_clin" type="text" value="" size="30" maxlength="255">
                             <span class="textfieldRequiredMsg">Obrigatório.</span><br>
                             <span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>Situação:</strong></td>
                       <td align="left"><span id="spryselect1">
                               <select name="situacao_clin" id="situacao_clin">
                                 <option value="0">Selecione....</option>
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
                               </select>
                             <span class="selectInvalidMsg">Invalido.</span><span class="selectRequiredMsg">.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right"><strong>Contato:</strong></td>
                       <td align="left"><span id="sprytextfield5">
                               <input name="contato_clin" type="text" size="30" maxlength="255">
                             <span class="textfieldRequiredMsg">Obrigatório.</span></span></td>
                           </tr>
                           <tr valign="baseline">
                             <td height="22" align="right" nowrap><strong>Usuário:</strong></td>
                             <td align="left"><input name="cadastrador_clin" type="text" value="<?php  echo $_SESSION['MM_Username'];?>" 
                             size="20" maxlength="20" readonly="readonly">
                             </td>
                           </tr>
                           <tr valign="baseline">
                             <td nowrap align="right">&nbsp;</td>
                             <td align="left"><input type="submit" class="btn-success" value="Gravar"></td>
                           </tr>
                         </table>
                         <input type="hidden" name="MM_insert" value="form1">
                      </form>
					</div>
					<div class="clearfix"></div>
	</div>
			</div>
		
</div>

		<!-- Rodapé -->
		
		<?php include('../restrito/rodape01.html');?>
		
		<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
		var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom", {useCharacterMasking:true, validateOn:["blur", "change"], pattern:"xx.xxx.xxx/xxxx-xx"});
		var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "custom", {pattern:"(xx)xxxxx-xxxx", validateOn:["blur", "change"], useCharacterMasking:true});
		var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "email", {validateOn:["blur", "change"]});
		var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
		var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "date", {format:"dd/mm/yyyy", validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "currency", {format:"dot_comma", isRequired:false, validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "currency", {format:"dot_comma", isRequired:false, validateOn:["blur", "change"], useCharacterMasking:true});
        </script>
	</body>
</html>
<?php
mysql_free_result($cs_situacao);
?>

