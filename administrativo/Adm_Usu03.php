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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$data = $_POST['data_usu'];
	$data = explode("/", $data);
    $data = $data[2]."-".$data[1]."-".$data[0];
  $updateSQL = sprintf("UPDATE usuario SET data_usu=%s, clin_id=%s, nome_usu=%s, fone_usu=%s, email_usu=%s, situacao_usu=%s, cadastrador_usu=%s WHERE id_usu=%s",
                       GetSQLValueString($data, "date"),
                       GetSQLValueString($_POST['clin_id'], "int"),
                       GetSQLValueString($_POST['nome_usu'], "text"),
                       GetSQLValueString($_POST['fone_usu'], "text"),
                       GetSQLValueString($_POST['email_usu'], "text"),
                       GetSQLValueString($_POST['situacao_usu'], "int"),
                       GetSQLValueString($_POST['cadastrador_usu'], "text"),
                       GetSQLValueString($_POST['id_usu'], "int"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($updateSQL, $rec01) or die(mysql_error());

  $updateGoTo = "Adm_Usu02.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_rec01, $rec01);
$query_cs_clinicas = "SELECT id_clin, nome_clin FROM clinica";
$cs_clinicas = mysql_query($query_cs_clinicas, $rec01) or die(mysql_error());
$row_cs_clinicas = mysql_fetch_assoc($cs_clinicas);
$totalRows_cs_clinicas = mysql_num_rows($cs_clinicas);

mysql_select_db($database_rec01, $rec01);
$query_cs_situacao = "SELECT id_situa, tipo_situa FROM situacao";
$cs_situacao = mysql_query($query_cs_situacao, $rec01) or die(mysql_error());
$row_cs_situacao = mysql_fetch_assoc($cs_situacao);
$totalRows_cs_situacao = mysql_num_rows($cs_situacao);

$colname_cs_usuarios = "-1";
if (isset($_GET['id_usu'])) {
  $colname_cs_usuarios = $_GET['id_usu'];
}
mysql_select_db($database_rec01, $rec01);
$query_cs_usuarios = sprintf("SELECT id_usu, data_usu, clin_id, nome_usu, fone_usu, email_usu, situacao_usu, cadastrador_usu FROM usuario WHERE id_usu = %s", GetSQLValueString($colname_cs_usuarios, "int"));
$cs_usuarios = mysql_query($query_cs_usuarios, $rec01) or die(mysql_error());
$row_cs_usuarios = mysql_fetch_assoc($cs_usuarios);
$totalRows_cs_usuarios = mysql_num_rows($cs_usuarios);
?>
<!doctype html>
<html>
	<!--Cabeçalho -->
	<?php include('../restrito/head01.html');?>
	<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
    <link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
    <link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">

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
					<a class="navbar-brand logo-st" href="#">Recebiveis</a>
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
						<h3>:..Usuário - Atualiza..:</h3>
						<span class="line"></span>
						<div class="features">
							<div class="col-md-6 fet-pad">
								<div class="div-margin">
									<div class="col-md-3 fet-pad wid">										
									</div>
									<div class="col-md-9 fet-pad wid2">
                                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                      <table align="left" cellpadding="10" cellspacing="10">
                                        <tr valign="baseline">
                                          <td nowrap align="right">Data:</td>
                                          <td><input name="data_usu" type="text" value="<?php 
										       //Convertendo data para formato brasileiro
											   $data01=htmlentities($row_cs_usuarios['data_usu']);
											   echo date('d/m/Y', strtotime($data01)); ?>" 
                                          size="10" readonly="readonly"></td>
                                        </tr>
                                        <tr valign="baseline">
                                          <td nowrap align="right">Clínica:</td>
                                          <td>
                                            <select name="clin_id" id="clin_id">
                                              <?php
do {  
?>
                                        <option value="<?php echo $row_cs_clinicas['id_clin']?>"<?php if (!(strcmp($row_cs_clinicas['id_clin'], $row_cs_usuarios['clin_id']))) {echo "selected=\"selected\"";} ?>><?php echo $row_cs_clinicas['nome_clin']?></option>
                                        <?php
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
                                          <td nowrap align="right">Nome:</td>
                                          <td><input name="nome_usu" type="text" value="<?php echo htmlentities($row_cs_usuarios['nome_usu'], ENT_COMPAT, 'utf-8'); ?>" size="45" maxlength="255"></td>
                                        </tr>
                                        <tr valign="baseline">
                                          <td nowrap align="right">Telefone:</td>
                                          <td><input name="fone_usu" type="text" value="<?php echo htmlentities($row_cs_usuarios['fone_usu'], ENT_COMPAT, 'utf-8'); ?>" size="20" maxlength="20"></td>
                                        </tr>
                                        <tr valign="baseline">
                                          <td nowrap align="right">Email:</td>
                                          <td><input name="email_usu" type="text" value="<?php echo htmlentities($row_cs_usuarios['email_usu'], ENT_COMPAT, 'utf-8'); ?>" size="45" maxlength="255"></td>
                                        </tr>
                                        <tr valign="baseline">
                                          <td nowrap align="right">Situação:</td>
                                          <td>
                                            <select name="situacao_usu" id="situacao_usu">
                                              <?php
do {  
?>
                                            <option value="<?php echo $row_cs_situacao['id_situa']?>"<?php if (!(strcmp($row_cs_situacao['id_situa'], $row_cs_usuarios['situacao_usu']))) {echo "selected=\"selected\"";} ?>><?php echo $row_cs_situacao['tipo_situa']?></option>
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
                                          <td><input name="cadastrador_usu" type="text" value="<?php  echo $_SESSION['MM_Username'];?>" 
                                          size="20" readonly="readonly">
                                          </td>
                                        </tr>
                                        <tr valign="baseline">
                                          <td nowrap align="right">&nbsp;</td>
                                          <td><input type="submit" value="Gravar"></td>
                                        </tr>
                                      </table>
                                      <input type="hidden" name="MM_update" value="form1">
                                      <input type="hidden" name="id_usu" value="<?php echo $row_cs_usuarios['id_usu']; ?>">
                                    </form>                                    
                                     </div>
									<div class="clearfix"></div>
								</div>
								<!-- Fim do Login -->
							</div>							
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
mysql_free_result($cs_clinicas);

mysql_free_result($cs_situacao);

mysql_free_result($cs_usuarios);
?>
