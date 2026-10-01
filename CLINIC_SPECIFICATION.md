# Especificación Técnica y Funcional: Clínica Dental Moderna (2024 - 2026)

Este documento define la arquitectura, modelos de datos, flujos de usuario, estándares de diseño y estrategia bilingüe para el desarrollo de la plataforma web y CMS de la **Clínica Dental**, utilizando **Laravel 13**, **Filament v5**, **Tailwind CSS v4** y **pnpm**.

---

## 1. Stack Tecnológico y Entorno

* **Backend:** Laravel 13 (PHP 8.3+)
* **Base de Datos:** SQLite (desarrollo ágil / portable)
* **Panel de Administración (CMS):** Filament v5 (`/admin`)
* **Frontend:** Blade + Tailwind CSS v4 + Vite
* **Gestor de Paquetes:** pnpm (v10+)
* **Automatización de Desarrollo en Windows:** `composer run dev` configurado con `concurrently` (sin dependencia de `pail` / `pcntl`).
* **Almacenamiento de Archivos:** `public/storage` vinculado con `php artisan storage:link`.

---

## 2. Arquitectura Multilingüe (English Primary + Español Demo)

El sistema está concebido para operar **primariamente en inglés**, pero cuenta con soporte nativo completo para **español**, permitiendo alternar entre ambos para presentaciones o demostraciones a clientes internacionales o hispanohablantes.

### 2.1 Configuración de Idiomas
* **Idioma por defecto (Primary Locale):** `en` (Inglés).
* **Idioma secundario:** `es` (Español).
* **Fallback Locale:** `en`.
* **Idiomas soportados:** `['en', 'es']`.

### 2.2 Selector en el Header y Modo Monoidioma
* **Componente en el Header:** Un botón o switch tipo "pill" minimalista: `[ EN | ES ]` con retroalimentación visual del idioma seleccionado.
* **Control de Demostración (Monoidioma vs. Bilingüe):**
  * Variable de entorno / configuración: `SHOW_LANGUAGE_SWITCHER=true/false` (en `.env` y en la configuración de la clínica).
  * **Si está en `true`:** Muestra el selector `[ EN | ES ]` en el header para alternar en tiempo real durante la demo.
  * **Si está en `false`:** Oculta el selector visualmente y la web se despliega en el idioma principal configurado (por ejemplo, 100% en Inglés), sin rastro de selector.

### 2.3 Mecanismo de Cambio de Idioma
* **Ruta de cambio:** `GET /locale/{lang}` que valida si el idioma está en `['en', 'es']`, lo guarda en sesión `session(['locale' => $lang])` y redirige al usuario a la página de origen (`back()`).
* **Middleware `SetLocale`:** Se ejecuta en el grupo `web` (en `bootstrap/app.php`), recuperando el idioma de sesión y llamando a `app()->setLocale($locale)`.

### 2.4 Textos de Interfaz vs. Contenido de Base de Datos
* **Textos estáticos del Frontend:** Gestionados mediante `lang/en.json` y `lang/es.json` usando `__('Book Appointment')`, `__('Our Services')`, etc.
* **Contenido dinámico del CMS (Filament):**
  * Cada entidad administrable incluye pares de columnas (`name_en` y `name_es`, `description_en` y `description_es`, etc.).
  * En Filament v5, los formularios utilizan **Tabs / Pestañas** elegantes (*"🇬🇧 English"* y *"🇪🇸 Español"*) para que la carga de información sea ordenada y familiar.
  * En los modelos Eloquent, accesores inteligentes devuelven el atributo correspondiente al idioma activo:
    ```php
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name_en ?? '';
    }
    ```
    *En las vistas Blade solo se invoca `$service->name` y se renderiza el idioma en curso automáticamente.*

---

## 3. Flujo y Estándar de Citas (Inspirado en LatePoint / Amelia / Doctoralia)

### 3.1 Flujo del Paciente (Frontend en 4 Pasos)

```
[1. Service / Treatment] ➜ [2. Specialist / Doctor] ➜ [3. Date & Time Slot] ➜ [4. Patient Info & Confirm]
```

1. **Step 1: Treatment / Service:**
   * Selección del tratamiento: *Initial Consultation / Exam, Teeth Cleaning, Orthodontics / Braces, Dental Implants, Teeth Whitening, Emergency Care*.
   * Tiempo estimado visible (ej. 30 min, 45 min) y precio referencial (*"From $X"* o *"Free Consultation"*).
2. **Step 2: Specialist:**
   * Elección de doctor con foto, cargo y especialidad, o la opción: **"Any available specialist"** (para disponibilidad inmediata).
3. **Step 3: Date & Time:**
   * Calendario interactivo con bloqueo de días cerrados y selección de franjas horarias libres.
4. **Step 4: Patient Info & Confirmation:**
   * Nombre completo, Teléfono/WhatsApp (campo prioritario para recordatorios), Email, Primera visita (Yes/No), Motivo o síntomas.
   * Generación de **Código de Cita** (ej. `CIT-2026-0591`).
   * Botón de confirmación con **enlace directo a WhatsApp** con texto preconfigurado en el idioma activo.

---

## 4. Modelo de Datos y Entidades (Bilingüe)

### 4.1 `appointments` (Gestión de Citas)
* `id`: ID autonumérico.
* `code`: String único (ej. `CIT-2026-0001`).
* `service_id`: Foreign key a `services` (nullable).
* `dentist_id`: Foreign key a `dentists` (nullable).
* `patient_name`: String.
* `patient_email`: String (nullable).
* `patient_phone`: String (WhatsApp principal).
* `appointment_date`: Date.
* `appointment_time`: Time (ej. `10:30:00`).
* `duration_minutes`: Integer (default 45).
* `is_first_visit`: Boolean (default true).
* `symptoms`: Text (motivo o síntoma descrito por el paciente).
* `internal_notes`: Text (notas privadas de recepción o doctor).
* `preferred_locale`: String (default `'en'`).
* `status`: Enum (`pending`, `confirmed`, `in_progress`, `completed`, `cancelled`, `no_show`).
* `timestamps`.

### 4.2 `services` (Tratamientos y Procedimientos)
* `id`: ID autonumérico.
* `name_en`: String (ej. *Clear Aligners & Orthodontics*).
* `name_es`: String (ej. *Alineadores Invisibles y Ortodoncia*).
* `slug`: String único.
* `category_en`: String (ej. *Cosmetic, Orthodontics, Surgery, Prevention*).
* `category_es`: String (ej. *Estética, Ortodoncia, Cirugía, Prevención*).
* `short_description_en`: String.
* `short_description_es`: String.
* `description_en`: Text / RichEditor.
* `description_es`: Text / RichEditor.
* `price`: Decimal (nullable, precio base o "desde").
* `duration_minutes`: Integer (default 45).
* `image`: String (foto representativa).
* `icon`: String (icono SVG o nombre de icono).
* `allows_online_booking`: Boolean (default true).
* `is_featured`: Boolean (destacado en landing).
* `is_active`: Boolean (default true).
* `order`: Integer (orden de visualización).
* `timestamps`.

### 4.3 `dentists` (Odontólogos y Especialistas)
* `id`: ID autonumérico.
* `name`: String (ej. *Dr. Julian Vance, DDS*).
* `slug`: String único.
* `title_en`: String (ej. *Lead Orthodontist & Implantologist*).
* `title_es`: String (ej. *Ortodoncista Principal e Implantólogo*).
* `license_number`: String (número de colegiatura o registro médico).
* `specialty_en`: String.
* `specialty_es`: String.
* `bio_en`: Text.
* `bio_es`: Text.
* `photo`: String.
* `email`: String (nullable).
* `phone`: String (nullable).
* `working_days`: JSON (días que atiende, ej. `["monday", "tuesday", "wednesday", "thursday", "friday"]`).
* `working_hours_start`: Time (ej. `09:00:00`).
* `working_hours_end`: Time (ej. `18:00:00`).
* `is_active`: Boolean (default true).
* `order`: Integer.
* `timestamps`.

### 4.4 `smile_cases` (Casos de Éxito Antes y Después)
* `id`: ID autonumérico.
* `title_en`: String (ej. *Full Porcelain Veneers Smile Makeover*).
* `title_es`: String (ej. *Diseño de Sonrisa con Carillas de Porcelana*).
* `service_id`: Foreign key a `services` (nullable).
* `dentist_id`: Foreign key a `dentists` (nullable).
* `before_image`: String.
* `after_image`: String.
* `description_en`: Text.
* `description_es`: Text.
* `patient_testimonial_en`: Text (nullable).
* `patient_testimonial_es`: Text (nullable).
* `is_featured`: Boolean (default true).
* `is_active`: Boolean (default true).
* `order`: Integer.
* `timestamps`.

### 4.5 `testimonials` (Reseñas de Pacientes)
* `id`: ID autonumérico.
* `patient_name`: String.
* `patient_avatar`: String (nullable).
* `treatment_en`: String (ej. *Laser Teeth Whitening*).
* `treatment_es`: String (ej. *Blanqueamiento Dental Láser*).
* `rating`: Integer (1 a 5 estrellas).
* `comment_en`: Text.
* `comment_es`: Text.
* `source`: String (ej. *Google Reviews, Verified Patient*).
* `is_verified`: Boolean (default true).
* `is_active`: Boolean (default true).
* `order`: Integer.
* `timestamps`.

### 4.6 `banners` (Banners y Promociones Principales)
* `id`: ID autonumérico.
* `badge_en`: String (ej. *Spring Smile Special*).
* `badge_es`: String (ej. *Especial de Primavera*).
* `title_en`: String.
* `title_es`: String.
* `subtitle_en`: String.
* `subtitle_es`: String.
* `button_text_en`: String (ej. *Book Your Consultation*).
* `button_text_es`: String (ej. *Agenda tu Consulta*).
* `button_url`: String.
* `background_image`: String.
* `is_active`: Boolean (default true).
* `order`: Integer.
* `timestamps`.

### 4.7 `faqs` (Preguntas Frecuentes)
* `id`: ID autonumérico.
* `category_en`: String (ej. *Appointments, Treatments, Insurance & Financing*).
* `category_es`: String (ej. *Citas y Horarios, Tratamientos, Pagos y Seguros*).
* `question_en`: String.
* `question_es`: String.
* `answer_en`: Text.
* `answer_es`: Text.
* `is_active`: Boolean (default true).
* `order`: Integer.
* `timestamps`.

### 4.8 `posts` (Blog de Salud Bucal y SEO Local)
* `id`: ID autonumérico.
* `title_en`: String.
* `title_es`: String.
* `slug`: String único.
* `category_en`: String.
* `category_es`: String.
* `excerpt_en`: Text.
* `excerpt_es`: Text.
* `content_en`: LongText / RichEditor.
* `content_es`: LongText / RichEditor.
* `cover_image`: String.
* `published_at`: DateTime (nullable).
* `is_published`: Boolean (default false).
* `timestamps`.

### 4.9 `clinic_settings` (Configuración General de la Clínica)
* `id`: ID autonumérico.
* `clinic_name`: String (ej. *Aura Dental Clinic*).
* `tagline_en`: String (ej. *Modern, gentle & pain-free dental care*).
* `tagline_es`: String (ej. *Odontología moderna, integral y sin dolor*).
* `phone`: String.
* `whatsapp`: String (para botón flotante y confirmaciones).
* `email`: String.
* `address`: String.
* `google_maps_url`: Text.
* `opening_hours_en`: Text (ej. *Mon - Fri: 8:00 AM - 7:00 PM | Sat: 9:00 AM - 2:00 PM*).
* `opening_hours_es`: Text (ej. *Lun - Vie: 8:00 AM - 7:00 PM | Sáb: 9:00 AM - 2:00 PM*).
* `emergency_phone`: String.
* `logo_url`: String.
* `show_language_switcher`: Boolean (default true).
* `social_links`: JSON (Instagram, Facebook, TikTok).
* `timestamps`.

---

## 5. Estructura del Panel Administrativo (Filament v5)

La navegación del panel `/admin` se organiza por grupos para mantener la ergonomía típica de un CMS dental profesional:

* **Clinical Management:**
  * **Appointments (`AppointmentResource`):** Tabla interactiva con filtros por estado, fecha y doctor; acciones rápidas ("Confirm Appointment", "Open WhatsApp").
  * **Specialists (`DentistResource`):** Directorio de odontólogos y configuración de horarios.
  * **Services (`ServiceResource`):** Catálogo de tratamientos con precios y duraciones.
* **Smile Gallery & Reviews:**
  * **Smile Cases (`SmileCaseResource`):** Galería interactiva Antes / Después.
  * **Testimonials (`TestimonialResource`):** Gestión y moderación de opiniones.
* **Web Content & CMS:**
  * **Banners (`BannerResource`):** Banners del Hero y promociones.
  * **Blog Posts (`PostResource`):** Artículos para educación de pacientes y SEO.
  * **FAQs (`FaqResource`):** Preguntas y respuestas frecuentes.
* **Settings:**
  * **Clinic Settings (`ManageClinicSettings`):** Teléfono, WhatsApp, dirección, horario e interruptor del selector de idioma (`show_language_switcher`).

---

## 6. Principios de Diseño Frontend (Anti-Slop / Alta Conversión)

1. **Diseño Internacional y Profesional:** Tipografía limpia (*Instrument Sans* / *Inter*), paleta en tonos azul marino médico, esmeralda suave, blanco clínico y toques cálidos.
2. **Humanización:** Especialistas reales, instalaciones higiénicas y resultados clínicos auténticos.
3. **Puntos de Conversión Directa:**
   * CTA principal en la cabecera: *"Book Appointment"* / *"Agendar Cita"*.
   * Selector de idioma discreto: `[ EN | ES ]` (conmutable desde panel o `.env`).
   * Botón flotante omnipresente de WhatsApp para urgencias o consultas rápidas.
   * Formulario de reserva de 4 pasos integrado con confirmación instantánea.
