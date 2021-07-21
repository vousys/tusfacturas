# TusFacturas.app - API de integración con Factura electrónica AFIP - PHP SDK

Mediante nuestra API podrás conectar tu sistema de gestión actual, con nuestra plataforma y emitir facturas electrónicas AFIP válidas. Estamos homologados por AFIP.

Encontrá toda la documentación aquí: https://developers.tusfacturas.app/
Registrate en: https://www.tusfacturas.app/


### SDK para PHP:
Estas librerias proveen un set de clases y metodos para interactuar con la API de TusFacturas.com.ar.

#### Versiones de PHP Soportadas:
El SDK soporta PHP 5 o superior

#### Inicio Rápido
1. Incluí en tu proyecto los archivos archivo tusfacturas_sdk.php y tusfacturas_sdk_entidades.php
2. Configura tus credenciales
3. Invocá el método que necesitas implementar.
4. Manejá las respuestas




## Ejemplos de uso.


### Estado de los servicios:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-estado-de-los-servicios-afip

```
   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
   $response             = $tusfacturas_sdk_obj->estado_servicios();
```


### Consultar datos asociados a un CUIT:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-clientes-consultar-cuit-en-constancia-de-inscripcion

```
   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
   $response             = $tusfacturas_sdk_obj->cliente_afip_info("1111111111");
```


### Consulta de numeración de comprobantes:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-consultar-numeracion-de-comprobantes.

```
   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
  
  // ===== Armo los datos a enviar =====
  
   $comprobante_data["tipo"] 		= "FACTURA B";
   $comprobante_data["punto_venta"] 	= "0001"; 
   $comprobante_data["operacion"] 	= "V"; 
   
   
  // ===== Consulto la proxima numeracion =====
   
   $response = $tusfacturas_sdk_obj->numeracion (  $tusfacturas_sdk_entidades->comprobante($comprobante_data) );
					  
					  


   // ===== Controlo si hay error =====

   if (!$tusfacturas_sdk_obj->hay_error($response)) {
            echo "<p>
                      Proximo numero a generar : ".$response->comprobante->numero. " 
                  </p>";

   }else{
            echo "<p>
                      Se han encontrado los siguientes errores:<br />
                       ".implode("<br />",$response->errores). "
                  </p>";
   }
   					  
   
   
   
```



### Generar Nuevo Comprobante de tipo factura:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-nuevo-comprobante

```
   $tusfacturas_sdk_obj          = new tusfacturas_sdk();
   $tusfacturas_sdk_entidades	 = new tusfacturas_sdk_entidades();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );



   // ===== Datos del cliente  =======
   
   $cliente_data	=  array(
                          "documento_tipo" => "DNI",    
                          "razon_social"   => "JUAN PEREZ NIO",
                          "email"          => "email@email.com",
                          "domicilio"      => "Avenida Siempreviva 742",
                          "documento_nro"  => "12345678",
                          "provincia"      => 1,
                          "envia_por_mail" => "S",
                          "condicion_pago" => 0,
                          "condicion_iva"  => "CF" 
                        );
                                        
                                          
   // =====  Datos del comprobante a generar =====
   
   // Cabecera de la factura
   
   $comprobante_data["tipo"] 		= "FACTURA B";
   $comprobante_data["punto_venta"] 	= "0001";
   $comprobante_data["fecha"]		= date("d/m/Y");
   $comprobante_data["numero"]		= 20;
   // (Existen otros datos opcionales a enviar, consultar la documentación)


   // Armo el detalle de los conceptos

$comprobante_data["detalle"]     = array();
$comprobante_data["detalle"][]   = $tusfacturas_sdk_entidades->comprobante_detalle_item(
						    array (
							  "cantidad" 		=> 1,
							  "afecta_stock"  	=> "N",
							  "leyenda"		=> "",
							  "producto"		=> $tusfacturas_sdk_entidades->producto( 
                                                                        array(
                                                                              "descripcion" 	=> "HONORARIOS",
                                                                              "unidad_bulto"    => 1,
                                                                              "lista_precios"   => "Lista general",
                                                                              "codigo"		=> "HON",	
                                                                              "precio_unitario_sin_iva" => 100,
                                                                              "alicuota"	=> 21,
                                                                              "unidad_medida" 	=> 7
                                                                           )
                                                         )
                                       )
                                    );
				    
   // totales
   
   $comprobante_data["total"]	   = 121; 



   // ===== Envio a generar el comprobante =====
   
   $response = $tusfacturas_sdk_obj->comprobante_nuevo(  
   						$tusfacturas_sdk_entidades->comprobante($comprobante_data) , 
                                             	$tusfacturas_sdk_entidades->comprobante_cliente($cliente_data)     
                                          	);
					  
					  


   // ===== Controlo si hay error =====

   if (!$tusfacturas_sdk_obj->hay_error($response)) {
            echo "<p>
                      Comprobante generado correctamente:<br />
                      CAE: ".$response->cae. " (Vencimiento: ".$response->vencimiento_cae. " ) <br />
                      Factura PDF: ".$response->comprobante_pdf_url. "
                  </p>";

   }else{
            echo "<p>
                      Se han encontrado los siguientes errores:<br />
                       ".implode("<br />",$response->errores). "
                  </p>";
   }
   					  
					  
                                          
```

### Generar Nuevo Comprobante de nota de débito detallando comprobantes asociados:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-nuevo-comprobante

```
   $tusfacturas_sdk_obj          = new tusfacturas_sdk();
   $tusfacturas_sdk_entidades	 = new tusfacturas_sdk_entidades();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );



   // ===== Datos del cliente  =======
   
   $cliente_data	=  array(
                          "documento_tipo" => "DNI",    
                          "razon_social"   => "JUAN PEREZ NIO",
                          "email"          => "email@email.com",
                          "domicilio"      => "Avenida Siempreviva 742",
                          "documento_nro"  => "12345678",
                          "provincia"      => 1,
                          "envia_por_mail" => "S",
                          "condicion_pago" => 0,
                          "condicion_iva"  => "CF" 
                        );
                                        
                                          
   // =====  Datos del comprobante a generar =====
   
   // Cabecera de la nota de debito
   
   $comprobante_data["tipo"] 		= "NOTA DE DEBITO B";
   $comprobante_data["punto_venta"] 	= "0001";
   $comprobante_data["fecha"]		= date("d/m/Y");
   $comprobante_data["numero"]		= 20;
   // (Existen otros datos opcionales a enviar, consultar la documentación)

   // comprobantes asociados 
   $comprobante_data["comprobantes_asociados"][]		=  $tusfacturas_sdk_entidades->comprobantes_asociados_detalle_item('03/03/2021', 'FACTURA B', '0003', '123', '1234567890') ;
   $comprobante_data["comprobantes_asociados"][]		=  $tusfacturas_sdk_entidades->comprobantes_asociados_detalle_item('03/03/2021', 'FACTURA B', '0003', '124', '1234567890') ;
 

   // Armo el detalle de los conceptos

$comprobante_data["detalle"]     = array();
$comprobante_data["detalle"][]   = $tusfacturas_sdk_entidades->comprobante_detalle_item(
						    array (
							  "cantidad" 		=> 1,
							  "afecta_stock"  	=> "N",
							  "leyenda"		=> "",
							  "producto"		=> $tusfacturas_sdk_entidades->producto( 
                                                                        array(
                                                                              "descripcion" 	=> "HONORARIOS",
                                                                              "unidad_bulto"    => 1,
                                                                              "lista_precios"   => "Lista general",
                                                                              "codigo"		=> "HON",	
                                                                              "precio_unitario_sin_iva" => 100,
                                                                              "alicuota"	=> 21,
                                                                              "unidad_medida" 	=> 7
                                                                           )
                                                         )
                                       )
                                    );
				    
   // totales
   
   $comprobante_data["total"]	   = 121; 



   // ===== Envio a generar el comprobante =====
   
   $response = $tusfacturas_sdk_obj->comprobante_nuevo(  
   						$tusfacturas_sdk_entidades->comprobante($comprobante_data) , 
                                             	$tusfacturas_sdk_entidades->comprobante_cliente($cliente_data)     
                                          	);
					  
					  


   // ===== Controlo si hay error =====

   if (!$tusfacturas_sdk_obj->hay_error($response)) {
            echo "<p>
                      Comprobante generado correctamente:<br />
                      CAE: ".$response->cae. " (Vencimiento: ".$response->vencimiento_cae. " ) <br />
                      Factura PDF: ".$response->comprobante_pdf_url. "
                  </p>";

   }else{
            echo "<p>
                      Se han encontrado los siguientes errores:<br />
                       ".implode("<br />",$response->errores). "
                  </p>";
   }
   					  
					  
                                          
```

### Generar Nuevo Comprobante de nota de crédito indicando periodo asociados:
Documentación: https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-nuevo-comprobante

```
   $tusfacturas_sdk_obj          = new tusfacturas_sdk();
   $tusfacturas_sdk_entidades	 = new tusfacturas_sdk_entidades();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );



   // ===== Datos del cliente  =======
   
   $cliente_data	=  array(
                          "documento_tipo" => "DNI",    
                          "razon_social"   => "JUAN PEREZ NIO",
                          "email"          => "email@email.com",
                          "domicilio"      => "Avenida Siempreviva 742",
                          "documento_nro"  => "12345678",
                          "provincia"      => 1,
                          "envia_por_mail" => "S",
                          "condicion_pago" => 0,
                          "condicion_iva"  => "CF" 
                        );
                                        
                                          
   // =====  Datos del comprobante a generar =====
   
   // Cabecera de la nota de credito
   
   $comprobante_data["tipo"] 		= "NOTA DE CREDITO B";
   $comprobante_data["punto_venta"] 	= "0001";
   $comprobante_data["fecha"]		= date("d/m/Y");
   $comprobante_data["numero"]		= 220;
   // (Existen otros datos opcionales a enviar, consultar la documentación)

   // comprobantes asociados por periodo
   $comprobante_data["comprobantes_asociados_periodo"]		=  $tusfacturas_sdk_entidades->comprobantes_asociados_periodo('03/03/2021', '10/03/2021' ) ;

 

   // Armo el detalle de los conceptos

$comprobante_data["detalle"]     = array();
$comprobante_data["detalle"][]   = $tusfacturas_sdk_entidades->comprobante_detalle_item(
						    array (
							  "cantidad" 		=> 1,
							  "afecta_stock"  	=> "N",
							  "leyenda"		=> "",
							  "producto"		=> $tusfacturas_sdk_entidades->producto( 
                                                                        array(
                                                                              "descripcion" 	=> "HONORARIOS",
                                                                              "unidad_bulto"    => 1,
                                                                              "lista_precios"   => "Lista general",
                                                                              "codigo"		=> "HON",	
                                                                              "precio_unitario_sin_iva" => 100,
                                                                              "alicuota"	=> 21,
                                                                              "unidad_medida" 	=> 7
                                                                           )
                                                         )
                                       )
                                    );
				    
   // totales
   
   $comprobante_data["total"]	   = 121; 



   // ===== Envio a generar el comprobante =====
   
   $response = $tusfacturas_sdk_obj->comprobante_nuevo(  
   						$tusfacturas_sdk_entidades->comprobante($comprobante_data) , 
                                             	$tusfacturas_sdk_entidades->comprobante_cliente($cliente_data)     
                                          	);
					  
					  


   // ===== Controlo si hay error =====

   if (!$tusfacturas_sdk_obj->hay_error($response)) {
            echo "<p>
                      Comprobante generado correctamente:<br />
                      CAE: ".$response->cae. " (Vencimiento: ".$response->vencimiento_cae. " ) <br />
                      Factura PDF: ".$response->comprobante_pdf_url. "
                  </p>";

   }else{
            echo "<p>
                      Se han encontrado los siguientes errores:<br />
                       ".implode("<br />",$response->errores). "
                  </p>";
   }
   					  
					  
                                          
```
