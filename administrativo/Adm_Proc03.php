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
  $updateSQL = sprintf("UPDATE procedimentos SET cod_proc=%s, nome_proc=%s, situacao_proc=%s, cadastrador_proc=%s WHERE id_proc=%s",
                       GetSQLValueString($_POST['cod_proc'], "text"),
                       GetSQLValueString($_POST['nome_proc'], "text"),
                       GetSQLValueString($_POST['situacao_proc'], "int"),
                       GetSQLValueString($_POST['cadastrador_proc'], "text"),
                       GetSQLValueString($_POST['id_proc'], "int"));

  mysql_select_db($database_rec01, $rec01);
  $Result1 = mysql_query($updateSQL, $rec01) or die(mysql_error());

  $updateGoTo = "Adm_Proc02.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_cs_proc = "-1";
if (isset($_GET['id_proc'])) {
  $colname_cs_proc = $_GET['id_proc'];
}
mysql_select_db($database_rec01, $rec01);
$query_cs_proc = sprintf("SELECT id_proc, cod_proc, nome_proc, situacao_proc, cadastrador_proc FROM procedimentos WHERE id_proc = %s", GetSQLValueString($colname_cs_proc, "int"));
$cs_proc = mysql_query($query_cs_proc, $rec01) or die(mysql_error());
$row_cs_proc = mysql_fetch_assoc($cs_proc);
$totalRows_cs_proc = mysql_num_rows($cs_proc);

mysql_select_db($database_rec01, $rec01);
$query_cs_situacao = "SELECT id_situa, tipo_situa FROM situacao";
$cs_situacao = mysql_query($query_cs_situacao, $rec01) or die(mysql_error());
$row_cs_situacao = mysql_fetch_assoc($cs_situacao);
$totalRows_cs_situacao = mysql_num_rows($cs_situacao);
?>
<!doctype html>
<html>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
	<!--Cabeçalho -->
	<?php include('../restrito/head01.html');?> 
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
						<h3>:..Procedimento - Consultar..:</h3>
						<span class="line"></span>
						<div class="features">
							<div class="col-md-6 fet-pad">
								<div class="div-margin">
									<div>										
									</div>
									<div>
                                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                        <table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
                                          <tr valign="baseline">
                                            <td width="14%" align="right" nowrap><strong>Código:</strong></td>
                                            <td width="86%"><input name="cod_proc" type="text" value="<?php echo htmlentities($row_cs_proc['cod_proc'], ENT_COMPAT, ''); ?>" size="10" maxlength="9"></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Procedimento:</strong></td>
                                            <td><input name="nome_proc" type="text" value="<?php echo htmlentities($row_cs_proc['nome_proc'], ENT_COMPAT, ''); ?>" size="45" maxlength="255"></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right"><strong>Situação:</strong></td>
                                            <td>
                                              <select name="situacao_proc" id="situacao_proc">
                                                <?php
												do {  
												?>
												<option value="<?php echo $row_cs_situacao['id_situa']?>"<?php if (!(strcmp($row_cs_situacao['id_situa'], $row_cs_proc['situacao_proc']))) {echo "selected=\"selected\"";} ?>><?php echo $row_cs_situacao['tipo_situa']?></option>
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
                                            <td nowrap align="right"><strong>Cadastrador:</strong></td>
                                            <td><input type="text" name="cadastrador_proc" value="<?php echo htmlentities($row_cs_proc['cadastrador_proc'], ENT_COMPAT, ''); ?>" size="32"></td>
                                          </tr>
                                          <tr valign="baseline">
                                            <td nowrap align="right">&nbsp;</td>
                                            <td><input type="submit" value="Gravar"></td>
                                          </tr>
                                        </table>
                                        <input type="hidden" name="MM_update" value="form1">
                                        <input type="hidden" name="id_proc" value="<?php echo $row_cs_proc['id_proc']; ?>">
                                      </form>
                                  </div>
                              </div>
                          </div>							
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
mysql_free_result($cs_proc);

mysql_free_result($cs_situacao);
?>