<?php
header( 'Cache-Control: no-cache' );
header( 'Content-type: application/xml; charset="utf-8"', true );

require_once('../Connections/rec01.php');

$cod_estados = mysql_real_escape_string( $_REQUEST['clin_id'] );

$cidades = array();

$sql = "SELECT id_med, nome_med
		FROM medico
		WHERE clin_id=$cod_estados
		ORDER BY nome_med";
$res = mysql_query( $sql );
while ( $row = mysql_fetch_assoc( $res ) ) {
	$cidades[] = array(
		'id_med'	=> $row['id_med'],
		'nome_med'			=> htmlentities($row['nome_med']),
	);
}

echo( json_encode( $cidades ) );
?>