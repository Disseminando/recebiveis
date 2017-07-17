<!doctype html>
<html>
	
    <?php include('../restrito/head01.html');?>
			<script type="text/javascript" src="../js/move-top.js"></script>
			<script type="text/javascript" src="../js/easing.js"></script>
	        <script src="../SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
            <link href="../SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css">
	<body>
		<div class="content">
			<div class="service_features" id="features">
				<div class="container">
					<div class="col-md-4 ser-fet">
						<h3>Menu</h3>
						<span class="line"></span>
						<div class="services">
							<div class="menu-grid">
							<ul id="MenuBar1" class="MenuBarVertical">
							  <li><a class="MenuBarItemSubmenu" href="#">Paciente</a>
							    <ul>
							      <li><a href="Oper_Pac01.php">Novo</a></li>
							      <li><a href="Oper_Pac02.php">Consulta</a></li>
						        </ul>
						      </li>
							  <li><a class="MenuBarItemSubmenu" href="#">Agenda</a>
							    <ul>
							      <li><a href="Oper_Age01.php">Nova</a></li>
							      <li><a href="Oper_Age02.php">Consulta</a></li>
						        </ul>
						      </li>
							  <li><a class="MenuBarItemSubmenu" href="#">Contas a Pagar</a>
							    <ul>
							      <li><a href="Oper_Contas_Pagar01.php">Nova</a></li>
                                  <li><a href="Oper_Contas_Pagar02.php">Consulta</a></li>
                                  <li><a href="#" class="MenuBarItemSubmenu">Plano de Contas</a>
                                    <ul>
                                      <li><a href="Oper_Plano_Contas01.php">Nova</a></li>
                                      <li><a href="#">Consulta</a></li>
                                    </ul>
                                  </li>
							    </ul>
						      </li>
							  </ul>							
						</div>
						</div>
					</div>
    <script type="text/javascript">
var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgRight:"../SpryAssets/SpryMenuBarRightHover.gif"});
                    </script>
	</body>
</html>