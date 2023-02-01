

<?php
$rutaxml = "../facturacion/xml/";
$rutacdr = "../facturacion/cdr/";
$rutacertificado = "../facturacion/";

if($comprobante['tipo_comprobante']=='01' || $comprobante['tipo_comprobante']=='03'){
    $objXML->CrearXMLFactura($rutaxml.$nombre, $emisor, $cliente, $comprobante, $detalle);
}

if($comprobante['tipo_comprobante']=='07'){
    $objXML->CrearXMLNotaCredito($rutaxml.$nombre, $emisor, $cliente, $comprobante, $detalle);
}

if($comprobante['tipo_comprobante']=='08'){
    $objXML->CrearXMLNotaDebito($rutaxml.$nombre, $emisor, $cliente, $comprobante, $detalle);
}

$resultado = $apiFact->EnviarComprobanteElectronico($emisor,$nombre,$rutacertificado,$rutaxml,$rutacdr);
$objComprobante->actualizarDatosFE($comprobantex['id'], $resultado['nombre_xml'], $resultado['base64'], $resultado['hash_cpe'], "", $resultado['codigo_error'], $resultado['mensaje_sunat'], $resultado['estado']);
//FIN FACTURACION ELECTRONICA

//FIN DE REGISTRO EN BASE DE DATOS
echo "COMPROBANTE REGISTRADO SATISFACTORIAMENTE";
session_destroy();
echo "<script>window.open('./presentacion/pdfFacturaElectronica.php?id=".$comprobantex['id']."','_blank')</script>";
echo "<script>window.open('./presentacion/ticket.php?id=".$comprobantex['id']."','_blank')</script>";
break;