<!-- Realizar un redirect para el componente con el input para insertar el fichero -->

<?php
require_once("./facturacion/ApiFacturacion.php");

$accion = "GUARDAR_COMPROBANTE";
controlador($accion);

function controlador($accion){

    $apiFact = new ApiFacturacion();

    
    switch ($accion) {
        case 'GUARDAR_COMPROBANTE':

            echo "PUTO GC";

            $emisor = array(
                "id"=>  1,
                "tipodoc"=>  "6" ,
                "ruc"=>  "20607599727" ,
                "razon_social"=>  "INSTITUTO INTERNACIONAL DE SOFTWARE SAC" ,
                "nombre_comercial"=>  "INSTITUTO INTERNACIONAL DE SOFTWARE SAC" ,
                "direccion"=>  "8 DE OCTUBRE N 123 - CHICLAYO - CHICLAYO - LAMBAYEQUE" ,
                "pais"=>  "PE" ,
                "departamento"=>  "LAMBAYEQUE" ,
                "provincia"=>  "CHICLAYO" ,
                "distrito"=>  "CHICLAYO" ,
                "ubigeo" =>  "140101" ,
                "usuario_sol"=>  "MODDATOS" ,
                "clave_sol"=>  "MODDATOS" 
            );
            
            $nombre = "20607599727-01-F001-14";
            $rutacertificado = "../facturacion/";
            $rutaxml = "../facturacion/xml/";
            $rutacdr = "../facturacion/cdr/";
            
            
            // ENVIO A LOS WS DE SUNAT 
            $resultado = $apiFact->EnviarComprobanteElectronico($emisor,$nombre,$rutacertificado,$rutaxml,$rutacdr);

            
            break;
    }

}

?>

