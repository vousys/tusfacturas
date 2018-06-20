# TusFacturas.com.ar - API Factura electrónica AFIP - PHP SDK

Mediante nuestra API podrás conectar tu sistema de gestión actual, con nuestra plataforma y emitir facturas electrónicas AFIP válidas. Estamos homologados por AFIP.

Encontrá toda la documentación aquí: https://tusfacturas.gitbook.io/api-factura-electronica-afip.
Registrate en: https://www.tusfacturas.com.ar/

## Ejemplos de uso.


### Estado de los servicios:
Documentación: https://tusfacturas.gitbook.io/api-factura-electronica-afip/estado-de-los-servicios-afip

```
   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
   $tusfacturas_sdk_obj->estado_servicios();
```


### Generar Nuevo Comprobante:
Documentación: https://tusfacturas.gitbook.io/api-factura-electronica-afip/facturacion-nuevo-comprobante

```
   $tusfacturas_sdk_obj          = new tusfacturas_sdk();
   $tusfacturas_sdk_entidades	 = new tusfacturas_sdk_entidades();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );



   // ===== Datos del cliente  =======
   
   $cliente_data	=  array(
                          "documento_tipo" => "DNI",    
                          "razon_social"   => "JUAN PEREZ",
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
   $comprobante_data["fecha"]		= "20/05/2018";
   $comprobante_data["numero"]		= 20;
   // (Existen otros datos opcionales a enviar, consultar la documentación)


   // Armo el detalle de los conceptos

$comprobante_data["detalle"]     = array();
$comprobante_data["detalle"][]   = $tusfacturas_sdk_entidades->comprobante_detalle_item(
						    array (
							  "cantidad" 		=> $pedido_items["cantidad"],
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
   
   $tusfacturas_sdk_obj->comprobante_nuevo(  $tusfacturas_sdk_entidades->comprobante($comprobante_data) , 
                                             $tusfacturas_sdk_entidades->comprobante_cliente($cliente_data)     
                                          );
                                          
```
