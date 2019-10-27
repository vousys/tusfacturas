<?
 /*
 * Copyright (c) Verónica Osorio para VOUSYS.com  
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * 
 * ======================================================================================
 * Class for tusfacturas.app
 * ======================================================================================
 * 
 * API Version:    2.0
 * Encoding:       UTF-8  
 * 
 * @author:         Verónica Osorio para VOUSYS.com 
 * @last-update:    2018-08-18
 * 
 * 
 * METODOS INCLUIDOS:
 *     comprobante                 Arma la estructura del comprobante, requerida para crear un nuevo comprobante .
 *     comprobante_cliente         Arma la estructura del cliente, requerida para crear un nuevo comprobante .
 *     comprobante_detalle_item    Arma la estructura del item para el objeto "detalle" de un comprobante.    
 *     producto                    Arma la estructura del producto, que componen un item del detalle.
 * 
 * ======================================================================================
 */
 
include_once "tusfacturas_sdk.php";

class tusfacturas_sdk_entidades extends tusfacturas_sdk{

    function __construct() {
        //
    }    



    /************************************************************** 
     *
     * FUNCIONALIDAD:  Genera el objeto comprobante  requerido para la 
     *                 creacion de un nuevo comprobante
     *         
     * DOCUMENTACION:  
     *                 https://tusfacturas.gitbook.io/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-detalle-de-conceptos
     *                                   
     * PARAMETROS:
     *
     *     @param object $comprobante_data       Un array formado de la siguiente manera:
     * 
                                                    fecha            Campo fecha. Para facturación afip deberá ser la fecha del día. Formato esperado: dd/mm/aaaa. Ejemplo: 13/05/2018 
                                                    tipo            Campo alfabético según tabla de referencia de Tipos de comprobantes(***).  
                                                    operacion       Campo alfanumérico. Longitud 1 caracter. Indica si envia una factura de venta (V) o de compra (C). Valores Permitidos: V, C Ejemplo: V 
                                                    idioma          Campo numérico. Longitud 1 caracter. Indica el idioma en que se imprimira el PDF del comprobante. Valores Permitidos: 1 = Español, 2= Ingles  
                                                    punto_venta     Campo numérico entero. Longitud máxima 5 digitos. 
                                                    moneda          Campo alfanumérico de 3 Digitos según tabla de referencia de Monedas .
                                                    cotizacion      Campo numérico con 2 decimales. Puede obtener la cotización del día según AFIP desde nuestro método de consulta de cotización Ejemplo: 15.20 
                                                    numero           El numero del comprobante a generar. Campo numérico entero. Longitud máxima 8 digitos. La numeración será validada internamente previa generación del comprobante contra AFIP. Ejemplo: 4567
                                                    periodo_facturado_desde     Campo fecha. Contenido opcional. Formato esperado: dd/mm/aaaa. 
                                                    periodo_facturado_hasta    Campo fecha. Contenido opcional. Formato esperado: dd/mm/aaaa.
                                                    rubro            Campo alfanumérico. Longitud máxima 255 caracteres. Indica el rubro al cual pertenecerá el comprobante. Ésta información no saldrá impresa en el comprobante. 
                                                    rubro_grupo_contable    Campo alfanumérico. Longitud máxima 255 caracteres. Indica el grupo contable al que pertenece el rubro. Ésta información no saldrá impresa en el comprobante. 
                                                    

                                                    abono            Campo alfabetico,  Longitud 1 caracter. Valores esperados: S o N
                                                    abono_frecuencia Campo numerico, indica cada que cantidad de meses se va a repetir ese abono.
                                                    abono_hasta      Campo fecha. Formato mm/yyyy. Indica hasta que mes y año se va a ejecutar el abono
                                                    abono_actualiza_precios  Campo alfabetico,  Longitud 1 caracter. Valores esperados: S o N. Indica si se actualiza el precio de los productos facturados en cada abono que se cree.


                                                    detalle          Lista de conceptos a facturar. Objeto JSON Según estructura que se detalla en la documentacion
                                                    fex              Solo para comprobantes de tipo E. Según estructura detallada en la documentacion de Factura electronica de exportacion". 
                                                    bonificacion     Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor aplicado en concepto de bonificación sin IVA Ejemplo: 12.67. Tener en cuenta para el cálculo que la bonificación se aplica sobre el primer subtotal SIN IVA y se lo gravará con el importe de IVA que le corresponda. 
                                                    leyenda_gral     Campo alfanumérico. Longitud máxima 255 caracteres. Contenido opcional. Es una leyenda general que saldrá impresa en el bloque central de productos del comprobante Ejemplo: Aplica plan 12 cuotas sin interes. 
                                                    comentario:      Campo alfanumerico, opcional. Longitud máxima: 255 caracteres. Éste campo no saldrá impreso en la factura.
                                                    percepciones_iibb    Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la percepción de ingresos brutos realizada Ejemplo: 142.67 
                                                    percepciones_iva     Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la percepción de IVA realizada Ejemplo: 42.67 
                                                    exentos          Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de exentos. Solo para comprobantes A y M Ejemplo: 72.67 
                                                    nogravados       Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de no gravados. Solo para comprobantes A y M Ejemplo: 62.67 impuestos_internos    Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de impuestos internos Ejemplo: 2.67 
                                                    total            Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la sumatoria de conceptos incluyendo IVA e impuestos. Ejemplo: 12452.67
     *  
     * RESPUESTA:
     *     @return object  $comprobante     El array requerido para generar un comprobante.
     *
     * @last-update  2018-08-17 
     *************************************************************** */


    function comprobante($comprobante_data) {


            // Cabecera del comprobante
            $comprobante                     = array();

            $comprobante["tipo"]             = $comprobante_data["tipo"];
            $comprobante["operacion"]        = (trim($comprobante_data["operacion"])     == '' ? "V" : $comprobante_data["operacion"]);
            $comprobante["punto_venta"]      = (intval($comprobante_data["punto_venta"]) == 0  ? 1 : $comprobante_data["punto_venta"]);
            $comprobante["fecha"]            = $comprobante_data["fecha"];
            $comprobante["idioma"]           = (trim($comprobante_data["idioma"])        == '' ? 1 : $comprobante_data["idioma"]);
            $comprobante["moneda"]           = (trim($comprobante_data["moneda"])        == '' ? "PES" : $comprobante_data["moneda"]);
            $comprobante["cotizacion"]       = (intval($comprobante_data["cotizacion"])  == 0  ? 1 : $comprobante_data["cotizacion"]);
            $comprobante["numero"]           = (trim($comprobante_data["numero"])           == '' ? 0 : $comprobante_data["numero"]);
            $comprobante["periodo_facturado_desde"] = $comprobante_data["periodo_facturado_desde"];
            $comprobante["periodo_facturado_hasta"] = $comprobante_data["periodo_facturado_hasta"];
            $comprobante["rubro"]             = utf8_encode(trim($comprobante_data["rubro"])        == '' ? "Deudores Varios" : $comprobante_data["rubro"]); 
            $comprobante["rubro_grupo_contable"] = utf8_encode (trim($comprobante_data["rubro_grupo_contable"]) == '' ? "Ventas" : $comprobante_data["rubro_grupo_contable"]); 

            // Detalle de comprobante
            $comprobante["detalle"]           = $comprobante_data["detalle"]; 

 
            // ABONOS
            $comprobante["abono"]             =  ( $comprobante_data["abono"] == "S" ? 1 : 2);
            $comprobante["abono_frecuencia"]  =  ( intval($comprobante_data["abono_frecuencia"]) == 0 ? 1 : $comprobante_data["abono_frecuencia"] ) ;
            $comprobante["abono_hasta"]       =  ( trim($comprobante_data["abono_hasta"]) == '' ? date('m/Y', strtotime('+1 month')) : (strlen($comprobante_data["abono_hasta"]) == 10 ?  substr($comprobante_data["abono_hasta"],3, 7)  : $comprobante_data["abono_hasta"] )   );
            $comprobante["abono_actualiza_precios"] = ( $comprobante_data["abono_actualiza_precios"] == "S" ? 1 : 2);
            // ABONOS


            // totales
            $comprobante["bonificacion"]      = doubleval($comprobante_data["bonificacion"]); 
            $comprobante["leyenda_gral"]      = utf8_encode( $comprobante_data["leyenda_gral"]); 
            $comprobante["comentario"]        = utf8_encode($comprobante_data["comentario"]); 
            $comprobante["percepciones_iibb"] = doubleval($comprobante_data["percepciones_iibb"]); 
            $comprobante["percepciones_iva"]  = doubleval($comprobante_data["percepciones_iva"]); 
            $comprobante["exentos"]           = doubleval($comprobante_data["exentos"]); 
            $comprobante["nogravados"]        = doubleval($comprobante_data["nogravados"]); 
            $comprobante["total"]             = round(doubleval($comprobante_data["total"]),3); 
              

             return($comprobante);

    }

    /************************************************************** 
     *
     * FUNCIONALIDAD:  Genera elobjeto "cliente" requerido para la 
     *                 creacion de un nuevo comprobante
     *         
     * DOCUMENTACION:  
     *                https://tusfacturas.gitbook.io/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-cliente
     *                                   
     * PARAMETROS:
     *
     * @param object $cliente_data         Un array formado de la siguiente manera:            

                                                    documento_tipo:   Valores Permitidos: CUIT , DNI 
                                                    documento_nro:    Campo numérico, sin puntos ni guiones.  
                                                    razon_social :    Campo alfanumérico. Longitud máxima 255 caracteres.  
                                                    email:            Campo alfanumérico. Longitud máxima 255 caracteres.   
                                                    domicilio:    Campo alfanumérico. Longitud máxima 255 caracteres. 
                                                    provincia:        Campo numérico según tabla de referencia(*).  
                                                    envia_por_mail:   Indica Si/No para el envio del comprobante por e-mail. Valores Permitidos: S , N 
                                                    condicion_pago:    Campo numérico que indica la cantidad de dias en los cuales vence el plazo de pago. Valores Permitidos: 0,30,60,90  
                                                    condicion_pago_otra:    Campo alfabetico que indica la descripcion de la nueva condicion de pago.  
                                                    condicion_iva:    Campo numérico que indica la condicion de iva, según tabla de referencia Condiciones ante el IVA(**).  
     *                                              
     * RESPUESTA:
     *     @return object  $cliente                 El array requerido para generar un comprobante.
     *
     * @last-update  2018-05-19 
     *************************************************************** */


    function comprobante_cliente($parametros) {

                $cliente = array(
                    "documento_tipo" => (trim($parametros["documento_tipo"]) == '' ? "DNI" : $parametros["documento_tipo"] ) ,    
                    "razon_social"   => utf8_encode($parametros["razon_social"]),
                    "email"          => $parametros["email"],
                    "domicilio"      => utf8_encode($parametros["domicilio"]),
                    "documento_nro"  => $parametros["documento_nro"],
                    "provincia"      => (intval($parametros["provincia"])   != 0  ? $parametros["provincia"] : 1),
                    "envia_por_mail" => (trim($parametros["envia_por_mail"])!= '' ? $parametros["envia_por_mail"] : "N"),
                    "condicion_pago" => intval($parametros["condicion_pago"]),
                    "condicion_pago_otra" => intval($parametros["condicion_pago_otra"]),
                    "condicion_iva"  => $parametros["condicion_iva"]  
                );

                return($cliente);


     }

    /************************************************************** 
     *
     * FUNCIONALIDAD:  Genera la entidad "item" de concepto para el detalle de un comprobante 
     *                 
     *                 La entidad item, es parte del detalle de conceptos del comprobante    
     *         
     * DOCUMENTACION:  
     *                 https://tusfacturas.gitbook.io/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-detalle-de-conceptos
     *                                   
     * PARAMETROS:
     *
     *     @param object $parametros     Todos los datos requeridos para armar el producto:
     *     
                                        cantidad         Campo numérico con 2 decimales. Separador de decimales: punto. Ejemplo: 1.50 
                                        afecta_stock     Campo alfanumérico de 1 posición. Valores posibles: "S" (si), "N" (no) Ejemplo: S 
                                        producto         Según estructura de producto 
                                        actualiza_precio Campo alfanumérico de 1 posición. Valores posibles: "S" (si), "N" (no) Ejemplo: S 
                                        leyenda          Campo alfanumérico. Longitud máxima 100 caracteres. Contenido opcional. Será una descripción que acompañe al producto. Ejemplo: Blanca, cepillada 
     *
     * RESPUESTA:
     *     @return object  $producto     El array requerido
     *
     * @last-update  2018-08-17 
     *************************************************************** */


    function comprobante_detalle_item($parametros) {

            if (intval($parametros["cantidad"])> 0) {

                        $item = array(
                                    "cantidad"         => $parametros["cantidad"],
                                    "afecta_stock"     => (trim($parametros["afecta_stock"]) != '' ? $parametros["afecta_stock"] : "N"),
                                    "leyenda"          => utf8_encode( $parametros["leyenda"]),
                                    "producto"         => $parametros["producto"],
                                    "actualiza_precio" => (trim($parametros["actualiza_precio"]) == '' ? "N"  : $parametros["actualiza_precio"])
                                );
             
            }else{
                        $item = array(
                                    "cantidad"         => 0,
                                    "afecta_stock"     => "N",
                                    "leyenda"          => "",
                                    "actualiza_precio" => "N" ,
                                    "producto"         => array()
                                );
           }

           return($item);

    }



    /************************************************************** 
     *
     * FUNCIONALIDAD:  Genera la entidad "producto" requerida para
     *                 crear un nuevo comprobante.
     *                 
     *                 La entidad producto, es parte de cada item
     *                 del detalle de conceptos del comprobante    
     *         
     * DOCUMENTACION:  
     *                 https://tusfacturas.gitbook.io/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-concepto-producto
     *                                   
     * PARAMETROS:
     *
     *     @param object $parametros     Todos los datos requeridos para armar el producto:
     *     
     *                                   descripcion         Campo alfanumérico. Longitud máxima 255 caracteres. Ejemplo: Papa blanca
                                         unidad_bulto        Campo numérico entero. Indica la cantidad de unidades que componen un bulto. Ejemplo: 12
                                         lista_precios       Campo alfanumérico. Longitud máxima 255 caracteres. Nombre de la lista de precios a la cual pertenece. Ejemplo: Verdura Orgánica 
                                         codigo              Campo alfanumérico. Longitud máxima 10 caracteres. campo Opcional Ejemplo: ABX780 
                                         precio_unitario_sin_iva Campo numérico con 2 decimales. separador de decimales: punto Ejemplo: 645.67 
                                         alicuota            Indica la alicuota de IVA con la que grava ese producto. Valores Permitidos: 21 , 10.5 Ejemplo: 10.5 
                                         unidad_medida       Campo numérico que indica la unidad de medida, según tabla de referencia Unidades de Medida(**). Ejemplo: 7 
     *
     * RESPUESTA:
     *     @return object  $producto     El array requerido
     *
     * @last-update  2018-05-19 
     *************************************************************** */


    function producto($parametros) {


                $producto = array(
                    "codigo"        => utf8_encode( $parametros["codigo"]) ,    
                    "descripcion"   => utf8_encode( $parametros["descripcion"]),
                    "unidad_bulto"  => ( intval($parametros["unidad_bulto"]) != 0 ? $parametros["unidad_bulto"] : 1),
                    "lista_precios" => utf8_encode(trim($parametros["lista_precios"])   != '' ? $parametros["lista_precios"] : 'Lista general'),
                    "precio_unitario_sin_iva" => round($parametros["precio_unitario_sin_iva"] ,3) ,
                    "alicuota"      => $parametros["alicuota"],
                    "unidad_medida" => ( intval($parametros["unidad_medida"]) != 0 ? $parametros["unidad_medida"] : 7)  
                );

                return($producto);

    }


}


?>
