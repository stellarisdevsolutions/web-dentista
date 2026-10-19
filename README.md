# Aura Dental Clinic & CMS

Sitio web moderno y sistema de gestión de contenidos (CMS) para una **Clínica Dental moderna**, construido con **Laravel 13**, **Filament v5**, **Tailwind CSS v4** y **pnpm**.

---

## 🚀 Stack Tecnológico

* **Framework:** Laravel 13 (PHP 8.3+)
* **Base de Datos:** SQLite (`database/database.sqlite`)
* **Panel de Administración:** Filament v5 (`/admin`)
* **Frontend:** Blade + Tailwind CSS v4 + Vite 7
* **Gestor de Paquetes:** pnpm (v10+)
* **Internacionalización:** Inglés primario (`en`) con soporte completo para Español (`es`).

---

## 📋 Documentación y Especificaciones

Para consultar la guía detallada de instalación, arquitectura técnica completa y directrices:

* 👉 [**`INSTALL.md`**](./INSTALL.md) — Guía completa de instalación y despliegue local paso a paso.
* 👉 [**`CLINIC_SPECIFICATION.md`**](./CLINIC_SPECIFICATION.md) — Arquitectura técnica y modelo de datos.
* 👉 [**`ALGORITMO_RESERVAS.md`**](./ALGORITMO_RESERVAS.md) — Lógica de turnos, horarios y asignación de citas.

---

## ⚙️ Instalación y Desarrollo Local (Windows)

1. **Instalar dependencias:**
   ```bash
   composer install
   pnpm install
   ```

2. **Configuración de entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   > **Nota sobre Zona Horaria:** Configura `APP_TIMEZONE` en tu `.env` con la zona horaria local de la clínica (ej. `America/Caracas`, `America/Bogota`, `America/Lima`). Esto garantiza que la agenda médica, el selector de citas y los reportes calculen el día actual de forma exacta.

3. **Migraciones y Enlace de Almacenamiento:**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

4. **Compilación de Frontend:**
   ```bash
   pnpm run build
   ```

5. **Iniciar Servidor de Desarrollo:**
   ```bash
   composer run dev
   ```
   *(El script ejecuta `concurrently` corriendo el servidor de Laravel, worker de colas y Vite de forma sincronizada, adaptado para Windows).*

---

## 🔐 Acceso al Panel de Administración

* **URL:** `http://127.0.0.1:8000/admin`
* **Módulos:**
  * **Gestión Clínica:** Citas, Especialistas, Servicios / Tratamientos.
  * **Casos & Reseñas:** Sonrisas Antes/Después, Testimonios verificados.
  * **CMS:** Banners, Blog de Salud Bucal, Preguntas Frecuentes (FAQ).
  * **Configuración:** Datos de contacto, WhatsApp y selector de idioma.
