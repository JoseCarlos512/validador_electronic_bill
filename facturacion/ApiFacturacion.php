<?php
require_once("signature.php");


class ApiFacturacion{

	public function EnviarComprobanteElectronico($emisor, $nombre, $rutacertificado="", $ruta_archivo_xml = "xml/", $ruta_archivo_cdr = "cdr/"){


		echo $emisor . "</br>";
		echo $nombre . "</br>";
		echo $rutacertificado . "</br>";
		echo $ruta_archivo_xml . "</br>";
		echo $ruta_archivo_cdr . "</br>";
		
		//firma del documento
		$objSignature = new Signature();

		$flg_firma = "0";
		$ruta = $ruta_archivo_xml.$nombre.'.XML';

		$ruta_firma = $rutacertificado."certificado_prueba.pfx";
		$pass_firma = "institutoisi";

		echo "ruta" . $ruta;
		echo "before signature";
		$resp = $objSignature->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma);
		echo "after signature";

		echo $resp['xml'];
		return $resp['xml'];
		$resultado = array(							
							"nombre_xml"	=>$nombre.'.XML',
							"mensaje_sunat" => "",
							"codigo_error"	=> "",
							"base64"		=> "",
							"xml" 			=> $resp['xml'],
							"hash_cpe"		=> $resp['hash_cpe'],
							"estado"		=> 0
							);
		//print_r($resp);
		//$hash = $resp['hash_cpe'];

		//Generar el .zip

		$zip = new ZipArchive();

		$nombrezip = $nombre.".ZIP";
		$rutazip = $ruta_archivo_xml.$nombre.".ZIP";

		if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre.'.XML');
			$zip->close();
		}


		//Enviamos el archivo a sunat

		$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";

		$ruta_archivo = $rutazip;
		$nombre_archivo = $nombrezip;

		$contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));


		$xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				 <soapenv:Header>
				 	<wsse:Security>
				 		<wsse:UsernameToken>
				 			<wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
				 			<wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
				 		</wsse:UsernameToken>
				 	</wsse:Security>
				 </soapenv:Header>
				 <soapenv:Body>
				 	<ser:sendBill>
				 		<fileName>'.$nombre_archivo.'</fileName>
				 		<contentFile>'.$contenido_del_zip.'</contentFile>
				 	</ser:sendBill>
				 </soapenv:Body>
				</soapenv:Envelope>';


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
			//para ejecutar los procesos de forma local en windows
			//enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");


			$response = curl_exec($ch);

			$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			$estadofe = "0";
			if($httpcode == 200){ //200->La comunicación fue satisfactoria
				$doc = new DOMDocument();
				$doc->loadXML($response);

					if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
						$cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
						$cdr = base64_decode($cdr);
						
						file_put_contents($ruta_archivo_cdr."R-".$nombrezip, $cdr);

						$zip = new ZipArchive;
						if($zip->open($ruta_archivo_cdr."R-".$nombrezip)===true){
							$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre.'.XML');
							$zip->close();
						}
						$estadofe ="1";
						echo "TODO OK";

						$doc_cdr = new DOMDocument();
						$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre.'.XML');
						$doc_cdr->loadXML($infocdr);
						$mensaje = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;

						$resultado['mensaje_sunat']=$mensaje;
					}else{		
						$estadofe = "2";
						$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
						$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
						echo "error ".$codigo.": ".$mensaje; 		
						$resultado['codigo_error'] = $codigo;
						$resultado['mensaje_sunat'] = $mensaje;
					}		

			}else{ //hay problemas comunicacion
					$estadofe = "3";
					echo curl_error($ch);
					echo "Problema de conexión";
					$resultado['mensaje_sunat']="ERROR EN CONEXION";	
					$resultado['codigo_error'] = "00";						
			}
			$resultado['estado'] = $estadofe;
			var_dump($resultado);
			curl_close($ch);
			return $resultado;
	}


public function EnviarResumenComprobantes($emisor,$nombre, $rutacertificado="", $ruta_archivo_xml = "xml/"){

		//firma del documento
		$objSignature = new Signature();

		$flg_firma = "0";
		//$ruta_archivo_xml = "xml/";
		$ruta = $ruta_archivo_xml.$nombre.'.XML';

		$ruta_firma = $rutacertificado."certificado_prueba.pfx";
		$pass_firma = "institutoisi";

		$resp = $objSignature->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma);

		//print_r($resp);


		//Generar el .zip

		$zip = new ZipArchive();

		$nombrezip = $nombre.".ZIP";
		$rutazip = $ruta_archivo_xml.$nombre.".ZIP";

		if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre.'.XML');
			$zip->close();
		}


		//Enviamos el archivo a sunat

		$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";

		$ruta_archivo = $ruta_archivo_xml.$nombrezip;
		$nombre_archivo = $nombrezip;
		$ruta_archivo_cdr = "cdr/";

		$contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));


		$xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				 <soapenv:Header>
				 	<wsse:Security>
				 		<wsse:UsernameToken>
				 			<wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
				 			<wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
				 		</wsse:UsernameToken>
				 	</wsse:Security>
				 </soapenv:Header>
				 <soapenv:Body>
				 	<ser:sendSummary>
				 		<fileName>'.$nombre_archivo.'</fileName>
				 		<contentFile>'.$contenido_del_zip.'</contentFile>
				 	</ser:sendSummary>
				 </soapenv:Body>
				</soapenv:Envelope>';


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
			//para ejecutar los procesos de forma local en windows
			//enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");


			$response = curl_exec($ch);

			$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			$estadofe = "0";

			$ticket = "0";
			if($httpcode == 200){
				$doc = new DOMDocument();
				$doc->loadXML($response);

				if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
	                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
					echo "TODO OK : ".$ticket;
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
			return $ticket;
	}


	function ConsultarTicket($emisor, $cabecera, $ticket, $ruta_archivo_cdr = "cdr/"){

		$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
		$nombre	= $emisor["ruc"]."-".$cabecera["tipodoc"]."-".$cabecera["serie"]."-".$cabecera["correlativo"];
		$nombre_xml	= $nombre.".XML";

		//===============================================================//
		//FIRMADO DEL cpe CON CERTIFICADO DIGITAL
		$objSignature = new Signature();
		$flg_firma = "0";
		$ruta = $nombre_xml;

		$ruta_firma = "certificado_prueba.pfx";
		$pass_firma = "institutoisi";

		//===============================================================//

		//ALMACENAR EL ARCHIVO EN UN ZIP
		$zip = new ZipArchive();

		$nombrezip = $nombre.".ZIP";

		if($zip->open($nombrezip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre_xml);
			$zip->close();
		}

		//===============================================================//

		//ENVIAR ZIP A SUNAT
		$ruta_archivo = $nombre;
		$nombre_archivo = $nombre;
		//$ruta_archivo_cdr = "cdr/";

		//$contenido_del_zip = base64_encode(file_get_contents($ruta_archivo.'.ZIP'));
		//FIN ZIP

		$xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
        <wsse:Security>
        <wsse:UsernameToken>
        <wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
        <wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
        </wsse:UsernameToken>
        </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
        <ser:getStatus>
        <ticket>' . $ticket . '</ticket>
        </ser:getStatus>
        </soapenv:Body>
        </soapenv:Envelope>';


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
		//para ejecutar los procesos de forma local en windows
		//enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);

		echo "codigo:".$httpcode;
		$estado = 0;
		$mensaje_sunat = "";
		$codigo_sunat = "";
		if($httpcode == 200){
			$doc = new DOMDocument();
			$doc->loadXML($response);

			if(isset($doc->getElementsByTagName('content')->item(0)->nodeValue)){
				$cdr = $doc->getElementsByTagName('content')->item(0)->nodeValue;
				$cdr = base64_decode($cdr);
				

				file_put_contents($ruta_archivo_cdr."R-".$nombre_archivo.".ZIP", $cdr);

				$zip = new ZipArchive;
				if($zip->open($ruta_archivo_cdr."R-".$nombre_archivo.".ZIP")===true){
					$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre_archivo.'.XML');
					$zip->close();
				}
				echo "TODO OK";

				$doc_cdr = new DOMDocument();
				$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre_archivo.'.XML');
				$doc_cdr->loadXML($infocdr);
				$mensaje_sunat = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;

				$estado = 1;
			}else{		
				$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
				$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
				$estado = 2;
				$codigo_sunat = $codigo;
				$mensaje_sunat = $mensaje;
				echo "error ".$codigo.": ".$mensaje; 
			}

		}else{
			echo curl_error($ch);
			echo "Problema de conexión";
			$mensaje = "Problema de conexión";
		}		
		curl_close($ch);
		$retorno = array(
						"estado"		=>$estado, 
						"mensaje_sunat"	=>$mensaje_sunat,
						"codigo_sunat"	=>$codigo_sunat
					);
		return $retorno;
	}

	
	function consultarComprobante(){
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
		            "usuario_sol" => "MODDATOS",
		            "clave_sol" => "MODDATOS"
		            );

				$tipodoc = "01";
				$serie = "F001";
				$correlativo = 123;
				$soapUrl = "https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl"; 
				$soapUser = "";  
				$soapPassword = "";

				// xml post structure
				$xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
				xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
				xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<soapenv:Header>
						<wsse:Security>
							<wsse:UsernameToken>
								<wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
								<wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
							</wsse:UsernameToken>
						</wsse:Security>
					</soapenv:Header>
					<soapenv:Body>
						<ser:getStatus>
							<rucComprobante>'.$emisor['ruc'].'</rucComprobante>
							<tipoComprobante>'.$tipodoc.'</tipoComprobante>
							<serieComprobante>'.$serie.'</serieComprobante>
							<numeroComprobante>'.$correlativo.'</numeroComprobante>
						</ser:getStatus>
					</soapenv:Body>
				</soapenv:Envelope>';
			
				$headers = array(
					"Content-type: text/xml;charset=\"utf-8\"",
					"Accept: text/xml",
					"Cache-Control: no-cache",
					"Pragma: no-cache",
					"SOAPAction: ",
					"Content-length: " . strlen($xml_post_string),
				);
			
				$url = $soapUrl;
			

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

				$response = curl_exec($ch);
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				echo var_dump($response);

	}

}
?>