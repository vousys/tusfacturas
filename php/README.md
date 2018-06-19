# TusFacturas.com.ar - API Factura electrónica AFIP - PHP SDK

Mediante nuestra API podrás conectar tu sistema de gestión actual, con nuestra plataforma y emitir facturas electrónicas AFIP válidas. Estamos homologados por AFIP.

Encontrá toda la documentación aquí: https://tusfacturas.gitbook.io/api-factura-electronica-afip.
Registrate en: https://www.tusfacturas.com.ar/

Ejemplo de uso:

   $tusfacturas_sdk_obj  = new tusfacturas_sdk();
   $tusfacturas_sdk_obj->set_keys( TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN  );
   $tusfacturas_sdk_obj->estado_servicios();
