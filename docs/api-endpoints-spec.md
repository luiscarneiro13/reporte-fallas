# IronFlow — Especificación completa de la API REST (`/api/v1/...`)

> Documento generado para replicar esta API en otro proyecto. Describe stack, librerías,
> convenciones de autenticación/respuesta, y **todos** los endpoints registrados en
> `routes/api.php`, con sus reglas de validación reales, formato de request/response y
> códigos de estado. Incluye advertencias sobre bugs/deuda técnica del código fuente
> original: al reimplementar, decide conscientemente si los replicas o los corriges.

---

## 1. Stack y librerías a instalar

Backend Laravel 10 (PHP ^8.1). Vía Composer:

```bash
composer require laravel/sanctum          # autenticación por token (API)
composer require spatie/laravel-permission # RBAC (roles + permisos)
composer require guzzlehttp/guzzle         # HTTP client (usado para Expo Push API)
composer require endroid/qr-code           # generación de QR (equipos)
composer require intervention/image:2.7    # procesamiento de imágenes (logos, uploads)
composer require dedoc/scramble --dev      # documentación OpenAPI automática (opcional)
```

No se usa ningún SDK dedicado de Firebase/FCM (no hay `kreait/firebase-php` ni similar) —
las notificaciones push se implementan a mano contra la **Expo Push API**
(`https://exp.host/--/api/v2/push/send`) usando Guzzle directo, vía un servicio propio
(`ExpoPushService`). Si el otro proyecto va a usar FCM nativo en vez de Expo, tenlo en
cuenta: la arquitectura interna asume tokens tipo Expo (`ExponentPushToken[...]`).

No hay paquete de "API Resources" de terceros: las respuestas son JSON armado a mano o
modelos Eloquent serializados directo (sin `JsonResource` en casi ningún controlador).

## 2. Autenticación y sesión

- **Laravel Sanctum**, tokens personales (`personal_access_tokens`), no cookies SPA.
- Login: `POST /api/v1/login` devuelve `token` (plain text) + `abilities` = nombres de
  permisos del usuario (Spatie) asignados como "abilities" del token Sanctum.
- Todas las rutas protegidas usan middleware `auth:sanctum`.
- Multi-tenancy: cada usuario pertenece a una `Branch` (sucursal = empresa cliente). El
  `branch_id` **nunca** se envía en el body de las requests protegidas — se resuelve
  server-side vía un helper (`BranchHelper::getBranchId()`) a partir de la sesión/branch
  asociada al usuario autenticado.
- Middleware `check.subscription` (alias de `CheckSubscription`) verifica que la sucursal
  tenga una suscripción activa. **Importante**: en el código original solo bloquea
  peticiones `POST`; `GET/PUT/PATCH/DELETE` no se ven afectados. Decide si replicar ese
  comportamiento (probablemente no era intencional) o aplicarlo a todos los verbos.
- RBAC con `spatie/laravel-permission`. Roles: `Super Admin`, `Admin`, `Supervisor`,
  `Operator` (en BD aparece como `Operador` en español). Permisos se verifican con
  middleware `permission:<Recurso> <Acción>` (p.ej. `permission:Fallas Crear`), aplicado
  en el `__construct()` de cada controlador, NO en las rutas.
- **Guard**: los roles/permisos deben crearse con `guard_name = 'sanctum'` (así los seedea
  el proyecto original) para que coincidan con las peticiones autenticadas vía Sanctum. El
  código de `RoleController::store` original NO fija el guard (usa el guard por defecto,
  `web`), lo que genera un mismatch — corrígelo en la reimplementación forzando siempre
  `guard_name: 'sanctum'` al crear roles/permisos para la API.

## 3. Convención de respuesta (envelope JSON)

Existen **dos convenciones distintas** conviviendo en el código original — decide cuál
adoptar limpiamente en el proyecto nuevo (se recomienda unificar en la primera):

**A) Trait `AlertResponser`** (la mayoría de los controladores CRUD):
```jsonc
// éxito
{ "success": true, "message": "Texto descriptivo", "data": { /* opcional, se omite si es null */ } }
// error
{ "success": false, "message": "Texto descriptivo", "errors": { /* opcional */ } }
```

**B) Trait `ApiResponser`** (solo definido, apenas usado):
```jsonc
{ "success": true, "data": { ... } }
{ "success": false, "data": { ... } }
```

**Errores de validación de un `FormRequest`** (422) usan el formato **default de Laravel**,
no el envelope custom:
```jsonc
{ "message": "The given data was invalid.", "errors": { "campo": ["mensaje"] } }
```

**Recomendación para el nuevo proyecto**: usar un único formato consistente en todas las
respuestas, incluidas las de validación (por ejemplo, envolver también los 422 en
`{success:false, message, errors}` sobreescribiendo `failedValidation()` en un
`FormRequest` base).

Códigos de estado usados en el original: `200` (index/show/update/destroy y, por
inconsistencia, también algunos `store`), `201` (la mayoría de `store` vía
`responseStore`), `400` (falta contexto, p.ej. sin sucursal), `401` (sin sesión), `403`
(prohibido por rol/plan), `404` (no encontrado), `422` (validación), `429` (rate limit
login), `500` (excepción no controlada).

## 4. Paginación, filtros y orden — convención general de los índices

Casi todos los `index()` de recursos CRUD siguen este patrón (documentado por endpoint
más abajo cuando hay diferencias):

- Paginación **fija** `paginate(10)` — no soporta `per_page` como query param (deuda
  técnica: agregar `per_page` configurable en la reimplementación).
- Filtro de texto libre vía query param `query` (`LIKE %valor%` sobre columnas relevantes).
- `sort_column` / `sort_direction` (`asc|desc`) como query params, con whitelist de
  columnas permitidas por endpoint y un valor default.
- Forma de respuesta estándar:
```jsonc
{
  "success": true, "message": "Operación exitosa",
  "data": { "data": [ /* items */ ], "pagination": { "current_page": 1, "last_page": 3, "per_page": 10, "total": 25 } }
}
```
- **Excepciones** a esta forma están marcadas explícitamente en cada sección abajo.

## 5. Idempotencia

Tabla `operation_idempotency_keys` (columnas: `idempotency_key` único, `operation`,
`result` JSON, `expires_at`). Se usa en `POST /fallas` (header `Idempotency-Key`) y en
`POST /fallas/sync` (campo `idempotency_key` por operación del batch). Antes de procesar,
se busca una entrada previa no expirada con la misma clave + operación; si existe, se
devuelve el `result` guardado sin reprocesar. TTL: 30 días. Un comando
(`CleanExpiredIdempotencyKeys`) limpia las vencidas. Recomendado para endpoints móviles
sensibles a reintentos por red inestable (crear/cerrar fallas).

---

# 6. Endpoints

## 6.1 Autenticación (`App\Http\Controllers\Api\AuthController`)

### `POST /api/v1/login` — público, `throttle:login-api`
Request:
```jsonc
{ "email": "string, required, email", "password": "string, required",
  "locale": "string, nullable", "expo_token": "string, nullable, max:512" }
```
Lógica: valida credenciales, bloquea cuenta tras intentos fallidos (`LoginLockoutService`,
respuesta `429` con `error_code: ACCOUNT_LOCKED`), exige email verificado (`403
EMAIL_NOT_VERIFIED`), exige que un usuario con rol `Operador` tenga `employee_id` asignado
(`403 OPERATOR_WITHOUT_EMPLOYEE`), actualiza idioma si viene `locale`, registra/actualiza
`PushToken` si viene `expo_token` (platform hardcodeado `android`, `device_id` = header
`User-Agent`), crea token Sanctum con **abilities = nombres de permisos del usuario**.

Respuesta 200:
```jsonc
{ "success": true, "data": {
  "user": { "id": 1, "name": "...", "email": "...", "phone": "...", "cedula": "...",
    "language": { "id": 1, "name": "Español", "code": "es" } | null,
    "roles": ["Admin"], "permissions": ["Fallas Ver", "..."], "employee_id": 5 | null },
  "token": "plainTextToken...", "token_name": "api-auth-20260810120000-<uniqid>" } }
```
Errores: `422` (validación), `401` (credenciales incorrectas), `429` (bloqueo),
`403` (email no verificado / operador sin empleado).

### `POST /api/v1/logout` — `auth:sanctum`
Revoca solo el token actual (`currentAccessToken()->delete()`).
200: `{ "success": true, "message": "Sesión cerrada exitosamente." }`. 401 si no hay sesión.

### `POST /api/v1/logout-all` — `auth:sanctum`
Revoca **todos** los tokens del usuario (`tokens()->delete()`).
200: `{ "success": true, "message": "Sesión cerrada en todos los dispositivos." }`.

### `PUT /api/v1/user/language` — `auth:sanctum`
Request: `{ "code": "string, required, exists:languages,code" }`
200: `{ "success": true, "message": "Idioma actualizado exitosamente.",
  "data": { "language": { "id":1, "name":"...", "code":"es" } } }`

### `GET /api/v1/user` — `auth:sanctum`
Devuelve el modelo `User` autenticado sin envelope (ruta ad-hoc con closure, no pasa por
ApiResponser/AlertResponser): `return $request->user();` → JSON plano del modelo.

---

## 6.2 Recursos sucursal-scoped — namespace `V1\AdminBranch`

Todos bajo `Route::prefix('v1')->middleware(['auth:sanctum','check.subscription'])`.
Multi-tenant: filtrados/forzados por `branch_id` del usuario autenticado (salvo las
excepciones marcadas explícitamente como bug de falta de filtro).

### 6.2.1 Clientes — `apiResource('clientes')` → `CustomerController`
Modelo `Customer` (`branch_id, name, rif, address, phone, email`).
**Nota**: el `FormRequest` `CustomerRequest`/`CustomerEditRequest` existe pero no está
conectado — en el original **no hay validación real** de este recurso. En la
reimplementación, aplica validación real:
```php
'name' => 'required|string|max:90',
'rif' => 'required|string|max:90',
'address' => 'nullable|string',
'phone' => 'nullable|string|max:90',
'email' => 'nullable|email|max:90',
```
- `GET /clientes` — permiso `Clientes Ver`. Query: `query` (LIKE en name/address/email/
  phone/rif), `sort_column` (`id,name,rif,email,phone,address`, default `id`),
  `sort_direction` (default `desc`). **Bug original: no filtra por branch_id** — en el
  nuevo proyecto sí debe filtrar.
- `show` — **no implementado** en el original (aunque la ruta existe); implementar en el
  nuevo proyecto devolviendo el cliente por id + branch_id.
- `POST /clientes` — permiso `Clientes Crear`. Body: `name, address, email, phone, rif`.
  201, `data` = modelo creado.
- `PUT/PATCH /clientes/{id}` — permiso `Clientes Editar`. Mismos campos. 404 si no existe.
- `DELETE /clientes/{id}` — permiso `Clientes Eliminar`. 200 sin `data`.

### 6.2.2 Divisiones — `apiResource('divisiones')` → `DivisionController`
Modelo `Division` (`branch_id, name, description`). Mismo caso: `DivisionRequest`/
`DivisionEditRequest` sin conectar en el original — validar en el nuevo proyecto:
```php
'name' => 'required|string|max:90',
'description' => 'nullable|string',
```
- `GET /divisiones` — permiso `Divisiones Ver`. Query: `query` (LIKE `name`),
  `sort_column` (`id,name,description`, default `name`), `sort_direction` default `asc`.
  Bug original: no filtra `branch_id`.
- `POST` — permiso `Divisiones Crear`. Body: `name, description`. 201.
- `PUT/PATCH` — permiso `Divisiones Editar`. 200/404.
- `DELETE` — permiso `Divisiones Eliminar`. 200 sin `data`.

### 6.2.3 Empleados — `apiResource('empleados')` → `EmployeeController`
Modelo `Employee` (`branch_id, identification_number, first_name, last_name, email
(mutator: lowercase), phone_number, address, executor:bool, external:bool, position`).
Request: `App\Http\Requests\V1\EmployeeRequest` (mismas reglas en create y update):
```php
'identification_number' => ['required','string','max:20', 'unique:employees,identification_number' /* ignora self id en update */],
'first_name' => 'required|string|max:100',
'last_name' => 'required|string|max:100',
'phone_number' => 'required|string|max:20',
'address' => 'nullable|string',
'position' => 'nullable|string|max:255',
'executor' => 'nullable|integer',
'email' => ['nullable','email','max:150','unique:employees,email' /* ignora self id */],
'password' => ['nullable','string','min:6','max:255', 'required si es creación (POST) y se envía email'],
'role_id' => ['integer', 'required|exists:roles,id|not_in:0 si se envía email', 'si no hay email: nullable|in:0'],
```
- `GET /empleados` — permiso `Empleados Ver`. Filtros fijos: `branch_id`, `external=0`.
  `with(['users.roles'])`. Query: `query` (LIKE identification_number/first_name/
  last_name/email/phone_number/address/position). Orden fijo `id desc`.
- `GET /empleados/{id}` (show) — `with(['users.roles'])`. 404 si no existe. `data`:
  `{ back_url: null, employee: {...employee, users:[{...user, roles:[{...}]}]} }`.
- `POST /empleados` — permiso `Empleados Crear`. Si vienen `email` + `role_id`: crea o
  actualiza un `User` de sistema vinculado (`password` obligatorio si el `User` no
  existía) y lo sincroniza como `employee->users()->sync([...])`. 201.
- `PUT/PATCH /empleados/{id}` — permiso `Empleados Editar`. Misma lógica de sync de user.
- `DELETE /empleados/{id}` — permiso `Empleados Eliminar`. 200 sin `data`.

### 6.2.4 Equipos — `apiResource('equipos')` + rutas extra → `EquipmentController`
Modelo `Equipment`:
```
branch_id, project_id, uuid, qr_code_path, internal_code, owner, placa, type,
serial_niv, body_serial_number, chassis_serial_number, engine_serial_number,
vehicle_model, brand_name, model_year, color, origin, racda
```
Relaciones: `belongsTo Branch`, `belongsToMany Project` (pivote `equipment_project`, con
timestamps), `lastProject`, `hasMany FaultHistory as history`.

Request `EquipmentRequest` (store):
```php
'placa' => ['required','string','max:20','min:3','unique:equipment,placa'],
'serial_niv' => ['nullable','string','max:90','min:3'],
'body_serial_number' => ['nullable','string','max:90','min:3'],
'chassis_serial_number' => ['nullable','string','max:90','min:3'],
'engine_serial_number' => ['nullable','string','max:90','min:3'],
'vehicle_model' => ['required','string','max:90','min:3'],
'type' => ['required','exists:equipment_types,name'],
'brand_name' => ['nullable','string','max:90','min:3'],
'owner' => ['nullable','string','max:20','min:3'],
'internal_code' => ['nullable','string','max:20','min:3'],
'color' => ['nullable','string','max:20','min:3'],
'origin' => ['nullable','string','max:255','min:3'],
```
`EquipmentEditRequest` (update): idénticas, `placa` ignora el propio id en el `unique`.
`project_id` puede venir como escalar o array; se normaliza y se sincroniza vía pivote
(`$item->projects()->sync($projectIds)`).

- `GET /equipos` — permiso `Equipos Ver`. Filtro fijo `branch_id`. Query: `query` (id
  exacto o LIKE en placa/model_year/internal_code/color, o nombre de proyecto asociado),
  `sort_column` (`id,internal_code,type,placa,brand_name,vehicle_model,model_year,color`,
  default `id`), `sort_direction` default `desc`. Eager load `lastProject:id,name`. **Bug
  original**: intenta cargar `history` como segundo argumento string de `with()`, pero
  Eloquent lo ignora cuando el primer argumento es array — en la reimplementación, si se
  necesita el historial en el listado, usar `with(['lastProject'=>..., 'history'])`
  correctamente.
- `GET /equipos/uuid/{uuid}` (showByUuid) — busca por `uuid`; 404 si no existe. Genera QR
  bajo demanda si no existe (`storage/app/public/qrcodes/{uuid}.svg`), actualiza
  `qr_code_path` sin disparar observers. Incluye `history` (fallas cerradas del equipo,
  `FaultHistory::where('equipment_id', ...)`). **No tiene middleware de permiso** (solo
  auth+subscription) — decide si en el nuevo proyecto quieres exigir permiso `Equipos Ver`
  también aquí (recomendado, ya que expone datos del equipo por QR).
- `GET /equipos/{id}/historial` (mapea al mismo método `show`) — 404 si no existe. `data`:
  `{ back_url: null, equipment: {...}, history: [...FaultHistory] }`.
- `POST /equipos` — permiso `Equipos Crear`. **Límite de plan**: si
  `branch->subscription->plan->max_equipment` se alcanza, `403` con mensaje de límite.
  Asigna todos los campos de `EquipmentRequest` (⚠️ el original olvida asignar
  `engine_serial_number` pese a validarlo — corregir en la reimplementación). `branch_id`
  forzado. Sincroniza proyectos. 201.
- `PUT/PATCH /equipos/{id}` — permiso `Equipos Editar`. Chequeo retroactivo de límite de
  plan (equipos más nuevos que exceden el cupo quedan bloqueados para edición → 403).
- `DELETE /equipos/{id}` — permiso `Equipos Eliminar`. Mismo chequeo de bloqueo por plan.
  200 sin `data`.

### 6.2.5 Tipos de Equipo — `apiResource('tipos-equipo')` → `EquipmentTypeController`
Modelo `EquipmentType` (`branch_id, name`). Request `EquipmentTypeRequest` (create y
update):
```php
'name' => 'required|string|min:3',
```
- `GET /tipos-equipo` — permiso `Tipos de equipo Ver`. Query `query` (LIKE name). Orden
  fijo `name asc`. **Bug original: no filtra por branch_id** — corregir en el nuevo.
- `POST` — permiso `Tipos de equipo Crear`. `name`; `branch_id` forzado. 201.
- `PUT/PATCH` — permiso `Tipos de equipo Editar`. Solo `name`. 200/404.
- `DELETE` — permiso `Tipos de equipo Eliminar`. 200 sin `data`.

### 6.2.6 Ejecutores — `apiResource('ejecutores')` → `ExecutorController`
Opera sobre el mismo modelo `Employee`, filtrando `executor = 1`. Reutiliza
`EmployeeRequest` (ver 6.2.3) para validar `identification_number, first_name, last_name,
phone_number, address, external` — **`executor` siempre se fuerza a `1`** en el servidor,
ignorando cualquier valor del body.
- `GET /ejecutores` — permiso `Ejecutores Ver`. Filtros fijos `branch_id, executor=1`.
  `with('executorServiceAreas:id,name')` (áreas donde el empleado ha ejecutado fallas).
  Query: `query` (LIKE en varios campos), `sort_column` (`id,identification_number,
  first_name,last_name,phone_number,address,external`, default `first_name`),
  `sort_direction` default `asc`.
- `POST /ejecutores` — permiso `Ejecutores Crear`. 201.
- `PUT/PATCH /ejecutores/{id}` — permiso `Ejecutores Editar`.
- `DELETE /ejecutores/{id}` — permiso `Ejecutores Eliminar`. 200 sin `data`.

### 6.2.7 Fallas — `GET /fallas/crear-datos` + `apiResource('fallas')` → `FaultController`
Modelo `Fault` (tabla `faults`):
```
branch_id, local_id(uuid,unique,nullable), internal_id, employee_reported_id,
equipment_id, service_area_id, description, fault_status_id, spare_part_status_id,
report_date, scheduled_execution, completed_execution, executor_id,
equipment_maintenance_log, closed
```
Accessor `days_since_report` (siempre serializado, texto humano: "hoy"/"hace N días").
Mutator: `executor_id` de `0`/`"0"` → `null`.

**Índice y show leen de una VIEW** `FaultView` (tabla `v_faults_base`) que desnormaliza:
`reported_by_name, equipment_name, internal_code, service_area_name, fault_status_name,
spare_part_status_name, executor_name, project_id, project_name, division_id,
division_name, duration_days`. Al **cerrar** una falla se copia a `fault_history` y se
borra de `faults` (archivado manual, no soft-delete). Considera si en el nuevo proyecto
prefieres implementar esto con una vista SQL real o con una consulta con JOINs en el
ORM (más portable entre motores de BD).

Request `FaultRequest` (usado tanto en `store` como en `update`):
`prepareForValidation()`:
- Si `executor_id === '0'` → normaliza a `0`.
- Si el payload trae la clave `closed` (flag de cierre) → se reemplaza por la fecha
  actual del servidor (el cliente no controla la fecha de cierre).
- Si el usuario autenticado es rol `Operador`: autocompleta `employee_reported_id` con su
  propio `employee_id` si no vino, y `fault_status_id` con el estado "open" de la
  sucursal si no vino.

`rules()` base (siempre):
```php
'internal_id' => ['nullable','string','max:255','unique:faults,internal_id' /* ignora self */],
'closed' => ['nullable','date'],
'employee_reported_id' => 'required|integer|exists:employees,id',
'equipment_id' => 'required|integer|exists:equipment,id',
'service_area_id' => 'required|integer|exists:service_areas,id',
'description' => 'required|string',
'fault_status_id' => 'required|integer|exists:fault_statuses,id',
'spare_part_status_id' => 'required|integer|exists:spare_part_statuses,id',
'report_date' => 'nullable|date',
'scheduled_execution' => 'nullable|date',
'completed_execution' => 'nullable|date',
'executor_id' => 'nullable|integer|exists:employees,id (0 permitido como "sin ejecutor")',
'equipment_maintenance_log' => 'nullable|string',
```
Si el payload trae `closed` (operación de cierre), se sobreescriben como **obligatorias**:
`employee_reported_id, equipment_id, service_area_id, fault_status_id,
spare_part_status_id, report_date, scheduled_execution, completed_execution` (`required
date`), `executor_id` (`required|integer|min:1`), `equipment_maintenance_log`
(`required|string`).

- `GET /fallas/crear-datos` (createData) — sin permiso extra. `400` si no hay branch en
  sesión. Devuelve catálogos para el formulario de creación: `equipment, service_area,
  fault_status (excluye 'closed', nombres traducidos), spare_part_status,
  employee_reported (map id→"cedula - nombre"), executors (map id→"cedula - nombre", con
  "0"→"Seleccione"), default_fault_status_id, default_employee_reported_id`. Si el usuario
  es `Operador`, los catálogos se acotan a su propio contexto.
- `GET /fallas` — permiso `Fallas Ver`. Fuente `FaultView` filtrada por `branch_id` (y por
  `reported_by_id` si el rol es `Operador`, para que solo vea sus propias fallas). Query
  params: `name` (LIKE reported_by_name), `query` (id exacto o LIKE en
  description/internal_id/internal_code), `equipment_name`, `service_area_name` (LIKE),
  `equipment_id, service_area_id, fault_status_id, spare_part_status_id, project_id,
  executor_id` (igualdad exacta), `close_status` (`1`=solo cerradas, `2`=solo abiertas,
  default=abiertas), `from`/`to` (rango sobre `report_date`), `sort_column` (`id,
  internal_code,equipment_name,description,fault_status_name,spare_part_status_name,
  service_area_name,duration_days,report_date,reported_by_name,executor_name`, default
  `id`), `sort_direction` default `desc`. Respuesta incluye además `data.stats`:
  `{total, open, in_progress, blocked, scheduled, closed}` calculado sobre el mismo
  filtro (heurística por texto de `fault_status_name`, multi-idioma — recomendable
  reemplazar por un campo de categoría real en la reimplementación, más robusto).
- `GET /fallas/{id}` (show) — permiso `Fallas Ver`. Fuente `FaultView` + `branch_id` (+
  `reported_by_id` si Operador). 404 si no existe.
- `POST /fallas` — permiso `Fallas Crear`. Header opcional `Idempotency-Key` (ver §5).
  Transforma fechas de `d-m-Y` (input) a `Y-m-d` (BD). Si el payload es un **cierre**
  (trae `closed`): envía email `CerrarFallaEmail`, copia a `FaultHistory`, notifica push
  (`PushNotificationService::notifyClosedFault`), borra de `faults`. Respuesta:
  `{success:true, message:"Falla cerrada y archivada correctamente"}` (sin `data`). Si es
  creación/edición normal: envía `ReportarFallaEmail`; si es alta nueva, notifica push
  (`notifyNewFault`). Respuesta: `{success:true, message:"Falla creada", data:{...}}`.
- `PUT/PATCH /fallas/{id}` — permiso `Fallas Editar`. Misma lógica que store (incluida la
  rama de cierre si el payload trae `closed`).
- `DELETE /fallas/{id}` — permiso `Fallas Eliminar`. Borra directo de `faults` (no pasa
  por `fault_history`). 200 sin `data`.

### 6.2.8 Sincronización offline-first — `SyncController`
Diseñado para que la app móvil (React Native) opere offline y sincronice un lote de
cambios cuando recupera conectividad.

#### `POST /api/v1/fallas/sync` — permiso `Fallas Crear`
Validación de forma (no de contenido de cada operación):
```php
'operations' => 'required|array|min:1',
'operations.*.operation' => 'required|string|in:create_fault,update_fault,close_fault',
'operations.*.local_id' => 'nullable|string|max:36',
'operations.*.idempotency_key' => 'nullable|string|max:255',
'operations.*.data' => 'required|array',
```
Body de ejemplo:
```jsonc
{ "operations": [
  { "operation": "create_fault", "local_id": "uuid-cliente", "idempotency_key": "clave-opt",
    "data": { "employee_reported_id": 12, "equipment_id": 5, "service_area_id": 3,
      "description": "texto", "fault_status_id": 1, "spare_part_status_id": 2,
      "report_date": "2026-08-10", "scheduled_execution": "2026-08-12",
      "completed_execution": null, "executor_id": 7,
      "equipment_maintenance_log": "solo requerido en close_fault" } }
] }
```
Cada operación se procesa en **su propia transacción DB** (un fallo no revierte el resto
del batch). Reglas laxas por tipo:
- Base común: `fault_status_id` (`required|integer|exists:fault_statuses,id`),
  `spare_part_status_id` (ídem), fechas `nullable|date`.
- `create_fault`: + `employee_reported_id` (`nullable|integer|exists:employees,id`),
  `equipment_id`/`service_area_id` (`nullable|integer`), `description` (`required|string`).
- `update_fault`: igual, `description` opcional.
- `close_fault`: + `description` y `equipment_maintenance_log` (`required|string`),
  `executor_id` (`nullable|integer`).

Lógica: `create_fault` crea con el `local_id` del cliente; `update_fault`/`close_fault`
buscan por `local_id` (prioridad) o `data.id`, si no existe se reporta error de esa
operación puntual sin abortar el batch; `close_fault` replica el archivado a
`fault_history` igual que el cierre normal. Idempotencia por operación vía
`idempotency_key` (30 días TTL).

Respuesta — **siempre HTTP 200**, éxito/fracaso reflejado dentro de `data.results` (mismo
índice que el array de entrada):
```jsonc
{ "success": true, "message": "Sincronización completada exitosamente | ... con errores parciales",
  "data": { "results": [
    { "success": true, "operation": "create_fault", "local_id": "...", "fault_id": 123, "already_processed": false },
    { "success": false, "index": 1, "error": "Error de validación", "validation_errors": {"campo":["msg"]} },
    { "success": false, "index": 2, "error": "Falla no encontrada para actualizar" }
  ] } }
```

#### `GET /api/v1/sync/initial-data` — sin permiso extra (solo auth+subscription)
Devuelve el "paquete inicial" para que la app móvil arranque offline:
```jsonc
{ "success": true, "message": "Datos iniciales obtenidos", "data": {
  "equipment": { "data": [ {...} ], "updated_at": "iso8601|null" },
  "service_areas": { "data": [...], "updated_at": "..." },
  "fault_statuses": { "data": [...traducidos, excluye 'closed'...], "updated_at": "..." },
  "spare_part_statuses": { "data": [...], "updated_at": "..." },
  "default_fault_status_id": 3,
  "faults": [ {...Fault activas de la sucursal, tabla base, con days_since_report...} ],
  "server_time": "2026-08-10T..." } }
```
`400` si el usuario no tiene sucursal asociada. `500` en excepción no controlada.

### 6.2.9 Estados de Falla — `apiResource('estados-falla')` → `FaultStatusController`
Modelo `FaultStatus` (`branch_id, name`). Requests `FaultStatusRequest`/
`FaultStatusEditRequest` (idénticas): `'name' => 'required|string|min:3'`.
- `GET /estados-falla` — permiso `Estatus de fallas Ver`. Filtra `branch_id` + excluye
  `name != 'closed'`. Query `query` (LIKE name). **Respuesta sin paginar**: `data` es un
  **array plano** (no `{data:[],pagination:{}}`) ordenado `name asc` — inconsistente con
  el resto de índices; decide si unificar en el nuevo proyecto (recomendado: paginar
  igual que los demás, o documentar explícitamente que este catálogo es siempre completo).
- `POST` — permiso `Estatus de fallas Crear`. `name`; `branch_id` forzado. 201.
- `PUT/PATCH` — permiso `Estatus de fallas Editar`. 200/404.
- `DELETE` — permiso `Estatus de fallas Eliminar`. 200 sin `data`.

### 6.2.10 Propietarios — `apiResource('propietarios')` → `OwnerController`
Modelo `Owner` (`first_name, last_name` — **sin `branch_id`**, es un catálogo global no
multi-tenant). Requests `OwnerRequest`/`OwnerEditRequest`:
```php
'first_name' => 'required|string|min:3',
'last_name' => 'required|string|min:3',
```
**Sin middleware de permisos** en el original (solo auth+subscription) — decide si el
nuevo proyecto quiere exigir un permiso `Propietarios {Ver|Crear|Editar|Eliminar}` (lo
más consistente con el resto del sistema).
- `GET /propietarios` — query `query` (LIKE first_name/last_name). Orden fijo `last_name
  asc`.
- `POST` — 201.
- `PUT/PATCH` — 200/404.
- `DELETE` — 200 sin `data`.

### 6.2.11 Proyectos — `apiResource('proyectos')` → `ProjectController`
Modelo `Project` (`branch_id, customer_id, division_id, name, contract_number,
description, geographic_area`). Requests `ProjectRequest`/`ProjectEditRequest`
(idénticas):
```php
'name' => 'required|string|min:3',
'description' => 'nullable|string|min:3',
'customer_id' => 'required|exists:customers,id',
'division_id' => 'required|exists:divisions,id',
'geographic_area' => 'required|string|min:3',
```
(`contract_number` no tiene regla en el original; en la reimplementación conviene
agregar `'contract_number' => 'nullable|string|max:90'`).
- `GET /proyectos` — permiso `Proyectos Ver`. **Devuelve filas planas con JOIN** a
  `customers`/`divisions` (no el modelo `Project` completo): cada item es `{ id,
  customer_name, project_name, division_name, project_geographic_area,
  project_contract_number }`. Query `query` (LIKE sobre name/customer/division/
  geographic_area/contract_number), `sort_column` (`projects.id,customer_name,
  project_name,division_name,project_geographic_area,project_contract_number`, default
  `projects.id`), `sort_direction` default `desc`.
- `POST /proyectos` — permiso `Proyectos Crear`. `customer_id, division_id, name,
  contract_number, description, geographic_area`; `branch_id` forzado. 201 con el modelo
  `Project` completo (a diferencia de `index`).
- `PUT/PATCH` — permiso `Proyectos Editar`.
- `DELETE` — permiso `Proyectos Eliminar`. 200 sin `data`.

### 6.2.12 Áreas de Servicio — `apiResource('areas-servicio')` → `ServiceAreaController`
Modelo `ServiceArea` (`branch_id, name, description`). Requests `ServiceAreaRequest`/
`ServiceAreaEditRequest`:
```php
'name' => 'required|string|min:3',
'description' => 'required|string|min:3',
```
- `GET /areas-servicio` — permiso `Areas de Servicio Ver`. Query `query` (LIKE name),
  `sort_column` (`id,name,description`, default `name`), `sort_direction` default `asc`.
  **Bug original: no filtra branch_id** — corregir.
- `POST` — permiso `Areas de Servicio Crear`. 201.
- `PUT/PATCH` — permiso `Areas de Servicio Editar`.
- `DELETE` — permiso `Areas de Servicio Eliminar`. 200 sin `data`.

### 6.2.13 Estados de Repuestos — `apiResource('estados-repuestos')` → `SparePartStatusController`
Modelo `SparePartStatus` (`branch_id, name`). Requests `SparePartStatusRequest`/
`SparePartStatusEditRequest`: `'name' => 'required|string|min:3'`.
- `GET /estados-repuestos` — permiso `Estatus de repuestos Ver`. Query `query` (LIKE
  name), `sort_column` (`id,name`, default `name`), `sort_direction` default `asc`.
  **Bug original: no filtra branch_id** — corregir. (A diferencia de FaultStatus, este sí
  pagina siempre con la forma estándar `{data:{data:[],pagination:{}}}`.)
- `POST` — permiso `Estatus de repuestos Crear`. 201.
- `PUT/PATCH` — permiso `Estatus de repuestos Editar`.
- `DELETE` — permiso `Estatus de repuestos Eliminar`. 200 sin `data`.

---

## 6.3 Catálogos legado — namespace `App\Http\Controllers\AdminBranch` (montados en `/api/v1`)

Usan trait `AlertResponser`. Todos con `branch_id` forzado server-side.

### 6.3.1 Marcas — `apiResource('marcas')` → `BrandController`
Modelo `Brand` (`branch_id, name`). Request `BrandRequest`:
```php
'name' => ['required', 'unique por name+branch_id (scoped, ignora self en update)'],
```
- `GET /marcas` — query `query` (LIKE name). Orden `name asc`.
- `POST /marcas` — 201, `data`: registro completo.
- `GET /marcas/{id}/edit` — 404 si no existe: `"Marca no encontrada"`.
- `PUT/PATCH /marcas/{id}` — 200: `"Marca actualizada: {name}"`.
- `DELETE /marcas/{id}` — hard delete. 200: `"Marca eliminada: {name}"`.

### 6.3.2 Modelos de Vehículos — `apiResource('modelos-vehiculos')` → `ModelVehicleController`
Modelo `ModelVehicle` (`branch_id, name`). **Sin validación en el original** (Request
genérico sin reglas) — en la reimplementación aplicar:
```php
'name' => 'required|string|max:90|unique:model_vehicles,name,NULL,id,branch_id,<branch_id_actual>',
```
- CRUD estándar igual patrón que Marcas (index/store/edit/update/destroy), mensajes
  `"Modelo de vehículo {creado|actualizado|eliminado|no encontrado}: {name}"`.

### 6.3.3 Tipos de Artículos — `apiResource('tipos-articulos')` → `TypeArticleController`
Modelo `TypeArticle` (`branch_id, name`). Request `TypeArticleRequest`:
```php
'name' => ['required', 'unique por name+branch_id (scoped)'],
```
(el original valida `name` dos veces por un `$request->validate()` extra redundante en
`store` — no hace falta replicar la duplicación, basta con `required|string|min:3|unique`.)
- CRUD estándar, mensajes `"Tipo de artículo {creado|actualizado|eliminado|no
  encontrado}: {name}"`.

### 6.3.4 Proveedores — `apiResource('proveedores')` → `SupplierController`
Modelo `Supplier` (`branch_id, name, address, phone, email`). Sin FormRequest en el
original; reglas recomendadas para la reimplementación:
```php
'name' => 'required|string|min:3|max:90',
'address' => 'nullable|string',
'phone' => 'nullable|string|max:90',
'email' => 'nullable|email|max:75',
```
- `GET /proveedores` — query `query` (LIKE en name/address/phone/email).
- CRUD estándar, mensajes `"Proveedor {creado|actualizado|eliminado|no encontrado}:
  {name}"`.

### 6.3.5 Servicios — `apiResource('servicios')` → `ServiceController`
Modelo `Service` (`branch_id, name, price:double default 0`). Request `ServiceRequest`:
```php
'name' => ['required', 'unique por name+branch_id (scoped)'],
'price' => 'nullable|numeric|min:0', // agregar en la reimplementación (el original no la valida)
```
- CRUD estándar, mensajes `"Servicio {creado|actualizado|eliminado|no encontrado}:
  {name}"`.

### 6.3.6 Métodos de Pago — `apiResource('metodos-pago')` → `MethodPaymentController`
Modelo `MethodPayment` (`branch_id, name, currency default 'bs'`, columna `slug`
existente en BD pero sin uso real). Request `MethodPaymentRequest`:
```php
'name' => ['required', 'unique por name+branch_id (scoped)'],
'currency' => 'nullable|string|max:10', // agregar en la reimplementación
```
- CRUD estándar, mensajes `"Metodo de pago {guardado}"` / `"Método de pago {actualizado|
  eliminado|no encontrado}: {name}"`.

### 6.3.7 Operadores — `apiResource('operadores')` → `OperatorController`
Actúa sobre `App\Models\User` filtrando rol Spatie `'Operador'`. Middleware de permisos
usa (en el original) el string de permiso `"Supervisores {Crear|Editar|Eliminar|Ver}"`
— **probable bug de copy-paste**; en la reimplementación usa `"Operadores {Ver|Crear|
Editar|Eliminar}"` consistente con el nombre del recurso.

Request `V1\UserRequest` (compartido con Supervisor/Administradores):
```php
'name' => 'required|string|min:3',
'email' => ['required','email','min:3', 'unique:users,email' /* ignora self en update */],
'password' => 'required|min:6 en creación; nullable|min:6 en update',
'password_confirmation' => 'min:6|same:password|required_with:password',
```
- `GET /operadores` — join manual con `model_has_roles/roles/user_branch/branches`,
  filtra `roles.name='Operador'`, `branches.id = branch_id`, excluye al propio usuario
  autenticado. Devuelve filas planas: `{ id, name, email, phone, rol, branch_id, branch }`
  (no el modelo `User` completo). Query `query` (LIKE name/email/phone).
- `POST /operadores` — crea `User` (password hasheado, `email_verified_at=now()`,
  `profile_photo_path` default), asigna rol `Operador`, crea `UserBranch` vinculando a la
  sucursal actual. 201, `data` = modelo `User` completo (con `full_name`,
  `profile_photo_url`, sin `password`).
- `GET /operadores/{id}/edit` — 404 `"Operador no encontrado"`. **Nota**: en el original
  busca por `User::find($id)` sin acotar a la sucursal — en la reimplementación, acotar
  la búsqueda a usuarios de la propia sucursal para no filtrar datos entre tenants.
- `PUT/PATCH /operadores/{id}` — actualiza `name, email, phone`, `password` solo si viene.
- `DELETE /operadores/{id}` — hard delete.

### 6.3.8 Supervisores — `apiResource('supervisores')` → `SupervisorController`
Idéntica estructura a Operadores, rol `'Supervisor'`, permiso `"Supervisores {Ver|Crear|
Editar|Eliminar}"` (aquí sí coincide). Mismo `UserRequest`.

### 6.3.9 Administradores — `apiResource('administradores')` → `AdministradoresController`
Idéntica estructura, rol `'Admin'`, permiso `"Administradores {Ver|Crear|Editar|
Eliminar}"`. Adicionalmente en `store` asigna `language_id` heredado de la sucursal
(`Branch::find($branchId)?->language_id`).

### 6.3.10 Mi Sucursal — `MyBranchController`
Modelo `Branch` (ver campos en §6.4.1). Permiso `Empresa Editar` en `edit`/`update`.
Request `BranchRequest` + validación inline adicional dentro de `update()`:
```php
'name' => 'required|string|min:3|unique:branches,name' /* ignora self, scope global */,
'rif' => 'required|string|min:3',
'address' => 'nullable|string|min:3',
'description' => 'nullable|string|min:3',
'phone' => 'required|numeric|min:3',
'email' => ['required','email','min:3', 'unique:branches,email' /* ignora self */],
```
- `GET /mi-sucursal` — sin params, devuelve la sucursal del usuario autenticado
  (`Branch::find(BranchHelper::getBranchId())`). 404 si no existe.
- `PUT /mi-sucursal/{id}` — soporta subida de archivo `logo` (multipart/form-data),
  guarda la imagen y persiste su URL. 200: `{success:true, message:"Se actualizó la
  sucursal {name}", data:{...branch}}`. 404/500 según corresponda.

### 6.3.11 Configuración — `ConfigurationController`
Modelo `Configuration` (**singleton**, un único registro global: `tax, discount` ambos
`double default 0`). Permiso implícito solo vía middleware general (revisar si el
original exige alguno — no reportado explícitamente, aplicar `Configuracion Editar` en
la reimplementación). Request `ConfigurationRequest`:
```php
'tax' => 'required|numeric|min:0',
'discount' => 'required|numeric|min:0',
```
- `GET /configuracion` — devuelve el único registro (`Configuration::first()`, puede ser
  `null`).
- `PUT /configuracion` — 404 si no existe ningún registro. 200 con el registro
  actualizado.

### 6.3.12 Tasa Diaria — `DailyRateController`
Modelo `DailyRate` (`rate, average_rate`, ambos `double default 0`). **No tiene update ni
delete**: cada `POST` inserta un **nuevo registro histórico** (no reemplaza el anterior).
Request `DailyRateRequest`:
```php
'rate' => 'required|numeric|min:0',
'average_rate' => 'nullable|numeric|min:0', // agregar en la reimplementación
```
- `GET /tasa-diaria` — en el original devuelve `DailyRate::first()` (el registro más
  antiguo). **Revisar intención**: probablemente se buscaba la tasa **más reciente**
  (`->latest()->first()`) — corregir en la reimplementación si el objetivo es mostrar la
  tasa vigente.
- `POST /tasa-diaria` — inserta nuevo registro. 200 (no 201 en el original, aunque
  semánticamente es una creación — usar 201 en la reimplementación).

---

## 6.4 Mobile — Push Notifications (`V1\Mobile\PushTokenController`)

Prefijo `mobile/push-tokens`, dentro del grupo `v1` (auth+subscription heredado).
**El `user_id` se toma siempre de `$request->user()->id` (Sanctum) — nunca del body.**

Modelo `PushToken` (SoftDeletes): `user_id, token (unique global, max 512), platform
(android|ios), device_id (nullable), app_version (nullable), last_used_at`.

### `GET /api/v1/mobile/push-tokens`
Sin query params. Devuelve todos los tokens **activos** del usuario autenticado,
ordenados por `last_used_at desc`.
```jsonc
{ "success": true, "message": "Operación exitosa", "data": { "data": [
  { "id": 1, "platform": "android", "device_id": "...", "app_version": "1.2.0",
    "last_used_at": "...", "created_at": "..." } ] } }
```

### `POST /api/v1/mobile/push-tokens`
Request (`PushTokenStoreRequest`):
```php
'token' => 'required|string|max:512',
'platform' => ['required','string', 'in:android,ios'],
'device_id' => 'nullable|string|max:191',
'app_version' => 'nullable|string|max:50',
```
Lógica: si viene `device_id`, invalida (soft-delete) tokens activos de ese mismo
dispositivo pertenecientes a **otros** usuarios (evita fuga de notificaciones al cambiar
de usuario en el mismo teléfono). Luego hace upsert idempotente por `token` (reactiva si
estaba soft-deleted). 200: `{ "success": true, "message": "Push token registered",
"data": { "token_registered": true } }`.

### `DELETE /api/v1/mobile/push-tokens`
Request (`PushTokenDestroyRequest`): `'token' => 'required|string|max:512'`. Soft-delete
solo si el token pertenece al usuario autenticado; idempotente (responde éxito aunque no
exista o no le pertenezca, sin lanzar 404). 200: `{ "success": true, "message": "Push
token removed" }`.

---

## 6.5 Admin — Push de prueba (`V1\Admin\PushTestController`)

### `POST /api/v1/admin/push/test`
Autorización manual (no middleware `permission:`): requiere rol `Super Admin` o `Admin`
(`$user->hasAnyRole([...])`); si no, `403 {"success":false,"message":"Forbidden"}`.

Envía la notificación vía **Expo Push API** (`ExpoPushService`), no FCM directo. Request:
```php
'user_id' => 'required|integer|exists:users,id',
'title' => 'required|string|max:120',
'body' => 'required|string|max:500',
'url' => 'nullable|string|max:500',
'type' => 'nullable|string|max:60',
'equipment_uuid' => 'nullable|string|max:64',
'large_icon' => 'nullable|string|max:500',
'big_picture' => 'nullable|string|max:500',
```
Envía a todos los `PushToken` activos del `user_id` indicado; elimina automáticamente
tokens que Expo reporte como `DeviceNotRegistered`. 200:
```jsonc
{ "success": true, "message": "Push test dispatched",
  "data": { "sent": 2, "failed": 0, "invalid_tokens_removed": 0 } }
```

---

## 6.6 Super Admin — namespace `SuperAdmin` (`/api/v1/super-admin/...`)

> ⚠️ **Advertencia importante para la reimplementación**: en el código original, este
> grupo de rutas **no tiene ningún middleware de rol/permiso propio** — cualquier usuario
> autenticado con Sanctum (de cualquier rol) puede llamarlas; el namespace "SuperAdmin" es
> solo organizativo. **En el nuevo proyecto, agrega explícitamente
> `->middleware('role:Super Admin')` (o el `permission:` equivalente) a todo este grupo**,
> ya que gestiona sucursales, usuarios globales y roles/permisos del sistema completo.
>
> Además, varios métodos **no están implementados** en el original pese a que
> `Route::apiResource` registra la ruta (fallan con error 500 al no encontrar el método).
> Impleméntalos todos como corresponde en el nuevo proyecto — se listan explícitamente
> abajo cuáles faltaban.

### 6.6.1 Sucursales — `apiResource('sucursales')` → `BranchController`
Modelo `Branch`: `name(90), rif(nullable), address(text,nullable), description(text,
nullable), email(90,unique), phone(90), logo(text,nullable), is_test(bool,default
false), language_id(FK languages, default 1)`.

- `GET /sucursales` (index) — en el original, **roto (500)**: pasa una `Collection`
  completa (`Branch::all()`) a una función que espera un paginador y llama a `->items()`,
  método inexistente en `Collection`. **En la reimplementación, usar `paginate()` real**
  y devolver la forma estándar `{data:{data:[],pagination:{}}}`.
- `show` — **no implementado** en el original; implementar `GET /sucursales/{id}`.
- `POST /sucursales` (store):
```php
'name' => 'required|string|min:3',
'description' => 'nullable|string|min:3', // hacer required en la reimplementación, ya que la columna es obligatoria en negocio real
'phone' => 'required|numeric|min:3',
'email' => ['required','email','min:3', 'unique:branches,email'],
'language_id' => 'nullable|exists:languages,id', // si no viene, usa el idioma default de config('languages.default')
```
  201: `{ "success": true, "message": "Se creó la Sucursal {name}", "data": {...branch} }`.
- `PUT/PATCH /sucursales/{id}` (update) — mismas reglas (`email` ignora self). 404 si no
  existe. 200 con el registro actualizado.
- `DELETE /sucursales/{id}` (destroy) — delega en un `BranchDeletionService` que, en
  transacción: borra los `Equipment` de la sucursal, borra su `FaultHistory`, revoca
  sesiones y tokens Sanctum de sus usuarios, **borra los `User` de la sucursal**, borra el
  `logo` del storage, y finalmente borra la `Branch`. 404 si no existe. 200: `{success:
  true, message:"Sucursal eliminada"}` (sin `data`).

### 6.6.2 Usuarios — `apiResource('usuarios')` → `UserController`
En el original **solo existe `POST /usuarios`**; `index/show/update/destroy` no están
implementados (500). Implementar los 5 verbos completos en la reimplementación,
incluyendo asignación de rol en la creación (el original crea el `User` sin rol
asignado, algo a corregir: idealmente `store` debería aceptar/asignar un `role_id` o
`role` directamente).

`POST /usuarios`:
```php
'name' => 'required|string|min:3',
'email' => 'required|email|min:3|unique:users,email',
'password' => 'required|min:6',
'password_confirmation' => 'required_with:password|same:password|min:6',
```
201:
```jsonc
{ "success": true, "message": "Usuario {name} guardado", "data": {
  "id": 1, "name": "...", "email": "...", "email_verified_at": "...",
  "profile_photo_path": "images/user-icon.webp", "created_at": "...", "updated_at": "...",
  "profile_photo_url": "https://.../storage/images/user-icon.webp", "full_name": "..." } }
```

### 6.6.3 Roles y permisos — `apiResource('roles')` + rutas extra → `RoleController`
Usa `Spatie\Permission\Models\{Role,Permission}` directo (sin Service). **Fijar siempre
`guard_name: 'sanctum'`** al crear roles/permisos en la reimplementación (ver §2).

- `GET /roles` (index) — en el original **roto (500)** por el mismo bug de `Collection`
  vs paginador que en Sucursales. Implementar con `paginate()` real. Considera además
  hacer eager-load de `permissions` por rol para que el índice sea útil de un vistazo.
- `show` — **no implementado** en el original; implementar.
- `POST /roles` (store) — en el original **sin validación** (`$request->input('nombre')`
  directo, riesgo de excepción SQL si falta). En la reimplementación:
```php
'name' => 'required|string|min:3|unique:roles,name,NULL,id,guard_name,sanctum',
```
  201: `{ "success": true, "message": "Rol creado", "data": {...role} }`.
- `GET /roles/{role}/permisos` (edit) — devuelve el rol + **todos** los permisos del
  sistema (sin filtrar por guard en el original — filtrar por `guard_name='sanctum'` en
  la reimplementación) para que el cliente arme el formulario de asignación. Recomendado
  además incluir `role.permissions` cargado (`$role->load('permissions')`) para que el
  cliente sepa cuáles ya están marcados, cosa que el original no hace.
```jsonc
{ "success": true, "message": "Datos para edición cargados",
  "data": { "role": {...}, "permissions": [ {...}, ... ] } }
```
- `PUT /roles/{role}/permisos` (update) — sincroniza permisos del rol:
```php
'permissions' => 'required|array',
'permissions.*' => 'integer|exists:permissions,id',
```
  `$role->permissions()->sync($request->permissions)`, invalida caché de Spatie
  (`Artisan::call('cache:forget', ['key'=>'spatie.permission.cache'])` — en Laravel/
  Spatie moderno preferir `app(PermissionRegistrar::class)->forgetCachedPermissions()`).
  200: `{ "success": true, "message": "Se agregaron permisos para el rol {name}",
  "data": {...role} }`.
- `DELETE /roles/{id}` (destroy) — 404 si no existe. 200: `{success:true, message:"Rol
  eliminado"}` (sin `data`).

---

# 7. Checklist de implementación para el nuevo proyecto

1. Instalar Sanctum + spatie/laravel-permission; configurar guard `sanctum` para todos
   los roles/permisos.
2. Definir modelo `Branch` (tenant) y helper de resolución de sucursal actual desde el
   usuario autenticado (nunca confiar en `branch_id` del body).
3. Middleware `check.subscription` — decide si aplicarlo a **todos** los verbos (no solo
   POST, a diferencia del original).
4. Definir un único envelope de respuesta (`{success, message, data}` recomendado,
   incluyendo los 422 de validación) y un trait/helper reutilizable, evitando duplicar
   dos convenciones distintas como el proyecto original.
5. Implementar CRUD sucursal-scoped para: clientes, divisiones, empleados, ejecutores,
   equipos (+ QR + historial), tipos de equipo, fallas (+ vista desnormalizada o
   consulta con JOINs), estados de falla, propietarios, proyectos, áreas de servicio,
   estados de repuestos, marcas, modelos de vehículo, tipos de artículo, proveedores,
   servicios, métodos de pago, operadores/supervisores/administradores (sobre `User` +
   rol Spatie), mi-sucursal, configuración (singleton), tasa diaria (histórico
   append-only).
6. Implementar sync offline-first (`/fallas/sync`, `/sync/initial-data`) con
   idempotencia por operación.
7. Implementar push tokens (Expo o FCM, según se decida) con invalidación cross-user por
   `device_id` y limpieza automática de tokens inválidos.
8. Super Admin: proteger explícitamente con rol/permiso `Super Admin`, completar todos
   los verbos CRUD faltantes, y corregir el bug `Collection::items()` usando paginación
   real en los índices.
9. Aplicar validación real y consistente (`FormRequest` conectado de verdad al
   controlador) en todos los recursos que en el original carecían de ella (Clientes,
   Divisiones, Modelos de Vehículo, Proveedores).
10. Añadir soporte a `per_page` configurable y unificar el criterio de paginación en
    todos los índices (incluyendo Estados de Falla, que en el original no pagina).

---

# 8. Estado de implementación (2026-08-11)

> Esta sección se agregó al implementar la API en este proyecto (`reporte-fallas`).
> Documenta qué se construyó realmente, qué se dejó fuera a propósito y qué bugs
> se corrigieron respecto a lo documentado arriba, para que quien retome esto no
> tenga que releer todo el código para saberlo.

## 8.1 Hecho (Fase 1: núcleo)

Se implementó **auth + CRUD sucursal-scoped + catálogos legado**, sirviendo bajo
`/api/v1/...`, probado end-to-end contra el entorno Docker local (login, permisos,
guard, branch-scoping, cierre de fallas con archivado, catálogos, trío de usuarios
por rol). Código nuevo relevante:

- `app/Helpers/BranchHelper.php` — resuelve `branch_id` desde `user->branches()`
  (reemplaza `session('branch')` para la API, que es stateless).
- `app/Traits/Api/ApiResponse.php` + `app/Http/Requests/Api/V1/ApiFormRequest.php`
  — envelope único `{success,message,data}` en TODAS las respuestas, incluidos los
  422 de validación (spec §3, recomendación aplicada).
- `app/Http/Middleware/ForceSanctumGuard.php` (alias `sanctum.guard`) — fuerza
  `Auth::shouldUse('sanctum')` en todo `/api/v1`, corrigiendo el mismatch de guard
  entre `auth.defaults.guard=web` y los roles/permisos sembrados con
  `guard_name=sanctum` (spec §2). Sin esto, `hasRole()`/`hasPermissionTo()`/
  `getAllPermissions()` devuelven vacío silenciosamente.
- `app/Exceptions/Handler.php` — unifica también las excepciones que no pasan por
  un controlador (401 sin sesión, 403 de permisos, 404, 500) al mismo envelope,
  solo para rutas `api/v1/*`.
- `app/Services/Api/LoginLockoutService.php` — bloqueo de cuenta por intentos
  fallidos (`ACCOUNT_LOCKED`) implementado sobre cache, sin tabla nueva.
- `app/Http/Controllers/Api/V1/Concerns/SimpleCrudApiController.php` — base
  reutilizada por los catálogos simples (Divisiones, Áreas de Servicio, Estados de
  Repuestos, Tipos de Equipo, Clientes, Propietarios, Marcas, Modelos de Vehículo,
  Tipos de Artículo, Proveedores, Servicios, Métodos de Pago) para no duplicar
  index/store/update/destroy en cada uno.
- `app/Http/Controllers/Api/V1/Catalog/Concerns/RoleUserApiController.php` — base
  para Operadores/Supervisores/Administradores (User + rol Spatie).
- Recursos con lógica propia: Empleados/Ejecutores (sync de usuario de sistema vía
  `UserService` existente), Equipos (sync de proyectos), Proyectos (join plano en
  índice), Fallas (lectura de `v_faults_base`, cierre con copia a `fault_history`
  + borrado, transacciones DB **activas** — el original las tenía comentadas),
  Estados de Falla (sin paginar, excluye `'closed'`), Mi Sucursal, Configuración
  (singleton), Tasa Diaria (append-only).

Bugs corregidos respecto al original (documentados arriba), según lo acordado:
falta de filtro `branch_id` en varios índices, guard mismatch de roles/permisos,
`engine_serial_number` no asignado en Equipos, email de Mi Sucursal validado
contra `users` en vez de `branches`, permiso `"Supervisores *"` copiado por error
en Operadores (ahora `"Operadores *"`), Tasa Diaria devolviendo el registro más
antiguo (ahora `->latest()`), transacciones deshabilitadas en cierre de Fallas,
falta de validación real en Clientes/Divisiones/Modelos de Vehículo/Proveedores.
También se corrigió en el camino un bug nuevo (no documentado arriba porque no
existía en el código original leído para la spec): `equipos` con
`with('lastProject:id,name')` producía `Column 'id' ambiguous` por el JOIN de
`hasOneThrough`; y el trío Operadores/Supervisores/Administradores podía dejar un
`User` huérfano si fallaba la vinculación a `user_branch` (ahora en transacción).

Permisos nuevos agregados a `app/Helpers/Permisos.php` (no existían): `Ejecutores
*`, `Propietarios *`, `Marcas *`, `Modelos de Vehiculo *`, `Tipos de Articulos *`,
`Servicios *`, `Metodos de Pago *`, `Configuracion Editar`, `Tasa Diaria Ver/Crear`.

## 8.1.1 Push notifications (Expo) — implementado (2026-08-20)

Igual arquitectura que ironflow (`ExpoPushService` de transporte + `PushNotificationService`
de negocio, sin SDK de Firebase), con dos desviaciones deliberadas respecto al original:

- Columna `platform` de `push_tokens` es **nullable**: el registro embebido en el
  login (único camino que usa hoy `app-reporte-fallas`, vía `expo_token` en
  `POST /login`) no recibe la plataforma real del dispositivo. ironflow hardcodea
  `'android'` ahí, lo cual es incorrecto para iOS — se prefirió dejar `null` en vez
  de mentir el dato. El endpoint dedicado `POST /mobile/push-tokens` sí exige
  `platform` (`required|in:android,ios`).
- El payload de push no incluye `url` (ironflow apunta a `https://tryironflow.com/...`,
  dominio que no existe acá): el listener de `app-reporte-fallas` (`App.js`) ya navega
  solo con `data.type` (`fault_created`/`fault_closed`) + `data.fault_id`.

Endpoints: `GET/POST/DELETE /api/v1/mobile/push-tokens` (`V1\Mobile\PushTokenController`)
y `POST /api/v1/admin/push/test` (`V1\Admin\PushTestController`, solo Super Admin/Admin),
igual contrato al documentado en §6.4-6.5. Disparado desde
`V1\AdminBranch\FaultController::saveOrUpdate()`: `notifyNewFault` solo en alta nueva
(no en `update`), `notifyClosedFault` en la rama de cierre — ambos envueltos en
`try/catch` (dentro del propio servicio), no rompen el flujo de creación/cierre si Expo
falla. Destinatarios: admins/supervisores de la sucursal de la falla (+ el operador que
la reportó, en el caso de cierre).

## 8.2 Pendiente / fuera de alcance (decisión explícita, no olvido)

- **Sync offline-first** (`POST /fallas/sync`, `GET /sync/initial-data`) — no
  implementado. Requiere columna `local_id` en `faults` (no existe) y tabla
  `operation_idempotency_keys` (no existe).
- **Namespace Super Admin** (`/api/v1/super-admin/...`: sucursales, usuarios,
  roles/permisos vía API) — no implementado.
- **Límite de plan por sucursal** (`Subscription`/`Plan`, `max_equipment`) — no
  implementado a propósito: es una feature de negocio (billing) nueva, no algo
  que ya existiera para "exponer". `check.subscription` tampoco se registró como
  middleware (no hay nada que chequear todavía).
- **Idioma / `locale`** — este proyecto no tiene módulo de idiomas (`languages`),
  a diferencia del proyecto donde se generó esta spec. `POST /login` no acepta
  `locale`, y no existe `PUT /user/language`.
- **QR y `GET /equipos/uuid/{uuid}`** — no implementado. Requiere columnas nuevas
  (`uuid`, `qr_code_path`) en `equipment` y el paquete `endroid/qr-code`, que hoy
  no están en el proyecto. Sí se implementó `GET /equipos/{id}/historial`
  (reutiliza la relación `history()` existente, sin depender del QR).
- **Stats de `GET /fallas`** (`data.stats`) — implementado con la misma heurística
  de texto sobre `fault_status_name` que ya advertía la spec como deuda técnica;
  no se reemplazó por un campo de categoría real (fuera de alcance de "exponer la
  API existente").
- **`Idempotency-Key`** en `POST /fallas` — no implementado (depende de la misma
  tabla que el sync offline).

## 8.3 Notas para quien continúe

- Los recursos legado (§6.3) se sirven bajo el mismo prefijo `/api/v1/...` que el
  resto (no bajo un prefijo "legado" separado): la spec original los agrupaba por
  namespace de código (`AdminBranch` sin versionar), pero las rutas HTTP conviven
  con las demás.
- Todas las rutas usan `Route::apiResource(...)->parameters([uri => 'id'])` para
  forzar el nombre del parámetro a `id` de forma consistente (necesario para que
  los FormRequest puedan hacer `$this->route('id')` al ignorar el propio registro
  en validaciones `unique`).
- Para pruebas locales se cambió temporalmente la contraseña de `admin@gmail.com`
  y `superadmin@gmail.com` (ambos en la BD del Docker local) a `Test1234` —
  cámbiala de nuevo si te importa mantenerla como estaba.
