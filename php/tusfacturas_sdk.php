<?

 /* Copyright (c) Verónica Osorio para VOUSYS.com 
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * 
 * =======================================================================
 * Class for tusfacturas.app
 * =======================================================================
 * SDK Version:    1.0   
 * last-update:    2020-04-04
 * API Version:    2.0
 * Encoding:       UTF-8  
 * 
 * @author:         Verónica Osorio para VOUSYS.com 
 * 
 * METODOS incluidos:
 *         
 *   comprobante_nuevo     Genera un nuevo comprobante.
 *   numeracion            Obtiene el proximo nro de comprobante
 *   hay_error             Determina si hay o no error en la respuesta
 *   api_call              Hace la llamada a la API  
 *   dump                  Para debug: hace un dump del array
 *   estado_servicios      Consulta de estado de servicios
 * 
 * ======================================================================
 * ¿Como usar la clase? 
 * ======================================================================
 * 
 
   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
   $tusfacturas_sdk_obj->estado_servicios();

 * 
 * =======================================================================
 */

const TUSFACTURAS_API_VERSION     = 2;
const TUSFACTURAS_PATH            = "https://www.tusfacturas.app/app/api/v".TUSFACTURAS_API_VERSION."/";


include "tusfacturas_sdk_entidades.php";


class tusfacturas_sdk {

    var $apitoken;            // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits
    var $apikey;              // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits  
    var $usertoken;           // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits
 
    var $json_data;           // JSON a enviar a la API.   (Sirve por si luego quiero guardar lo enviado a modo de log) 
    var $json_respuesta;      // JSON devuelto por la API. (Sirve por si luego quiero guardar lo enviado a modo de log) 

    var $debug;               // true / false para ir recibiendo dumps

     
    function __construct() {
       
       $this->json_data        = "";
       $this->json_respuesta   = "";
       $this->debug            = false;

    }

    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     * ---------------
     *                 Configura las KEYS de tu cuenta
     * PARAMETROS: 
     * ------------
     *    @param string   $apikey                // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits
     *    @param string   $apitoken                // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits
     *    @param string   $usertoken             // Tus credenciales de acceso. Registrate en tusfacturas.app y obtenelas desde Mis Cuits
     * 
     * RESPUESTA:
         none
     *
     * @last-update  2018-06-18
     *************************************************************************************************************** */

    function set_keys($apikey,$apitoken,$usertoken) {
       $this->apikey           = $apikey;
       $this->apitoken         = $apitoken;
       $this->usertoken        = $usertoken;
   }

    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     * ---------------
     *                 Mediante éste método podrás generar comprobantes de venta o de compra.
     *
     * DOCUMENTACION: 
     * --------------- 
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante
     * 
     * PARAMETROS: 
     * ------------
     *    @param objeto   $comprobante_data       segun tusfacturas_sdk_entidades->comprobante();
     *    @param objeto   $cliente_data           segun tusfacturas_sdk_entidades->comprobante_cliente();  
     * 
     * RESPUESTA:
         @return object $resultado
     *
     * @last-update  2018-06-18
     *************************************************************************************************************** */


    function comprobante_nuevo($comprobante_data,$cliente_data) {

        // preparo la info a enviar
        $data               = $this->request_preparar($comprobante_data,$cliente_data) ;

        // Si habilito debug, dump del array
        if ($this->debug)         $this->dump($data); 

        // Encodeo la data
        $this->json_data    = json_encode ($data);

        // Do API CALL
        $resultado          = $this->api_call("facturacion/nuevo");

        return($resultado);
    }


    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     * ---------------
     *                 Mediante éste método me prepara el request a enviar
     *
     * DOCUMENTACION: 
     * --------------- 
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante
     * 
     * PARAMETROS: 
     * ------------
     *    @param objeto   $comprobante_data       segun tusfacturas_sdk_entidades->comprobante();
     *    @param objeto   $cliente_data           segun tusfacturas_sdk_entidades->comprobante_cliente();  
     * 
     * RESPUESTA:
         @return object $data
     *
     * @last-update  2020-04-20
     *************************************************************************************************************** */


    function request_preparar($comprobante_data,$cliente_data) {

        // Credenciales de acceso

        $data["apitoken"]   = $this->apitoken;
        $data["apikey"]     = $this->apikey;
        $data["usertoken"]  = $this->usertoken;

        // Datos del comprobante
        $data["comprobante"]= $comprobante_data; 
        $data["cliente"]    = $cliente_data; 
 

        return($data);
    }




    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     * ---------------
     *                 Mediante éste método podrás generar comprobantes de venta o de compra.
     *
     * DOCUMENTACION: 
     * --------------- 
     *                 https://developers.tusfacturas.app/api-factura-electronica-afip/facturacion-nuevo-comprobante
     * 
     * PARAMETROS: 
     * ------------
     *    @param objeto   $requests_preparados     un array con la info que se requiere para generar un comprobante 
     *                                             se debe iterar el metodo "request_preparar" 
     *                                             tantas veces como sea necesario para generar cada request
     *                                                  ->request_preparar($comprobante_data,$cliente_data) ;


     * 
     * RESPUESTA:
         @return object $resultado
     *
     * @last-update  2020-04-04
     *************************************************************************************************************** */


    function comprobante_lotes($requests_preparados) {

        // Credenciales de acceso

        $data["apitoken"]   = $this->apitoken;
        $data["apikey"]     = $this->apikey;
        $data["usertoken"]  = $this->usertoken;

        // Datos del comprobante
        $data["requests"]   = $requests_preparados; 

        // Si habilito debug, dump del array
        if ($this->debug)         $this->dump($data); 

        // Encodeo la data
        $this->json_data    = json_encode ($data);

        // Do API CALL
        $resultado          = $this->api_call("facturacion/lotes");

        return($resultado);
    }
 
    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     *                 Mediante éste método podrás consultar si los servicios de AFIP se encuentran funcionando 
     *                 correctamente y/o si nuestra plataforma tiene que notificarte algún evento.
     *
     * DOCUMENTACION: 
     *                https://developers.tusfacturas.app/api-factura-electronica-afip/estado-de-los-servicios-afip
     * 
     * PARAMETROS: 
     *               ninguno
     * RESPUESTA:
     *     @return object $resultado
     *
     * @last-update  2018-06-18
     *************************************************************************************************************** */


    function estado_servicios() {

        // Credenciales de acceso

        $data["apitoken"]   = $this->apitoken;
        $data["apikey"]     = $this->apikey;
        $data["usertoken"]  = $this->usertoken;
       
       // Si habilito debug, dump del array
        if ($this->debug)         $this->dump($data); 

        // Encodeo la data
        $this->json_data    = json_encode ($data);

        // Do API CALL
        $resultado          = $this->api_call("estado_servicios/alertas");

        return($resultado);
    }


    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     *                 Mediante éste método podrás consultar la próxima numeración de un tipo de comprobante.
     *
     * DOCUMENTACION: 
     *                https://developers.tusfacturas.app/api-factura-electronica-afip/consultar-numeracion-de-comprobantes.
     * 
     * PARAMETROS: 
     *     @param object $comprobante      Un array formado de la siguiente manera:
     * 
     *                                 $comprobante["tipo"]         = Segun tabla referencia: https://developers.tusfacturas.app/api-factura-electronica-afip/tablas-de-referencia#tipos-de-comprobantes
     *                                 $comprobante["operacion"]    = "V" o "C" segun corresponda para ventas o compras
     *                                 $comprobante["punto_venta"]  = El punto de venta asociado a tu CUIT
     * RESPUESTA:
     *     @return object $resultado
     *
     * @last-update  2018-06-18
     *************************************************************************************************************** */


    function numeracion($comprobante) {

        // Credenciales de acceso

        $data["apitoken"]   = $this->apitoken;
        $data["apikey"]     = $this->apikey;
        $data["usertoken"]  = $this->usertoken;
        $data["comprobante"]= $comprobante; 

       // Si habilito debug, dump del array
        if ($this->debug)         $this->dump($data); 

        // Encodeo la data
        $this->json_data    = json_encode ($data);

        // Do API CALL
        $resultado          = $this->api_call("facturacion/numeracion");

        return($resultado);
    }


    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     *          Determina si hay error o no
     * 
     * PARAMETROS: 
     *         @param   object  $resultado 
     *
     * RESPUESTA:
     *         @return  boolean true/false
     *
     * @last-update  2018-06-19
     *************************************************************************************************************** */


    function hay_error($resultado) {

        return(  $resultado->error == "S" ? true : false    );

    }

    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     *          hace un dump del array
     * 
     * PARAMETROS: 
     *         @param   array  $data  
     *
     * RESPUESTA:
     *         none
     *
     * @last-update  2018-06-19
     *************************************************************************************************************** */


    function dump($data) {

       echo "--- TUSFACTURAS SDK --- DUMP ---- <br /><pre>"; print_r($data); echo "</pre><br /> --- TUSFACTURAS SDK --- DUMP ---- <br />";

    }




    /****************************************************************************************************************
     *
     * FUNCIONALIDAD: 
     *         Mediante éste método se ejecutaran las llamdas en TusFacturas
     *
     * PARAMETROS:
     * 
     *         @param JSON     $json_data      El JSON a enviar con la informacion
     *         @param string   $metodo         El metodo que voy a ejecutar
     * 
     * RESPUESTA:
     *         @return object  $resultado      El resultado en formato JSON devuelto por la plataforma
     *
     * @last-update  2018-06-18
     *************************************************************************************************************** */

    function api_call($metodo) {

        if ($this->json_data === null) { // ERROR DE ENCODING

            $resultado["error"]     = "S";
            $resultado["errores"][] = utf8_encode("Existian errores de formato en el JSON enviado, que impedian la ejecucion del metodo: ".$metodo) ;

        }else{ // FORMATO OK

                    // open url
                    $url            = TUSFACTURAS_PATH.$metodo;
                    $ch             = curl_init($url );

                    // Data
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->json_data );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );


                   // Si habilito debug 
                    if ($this->debug)   {
                        echo "CURL: ".$url." <br /> <br />
                              DATA: <br /> ".$this->json_data."<br /> <br />";
                    }


                    // Capturo la respuesta
                    $this->json_respuesta     =  curl_exec($ch);

                  // Si habilito debug 
                    if ($this->debug)   {
                        echo "RESPONSE: ".$this->json_respuesta."<br /> <br />";
                    }

                    $resultado                =  json_decode($this->json_respuesta);  
                    
                    // Cierro curl            
                    curl_close($ch);
        }

        return($resultado);

    }


} // end of class



?>
