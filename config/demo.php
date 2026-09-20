<?php

/**
 * Cuentas y sucursales de demostración para las revisiones de App Store y
 * Google Play. Ver docs/demo-accounts.md para el procedimiento completo.
 *
 * Este archivo es config (no env() suelto dentro de controladores) para que
 * siga funcionando si algún día se ejecuta `config:cache` en el hosting.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Token del endpoint de provisioning
    |--------------------------------------------------------------------------
    |
    | Sin este valor en .env el endpoint responde 503 y no hace nada: no hay
    | fallback ni token por defecto a propósito — un default conocido dejaría
    | expuesta la creación de usuarios en producción.
    |
    */
    'provision_token' => env('DEMO_PROVISION_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Contraseña por defecto de las cuentas demo
    |--------------------------------------------------------------------------
    |
    | Opcional. Si queda vacía y el request no manda `password`, el endpoint
    | genera una aleatoria y la devuelve en la respuesta. No se define ninguna
    | contraseña fija en el código: quedaría versionada en el repositorio y
    | estas cuentas viven en producción.
    |
    */
    'default_password' => env('DEMO_DEFAULT_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sucursales autorizadas a enviar correo de fallas
    |--------------------------------------------------------------------------
    |
    | Lista blanca (IDs separados por coma). Al crear o cerrar una falla, el
    | correo a EMAIL_FALLAS solo sale si la sucursal de la falla está acá.
    | Por defecto solo la sucursal 1 (la real), de modo que las sucursales
    | demo nunca escriben al buzón de mantenimiento.
    |
    | OJO: es una lista blanca, o sea "falla cerrada". Si algún día se agrega
    | una sucursal REAL nueva, hay que sumar su ID acá o se quedará sin correos
    | de forma silenciosa. Ver docs/demo-accounts.md §Mantenimiento.
    |
    | El fallback a [1] es deliberado y cubre dos riesgos si la variable queda
    | definida pero vacía (MAIL_ENABLED_BRANCH_IDS= en el .env):
    |   1. Una lista vacía dejaría SIN CORREOS a la sucursal real.
    |   2. DemoProvisioningService la usa como referencia de "sucursales
    |      productivas"; vacía, desactivaría la salvaguarda que impide
    |      provisionar sobre la sucursal real.
    |
    */
    'mail_enabled_branch_ids' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) (env('MAIL_ENABLED_BRANCH_IDS') ?: '1'))
    ))) ?: [1],

];
