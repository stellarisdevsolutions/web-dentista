# 🛠️ Guía de Instalación y Despliegue Local

Esta guía describe paso a paso cómo instalar, configurar y poner en marcha **Aura Dental Clinic & CMS** en un entorno local (Windows / Linux / macOS).

---

## 📋 1. Requisitos Previos del Sistema

Asegúrate de tener instaladas las siguientes herramientas en tu sistema:

* **PHP:** Versión `8.3` o superior.
  * Extensiones PHP requeridas: `pdo`, `pdo_sqlite` (o `pdo_mysql`), `mbstring`, `openssl`, `curl`, `fileinfo`, `intl`, `gd` o `imagick`.
* **Composer:** Gestor de paquetes PHP (`v2.x`).
* **Node.js:** Versión `20.x` o superior (LTS recomendada).
* **pnpm:** Gestor de paquetes frontend (`v9` o `v10`).
  > **Nota:** Este proyecto utiliza **`pnpm`** de forma exclusiva para la gestión de dependencias de frontend y ejecución de scripts. Evita usar `npm` o `yarn`.
* **Git:** Para control de versiones.

---

## 🚀 2. Pasos de Instalación

### Paso 1: Clonar el Repositorio

Si aún no tienes el código clonado localmente:

```bash
git clone <URL_DEL_REPOSITORIO>
cd "web dentista"
```

---

### Paso 2: Instalar Dependencias Backend y Frontend

1. **Dependencias PHP con Composer:**
   ```bash
   composer install
   ```

2. **Dependencias Frontend con pnpm:**
   ```bash
   pnpm install
   ```

---

### Paso 3: Configuración del Entorno (`.env`)

1. Copia el archivo de ejemplo:
   ```bash
   cp .env.example .env
   ```
   *(En Windows PowerShell: `Copy-Item .env.example .env`)*

2. Genera la clave de aplicación:
   ```bash
   php artisan key:generate
   ```

3. **Variables clave en el archivo `.env`:**

   * **Nombre de la Aplicación y URL:**
     ```env
     APP_NAME="Aura Dental Studio"
     APP_ENV=local
     APP_DEBUG=true
     APP_URL=http://localhost:8000
     ```

   * **Zona Horaria y Localización:**
     Configura la zona horaria de tu clínica para que el calendario, turnos y citas se calculen con exactitud respecto al horario local:
     ```env
     APP_TIMEZONE=America/Caracas
     APP_LOCALE=es
     APP_FALLBACK_LOCALE=en
     ```

   * **Formato de Visualización de Horas (`APP_TIME_FORMAT`):**
     Controla si las horas se muestran en formato de 12 horas con AM/PM o 24 horas en el sitio web de reservas, la agenda médica y el panel de control:
     ```env
     APP_TIME_FORMAT=12h
     # Opciones: 12h (09:00 AM / 02:00 PM) | 24h (09:00 / 14:00)
     ```

   * **Base de Datos (SQLite por defecto):**
     Por defecto el proyecto está preconfigurado para SQLite:
     ```env
     DB_CONNECTION=sqlite
     ```
     Si el archivo `database/database.sqlite` no existe, créalo:
     * En Windows PowerShell:
       ```powershell
       New-Item -ItemType File -Path "database/database.sqlite" -Force
       ```
     * En Linux / macOS / Bash:
       ```bash
       touch database/database.sqlite
       ```

---

### Paso 4: Migraciones y Datos de Prueba (Seeders)

Ejecuta las migraciones y puebla la base de datos con los datos iniciales de la clínica (servicios, odontólogos, horarios por turnos y artículos de blog):

```bash
php artisan migrate:fresh --seed
```

---

### Paso 5: Enlace Simbólico de Almacenamiento

Permite que las imágenes de fotos de dentistas, banners, casos antes/después y blog se sirvan públicamente:

```bash
php artisan storage:link
```

---

### Paso 6: Optimización de Imágenes y Conversión a WebP

El proyecto integra **Intervention Image v4** para convertir automáticamente cualquier imagen subida en Filament a `.webp` (resolución máxima de 1600px y calidad óptima 82%, ahorrando hasta un 95% de peso).

Para optimizar las imágenes existentes en lote y actualizar las referencias en la base de datos:

```bash
php artisan images:optimize
```

---

### Paso 7: Compilar los Assets Frontend

Compila el CSS de Tailwind v4 y los bundles de Vite:

```bash
pnpm run build
```

---

## 💻 3. Ejecución del Servidor Local

Puedes iniciar todo el ecosistema de desarrollo con un solo comando:

```bash
composer run dev
```

Este comando levanta de forma simultánea y sincronizada:
1. Servidor HTTP de Laravel (`http://localhost:8000`).
2. Worker de Colas (`php artisan queue:listen`).
3. Servidor de desarrollo de Vite con recarga en vivo (`pnpm run dev`).

> **Alternativa (Terminales separadas):**
> * Terminal 1: `php artisan serve`
> * Terminal 2: `pnpm run dev`
> * Terminal 3 (opcional): `php artisan queue:listen`

---

## 🔐 4. Credenciales de Acceso

### Panel de Administración (Filament)
* **URL:** [http://localhost:8000/admin](http://localhost:8000/admin)
* **Email:** `admin@admin.com`
* **Contraseña:** `password`

### Sitio Web Público
* **Página Principal:** [http://localhost:8000](http://localhost:8000)
* **Reservas de Citas:** [http://localhost:8000/schedule](http://localhost:8000/schedule)
* **Servicios / Tratamientos:** [http://localhost:8000/services](http://localhost:8000/services)
* **Especialistas:** [http://localhost:8000/about](http://localhost:8000/about)
* **Blog:** [http://localhost:8000/blog](http://localhost:8000/blog)
* **Contacto:** [http://localhost:8000/contact](http://localhost:8000/contact)

---

## 🧪 5. Pruebas Automatizadas y Calidad de Código

### Ejecución de Pruebas (PHPUnit)
El proyecto cuenta con una cobertura integral de pruebas unitarias y de integración (más de 100 tests):

```bash
# Ejecutar toda la suite de pruebas
php artisan test --compact

# Ejecutar una prueba específica (ejemplo formato de tiempo)
php artisan test --filter=TimeFormatTest
```

### Formateo de Código (Laravel Pint)
Para asegurar que todo el código PHP cumpla con los estándares oficiales de estilo:

```bash
vendor/bin/pint --format agent
```

---

## ❓ 6. Solución de Problemas Frecuentes

1. **Error: `Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest`**
   * **Causa:** No se han compilado los archivos estáticos de Vite.
   * **Solución:** Ejecuta `pnpm run build` o mantén activo `pnpm run dev`.

2. **Error de permisos o archivo de base de datos no encontrado (`database.sqlite`)**
   * **Causa:** El archivo de base de datos SQLite no existe físicamente en la carpeta `database/`.
   * **Solución:** Ejecuta `touch database/database.sqlite` (o en PowerShell `New-Item -ItemType File -Path "database/database.sqlite" -Force`) y luego corre `php artisan migrate --seed`.

3. **Imágenes o fotos no se muestran en el sitio o panel**
   * **Causa:** Falta el enlace simbólico hacia `public/storage`.
   * **Solución:** Ejecuta `php artisan storage:link`. Si falla en Windows, asegúrate de tener permisos de administrador o habilitar el modo desarrollador en Windows.

4. **El horario no coincide con la hora local de la clínica**
   * **Solución:** Modifica `APP_TIMEZONE` en el `.env` (ej. `APP_TIMEZONE=America/Caracas` o tu huso horario correspondiente) y limpia la caché con `php artisan config:clear`.
