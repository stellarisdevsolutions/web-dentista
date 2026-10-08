# 🌐 Guía de Arquitectura de Idioma y Roadmap Multilingüe

Este documento detalla la estrategia de internacionalización del proyecto **Aura Dental Studio**, cómo opera actualmente con **un solo idioma centralizado** y cuál es el procedimiento exacto paso a paso para habilitar **soporte multilingüe profesional** en caso de que un cliente futuro lo requiera.

---

## 📌 1. Situación Actual: Arquitectura de Idioma Único (Single-Language)

### 🎯 Por qué se unificó la base de datos
Anteriormente, el sistema contaba con columnas duplicadas con sufijos de idioma (`name_es`, `name_en`, `title_es`, `title_en`, etc.). 
Para un sitio web y panel clínico enfocado en un solo idioma nativo (ej. Español), este enfoque presentaba varios inconvenientes:
- **Redundancia y desperdicio:** La mitad de las columnas en la base de datos permanecían vacías (`NULL`) o duplicaban texto idéntico.
- **Inflexibilidad:** Si un cliente pedía un tercer idioma (ej. Portugués o Francés), obligaba a crear manualmente migraciones con `_pt`, `_fr`, alterando docenas de tablas y formularios.
- **Sobrecarga en formularios:** Los paneles de administración requerían campos o pestañas dobles innecesarias para administradores que solo redactan en su idioma nativo.

### 📐 Cómo funciona la arquitectura actual
1. **Configuración centralizada en `.env`:**
   El idioma de la aplicación se rige por:
   ```env
   APP_LOCALE=es
   APP_FALLBACK_LOCALE=es
   ```
2. **Columnas limpias y canónicas:**
   Todas las tablas tienen campos estándar:
   - `services`: `name`, `category`, `short_description`, `description`
   - `dentists`: `title`, `specialty`, `bio`
   - `posts`: `title`, `category`, `excerpt`, `content`
   - `banners`: `badge`, `title`, `subtitle`, `button_text`
   - `faqs`: `category`, `question`, `answer`
   - `smile_cases`: `title`, `description`, `patient_testimonial`
   - `testimonials`: `treatment`, `comment`
   - `clinic_settings`: `tagline`, `opening_hours`
3. **Formularios limpios y directos en Filament:**
   Cada recurso en Filament presenta los inputs de forma directa, ágil y sin pestañas redundantes.
4. **Vistas Blade y CMS 100% integrados:**
   Las vistas públicas leen directamente `$service->name`, `$post->title`, etc., asegurando máxima velocidad de consulta y cero sobrecarga de memoria.

---

## 🚀 2. Gestión Operativa: CMS vs. Citas y Reservas

| Módulo | Tipo de Gestión | Consideración de Idioma |
| :--- | :--- | :--- |
| **CMS (Banners, Artículos, FAQs, Galería)** | Contenido editable por el administrador | El contenido se redacta en el idioma configurado para la clínica. |
| **Servicios y Especialistas** | Catálogo clínico | Nombres de tratamientos y especialidades odontológicas en el idioma base. |
| **Citas y Reservas** | Lógica transaccional / Algoritmo | Los datos del paciente (nombre, teléfono, notas) y la asignación horaria son universales. La interfaz de agendamiento se muestra en el idioma de la app. |
| **Notificaciones WhatsApp** | Mensajería automática | Plantillas de mensajes configuradas en el idioma de la clínica. |

---

## 🔮 3. Roadmap: Cómo habilitar Multilingüe en el Futuro

Si en el futuro un cliente internacional adquiere el sistema y requiere soporte multilingüe simultáneo (por ejemplo: Español e Inglés con selector en la web y panel), **NO es necesario rehacer las tablas ni duplicar columnas**.

El estándar de oro en el ecosistema Laravel + Filament es utilizar **almacenamiento JSON translatable** con los paquetes oficiales:
- `spatie/laravel-translatable`
- `spatie/filament-translatable-content` (o plugin oficial de Filament Translatable)

### 🛠️ Pasos de Implementación Futura

#### Paso 1: Instalar paquetes
```bash
composer require spatie/laravel-translatable
composer require filament/spatie-laravel-translatable-plugin
```

#### Paso 2: Convertir columnas a tipo `JSON`
En lugar de crear nuevas columnas `_es` o `_en`, se crea una migración que cambia el tipo de las columnas existentes a `json`:
```php
Schema::table('services', function (Blueprint $table) {
    $table->json('name')->change();
    $table->json('short_description')->nullable()->change();
    $table->json('description')->nullable()->change();
});
```

#### Paso 3: Agregar el Trait en los Modelos Eloquent
En el modelo (ej. `Service.php`):
```php
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    public array $translatable = [
        'name',
        'category',
        'short_description',
        'description',
    ];
}
```

> **Ventaja clave:** `$service->name` sigue funcionando exactamente igual en las vistas Blade. Spatie automáticamente devuelve la traducción para el locale actual (`app()->getLocale()`) y almacena en la base de datos un JSON limpio:
> ```json
> {"es": "Ortodoncia Invisible", "en": "Clear Aligners"}
> ```

#### Paso 4: Habilitar selector de idiomas en Filament
En los recursos de Filament correspondientes:
```php
use Filament\Resources\Concerns\Translatable;

class ServiceResource extends Resource
{
    use Translatable;

    // ...
}
```
Esto añade automáticamente el selector de idioma en la cabecera de edición del recurso en Filament para traducir cada campo con un solo clic.

#### Paso 5: Activar el selector público en el Header
La tabla `clinic_settings` ya cuenta con el interruptor:
- `show_language_switcher` (booleano)
Al activarlo, se descomenta o habilita el middleware de locale para que el visitante pueda alternar entre `es` y `en` en el sitio web público.

---

## 📊 4. Conclusión

- **Actualmente:** El sistema es **ultra limpio**, **rápido**, **mantenible** y tiene el 100% de sus tests pasando (36/36) con validación de código según Laravel Pint.
- **A futuro:** La arquitectura está preparada para escalar a 2, 3 o más idiomas sin deuda técnica y siguiendo las mejores prácticas recomendadas de Laravel.
