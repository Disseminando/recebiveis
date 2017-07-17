<?php require_once('Connections/rec01.php'); ?>
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['login'])) {
  $loginUsername=$_POST['login'];
  $password=$_POST['senha'];
  $MM_fldUserAuthorization = "perfil_id";
  $MM_redirectLoginSuccess = "restrito/principal.php";
  $MM_redirectLoginFailed = "erro/Usuario_Nao_Autorizado.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_rec01, $rec01);
  	
  $LoginRS__query=sprintf("SELECT login, senha, perfil_id FROM acesso WHERE login=%s AND senha=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $rec01) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'perfil_id');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!doctype html>
<html>
	<head>
        <title>Recebiveis</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="keywords" content="Clinica, Hospital, Laboratorio, Medicos, Plano de Saude, Faturamento, Credenciamento" />
    <script type="application/x-javascript"> addEventListener("load", function() {setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!meta charset utf="8">
		<!--fonts-->
			<link href='http://fonts.googleapis.com/css?family=Monda:400,700' rel='stylesheet' type='text/css'>
			<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,300,100,700' rel='stylesheet' type='text/css'>
		<!--fonts-->
		<!--owlcss-->
<link href="css/owl.carousel.css" rel="stylesheet">
		<!--bootstrap-->
			<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<!--coustom css-->
			<link href="css/style.css" rel="stylesheet" type="text/css"/>
		<!--default-js-->
			<script src="js/jquery-2.1.4.min.js"></script>
		<!--bootstrap-js-->
			<script src="js/bootstrap.min.js"></script>
		<!--script-->
			<script type="text/javascript" src="js/move-top.js"></script>
			<script type="text/javascript" src="js/easing.js"></script>
	        <script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
            <link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
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
						<a class="navbar-brand logo-st" href="#">Recebiveis</a>
						</div>
					<!-- Collect the nav links, forms, and other content for toggling -->					
					</div><!-- /.container-fluid -->
				</nav>
			</div>
			<div class="header_banner">
				<div id="myCarousel" class="carousel slide" data-ride="carousel">
				<!-- Indicators -->
					<ol class="carousel-indicators">
					<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
					<li data-target="#myCarousel" data-slide-to="1"></li>
					<li data-target="#myCarousel" data-slide-to="2"></li>
					<li data-target="#myCarousel" data-slide-to="3"></li>
					</ol>

				<!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
					<div class="item active  image-wid">
					<img src="./images/1a.jpg" alt="..." class="img-responsive">
					<div class="carousel-caption">
					</div>
					</div>					
					<div class="item  image-wid">
					<img src="./images/1g.jpg" alt="..." class="img-responsive">
					<div class="carousel-caption">
					</div>
					</div>
				</div>
				<!-- Controls -->
				<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
				</a>
				<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
				</a>
				</div>
			</div>
		</div>
		<div class="style-label">
			<div class="container">
				<ul class="box-shadow effect2">					
					<li class="col-md-3">						
						<div class="label-text">
						<h3><font color="white">Administração</font></h3>						
						</div>
					</li>
					<li class="col-md-3">						
						<div class="label-text">
						<h3><font color="white">Planejamento</font></h3>						
						</div>
					</li>
                    <li class="col-md-3">						
						<div class="label-text">
						<h3><font color="white">Controle</font></h3>						
						</div>
					</li>
                    <li class="col-md-3">						
						<div class="label-text">
						<h3><font color="white">Segurança</font></h3>						
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
		<div class="content">
			<div class="service_features" id="features">
				<div class="container">
					<div class="col-md-4 ser-fet">												
						<span class="line"></span>
						<div class="services">
							<div class="menu-grid">						
						</div>
						</div>
					</div>
					<div class="col-md-8 ser-fet">
						<h3>Acesso Restrito</h3>
						<span class="line"></span>
						<div class="features">
							<div class="col-md-6 fet-pad">
								<div class="div-margin">
									<div class="col-md-3 fet-pad wid">
										<span class="glyphicon glyphicon-user aim-icn" aria-hidden="true"></span>
									</div>
									<div class="col-md-9 fet-pad wid2">
                                        <br>
                                        <br>
                                        <br>
                                        <br>
								      <form name="login" method="POST" action="<?php echo $loginFormAction; ?>">
								        <table width="100%" border="0" cellspacing="10" cellpadding="10">
								          <tr>
								            <th width="11%" scope="row">Usuario&nbsp;</th>
								            <td width="89%"><span id="sprytextfield1">
								              <input name="login" type="text" id="login" maxlength="20">
							                <span class="textfieldRequiredMsg">Obrigatorio.</span></span></td>
							              </tr>
								          <tr>
								            <th scope="row">Senha&nbsp;</th>
								            <td><span id="sprytextfield2">
								              <input name="senha" type="password" id="senha" maxlength="20">
							                <span class="textfieldRequiredMsg">Obrigatorio.</span></span></td>
							              </tr>
								          <tr>
								            <th scope="row">&nbsp;</th>
								            <td>&nbsp;<input type="submit" name="ok" id="ok" value="Acessar"></td>
							              </tr>
							            </table>
								      </form>
								     </div>
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
		<?php include('./restrito/rodape01.html');?>
        
        <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur", "change"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur", "change"]});

    </script>
	</body>
</html>