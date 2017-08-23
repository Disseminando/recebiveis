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
$MM_authorizedUsers = "1";
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
	$data = $_POST['data_lanc'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];
	
	$valor = $_POST['valor_lanc'];
	$valor = str_replace(',', '.',$valor);
	
	$valor2 = $_POST['tx_mat_med'];
	$valor2 = str_replace(',', '.',$valor2);
	$insertSQL = sprintf("INSERT INTO lancamento (data_lanc, clin_id_lanc, med_id_lanc, tipo_id_age, pac_id_lanc, situa_age, tipo_id_pag, conv_id_lanc, proc_id_lanc, valor_lanc, tx_mat_med, obs_lanc, cadastrador_lanc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data, "date"),
                       GetSQLValueString($_POST['clin_id_lanc'], "int"),
                       GetSQLValueString($_POST['med_id_lanc'], "int"),
                       GetSQLValueString($_POST['tipo_id_age'], "int"),
                       GetSQLValueString($_POST['pac_id_lanc'], "int"),
                       GetSQLValueString($_POST['situa_age'], "int"),
                       GetSQLValueString($_POST['tipo_id_pag'], "int"),
                       GetSQLValueString($_POST['conv_id_lanc'], "int"),
                       GetSQLValueString($_POST['proc_id_lanc'], "int"),
                       GetSQLValueString($valor, "double"),
                       GetSQLValueString($valor2, "double"),
                       GetSQLValueString($_POST['obs_lanc'], "text"),
                       GetSQLValueString($_POST['cadastrador_lanc'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "principal_Adm01.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_rec01, $rec01);
$query_cs_clinica = "SELECT id_clin, nome_clin FROM clinica ORDER BY nome_clin ASC";
$cs_clinica = mysql_query($query_cs_clinica, $rec01) or die(mysql_error());
$row_cs_clinica = mysql_fetch_assoc($cs_clinica);
$totalRows_cs_clinica = mysql_num_rows($cs_clinica);

mysql_select_db($database_rec01, $rec01);
$query_cs_tipo = "SELECT id_tp_age, tipo_age FROM agenda_tipo ORDER BY tipo_age ASC";
$cs_tipo = mysql_query($query_cs_tipo, $rec01) or die(mysql_error());
$row_cs_tipo = mysql_fetch_assoc($cs_tipo);
$totalRows_cs_tipo = mysql_num_rows($cs_tipo);

mysql_select_db($database_rec01, $rec01);
$query_cs_paciente = "SELECT id_pac, nome_pac FROM paciente ORDER BY nome_pac ASC";
$cs_paciente = mysql_query($query_cs_paciente, $rec01) or die(mysql_error());
$row_cs_paciente = mysql_fetch_assoc($cs_paciente);
$totalRows_cs_paciente = mysql_num_rows($cs_paciente);

mysql_select_db($database_rec01, $rec01);
$query_cs_agenda_situacao = "SELECT id_sit_age, tipo_sit_age FROM agenda_situacao ORDER BY tipo_sit_age ASC";
$cs_agenda_situacao = mysql_query($query_cs_agenda_situacao, $rec01) or die(mysql_error());
$row_cs_agenda_situacao = mysql_fetch_assoc($cs_agenda_situacao);
$totalRows_cs_agenda_situacao = mysql_num_rows($cs_agenda_situacao);

mysql_select_db($database_rec01, $rec01);
$query_cs_tipo_pagamento = "SELECT id_pag, nome_pag FROM tpo_pagamento ORDER BY nome_pag ASC";
$cs_tipo_pagamento = mysql_query($query_cs_tipo_pagamento, $rec01) or die(mysql_error());
$row_cs_tipo_pagamento = mysql_fetch_assoc($cs_tipo_pagamento);
$totalRows_cs_tipo_pagamento = mysql_num_rows($cs_tipo_pagamento);

mysql_select_db($database_rec01, $rec01);
$query_cs_convenio = "SELECT id_conv, nome_conv FROM convenio ORDER BY nome_conv ASC";
$cs_convenio = mysql_query($query_cs_convenio, $rec01) or die(mysql_error());
$row_cs_convenio = mysql_fetch_assoc($cs_convenio);
$totalRows_cs_convenio = mysql_num_rows($cs_convenio);

mysql_select_db($database_rec01, $rec01);
$query_cs_procedimentos = "SELECT id_proc, nome_proc FROM procedimentos ORDER BY nome_proc ASC";
$cs_procedimentos = mysql_query($query_cs_procedimentos, $rec01) or die(mysql_error());
$row_cs_procedimentos = mysql_fetch_assoc($cs_procedimentos);
$totalRows_cs_procedimentos = mysql_num_rows($cs_procedimentos);
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
            
            <!-- Incio da Função Calendario -->
         <style>
			.dia {font-family: helvetica, arial; font-size: 8pt; color: #FFFFFF}
			.data {font-family: helvetica, arial; font-size: 8pt; text-decoration:none; color:#191970}
			.mes {font-family: helvetica, arial; font-size: 8pt}
			.Cabecalho_Calendario {font-family: helvetica, arial; font-size: 10pt; color: #000000; text-decoration:none; font-weight:bold}
		</style>
        <script language='Javascript'>
 
			// construindo o calendário
			function popdate(obj,div,tam,ddd)
			{
				if (ddd) 
				{
					day = ""
					mmonth = ""
					ano = ""
					c = 1
					char = ""
					for (s=0;s<parseInt(ddd.length);s++)
					{
						char = ddd.substr(s,1)
						if (char == "/") 
						{
							c++; 
							s++; 
							char = ddd.substr(s,1);
						}
						if (c==1) day    += char
						if (c==2) mmonth += char
						if (c==3) ano    += char
					}
					ddd = mmonth + "/" + day + "/" + ano
				}
			  
				if(!ddd) {today = new Date()} else {today = new Date(ddd)}
				date_Form = eval (obj)
				if (date_Form.value == "") { date_Form = new Date()} else {date_Form = new Date(date_Form.value)}
			  
				ano = today.getFullYear();
				mmonth = today.getMonth ();
				day = today.toString ().substr (8,2)
			  
				umonth = new Array ("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro")
				days_Feb = (!(ano % 4) ? 29 : 28)
				days = new Array (31, days_Feb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31)
			 
				if ((mmonth < 0) || (mmonth > 11))  alert(mmonth)
				if ((mmonth - 1) == -1) {month_prior = 11; year_prior = ano - 1} else {month_prior = mmonth - 1; year_prior = ano}
				if ((mmonth + 1) == 12) {month_next  = 0;  year_next  = ano + 1} else {month_next  = mmonth + 1; year_next  = ano}
				txt  = "<table bgcolor='#efefff' style='border:solid #330099; border-width:2' cellspacing='0' cellpadding='3' border='0' width='"+tam+"' height='"+tam*1.1 +"'>"
				txt += "<tr bgcolor='#FFFFFF'><td colspan='7' align='center'><table border='0' cellpadding='0' width='100%' bgcolor='#FFFFFF'><tr>"
				txt += "<td width=20% align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+((mmonth+1).toString() +"/01/"+(ano-1).toString())+"') class='Cabecalho_Calendario' title='Ano Anterior'><<</a></td>"
				txt += "<td width=20% align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+( "01/" + (month_prior+1).toString() + "/" + year_prior.toString())+"') class='Cabecalho_Calendario' title='Mês Anterior'><</a></td>"
				txt += "<td width=20% align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+( "01/" + (month_next+1).toString()  + "/" + year_next.toString())+"') class='Cabecalho_Calendario' title='Próximo Mês'>></a></td>"
				txt += "<td width=20% align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+((mmonth+1).toString() +"/01/"+(ano+1).toString())+"') class='Cabecalho_Calendario' title='Próximo Ano'>>></a></td>"
				txt += "<td width=20% align=right><a href=javascript:force_close('"+div+"') class='Cabecalho_Calendario' title='Fechar Calendário'><b>X</b></a></td></tr></table></td></tr>"
				txt += "<tr><td colspan='7' align='right' bgcolor='#ccccff' class='mes'><a href=javascript:pop_year('"+obj+"','"+div+"','"+tam+"','" + (mmonth+1) + "') class='mes'>" + ano.toString() + "</a>"
				txt += " <a href=javascript:pop_month('"+obj+"','"+div+"','"+tam+"','" + ano + "') class='mes'>" + umonth[mmonth] + "</a> <div id='popd' style='position:absolute'></div></td></tr>"
				txt += "<tr bgcolor='#330099'><td width='14%' class='dia' align=center><b>Dom</b></td><td width='14%' class='dia' align=center><b>Seg</b></td><td width='14%' class='dia' align=center><b>Ter</b></td><td width='14%' class='dia' align=center><b>Qua</b></td><td width='14%' class='dia' align=center><b>Qui</b></td><td width='14%' class='dia' align=center><b>Sex<b></td><td width='14%' class='dia' align=center><b>Sab</b></td></tr>"
				today1 = new Date((mmonth+1).toString() +"/01/"+ano.toString());
				diainicio = today1.getDay () + 1;
				week = d = 1
				start = false;
			 
				for (n=1;n<= 42;n++) 
				{
					if (week == 1)  txt += "<tr bgcolor='#efefff' align=center>"
					if (week==diainicio) {start = true}
					if (d > days[mmonth]) {start=false}
					if (start) 
					{
						dat = new Date((mmonth+1).toString() + "/" + d + "/" + ano.toString())
						day_dat   = dat.toString().substr(0,10)
						day_today  = date_Form.toString().substr(0,10)
						year_dat  = dat.getFullYear ()
						year_today = date_Form.getFullYear ()
						colorcell = ((day_dat == day_today) && (year_dat == year_today) ? " bgcolor='#FFCC00' " : "" )
						txt += "<td"+colorcell+" align=center><a href=javascript:block('"+  d + "/" + (mmonth+1).toString() + "/" + ano.toString() +"','"+ obj +"','" + div +"') class='data'>"+ d.toString() + "</a></td>"
						d ++ 
					} 
					else 
					{ 
						txt += "<td class='data' align=center> </td>"
					}
					week ++
					if (week == 8) 
					{ 
						week = 1; txt += "</tr>"} 
					}
					txt += "</table>"
					div2 = eval (div)
					div2.innerHTML = txt 
			}
			  
			// função para exibir a janela com os meses
			function pop_month(obj, div, tam, ano)
			{
			  txt  = "<table bgcolor='#CCCCFF' border='0' width=80>"
			  for (n = 0; n < 12; n++) { txt += "<tr><td align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+("01/" + (n+1).toString() + "/" + ano.toString())+"')>" + umonth[n] +"</a></td></tr>" }
			  txt += "</table>"
			  popd.innerHTML = txt
			}
			 
			// função para exibir a janela com os anos
			function pop_year(obj, div, tam, umonth)
			{
			  txt  = "<table bgcolor='#CCCCFF' border='0' width=160>"
			  l = 1
			  for (n=1991; n<2012; n++)
			  {  if (l == 1) txt += "<tr>"
				 txt += "<td align=center><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+(umonth.toString () +"/01/" + n) +"')>" + n + "</a></td>"
				 l++
				 if (l == 4) 
					{txt += "</tr>"; l = 1 } 
			  }
			  txt += "</tr></table>"
			  popd.innerHTML = txt 
			}
			 
			// função para fechar o calendário
			function force_close(div) 
				{ div2 = eval (div); div2.innerHTML = ''}
				
			// função para fechar o calendário e setar a data no campo de data associado
			function block(data, obj, div)
			{ 
				force_close (div)
				obj2 = eval(obj)
				obj2.value = data 
			}
			 
			</script>       
          <!-- Fim da Função Calendario -->
          
	  <script src="http://www.google.com/jsapi"></script>
          <script type="text/javascript">google.load('jquery', '1.3');</script>
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
					
					$.getJSON(paginaPhpCidades+'?search=',{clin_id_lanc: $(this).val(), ajax: 'true'}, function(j){
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
			$("#clin_id_lanc option:first").attr('selected','selected');
			// Aqui eu chamo a função e o método que irá carregá-la
			$('#clin_id_lanc').change(function(){ $(this).carregaCidades({idSelectCidade: '#med_id_lanc'}); })
		});
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
                                       
                      <!--Função para ocultar campo procedimento caso opção seja consulta-->
					  <script type="text/javascript">
								function Ocultar2(valor2){
									document.getElementById('tipo_age');
									document.getElementById('proc_id_lanc');
												
								if(valor2 == "1")
								{
								document.getElementById("proc_id_lanc").style.display = "none";
								}else{
								document.getElementById("proc_id_lanc").style.display = "block";	
								}
						}
					  </script>
                      <!--Fim da Função.-->
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
						 <br>                          
			             <div class="w3-container">
                           <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                             <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10" class="form-group">
                               <tr valign="baseline">
                                 <td width="12%" align="right" nowrap>Data:</td>
                                 <td width="88%" align="left">
                                 <input type="text" name="data_lanc" value="" size="10">
                                 <input TYPE="button" NAME="btndata_lanc" VALUE="..." Onclick="javascript:popdate('document.form1.data_lanc','pop1','150',document.form1.data_lanc.value)">
                                 <span id="pop1" style="position:absolute"></span> 
                                 </td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Clínica:</td>
                                 <td align="left">
                                   <select name="clin_id_lanc" id="clin_id_lanc">
                                   <option value="0">Selecione...</option>
                                     <?php
										do {  
										?>
									 <option value="<?php echo $row_cs_clinica['id_clin']?>"><?php echo $row_cs_clinica['nome_clin']?></option>
										<?php
										} while ($row_cs_clinica = mysql_fetch_assoc($cs_clinica));
										  $rows = mysql_num_rows($cs_clinica);
										  if($rows > 0) {
											  mysql_data_seek($cs_clinica, 0);
											  $row_cs_clinica = mysql_fetch_assoc($cs_clinica);
										  }
										?>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Médico:</td>
                                 <td align="left">
                                   <select name="med_id_lanc" id="med_id_lanc">
                                   <option value="">--Escolha uma Clinica--</option>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Tipo:</td>
                                 <td align="left">
                                   <select name="tipo_id_age" id="tipo_id_age" onChange="Ocultar2(this.value)">
                                   <option value="0">Selecione...</option>
                                     <?php
										do {  
										?>
										<option value="<?php echo $row_cs_tipo['id_tp_age']?>"><?php echo $row_cs_tipo['tipo_age']?></option><br>
										<?php
										} while ($row_cs_tipo = mysql_fetch_assoc($cs_tipo));
										  $rows = mysql_num_rows($cs_tipo);
										  if($rows > 0) {
											  mysql_data_seek($cs_tipo, 0);
											  $row_cs_tipo = mysql_fetch_assoc($cs_tipo);
										  }
										?>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Paciente:</td>
                                 <td align="left">
                                   <select name="pac_id_lanc" id="pac_id_lanc">
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
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Situação:</td>
                                 <td align="left"><label for="situa_age"></label>
                                   <select name="situa_age" id="situa_age">
                                   <option value="0">Selecione...</option>
                                     <?php
										do {  
										?>
									 <option value="<?php echo $row_cs_agenda_situacao['id_sit_age']?>">
									 <?php echo $row_cs_agenda_situacao['tipo_sit_age']?></option>
										<?php
										} while ($row_cs_agenda_situacao = mysql_fetch_assoc($cs_agenda_situacao));
										  $rows = mysql_num_rows($cs_agenda_situacao);
										  if($rows > 0) {
											  mysql_data_seek($cs_agenda_situacao, 0);
											  $row_cs_agenda_situacao = mysql_fetch_assoc($cs_agenda_situacao);
										  }
										?>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Pagamento:</td>
                                 <td align="left">
                                   <select name="tipo_id_pag" id="tipo_id_pag" >
                                    <option value="0">Selecione...</option>
                                     <?php
										do {  
										?>
										<option value="<?php echo $row_cs_tipo_pagamento['id_pag']?>">
										<?php echo $row_cs_tipo_pagamento['nome_pag']?></option>
										<?php
										} while ($row_cs_tipo_pagamento = mysql_fetch_assoc($cs_tipo_pagamento));
										  $rows = mysql_num_rows($cs_tipo_pagamento);
										  if($rows > 0) {
											  mysql_data_seek($cs_tipo_pagamento, 0);
											  $row_cs_tipo_pagamento = mysql_fetch_assoc($cs_tipo_pagamento);
										  }
										?>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Convênio:</td>
                                 <td align="left">
                                   <select name="conv_id_lanc" id="conv_id_lanc">
                                   <option value="0">Selecione...</option>
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
                                 <td nowrap align="right">Procecimento:</td>
                                 <td align="left">
                                   <select name="proc_id_lanc" id="proc_id_lanc">
                                   <option value="0">Selecione...</option>
                                     <?php
										do {  
										?>
										<option value="<?php echo $row_cs_procedimentos['id_proc']?>">
										<?php echo $row_cs_procedimentos['nome_proc']?></option>
                                        <?php
										} while ($row_cs_procedimentos = mysql_fetch_assoc($cs_procedimentos));
										  $rows = mysql_num_rows($cs_procedimentos);
										  if($rows > 0) {
											  mysql_data_seek($cs_procedimentos, 0);
											  $row_cs_procedimentos = mysql_fetch_assoc($cs_procedimentos);
										  }
										?>
                                 </select></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Valor R$:</td>
                                 <td align="left"><input type="text" name="valor_lanc" id="valor_lanc" value="" size="10"></td>
                               </tr>
                               <tr valign="baseline">
                                 <td nowrap align="right">Taxa material:</td>
                                 <td align="left"><input type="text" name="tx_mat_med" id="tx_mat_med" value="0,00" size="10"></td>
                               </tr>
                               <tr valign="baseline">
                                 <td align="right" valign="top" nowrap>Observação:</td>
                                 <td align="left">
                                 <textarea name="obs_lanc" id="obs_lanc" cols="30" rows="3"></textarea></td>
                               </tr>
                               <tr valign="baseline">
                                 <td height="30" align="right" nowrap>Cadastrador:</td>
                                 <td align="left"><input type="text" name="cadastrador_lanc" value="<?php  echo $_SESSION['MM_Username'];?>" 
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
                        </div>
                            </div>                
                            </div>

		<!-- Rodapé -->
		
		<?php include('../restrito/rodape01.html');?>
        
</body>
</html>
<?php
mysql_free_result($cs_clinica);

mysql_free_result($cs_tipo);

mysql_free_result($cs_paciente);

mysql_free_result($cs_agenda_situacao);

mysql_free_result($cs_tipo_pagamento);

mysql_free_result($cs_convenio);

mysql_free_result($cs_procedimentos);
?>
