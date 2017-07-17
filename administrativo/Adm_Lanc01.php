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
$query_Recordset1 = "SELECT id_clin, nome_clin FROM clinica ORDER BY nome_clin ASC";
$Recordset1 = mysql_query($query_Recordset1, $rec01) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_rec01, $rec01);
$query_cs_paciente = "SELECT id_pac, nome_pac FROM paciente ORDER BY nome_pac ASC";
$cs_paciente = mysql_query($query_cs_paciente, $rec01) or die(mysql_error());
$row_cs_paciente = mysql_fetch_assoc($cs_paciente);
$totalRows_cs_paciente = mysql_num_rows($cs_paciente);

mysql_select_db($database_rec01, $rec01);
$query_cs_convenio = "SELECT id_conv, nome_conv FROM convenio ORDER BY nome_conv ASC";
$cs_convenio = mysql_query($query_cs_convenio, $rec01) or die(mysql_error());
$row_cs_convenio = mysql_fetch_assoc($cs_convenio);
$totalRows_cs_convenio = mysql_num_rows($cs_convenio);

mysql_select_db($database_rec01, $rec01);
$query_cs_procedimento = "SELECT DISTINCT nome_proc, id_proc FROM procedimentos ORDER BY nome_proc ASC";
$cs_procedimento = mysql_query($query_cs_procedimento, $rec01) or die(mysql_error());
$row_cs_procedimento = mysql_fetch_assoc($cs_procedimento);
$totalRows_cs_procedimento = mysql_num_rows($cs_procedimento);

mysql_select_db($database_rec01, $rec01);
$query_cs_pagamento = "SELECT id_pag, nome_pag FROM tpo_pagamento ORDER BY nome_pag ASC";
$cs_pagamento = mysql_query($query_cs_pagamento, $rec01) or die(mysql_error());
$row_cs_pagamento = mysql_fetch_assoc($cs_pagamento);
$totalRows_cs_pagamento = mysql_num_rows($cs_pagamento);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$data = $_POST['data_lanc'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];
	$valor = $_POST['valor_lanc'];
	$valor1 = str_replace('.', ',',$valor);
	$taxa = $_POST['tx_mat_med_lanc'];
	$taxa1 = str_replace('.', ',',$taxa);
  $insertSQL = sprintf("INSERT INTO lancamento (data_lanc, clin_id, med_id, pac_id, conv_id, proc_id, valor_lanc, pag_id, tx_mat_med_lanc, cadastrador_lanc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data, "date"),
                       GetSQLValueString($_POST['clin_id'], "int"),
                       GetSQLValueString($_POST['med_id'], "int"),
                       GetSQLValueString($_POST['pac_id'], "int"),
                       GetSQLValueString($_POST['conv_id'], "int"),
                       GetSQLValueString($_POST['proc_id'], "int"),
                       GetSQLValueString($valor, "double"),
                       GetSQLValueString($_POST['pag_id'], "int"),
                       GetSQLValueString($taxa1, "double"),
                       GetSQLValueString($_POST['cadastrador_lanc'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "../restrito/principal.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
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
	        <script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
			<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
			<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
            <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
			<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
			<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">         
			<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
			<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
			
			<style type="text/css">
			.carregando {
				color:#666;
				display:none;
			}
			</style>
            
            <script type="text/javascript">
		function taxa(valor) {
		if(valor=="Sim") {
		document.form1.tx_mat_med_lanc.style.display="block";
		
		}
		if(valor=="Nao") {
		document.form1.tx_mat_med_lanc.style.display="none";
		
		}
		}
		</script>
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
						<h3>:..Lançamento - Cadastro..:</h3>
						<span class="line"></span>
						<div class="features">
							<div class="col-md-6 fet-pad">
								<div class="div-margin">
									<div class="col-md-3 fet-pad wid">										
									</div>
									<div class="col-md-9 fet-pad wid2">
                                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                        <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
                                          <tr valign="baseline">
                                            <td width="10%" align="right" nowrap><strong>Data:</strong></td>
                                            <td colspan="2"><input type="text" name="data_lanc" value="<?php  
																	                                    $date = date('d/m/Y');
																										echo $date;
																										?>" size="10" readonly="readonly"></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Clínica:</strong></td>
                                            <td colspan="2"><span id="spryselect1">
                                              <select name="clin_id" id="clin_id">
                                                <option value="0">Selecione...</option>
                                                <?php
													do {  
													?>
                                                <option value="<?php echo $row_Recordset1['id_clin']?>"><?php echo $row_Recordset1['nome_clin']?></option>
                                                <?php
													} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
													  $rows = mysql_num_rows($Recordset1);
													  if($rows > 0) {
														  mysql_data_seek($Recordset1, 0);
														  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
													  }
													?>
                                              </select>
                                            <span class="selectInvalidMsg">Inválido.</span>
                                             <span class="selectRequiredMsg">.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Médico:</strong></td>
                                            <td colspan="2">
                                              <select name="med_id" id="med_id">
                                                  <option value="">-- Escolha uma Clinica --</option>
                                              </select>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Paciente:</strong></td>
                                            <td colspan="2"><span id="spryselect3">
                                              <select name="pac_id" id="pac_id">
                                                <option value="0">Selecione...</option>
                                                <?php
													do {  
													?>
                                                <option value="<?php echo $row_cs_paciente['id_pac']?>"><?php echo $row_cs_paciente['nome_pac']?></option>
                                                <?php
													} while ($row_cs_paciente = mysql_fetch_assoc($cs_paciente));
													  $rows = mysql_num_rows($cs_paciente);
													  if($rows > 0) {
														  mysql_data_seek($cs_paciente, 0);
														  $row_cs_paciente = mysql_fetch_assoc($cs_paciente);
													  }
													?>
                                              </select>
                                           <span class="selectInvalidMsg">Inválido.</span>
                                             <span class="selectRequiredMsg">.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Convênio:</strong></td>
                                            <td colspan="2"><span id="spryselect4">
                                              <select name="conv_id" id="conv_id">
                                                <option value="0">Selecione...</option>
                                                <?php
													do {  
													?>
                                                <option value="<?php echo $row_cs_convenio['id_conv']?>"> <?php echo $row_cs_convenio['nome_conv']?></option>
                                                <?php
													} while ($row_cs_convenio = mysql_fetch_assoc($cs_convenio));
													  $rows = mysql_num_rows($cs_convenio);
													  if($rows > 0) {
														  mysql_data_seek($cs_convenio, 0);
														  $row_cs_convenio = mysql_fetch_assoc($cs_convenio);
													  }
													?>
                                              </select>
                                            <span class="selectInvalidMsg">Inválido.</span>
                                             <span class="selectRequiredMsg">.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Procedimento:</strong></td>
                                            <td colspan="2"><span id="spryselect5">
                                              <select name="proc_id" id="proc_id">
                                                <option value="0">Selecione...</option>
                                                <?php
													do {  
													?>
                                                <option value="<?php echo $row_cs_procedimento['id_proc']?>"> <?php echo $row_cs_procedimento['nome_proc']?></option>
                                                <?php
													} while ($row_cs_procedimento = mysql_fetch_assoc($cs_procedimento));
													  $rows = mysql_num_rows($cs_procedimento);
													  if($rows > 0) {
														  mysql_data_seek($cs_procedimento, 0);
														  $row_cs_procedimento = mysql_fetch_assoc($cs_procedimento);
													  }
													?>
                                              </select>
                                            <span class="selectInvalidMsg">Inválido.</span>
                                             <span class="selectRequiredMsg">.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Valor R$:</strong></td>
                                            <td colspan="2"><span id="sprytextfield1">
                                              <input type="text" name="valor_lanc" value="" size="10">
                                            <span class="textfieldRequiredMsg">Um valor é necessário.</span></span></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Pagamento:</strong></td>
                                            <td colspan="2"><span id="spryselect6">
                                              <select name="pag_id" id="pag_id">
                                                <option value="0">Selecione...</option>
                                                <?php
													do {  
													?>
                                                <option value="<?php echo $row_cs_pagamento['id_pag']?>"><?php echo $row_cs_pagamento['nome_pag']?></option>
                                                <?php
													} while ($row_cs_pagamento = mysql_fetch_assoc($cs_pagamento));
													  $rows = mysql_num_rows($cs_pagamento);
													  if($rows > 0) {
														  mysql_data_seek($cs_pagamento, 0);
														  $row_cs_pagamento = mysql_fetch_assoc($cs_pagamento);
													  }
													?>
                                              </select>
                                            <span class="selectInvalidMsg">Inválido.</span>
                                             <span class="selectRequiredMsg">.</span></span></td>
                                          </tr> 
                                          <tr>
                                             <td></td>
                                             <td width="10%"><input type="radio" value="Sim" name="valor" onClick="taxa('Sim')" checked><font face="Arial Narrow" size="2" color="#696969">Sim </font>                                 
                                             <td width="76%"><input type="radio" value="Nao" name="valor" onClick="taxa('Nao')"><font face="Arial Narrow" size="2" color="#696969">Nao</font></td>                                             
                                             </td>
                                          </tr>                                         
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Taxa:</strong></td>
                                            <td colspan="2"><input type="text" name="tx_mat_med_lanc" value="0,00" size="10" onFocus="this.value=''" display:none;"></td>                                            
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Cadastrador:</strong></td>
                                            <td colspan="2"><input name="cadastrador_lanc" type="text" value="<?php  echo $_SESSION['MM_Username'];?>" 
                                             size="20" maxlength="40" readonly="readonly"></td> 
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">&nbsp;</td>
                                            <td colspan="2"><input type="submit" value="Gravar"></td>
                                          </tr>
                                        </table>
                                        <input type="hidden" name="MM_insert" value="form1">
                                      </form> 
									  
								<script src="http://www.google.com/jsapi"></script>
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
										var paginaPhpCidades		= 'cidades.ajax.php';
										
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
											
											$.getJSON(paginaPhpCidades+'?search=',{clin_id: $(this).val(), ajax: 'true'}, function(j){
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
									$("#clin_id option:first").attr('selected','selected');
									// Aqui eu chamo a função e o método que irá carregá-la
									$('#clin_id').change(function(){ $(this).carregaCidades({idSelectCidade: '#med_id'}); })
								});
							</script>

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
		var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"0", validateOn:["blur", "change"]});
		var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});		
		var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {invalidValue:"0", validateOn:["blur", "change"]});
		var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {invalidValue:"0", validateOn:["blur", "change"]});
		var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {invalidValue:"0", validateOn:["blur", "change"]});
		var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {invalidValue:"0", validateOn:["blur", "change"]});		
    	</script>

</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($cs_paciente);

mysql_free_result($cs_convenio);

mysql_free_result($cs_procedimento);

mysql_free_result($cs_pagamento);
?>