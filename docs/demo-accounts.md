# Cuentas demo para App Store y Google Play

Apple y Google exigen credenciales funcionales para revisar una app con login.
Este documento explica cómo crearlas en producción y por qué están aisladas de
la operación real.

- **Endpoint:** `POST /api/v1/super-admin/provision-demo-accounts`
- **Código:** `app/Http/Controllers/Api/V1/SuperAdmin/DemoAccountProvisionController.php`,
  `app/Services/DemoProvisioningService.php`
- **Config:** `config/demo.php`

---

## 1. Por qué un endpoint y no un seeder

El hosting es compartido: no hay SSH ni acceso a `artisan`, así que no se pueden
ejecutar seeders ni migraciones por CLI. Se sigue el mismo patrón que el
endpoint preexistente de backfill de uuid (`EquipmentUuidBackfillController`).

Tampoco sirve crear estas cuentas desde el panel web: el panel tiene la sucursal
**hardcodeada a la 1**, tanto al iniciar sesión como al asignar la sucursal de un
usuario nuevo.

```php
// app/Http/Controllers/Auth/LoginController.php:48
Session::put('branch', Branch::find(1));

// app/Http/Controllers/AdminBranch/SupervisorController.php:74
$userBranch->branch_id = session('branch')->id;
```

Cualquier usuario creado por la UI quedaría en la sucursal real. De ahí el
endpoint.

---

## 2. Qué crea

Dos sucursales ficticias, una por tienda, cada una con datos completos y
**totalmente aisladas** de la sucursal 1:

| Sucursal | Tienda |
|---|---|
| `Demo Review iOS` | App Store (Apple) |
| `Demo Review Android` | Google Play |

Por cada sucursal:

- **Catálogos** para que ningún dropdown quede vacío: estatus de falla (incluido
  `Por programación interna`, el único que ve el rol Operador), estatus de
  repuesto, áreas de servicio, tipos de equipo, marcas y modelos.
- **Cadena de negocio:** cliente → división → proyecto.
- **3 equipos** ficticios vinculados al proyecto.
- **4 empleados** que cubren las tres combinaciones de flags que usan los
  dropdowns de personas (reportante, ejecutor interno, ejecutor externo).
- **2 fallas de ejemplo**, para que el revisor no vea listados vacíos.
- **2 usuarios:** un Supervisor y un Operador.

### Cuentas resultantes

| Email | Rol | Sucursal |
|---|---|---|
| `ios.supervisor@example.com` | Supervisor | Demo Review iOS |
| `ios.operador@example.com` | Operador | Demo Review iOS |
| `android.supervisor@example.com` | Supervisor | Demo Review Android |
| `android.operador@example.com` | Operador | Demo Review Android |

Quedan pre-verificadas (`email_verified_at`), con su rol en el guard `sanctum`,
con **exactamente una** fila en `user_branch` y —en el caso del Operador— con su
empleado asociado en `employee_users`, que el login de la API exige.

---

## 3. Configuración previa (una sola vez)

Editar el `.env` de producción:

```env
# Token del endpoint. Generar uno largo y aleatorio.
DEMO_PROVISION_TOKEN=pegar-aqui-un-token-largo-y-aleatorio

# Sucursales que SÍ envían correo de fallas. Dejar solo la real.
MAIL_ENABLED_BRANCH_IDS=1

# Opcional: si se deja vacío, el endpoint genera contraseñas aleatorias
# y las devuelve en la respuesta.
DEMO_DEFAULT_PASSWORD=
```

Para generar un token en Linux/macOS:

```bash
openssl rand -hex 32
```

> Si `DEMO_PROVISION_TOKEN` está vacío, el endpoint responde `503` y no ejecuta
> nada. Es deliberado: no hay token por defecto.

### Comprobaciones antes de ejecutar

- [ ] `APP_DEBUG=false` en producción. Con `true`, los errores de toda la API
      exponen detalles internos.
- [ ] `EMAIL_FALLAS` definida (si falta, no se envían correos a nadie).
- [ ] `MAIL_ENABLED_BRANCH_IDS=1` — **no dejarla definida pero vacía**. El código
      cae a `[1]` si eso pasa, pero conviene que sea explícita.
- [ ] Los roles `Supervisor` y `Operador` existen con guard `sanctum` (ya es el
      caso en producción; si faltaran, el endpoint aborta con `409` sin tocar nada).

---

## 4. Ejecución

### Opción A: contraseña elegida por ti (recomendada)

```bash
curl -X POST https://servicioscasmar.com/api/v1/super-admin/provision-demo-accounts \
  -H "X-Demo-Provision-Token: EL_TOKEN_DEL_ENV" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"password":"TuClaveDemo2026"}'
```

### Opción B: contraseña generada por el servidor

```bash
curl -X POST https://servicioscasmar.com/api/v1/super-admin/provision-demo-accounts \
  -H "X-Demo-Provision-Token: EL_TOKEN_DEL_ENV" \
  -H "Accept: application/json"
```

La contraseña generada viene en la respuesta y **no se vuelve a mostrar**.

### Solo una de las dos tiendas

```bash
-d '{"password":"TuClaveDemo2026","only":["ios"]}'
```

Valores válidos de `only`: `ios`, `android`.

### Si no tienes terminal a mano

El token también se acepta en el cuerpo, así se puede disparar desde Postman o
cualquier cliente HTTP:

```json
POST https://servicioscasmar.com/api/v1/super-admin/provision-demo-accounts
{
  "token": "EL_TOKEN_DEL_ENV",
  "password": "TuClaveDemo2026"
}
```

No funciona pegando la URL en el navegador: el endpoint es `POST` a propósito.

---

## 5. Cómo leer la respuesta

```json
{
  "success": true,
  "message": "Cuentas demo provisionadas correctamente.",
  "data": {
    "all_ok": true,
    "branches": {
      "ios": {
        "store": "App Store (Apple)",
        "branch": { "id": 5, "name": "Demo Review iOS" },
        "users": [
          {
            "email": "ios.supervisor@example.com",
            "role": "Supervisor",
            "created": true,
            "password": "TuClaveDemo2026",
            "password_note": "Guardar ahora: no se vuelve a mostrar."
          }
        ],
        "dropdowns": {
          "equipment": 3, "service_area": 3, "fault_status": 4,
          "spare_part_status": 3, "employee_reported": 3,
          "executors_internal": 1, "executors_external": 1, "projects": 1
        },
        "dropdowns_ok": true,
        "mail_enabled": false
      }
    }
  }
}
```

Tres campos a verificar:

| Campo | Valor esperado | Significado |
|---|---|---|
| `all_ok` | `true` | Todo quedó correcto |
| `dropdowns_ok` | `true` | Ningún dropdown de "crear falla" quedó vacío |
| `mail_enabled` | **`false`** | La sucursal demo no escribe al buzón real |

Si `all_ok` es `false`, revisar cuál de los dos anteriores falló antes de
entregar las credenciales a la tienda.

---

## 6. Aislamiento: por qué las pruebas de Apple no afectan la operación

### Datos

Casi todo el sistema está filtrado por sucursal, y la API resuelve la sucursal
del usuario autenticado:

```php
// app/Helpers/BranchHelper.php:24
return $user->branches()->first()?->id;
```

Como cada usuario demo está vinculado a una sola sucursal (la suya), las fallas
que cree el revisor quedan en la sucursal demo y **no aparecen** en la operación
real.

### Correos

Crear o cerrar una falla envía correo a `EMAIL_FALLAS`, un buzón real de la
empresa. Ahora ese envío pasa por `FaultMailService`, que solo escribe si la
sucursal está en la lista blanca `MAIL_ENABLED_BRANCH_IDS`:

```php
// app/Services/FaultMailService.php
public function isMailEnabledForBranch(?int $branchId): bool
```

Las sucursales demo no están en la lista → **no se envía nada**. El caso omitido
queda en el log (`FaultMailService: correo omitido por sucursal no habilitada`).

### Notificaciones push

Ya estaban aisladas por sucursal desde antes: los destinatarios se calculan
filtrando `user_branch` por la sucursal de la falla.

```php
// app/Services/PushNotificationService.php:138
->where('user_branch.branch_id', $branchId)
->whereIn('roles.name', ['Admin', 'Supervisor', 'Coordinador'])
```

Una falla creada por el operador demo notifica únicamente al supervisor demo de
su misma sucursal. Ningún empleado real recibe nada, y el revisor igual puede
comprobar que las notificaciones funcionan.

---

## 7. Salvaguardas del endpoint

El endpoint corre sobre la base de producción, así que aborta con `409` antes de
tocar nada si detecta una situación anómala:

| Situación | Comportamiento |
|---|---|
| Token ausente o inválido | `401`, se registra IP en el log |
| `DEMO_PROVISION_TOKEN` sin configurar | `503`, no ejecuta nada |
| La sucursal demo resolvería a una sucursal declarada productiva | `409`, aborta |
| Un email demo ya existe vinculado a **otra** sucursal | `409`, aborta sin modificar ese usuario |
| Falta el rol `Supervisor` u `Operador` en guard sanctum | `409`, aborta |

Además:

- **Es idempotente.** Se puede ejecutar las veces que sea: usa `firstOrCreate`
  sobre claves naturales y no duplica ni sobrescribe nada.
- **No cambia contraseñas existentes.** Al reejecutarlo, las cuentas ya creadas
  conservan su contraseña (la respuesta lo indica con `created: false`). Para
  forzar un cambio hay que mandar `"reset_passwords": true`.
- **No envía correos ni push.** Las fallas de ejemplo se crean directo con
  Eloquent, sin pasar por los controladores que notifican.
- **Rate limit** de 6 intentos por minuto.

---

## 8. Mantenimiento

### Al agregar una sucursal REAL nueva

`MAIL_ENABLED_BRANCH_IDS` es una lista blanca. Una sucursal real nueva que no se
agregue ahí **se quedará sin correos de fallas de forma silenciosa**. Es el único
punto de este diseño que requiere acción manual.

### Reejecutar antes de cada revisión

No es necesario, pero es inofensivo. Sirve para reparar la sucursal demo si
alguien borró datos (por ejemplo, si un revisor cerró las dos fallas de ejemplo y
quieres reponerlas).

### Datos acumulados

Las fallas que creen los revisores se quedan en la sucursal demo. No molestan a
nadie; si se quieren limpiar, hay que hacerlo por base de datos.

### Rotación y retiro del token

Cambiar `DEMO_PROVISION_TOKEN` en el `.env` es suficiente: no queda almacenado en
ningún otro lugar.

Cuando las revisiones terminen, **vaciar `DEMO_PROVISION_TOKEN`** en el `.env`.
El endpoint pasa a responder `503` y queda inerte sin necesidad de tocar código.
Las cuentas demo siguen funcionando: solo se desactiva la capacidad de
re-provisionar.

### Cuidado con el bloqueo por intentos fallidos

`LoginLockoutService` bloquea una cuenta 15 minutos tras 5 contraseñas erróneas.
Si un revisor teclea mal la clave varias veces, queda bloqueado durante la
revisión y no hay forma de desbloquearlo salvo esperar (vive en caché).

Por eso conviene:

1. Fijar la contraseña tú mismo (no usar la aleatoria).
2. **Probar el login una vez** antes de enviar la app a revisión.
3. Copiar la contraseña a App Store Connect / Play Console con copiar-pegar, sin
   tipearla.

### Deuda conocida: el endpoint de backfill de uuid

`GET /api/v1/super-admin/equipment/backfill-uuid` (preexistente, no forma parte de
este trabajo) **no valida ningún token**. Su propio docblock pide retirarlo cuando
`remaining` llegue a 0. Conviene hacerlo en el mismo despliegue.

---

## 9. Qué entregar a las tiendas

**App Store Connect** → App Review Information → Sign-In Required:

```
Usuario:    ios.supervisor@example.com
Contraseña: (la que definiste)
```

**Google Play Console** → App content → App access → All or some
functionality is restricted:

```
Usuario:    android.supervisor@example.com
Contraseña: (la que definiste)
```

Se recomienda entregar la cuenta **Supervisor**: tiene 85 permisos y puede ver y
crear fallas, equipos y proyectos, así el revisor recorre la app completa. La
cuenta **Operador** solo tiene 3 permisos (`Fallas Ver`, `Fallas Crear`,
`Proyectos Ver`) — es el flujo restringido del personal de campo y sirve como
credencial secundaria.

> El Operador recibe `403` en pantallas de catálogos (equipos, empleados,
> clientes, etc.) porque su rol no las incluye. Es el comportamiento correcto del
> sistema, pero por eso conviene que la credencial principal entregada a la tienda
> sea la de Supervisor.

Nota: cada Operador solo ve **las fallas que él mismo reportó**. El provisioning
crea una falla a nombre de su empleado justamente para que el listado no aparezca
vacío.

Conviene añadir una nota para el revisor:

> Esta es una aplicación corporativa interna de Servicios Casmar para el reporte
> de fallas de equipos. La cuenta provista opera sobre una sucursal de
> demostración con datos ficticios.

---

## 10. Pruebas automatizadas

```bash
php artisan test tests/Feature/Demo
```

- `tests/Feature/Demo/DemoAccountProvisionTest.php` — token, idempotencia,
  dropdowns, salvaguardas.
- `tests/Feature/Demo/FaultMailBranchPolicyTest.php` — que la sucursal demo no
  envíe correo y que la real **sí** lo siga enviando.
