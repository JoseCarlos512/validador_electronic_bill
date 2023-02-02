<!-- Realizar un redirect para el componente con el input para insertar el fichero -->

<?php
require_once("./facturacion/ApiFacturacion.php");

//$accion = "GUARDAR_COMPROBANTE";
$accion = "GUIA_REMISION";
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

        case 'GUIA_REMISION':

        $carpetaxml = "C:/xampp/htdocs/validador_electronic_bill/facturacion/xml/";
        $carpetacdr = "C:/xampp/htdocs/validador_electronic_bill/facturacion/cdr/";
        $nombrexml = "20607599727-09-T001-456";

        $emisor = array(
            "tipo_documento" => 6,
            "ruc" => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_emisor" => "MODDATOS",
            "clave_emisor" => "MODDATOS"
        );

        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="utf-8"?><DespatchAdvice xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent><ds:Signature Id="SignatureSP"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/><ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/><ds:Reference URI=""><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>140jilQAt5/8ZQBelsFhFU6+eOE=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>l/LFH566sqbX2NxEr0AuKq7mU6Twts0zpYuUPMh6/YokkCJNWUK60pt1ISmPWxVjM+8dtZLEvGHQ04w7NFqnG0xXBc4EOd59aEJHVorkVn8aULd6xJv5wgWQnD69GJgoZRAc5W1Wi0H45xaZ6M3/LtluFN/caPog4QO3u2H6cYRL/Z1ZTDPG7/AHITXRcyR+rgmdk9aFD4S6hOF+iPqYUCH2DF1PmKUWgLxAoR71nZ03tYxZBYRoAh2GXgdL0vgn/BHDZFlnEDOAuF2piafwmgTQXj0f2vp3euP0kdv0YB4QOMPJrbb5d4tWUU+9BdLbCozqLoGTIgL3us+C63IHRA==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIFBzCCA++gAwIBAgIIWHDnQ8FmCHcwDQYJKoZIhvcNAQELBQAwggENMRswGQYKCZImiZPyLGQBGRYLTExBTUEuUEUgU0ExCzAJBgNVBAYTAlBFMQ0wCwYDVQQIDARMSU1BMQ0wCwYDVQQHDARMSU1BMRgwFgYDVQQKDA9UVSBFTVBSRVNBIFMuQS4xRTBDBgNVBAsMPEROSSA5OTk5OTk5IFJVQyAyMDYwNzU5OTcyNyAtIENFUlRJRklDQURPIFBBUkEgREVNT1NUUkFDScOTTjFEMEIGA1UEAww7Tk9NQlJFIFJFUFJFU0VOVEFOVEUgTEVHQUwgLSBDRVJUSUZJQ0FETyBQQVJBIERFTU9TVFJBQ0nDk04xHDAaBgkqhkiG9w0BCQEWDWRlbW9AbGxhbWEucGUwHhcNMjIwMjA4MDA0MTUyWhcNMjQwMjA4MDA0MTUyWjCCAQ0xGzAZBgoJkiaJk/IsZAEZFgtMTEFNQS5QRSBTQTELMAkGA1UEBhMCUEUxDTALBgNVBAgMBExJTUExDTALBgNVBAcMBExJTUExGDAWBgNVBAoMD1RVIEVNUFJFU0EgUy5BLjFFMEMGA1UECww8RE5JIDk5OTk5OTkgUlVDIDIwNjA3NTk5NzI3IC0gQ0VSVElGSUNBRE8gUEFSQSBERU1PU1RSQUNJw5NOMUQwQgYDVQQDDDtOT01CUkUgUkVQUkVTRU5UQU5URSBMRUdBTCAtIENFUlRJRklDQURPIFBBUkEgREVNT1NUUkFDScOTTjEcMBoGCSqGSIb3DQEJARYNZGVtb0BsbGFtYS5wZTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAL5GqKklGgLoMNCd/1BHQNDkqf37yRBZ8aaT3dmeECEOM5lRcZ7AMHjobcZP4nXdVRfWycv3am+NxmtlZmdAiY/yfoYK9mLJpyw7ods58ScdoFsuHh9yrr52FLBQEdUAp3//BCZ9Nl5BLWYKWjo9idm6pKdJeRqrp/DET38PJg86Qdvxk5czAqx4M3RkBfGx5ba2ND1j+xO4RjHC0WbDFeJT13wfsJW3Ol5M2moqs6KbRnU5y9A2zh+Kj3rhC85tExyaJ8VY4kuOVD9DlWbzs7iXZrdPRjQLsG7Mc8btUpuAH5ZZo+EExH4YwTUI8lwnS7tKu7GuzU4AyRFN4kkHNMcCAwEAAaNnMGUwHQYDVR0OBBYEFMJLkMhQufF4m09/ZQ+QeOUUbNoVMB8GA1UdIwQYMBaAFMJLkMhQufF4m09/ZQ+QeOUUbNoVMBMGA1UdJQQMMAoGCCsGAQUFBwMBMA4GA1UdDwEB/wQEAwIHgDANBgkqhkiG9w0BAQsFAAOCAQEAuyjNYcW6IAm4cytWrHiH1cQbzpxDXmuNXUCJuknZ8JzR1Ep51vQHguAQoq8UP69kbnbrVuiZPTwU3OImha/xKTO+EXPUh7Ptq0Aqb/ScXmCZI01WnL2/t651ubd+UlmHVBmYD/4yu1AKhg/ueelRj6DwErwcAr2LUYGBUnON8z/Lv/5IGeMBPbGm8mkHCUeP54K83COn49EdXwBO40efobyvmHghm0Bs0yjM53RIAmdJckeoAiqMv25JzK6u2ndVyfTB0yQgT62l+VheOPQkuIvlDaektZGjIQR/YTZ/CVRFEcbKSxpTJIVSrbZe8/bQSywhsTu3BwD+zRJma1/Ouw==</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature></ext:ExtensionContent>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>1.0</cbc:CustomizationID>
            <cbc:ID>T001-456</cbc:ID>
            <cbc:IssueDate>2023-02-02</cbc:IssueDate>
            <cbc:IssueTime>00:00:00</cbc:IssueTime>
            <cbc:DespatchAdviceTypeCode>09</cbc:DespatchAdviceTypeCode>
            <cbc:Note>--</cbc:Note>
           <cac:Signature>
              <cbc:ID>T001-456</cbc:ID>
              <cac:SignatoryParty>
                 <cac:PartyIdentification>
                    <cbc:ID>20607599727</cbc:ID>
                 </cac:PartyIdentification>
                 <cac:PartyName>
                    <cbc:Name><![CDATA[INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.]]></cbc:Name>
                 </cac:PartyName>
              </cac:SignatoryParty>
              <cac:DigitalSignatureAttachment>
                 <cac:ExternalReference>
                    <cbc:URI>#SignatureSP</cbc:URI>
                 </cac:ExternalReference>
              </cac:DigitalSignatureAttachment>
           </cac:Signature>    
            <cac:DespatchSupplierParty>
                    <cbc:CustomerAssignedAccountID schemeID="6">20607599727</cbc:CustomerAssignedAccountID>
                    <cac:Party>
                        <cac:PartyLegalEntity>
                            <cbc:RegistrationName><![CDATA[INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.]]></cbc:RegistrationName>
                        </cac:PartyLegalEntity>
                    </cac:Party>
                </cac:DespatchSupplierParty>    
            <cac:DeliveryCustomerParty>
            <cbc:CustomerAssignedAccountID schemeID="6">20605145648</cbc:CustomerAssignedAccountID>
                <cac:Party>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA[AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.]]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:DeliveryCustomerParty>    
            <cac:Shipment>
                    <cbc:ID>1</cbc:ID>
                    <cbc:HandlingCode>01</cbc:HandlingCode>
                    <cbc:Information>VENTA</cbc:Information>
                    <cbc:GrossWeightMeasure unitCode="KGM">5500</cbc:GrossWeightMeasure>
                    <cac:ShipmentStage>
                        <cbc:TransportModeCode>02</cbc:TransportModeCode>
                        <cac:TransitPeriod>
                            <cbc:StartDate>2023-02-02</cbc:StartDate>
                        </cac:TransitPeriod><cac:TransportMeans>
                          <cac:RoadTransport>
                             <cbc:LicensePlateID>M1X-328</cbc:LicensePlateID>
                          </cac:RoadTransport>
                      </cac:TransportMeans>
                       <cac:DriverPerson>
                          <cbc:ID schemeID="1">12345678</cbc:ID>
                       </cac:DriverPerson></cac:ShipmentStage>            
                    <cac:Delivery>
                        <cac:DeliveryAddress>
                            <cbc:ID>140103</cbc:ID>
                            <cbc:StreetName><![CDATA[MI DESTINO 456 - MORROPE]]></cbc:StreetName>
                        </cac:DeliveryAddress>
                    </cac:Delivery>
                    
                    <cac:OriginAddress>
                                <cbc:ID>140101</cbc:ID>
                                <cbc:StreetName><![CDATA[MI ORIGEN 123 - LAMBAYEQUE]]></cbc:StreetName>
                    </cac:OriginAddress>
                </cac:Shipment><cac:DespatchLine>
                    <cbc:ID>1</cbc:ID>
                    <cbc:DeliveredQuantity unitCode="NIU">1</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                        <cbc:LineID>1</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                        <cbc:Name><![CDATA[MOCHILA]]></cbc:Name>
                        <cac:SellersItemIdentification>
                            <cbc:ID>11</cbc:ID>
                        </cac:SellersItemIdentification>
                    </cac:Item>
                </cac:DespatchLine><cac:DespatchLine>
                    <cbc:ID>2</cbc:ID>
                    <cbc:DeliveredQuantity unitCode="NIU">2</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                        <cbc:LineID>2</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                        <cbc:Name><![CDATA[LIBRO COQUITO]]></cbc:Name>
                        <cac:SellersItemIdentification>
                            <cbc:ID>22</cbc:ID>
                        </cac:SellersItemIdentification>
                    </cac:Item>
                </cac:DespatchLine><cac:DespatchLine>
                    <cbc:ID>3</cbc:ID>
                    <cbc:DeliveredQuantity unitCode="NIU">3</cbc:DeliveredQuantity>
                    <cac:OrderLineReference>
                        <cbc:LineID>3</cbc:LineID>
                    </cac:OrderLineReference>
                    <cac:Item>
                        <cbc:Name><![CDATA[MANZANA]]></cbc:Name>
                        <cac:SellersItemIdentification>
                            <cbc:ID>33</cbc:ID>
                        </cac:SellersItemIdentification>
                    </cac:Item>
                </cac:DespatchLine></DespatchAdvice>';
               
        $doc->loadXML($xml);
        $doc->save($carpetaxml.$nombrexml.'.XML');


        //PASO 02
        //FIRMAR EL XML
        require_once("C:/xampp/htdocs/validador_electronic_bill/facturacion/signature.php");
        $objSignature = new Signature();

        $flg_firma = "0";
        $ruta = $carpetaxml.$nombrexml.'.XML';

        $ruta_firma = "certificado_prueba.pfx";
        $pass_firma = "institutoisi";

        $resp = $objSignature->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma);

        print_r($resp);

        //PASO 03
        $zip = new ZipArchive();
        $nombrezip = $nombrexml.".ZIP";
        $rutazip = $carpetaxml.$nombrexml.".ZIP";

        if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
        $zip->addFile($carpetaxml.$nombrexml.'.XML', $nombrexml.'.XML');
        $zip->close();
        }

        //PASO 04
        //PREPARAR EL ENVÍO DEL XML
        $contenido_del_zip = base64_encode(file_get_contents($rutazip));
        $xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
                xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                    <wsse:Security>
                        <wsse:UsernameToken>
                            <wsse:Username>'.$emisor['ruc'].$emisor['usuario_emisor'].'</wsse:Username>
        <wsse:Password>'.$emisor['clave_emisor'].'</wsse:Password>
                        </wsse:UsernameToken>
                </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
        <ser:sendBill>
            <fileName>'.$nombrezip.'</fileName>
            <contentFile>'.$contenido_del_zip.'</contentFile>
        </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';

        //PASO 05
        //ENVÍO DEL CPE A WS DE SUNAT

        $ws = "https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService";
        $header = array(
                "Content-type: text/xml; charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-lenght: ".strlen($xml_envio)
            );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
        curl_setopt($ch,CURLOPT_URL,$ws);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        //curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
        curl_setopt($ch, CURLOPT_CAINFO, "C:/xampp/htdocs/validador_electronic_bill/facturacion"."/cacert.pem");
        $response = curl_exec($ch);


        //PASO 06 
        // OBTENEMOS RESPUESTA (CDR)
        echo "<br/>";
        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if($httpcode == 200){
        $doc = new DOMDocument();
        $doc->loadXML($response);
            if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                $cdr = base64_decode($cdr);         
                file_put_contents($carpetacdr."R-".$nombrezip, $cdr);
                $zip = new ZipArchive;
                if($zip->open($carpetacdr."R-".$nombrezip)===true){
                    $zip->extractTo($carpetacdr.'R-'.$nombrexml);
                    $zip->close();
                }
                
                $objCdrXML = new DOMDocument();
                $rutacdr = $carpetacdr.'R-'.$nombrexml.'/R-'.$nombrexml.'.XML';
                $objCdrXML->loadXML(file_get_contents($rutacdr));
                $mensaje = $objCdrXML->getElementsByTagName("Description")->item(0)->nodeValue;
                echo "<br/><b>".$mensaje."</b><br/>";
                
                $receptor = $objCdrXML->getElementsByTagName("DocumentResponse")->item(0)->getElementsByTagName("RecipientParty")->item(0)->getElementsByTagName("ID")->item(0)->nodeValue;
                //$receptor = $objCdrXML->getElementsByTagName("ID")->item(6)->nodeValue;
                echo "<br/><b>".$receptor."</b><br/>";

                echo "GUIA ENVIADA CORRECTAMENTE </br>";

                $responseCode = $objCdrXML->getElementsByTagName("ResponseCode")->item(0)->nodeValue;

                if($responseCode==0){
                    echo "GUIAR REMISION APROBADA";   
                }else{
                    echo "GUIA RECHAZADA CON CODIGO DE ERROR:".$responseCode;
                }

            }else{      
                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
                echo "error ".$codigo.": ".$mensaje; 
            }
        }else{
            echo curl_error($ch);
            echo "Problema de conexión";
        }
        curl_close($ch);
            break;
    }

}

?>

