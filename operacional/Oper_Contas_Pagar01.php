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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$data = $_POST['data_ct_pag'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];
	
	$data1 = $_POST['dtvenc_ct_pag'];
	$data1 = explode("/", $data1);
    $data1 = $data1[2]."-".$data1[1]."-".$data1[0];
	
	$valor = $_POST['valor_ct_pag'];
	$valor = str_replace(',', '.',$valor);
  $insertSQL = sprintf("INSERT INTO contas_pagar (data_ct_pag, clin_id_ct_pag, conta_id_pag, doc_ct_pag, dtvenc_ct_pag, valor_ct_pag, dtpag_ct_pag, valorpag_ct_pag, desc_ct_pag, juros_ct_pag, obs_ct_pag, cadastrador_ct_pag) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($data, "date"),
                       GetSQLValueString($_POST['clin_id_ct_pag'], "int"),
                       GetSQLValueString($_POST['conta_id_pag'], "int"),
                       GetSQLValueString($_POST['doc_ct_pag'], "text"),
                       GetSQLValueString($data1, "date"),
                       GetSQLValueString($valor, "double"),
                       GetSQLValueString($_POST['dtpag_ct_pag'], "date"),
                       GetSQLValueString($_POST['valorpag_ct_pag'], "double"),
                       GetSQLValueString($_POST['desc_ct_pag'], "double"),
                       GetSQLValueString($_POST['juros_ct_pag'], "double"),
                       GetSQLValueString($_POST['obs_ct_pag'], "text"),
                       GetSQLValueString($_POST['cadastrador_ct_pag'], "text"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($insertSQL, $rec01) or die(mysql_error());

  $insertGoTo = "principal_Oper01.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$usuario=$_SESSION['MM_Username'];

mysql_select_db($database_rec01, $rec01);
$query_cs_clinica = "SELECT id_clin, nome_clin, usu_id, login, id_usu, clin_id FROM clinica, acesso, usuario WHERE login='$usuario' AND id_usu=usu_id AND id_clin=clin_id ORDER BY nome_clin ASC";
$cs_clinica = mysql_query($query_cs_clinica, $rec01) or die(mysql_error());
$row_cs_clinica = mysql_fetch_assoc($cs_clinica);
$totalRows_cs_clinica = mysql_num_rows($cs_clinica);

mysql_select_db($database_rec01, $rec01);
$query_cs_conta = "SELECT id_plan_ct, nome_plan_ct, id_clin, usu_id, login, id_usu
FROM plano_contas, clinica, acesso, usuario
WHERE clin_id_plan_ct=id_clin 
AND login='$usuario'
AND usu_id=id_usu
AND id_clin=clin_id
ORDER BY nome_plan_ct ASC";
$cs_conta = mysql_query($query_cs_conta, $rec01) or die(mysql_error());
$row_cs_conta = mysql_fetch_assoc($cs_conta);
$totalRows_cs_conta = mysql_num_rows($cs_conta);
?>
<!doctype html>
<html>
<!--Cabeçalho -->
<head>
		<title>:..Recebiveis..:</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Clinica, Hospital, Laboratorio, Medicos, Plano de Saude, Faturamento, Credenciamento" />
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
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
						<h3>:..Contas a Pagar - Cadastro..:</h3>
						<span class="line"></span>                           
			             <div class="w3-container">
                                <br>
                                <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                  <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
                                    <tr valign="baseline">
                                      <td width="13%" align="right" nowrap>Data:</td>
                                      <td width="87%"><input type="text" name="data_ct_pag" value="" size="10">
                                      <input TYPE="button" NAME="btndata_ct_pag" VALUE="..." Onclick="javascript:popdate('document.form1.data_ct_pag','pop1','150',document.form1.data_ct_pag.value)">
                                           <span id="pop1" style="position:absolute"></span>
                                      </td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td nowrap align="right">Clínica:</td>
                                      <td>
                                        <select name="clin_id_ct_pag" id="clin_id_ct_pag">
                                          <?php
												do {  
												?>
												 <option value="<?php echo $row_cs_clinica['id_clin']?>"><?php echo $row_cs_clinica['nome_clin']?></option> 										  <?php
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
                                      <td nowrap align="right">Conta:</td>
                                      <td>
                                        <select name="conta_id_pag" id="conta_id_pag">
                                          <?php
											do {  
											?>
											<option value="<?php echo $row_cs_conta['id_plan_ct']?>"><?php echo $row_cs_conta['nome_plan_ct']?></option>										     <?php
											} while ($row_cs_conta = mysql_fetch_assoc($cs_conta));
											  $rows = mysql_num_rows($cs_conta);
											  if($rows > 0) {
												  mysql_data_seek($cs_conta, 0);
												  $row_cs_conta = mysql_fetch_assoc($cs_conta);
											  }
											?>
                                      </select></td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td nowrap align="right">Documento Nº:</td>
                                      <td><input name="doc_ct_pag" type="text" value="" size="10" maxlength="6"></td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td nowrap align="right">Vencimento:</td>
                                      <td><input type="text" name="dtvenc_ct_pag" value="" size="10">
                                      <input TYPE="button" NAME="btndtvenc_ct_pag" VALUE="..." Onclick="javascript:popdate('document.form1.dtvenc_ct_pag','pop2','150',document.form1.dtvenc_ct_pag.value)">
                                           <span id="pop2" style="position:absolute"></span></td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td nowrap align="right">Valor R$</td>
                                      <td><input type="text" name="valor_ct_pag" value="" size="10"></td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td align="right" valign="top" nowrap>Observação:</td>
                                      <td>
                                      <textarea name="obs_ct_pag" id="obs_ct_pag" cols="45" rows="5"></textarea></td>
                                    </tr>
                                    <tr valign="baseline">
                                      <td nowrap align="right">Cadastrador:</td>
                                      <td><input type="text" name="cadastrador_ct_pag" value="<?php  echo $_SESSION['MM_Username'];?>" 
                                            size="20" maxlength="40" readonly="readonly"></td>
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
                            <div class="clearfix"></div>
                            </div>
</div>		
                        </div>

		<!-- Rodapé -->
		
		<?php include('../restrito/rodape01.html');?>
        

</body>
</html>
<?php
mysql_free_result($cs_clinica);

mysql_free_result($cs_conta);
?>
