<p align="center">
  <a href="https://developers.tusfacturas.app/">
    <img src="https://cdn.tusfacturas.app/web/images/ig/queteresuelvelaapiarcaafip-tusfacturasapp.webp" alt="SDK PHP para facturación electrónica AFIP/ARCA - TusFacturasAPP" width="640">
  </a>
</p>

<p align="center">
  <a href="https://php.net/"><img src="https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg?logo=php&logoColor=white" alt="PHP >= 7.2"></a>
  <a href="https://github.com/vousys/tusfacturas/releases/"><img src="https://img.shields.io/badge/SDK-v1.0-brightgreen" alt="SDK v1.0"></a>
  <a href="https://developers.tusfacturas.app/changelog"><img src="https://img.shields.io/badge/API-v2-orange" alt="API v2"></a>
  <a href="#licencia"><img src="https://img.shields.io/badge/licencia-MIT%20%2F%20GPL-blue" alt="MIT / GPL"></a>
  <a href="https://github.com/vousys/tusfacturas/graphs/commit-activity"><img src="https://img.shields.io/badge/Mantenido-s%C3%AD-green.svg" alt="Mantenido"></a>
</p>

# SDK PHP para Factura Electrónica AFIP/ARCA

Librería PHP para emitir comprobantes electrónicos válidos ante ARCA (ex AFIP) desde tu aplicación, sin tocar SOAP, WSAA, certificados X.509 ni WSFEv1.

Dos archivos, sin dependencias externas, sobre la API REST v2 de [TusFacturasAPP](https://www.tusfacturas.app/).

```php
$sdk = new tusfacturas_sdk();
$sdk->set_keys(TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN);
$response = $sdk->comprobante_nuevo($comprobante, $cliente);

echo $response->cae;  // 65301278726386
```

📚 [Documentación de la API](https://developers.tusfacturas.app/) · 🧪 [Cuenta de prueba gratis](https://www.tusfacturas.app/probar-gratis-api-factura-electronica-tusfacturasapp) · 🏢 [Sobre la plataforma](https://github.com/vousys/tusfacturas.app)

---

## Contenido

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Credenciales](#credenciales)
- [Uso](#uso)
  - [Verificar el estado de los servicios](#verificar-el-estado-de-los-servicios)
  - [Consultar un CUIT en el padrón](#consultar-un-cuit-en-el-padrón)
  - [Consultar la próxima numeración](#consultar-la-próxima-numeración)
  - [Emitir una factura](#emitir-una-factura)
  - [Emitir una nota de débito con comprobantes asociados](#emitir-una-nota-de-débito-con-comprobantes-asociados)
  - [Emitir por lotes](#emitir-por-lotes)
- [Referencia de métodos](#referencia-de-métodos)
- [Manejo de errores](#manejo-de-errores)
- [Debug](#debug)
- [¿Otro lenguaje?](#otro-lenguaje)
- [Contribuir](#contribuir)
- [Licencia](#licencia)

---

## Requisitos

| | |
| --- | --- |
| PHP | 7.2 o superior |
| Extensiones | `curl`, `json` |
| Cuenta | CUIT y punto de venta configurados en TusFacturasAPP |
| API | v2 — `https://www.tusfacturas.app/app/api/v2/` |

---

## Instalación

El SDK no tiene dependencias. Copiá los dos archivos de la carpeta [`/php`](php) a tu proyecto:

```bash
git clone https://github.com/vousys/tusfacturas.git
cp tusfacturas/php/tusfacturas_sdk*.php tu-proyecto/lib/
```

E incluilo:

```php
require_once 'lib/tusfacturas_sdk.php';
```

> `tusfacturas_sdk.php` ya hace el `include` de `tusfacturas_sdk_entidades.php`, así que alcanza con requerir el primero. Los dos tienen que estar en el mismo directorio.

---

## Credenciales

Necesitás tres valores, que salen de tu cuenta en **Mis CUITs**:

```php
define('TUSFACTURAS_APIKEY',    'tu_apikey');
define('TUSFACTURAS_APITOKEN',  'tu_apitoken');
define('TUSFACTURAS_USERTOKEN', 'tu_usertoken');
```

⚠️ Nunca los commitees. Usá variables de entorno o un archivo de configuración fuera del repositorio.

📖 [Cómo obtener las credenciales](https://developers.tusfacturas.app/como-empiezo/api-factura-electronica-afip-autenticacion)

---

## Uso

### Verificar el estado de los servicios

Útil antes de una emisión masiva: te dice si ARCA está operativo.

```php
$sdk = new tusfacturas_sdk();
$sdk->set_keys(TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN);

$response = $sdk->estado_servicios();
```

📖 [Documentación](https://developers.tusfacturas.app/web-services-afip-api-arca/api-factura-electronica-afip-estado-de-los-servicios-afip)

---

### Consultar un CUIT en el padrón

Trae los datos de la constancia de inscripción. Sirve para autocompletar el alta de clientes a partir del CUIT.

```php
$sdk = new tusfacturas_sdk();
$sdk->set_keys(TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN);

$response = $sdk->clientes_afip_info('20123456789');
```

📖 [Documentación](https://developers.tusfacturas.app/consultas-varias-a-servicios-afip-arca/api-factura-electronica-afip-clientes-consultar-cuit-en-constancia-de-inscripcion)

---

### Consultar la próxima numeración

```php
$sdk       = new tusfacturas_sdk();
$entidades = new tusfacturas_sdk_entidades();
$sdk->set_keys(TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN);

$comprobante_data['tipo']        = 'FACTURA B';
$comprobante_data['punto_venta'] = '0001';
$comprobante_data['operacion']   = 'V';   // V = venta, C = compra

$response = $sdk->numeracion($entidades->comprobante($comprobante_data));

if (!$sdk->hay_error($response)) {
    echo 'Próximo número: ' . $response->comprobante->numero;
} else {
    echo implode("\n", $response->errores);
}
```

📖 [Documentación](https://developers.tusfacturas.app/web-services-afip-api-arca/api-factura-electronica-afip-consultar-numeracion-de-comprobantes.)

---

### Emitir una factura

El caso principal. El SDK arma el JSON a partir de arrays PHP mediante las entidades.

```php
$sdk       = new tusfacturas_sdk();
$entidades = new tusfacturas_sdk_entidades();
$sdk->set_keys(TUSFACTURAS_APIKEY, TUSFACTURAS_APITOKEN, TUSFACTURAS_USERTOKEN);

// ---------- Cliente ----------
$cliente_data = array(
    'documento_tipo' => 'DNI',
    'documento_nro'  => '12345678',
    'razon_social'   => 'JUAN PEREZ',
    'email'          => 'cliente@ejemplo.com',
    'domicilio'      => 'Avenida Siempreviva 742',
    'provincia'      => 1,
    'condicion_iva'  => 'CF',   // CF, RI, EX, M, MT
    'condicion_pago' => 0,      // 0 = contado
    'envia_por_mail' => 'S',
);

// ---------- Cabecera del comprobante ----------
$comprobante_data['tipo']        = 'FACTURA B';
$comprobante_data['punto_venta'] = '0001';
$comprobante_data['fecha']       = date('d/m/Y');
$comprobante_data['numero']      = 20;

// ---------- Detalle ----------
$comprobante_data['detalle']   = array();
$comprobante_data['detalle'][] = $entidades->comprobante_detalle_item(array(
    'cantidad'     => 1,
    'afecta_stock' => 'N',
    'leyenda'      => '',
    'producto'     => $entidades->producto(array(
        'descripcion'             => 'HONORARIOS',
        'codigo'                  => 'HON',
        'lista_precios'           => 'Lista general',
        'unidad_bulto'            => 1,
        'unidad_medida'           => 7,
        'precio_unitario_sin_iva' => 100,
        'alicuota'                => 21,
    )),
));

$comprobante_data['total'] = 121;

// ---------- Emisión ----------
$response = $sdk->comprobante_nuevo(
    $entidades->comprobante($comprobante_data),
    $entidades->comprobante_cliente($cliente_data)
);

if (!$sdk->hay_error($response)) {
    echo 'CAE: '  . $response->cae . PHP_EOL;
    echo 'Vence: ' . $response->vencimiento_cae . PHP_EOL;
    echo 'PDF: '   . $response->comprobante_pdf_url . PHP_EOL;
} else {
    echo implode("\n", $response->errores);
}
```

> 💡 La URL del PDF es temporal. Descargalo y guardalo de tu lado apenas lo recibís.

📖 [Documentación](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/api-factura-electronica-afip-facturacion-nuevo-comprobante) · [Referencia completa de campos](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/referencia-api-afip-arca)

---

### Emitir una nota de débito con comprobantes asociados

Las notas de crédito y débito requieren informar a qué comprobantes afectan:

```php
$comprobante_data['tipo']        = 'NOTA DE DEBITO B';
$comprobante_data['punto_venta'] = '0001';
$comprobante_data['fecha']       = date('d/m/Y');

$comprobante_data['comprobantes_asociados'][] =
    $entidades->comprobantes_asociados_detalle_item(
        '03/03/2026',   // fecha
        'FACTURA B',    // tipo
        '0003',         // punto de venta
        '123',          // número
        '20123456789'   // CUIT
    );
```

El resto (detalle, total, emisión) es idéntico al ejemplo de factura.

También podés asociar por período con `comprobantes_asociados_periodo($desde, $hasta)`.

📖 [Notas de crédito y débito](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/api-factura-electronica-afip-notas-credito-debito)

---

### Emitir por lotes

Para facturación masiva, preparás cada request y los mandás juntos:

```php
$requests = array();

foreach ($ventas as $venta) {
    $requests[] = $sdk->request_preparar(
        $entidades->comprobante($venta['comprobante']),
        $entidades->comprobante_cliente($venta['cliente'])
    );
}

$response = $sdk->comprobante_lotes($requests);
```

📖 [Facturación por lotes](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/api-factura-electronica-afip-api-facturacion-por-lotes)

---

## Referencia de métodos

### `tusfacturas_sdk`

| Método | Qué hace |
| --- | --- |
| `set_keys($apikey, $apitoken, $usertoken)` | Configura las credenciales. Siempre primero |
| `comprobante_nuevo($comprobante, $cliente)` | Emite un comprobante individual |
| `comprobante_lotes($requests_preparados)` | Emite un lote de comprobantes |
| `request_preparar($comprobante, $cliente)` | Arma el payload sin enviarlo. Se usa para construir lotes |
| `numeracion($comprobante)` | Devuelve el próximo número para ese tipo y punto de venta |
| `clientes_afip_info($documento_nro)` | Datos de la constancia de inscripción de un CUIT |
| `estado_servicios()` | Estado de los servicios de ARCA y de la API |
| `hay_error($response)` | `true` si la respuesta trae errores |
| `dump($data)` | Vuelca un array para inspección |

**Propiedades útiles**

| Propiedad | Para qué |
| --- | --- |
| `$sdk->debug` | En `true` muestra dumps de lo que se envía |
| `$sdk->json_data` | El JSON enviado. Guardalo como log |
| `$sdk->json_respuesta` | El JSON recibido. Guardalo como log |

### `tusfacturas_sdk_entidades`

Construye los bloques del request con los nombres de campo que espera la API:

| Método | Construye |
| --- | --- |
| `comprobante($data)` | La cabecera del comprobante |
| `comprobante_cliente($data)` | El bloque del cliente |
| `comprobante_detalle_item($data)` | Un ítem del detalle |
| `producto($data)` | El producto dentro de un ítem |
| `comprobantes_asociados_detalle_item(...)` | Un comprobante asociado puntual |
| `comprobantes_asociados_periodo($desde, $hasta)` | Asociación por período |
| `bloque_rg_especiales($regimen, $datos)` | Regímenes especiales (ej. RG 5329) |
| `tabla_referencia_provincia($provincia)` | Traduce nombre de provincia a su código |
| `tabla_referencia_alicuota($alicuota)` | Traduce alícuota a su código de ARCA |

---

## Manejo de errores

La API nunca usa códigos HTTP para los errores de negocio: siempre responde 200 y marca el error en el cuerpo. Por eso hay que chequear con `hay_error()`.

```php
if ($sdk->hay_error($response)) {
    foreach ($response->errores as $error) {
        error_log('[TusFacturas] ' . $error);
    }
    // $response->error_details[] trae códigos (ej. TFC-8004) para ramificar
}
```

⚠️ **Regla de reintento.** Si un comprobante falla por una intermitencia de ARCA, reprocesalo **en el mismo orden cronológico** en que lo emitiste. La numeración es secuencial y no admite saltos: si reintentás desordenado, bloqueás el punto de venta.

También puede pasar que ARCA haya procesado el comprobante aunque la respuesta diga error. Antes de reintentar, consultá la numeración o buscá por `external_reference` para no duplicar.

📖 [Errores comunes de ARCA](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/errores-comunes-de-arca-en-tusfacturasapp)

---

## Debug

```php
$sdk->debug = true;   // dumps de lo que se envía
```

Además, cada llamada deja el payload y la respuesta en `$sdk->json_data` y `$sdk->json_respuesta`, listos para guardar en tu tabla de logs.

Desde tu cuenta también podés ver el historial de requests y hooks enviados.

📖 [Cómo debuguear tu integración](https://developers.tusfacturas.app/como-puedo-debugear)

---

## ¿Otro lenguaje?

La API es REST con JSON sobre `POST`, así que no necesitás SDK. Ya está en producción con Python, Node.js, Ruby, .NET, Go y hasta Visual Basic 6.0.

```bash
curl -X POST https://www.tusfacturas.app/app/api/v2/facturacion/nuevo \
  -H "Content-Type: application/json" \
  -d '{"usertoken":"...","apikey":"...","apitoken":"...","cliente":{},"comprobante":{}}'
```

📖 [Referencia API](https://developers.tusfacturas.app/api-factura-electronica-afip-facturacion-ventas/referencia-api-afip-arca) · [Ejemplos JSON por tipo de comprobante](https://developers.tusfacturas.app/web-services-afip-api-arca)

¿Portaste el SDK a otro lenguaje? Abrí un issue y lo enlazamos desde acá.

---

## Contribuir

1. Abrí un [issue](https://github.com/vousys/tusfacturas/issues) con el caso
2. Forkeá y creá una rama (`git checkout -b fix/mi-mejora`)
3. Mandá un Pull Request

Buscamos especialmente: ports a otros lenguajes, ejemplos para frameworks (Laravel, Symfony, WooCommerce) y mejoras al manejo de errores.

---

## Soporte

- 💬 [Centro de ayuda](https://ayuda.tusfacturas.app)
- 🌐 [Contacto y chat](https://www.tusfacturas.app/contacto.html)

---

## Licencia

Licencia dual **MIT** y **GPL**, a elección de quien lo use.

---

<sub>Mantenido por [@vousys](https://www.vousys.com) · SDK PHP de [TusFacturasAPP](https://www.tusfacturas.app) — facturación electrónica AFIP/ARCA Argentina</sub>
