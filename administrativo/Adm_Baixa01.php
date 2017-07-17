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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$data = $_POST['data_bax'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];
	$valor = $_POST['valor_bax'];
	$valor = str_replace(',', '.',$valor);
	$taxa = $_POST['imp_bax'];
	$taxa = str_replace(',', '.',$taxa);
  $insertSQL = sprintf("INSERT INTO baixa (data_bax, clin_id_bax, cod_lanc_bax, med_id_bax, valor_bax, imp_bax, cadastrador_bax) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data, "date"),
                       GetSQLValueString($_POST['clin_id_bax'], "int"),
                       GetSQLValueString($_POST['cod_lanc_bax'], "int"),
                       GetSQLValueString($_POST['med_id_bax'], "int"),
                       GetSQLValueString($valor, "decimal"),
                       GetSQLValueString($taxa, "decimal"),
                       GetSQLValueString($_POST['cadastrador_bax'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "../restrito/principal.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_rec01, $rec01);
$query_cs_clinicas = "SELECT id_clin, nome_clin FROM clinica ORDER BY nome_clin ASC";
$cs_clinicas = mysql_query($query_cs_clinicas, $rec01) or die(mysql_error());
$row_cs_clinicas = mysql_fetch_assoc($cs_clinicas);
$totalRows_cs_clinicas = mysql_num_rows($cs_clinicas);
?>
<!doctype html>
<html>
        <head>
		<title>:..Recebiveis..:</title>
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
        <script src="http://www.google.com/jsapi"></script>
        <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <script type="text/javascript">
			google.load('jquery', '1.3');
		</script>
		<script type="text/javascript">

								$(document).ready(function(){
									
									jQuery.fn.carregaCidades = function() {
										
										// Objeto que guarda os argumentos
										var args 					= arguments[0] || {};
										
										//id do Select de Cidades
										var idSelectCidade 			= args.idSelectCidade;
										
										// Página que irá criar o JSon
										var paginaPhpCidades		= 'baixa.ajax.php';
										
										// Conteúdo do elemento span que vai aparecer enquanto carregam as cidades, 
										// pode ser substituído por uma imagem. Coloque a tag completa	
										var carregandoMsg			= 'Aguarde, carregando...' 	
										
										// Classe do elemento span que vai aparecer enquanto carregam as cidades
										var carregandoClass			= 'class';
										// após as cidades carregarem aparece esta mensagem
										var jsonPrimeiroElemento 	= '(selecione....)';
										// Aqui eu pego a frase do primeiro option de Cidade  
										var primeiroElemento		= $(idSelectCidade).find('option:first').html();
										
										
										if( $(this).val() ) {
											// escondendo as cidades até carregarem
											$(idSelectCidade).hide();
											// mensagem de espera: carregando
											$(idSelectCidade).after('<span class='+ carregandoClass +'>'+carregandoMsg+'</span>');
											
											$.getJSON(paginaPhpCidades+'?search=',{clin_id_bax: $(this).val(), ajax: 'true'}, function(j){
													// É importante que o value seja vazio pra que o formulário não seja enviado vazio
													// caso use o form validate
												var options = '<option value="">'+jsonPrimeiroElemento+'</option>';    
												for (var i = 0; i < j.length; i++) {
													// É importante que o value seja vazio pra que o formulário não seja enviado vazio
													// caso use o form validate
													options += '<option value="' + j[i].id_med + '">' + j[i].nome_med + '</option>';
												} 
												// mostrando as cidades após carregarem e removendo a mensagem de espera
												$(idSelectCidade).html(options).show();
												$(idSelectCidade).next().remove();
											});
										} else {
											$(idSelectCidade).html('<option value="">'+primeiroElemento+'</option>');
										}
										
									};
									//Inciando o SELECT, importante ao recarregar a página
									$("#clin_id_bax option:first").attr('selected','selected');
									// Aqui eu chamo a função e o método que irá carregá-la
									$('#clin_id_bax').change(function(){ $(this).carregaCidades({idSelectCidade: '#med_id_bax'}); })
								});
							</script>
        <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
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
                <?php include('../restrito/identifica01.php');?>
                <?php include('../restrito/Menu_lateral.php');?> 
                <div class="col-md-8 ser-fet">
						<h3>:..Baixa - Cadastro..:</h3>
						<span class="line"></span>
						<div class="features">
							<div class="col-md-6 fet-pad">
								<div class="div-margin">
									<div class="col-md-9 fet-pad wid2">
                                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                        <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
                                          <tr valign="baseline">
                                            <td width="12%" align="right" nowrap>Data:</td>
                                            <td width="88%"><span id="sprytextfield1">
                                            <input type="text" name="data_bax" value="" size="10">
                                            <span class="textfieldRequiredMsg">Um valor é necessário.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">Clínica:</td>
                                            <td>
                                              <select name="clin_id_bax" id="clin_id_bax">
                                              <option value="">-- Escolha --</option>
                                                <?php
													do {  
													?>
												<option value="<?php echo $row_cs_clinicas['id_clin']?>"><?php echo $row_cs_clinicas['nome_clin']?></option>											<?php
													} while ($row_cs_clinicas = mysql_fetch_assoc($cs_clinicas));
													  $rows = mysql_num_rows($cs_clinicas);
													  if($rows > 0) {
														  mysql_data_seek($cs_clinicas, 0);
														  $row_cs_clinicas = mysql_fetch_assoc($cs_clinicas);
													  }
													?>
                                            </select></td>
                                          </tr>                                          
                                          <tr valign="baseline">
                                            <td nowrap align="right">Médico:</td>
                                            <td>
                                              <select name="med_id_bax" id="med_id_bax">
                                              	<option value="">-- Escolha uma Clinica --</option>
                                            </select></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">Lançamento:</td>
                                            <td><span id="sprytextfield2">
                                            <input name="cod_lanc_bax" type="text" id="cod_lanc_bax" size="10" maxlength="5">
                                            <span class="textfieldRequiredMsg">Um valor é necessário.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">Valor R$:</td>
                                            <td><span id="sprytextfield3">
                                            <input type="text" name="valor_bax" value="" size="10">
                                            <span class="textfieldRequiredMsg">Um valor é necessário.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">Imposto %:</td>
                                            <td><span id="sprytextfield4">
                                            <input type="text" name="imp_bax" value="" size="10">
                                            <span class="textfieldRequiredMsg">Um valor é necessário.</span><span class="textfieldInvalidFormatMsg">Formato inválido.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">Cadastrador:</td>
                                            <td><input type="text" name="cadastrador_bax" value="<?php  echo $_SESSION['MM_Username'];?>" size="20" maxlength="20" readonly="readonly"></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">&nbsp;</td>
                                            <td><input type="submit" value="Gravar"></td>
                                          </tr>
                                        </table>
                                        <input type="hidden" name="MM_insert" value="form1">
                                      </form>
                                      
                                      
                                                                
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
		
		<?php include('../restrito/rodape01.html');?>

<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "date", {format:"dd/mm/yyyy", validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "currency", {format:"dot_comma", validateOn:["blur", "change"], useCharacterMasking:true});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "currency", {format:"dot_comma", validateOn:["blur", "change"], useCharacterMasking:true});
</script>
</body>
</html>
<?php
mysql_free_result($cs_clinicas);
?>