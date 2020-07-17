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
 * @last-update:    2020-04-04
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
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-detalle-de-conceptos
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
                                                    percepciones_iibb_base    Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la base computada como percepción de ingresos brutos  Ejemplo: 142.67 
                                                    percepciones_iibb_alicuota    Campo numérico con 2 decimales. separador de decimales: punto. Indica la alicuota aplicada como percepción de ingresos brutos  Ejemplo: 142.67 
      
                                                    percepciones_iva     Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la percepción de IVA realizada Ejemplo: 42.67 
                                                    percepciones_iva_base     Campo numérico con 2 decimales. separador de decimales: punto.  Indica el valor monetario de la base computada para percepción de IVA realizada Ejemplo: 42.67 
                                                    percepciones_iva_alicuota     Campo numérico con 2 decimales. separador de decimales: punto. Indica la alicuota aplicada como percepción de IVA  Ejemplo: 42.67 
      
                                                    impuestos_internos_iva     Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de los impuestos internos aplicados Ejemplo: 42.67 
                                                    impuestos_internos_base     Campo numérico con 2 decimales. separador de decimales: punto.  Indica el valor monetario de la base computada para impuestos internos  realizada Ejemplo: 42.67 
                                                    impuestos_internos_alicuota     Campo numérico con 2 decimales. separador de decimales: punto. Indica la alicuota aplicada como impuestos internos  Ejemplo: 42.67 
      
                                                    exentos          Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de exentos. Solo para comprobantes A y M Ejemplo: 72.67 
                                                    nogravados       Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de no gravados. Solo para comprobantes A y M Ejemplo: 62.67 impuestos_internos    Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario en concepto de impuestos internos Ejemplo: 2.67 
                                                    total            Campo numérico con 2 decimales. separador de decimales: punto. Indica el valor monetario de la sumatoria de conceptos incluyendo IVA e impuestos. Ejemplo: 12452.67
     *  
     * RESPUESTA:
     *     @return object  $comprobante     El array requerido para generar un comprobante.
     *
     * @last-update  2020-04-04 
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
            $comprobante["impuestos_internos"] = doubleval($comprobante_data["impuestos_internos"]); 
            $comprobante["impuestos_internos_base"] = doubleval($comprobante_data["impuestos_internos_base"]); 
            $comprobante["impuestos_internos_alicuota"] = doubleval($comprobante_data["impuestos_internos_alicuota"]); 
            $comprobante["percepciones_iibb"] = doubleval($comprobante_data["percepciones_iibb"]); 
            $comprobante["percepciones_iibb_base"] = doubleval($comprobante_data["percepciones_iibb_base"]); 
            $comprobante["percepciones_iibb_alicuota"] = doubleval($comprobante_data["percepciones_iibb_alicuota"]); 
            $comprobante["percepciones_iva"]  = doubleval($comprobante_data["percepciones_iva"]); 
            $comprobante["percepciones_iva_base"]  = doubleval($comprobante_data["percepciones_iva_base"]); 
            $comprobante["percepciones_iva_alicuota"]  = doubleval($comprobante_data["percepciones_iva_alicuota"]); 
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
     *                https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-cliente
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
                    "codigo"         => trim($parametros["codigo"]),
                    "domicilio"      => utf8_encode($parametros["domicilio"]),
                    "documento_nro"  => $parametros["documento_nro"],
                    "provincia"      => (intval($parametros["provincia"])   != 0  ? $parametros["provincia"] : $this->tabla_referencia_provincia($parametros["provincia"]) ),
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
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-detalle-de-conceptos
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
     *                                  bonificacion_porcentaje     Campo numérico con 2 decimales. Separador de decimales: punto. Ejemplo: 1.50               
     * 
     * RESPUESTA:
     *     @return object  $producto     El array requerido
     *
     * @last-update  2020-04-20 
     *************************************************************** */


    function comprobante_detalle_item($parametros) {

            if (intval($parametros["cantidad"])> 0) {

                        $item = array(
                                    "cantidad"         => $parametros["cantidad"],
                                    "bonificacion_porcentaje" => $parametros["bonificacion_porcentaje"],
                                    "afecta_stock"     => (trim($parametros["afecta_stock"]) != '' ? $parametros["afecta_stock"] : "N"),
                                    "leyenda"          => utf8_encode( $parametros["leyenda"]),
                                    "producto"         => $parametros["producto"],
                                    "actualiza_precio" => (trim($parametros["actualiza_precio"]) == '' ? "N"  : $parametros["actualiza_precio"])
                                );
             
            }else{
                        $item = array(
                                    "cantidad"         => 0,
                                    "afecta_stock"     => "N",
                                    "bonificacion_porcentaje" => 0,
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
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante#estructura-de-concepto-producto
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
                                        actualiza_precio Campo alfanumérico de 1 posición. Valores posibles: "S" (si), "N" (no) Ejemplo: S 
                                         impuestos_internos_alicuota    Indica la alicuota de Impuestos internos con la que grava ese producto.  Ejemplo: 10.5 
     *
     * RESPUESTA:
     *     @return object  $producto     El array requerido
     *
     * @last-update  2020-04-20 
     *************************************************************** */


    function producto($parametros) {


                $producto = array(
                    "codigo"        => utf8_encode( $parametros["codigo"]) ,    
                    "descripcion"   => utf8_encode( $parametros["descripcion"]),
                    "unidad_bulto"  => ( intval($parametros["unidad_bulto"]) != 0 ? $parametros["unidad_bulto"] : 1),
                    "lista_precios" => utf8_encode(trim($parametros["lista_precios"])   != '' ? $parametros["lista_precios"] : 'Lista general'),
                    "precio_unitario_sin_iva" => round($parametros["precio_unitario_sin_iva"] ,3) ,
                    "alicuota"      => doubleval($this->tabla_referencia_alicuota($parametros["alicuota"])),
                    "impuestos_internos_alicuota"      => doubleval($parametros["impuestos_internos_alicuota"]),
                    "actualiza_precio" => $parametros["actualiza_precio"],
                    "unidad_medida" => ( intval($parametros["unidad_medida"]) != 0 ? $parametros["unidad_medida"] : 7)  
                );

                return($producto);

    }



    /************************************************************** 
     *
     * FUNCIONALIDAD:  Me devuelve la provincia que corresponde 
     *                 segun tabla de referencia  
     *         
     * DOCUMENTACION:  
     *                 https://developers.tusfacturas.app/tablas-de-referencia#provincias
     *                                   
     * PARAMETROS:
     *
     *     @param       string      el nombre de la provincia
     * RESPUESTA:
     *     @return     number       el nro asociado a esa provincia
     *
     * @last-update  2020-04-12 
     *************************************************************** */

function tabla_referencia_provincia($provincia ) {


    switch(strtoupper(trim($provincia ))) {
        case "BUENOS AIRES":
            return 2;
            break;
        case "CATAMARCA":
            return 3;
            break;
        case "CHACO":
            return 4;
            break;
        case "CHUBUT":
            return 5;
            break;
        case "CIUDAD AUTONOMA DE BUENOS AIRES":
            return 1;
            break;
        case "CORDOBA":
            return 6;
            break;
        case "CORRIENTES":
            return 7;
            break;
        case "ENTRE RIOS":
            return 8;
            break;
        case "FORMOSA":
            return 9;
            break;
        case "JUJUY":
            return 10;
            break;
        case "LA PAMPA":
            return 11;
            break;
        case "LA RIOJA":
            return 12;
            break;
        case "MENDOZA":
            return 13;
            break;
        case "MISIONES":
            return 14;
            break;
        case "NEUQUEN":
            return 15;
            break;
        case "OTRO":
            return 25;
            break;
        case "RIO NEGRO":
            return 16;
            break;
        case "SALTA":
            return 17;
            break;
        case "SAN JUAN":
            return 18;
            break;
        case "SAN LUIS":
            return 19;
            break;
        case "SANTA CRUZ":
            return 20;
            break;
        case "SANTA FE":
            return 21;
            break;
        case "SANTIAGO DEL ESTERO":
            return 22;
            break;
        case "TIERRA DEL FUEGO":
            return 23;
            break;
        case "TUCUMAN":
            return 24;
            break;

        default:
            return 26; 

    }
}


    /************************************************************** 
     *
     * FUNCIONALIDAD:  Me devuelve la alicuota que corresponde 
     *                 segun tabla de referencia  
     *         
     * DOCUMENTACION:  
     *                 https://developers.tusfacturas.app/tablas-de-referencia#alicuotas-de-iva
     *                                   
     * PARAMETROS:
     *
     *     @param           string    alicuota            Indica la alicuota de IVA con la que grava ese producto. 
     *                                                    Valores Permitidos: segun tabla de referencia https://developers.tusfacturas.app/tablas-de-referencia#alicuotas-de-iva
     *
     * RESPUESTA:
     *     @return          number    alicuota
     *
     * @last-update  2020-04-12 
     *************************************************************** */

    function tabla_referencia_alicuota($alicuota) {

        $alicuota = str_replace("%","",$alicuota);

        switch ($alicuota) {
            case "IVA EXENTO":  case "EXENTO":
                return (-1);
                break;
            case "IVA NO GRAVADO": case "NO GRAVADO":
                return (-2);
                break;
            default:
                return doubleval($alicuota);
                break;
            }
    }


}


?>
