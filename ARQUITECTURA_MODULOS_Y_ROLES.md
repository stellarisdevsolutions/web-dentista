# 🏛️ Arquitectura del Sistema: Módulos Operativos y Matriz de Roles

Este documento define la estructura funcional del sistema para **Aura Dental Studio**, delimitando las responsabilidades de cada rol y detallando los módulos operativos clínicos, manteniendo el **CMS (Gestor de Contenido Web)** de forma generalizada para su próxima reestructuración.

---

## 🎯 1. Filosofía de Separación del Sistema

El software está dividido en dos grandes pilares independientes:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        SISTEMA DENTAL INTEGRAL                         │
├───────────────────────────────────┬────────────────────────────────────┤
│   A. GESTIÓN OPERATIVA CLÍNICA    │    B. CMS / GESTOR DE CONTENIDO    │
│   • Citas y Reservas              │    • Landing Page y Secciones      │
│   • Doctores y Horarios           │    • Artículos y Blog Clínico      │
│   • Catálogo de Tratamientos      │    • Banners y Multimedia          │
│   • Usuarios y Permisos           │    • FAQs, Reseñas y Casos         │
│   • Mensajes de Contacto          │    *(En rediseño arquitectónico)*  │
└───────────────────────────────────┴────────────────────────────────────┘
```

Esta separación garantiza que la operación médica diaria no interfiera con el marketing digital y permite evolucionar el CMS sin riesgo de afectar la agenda médica.

---

## 👥 2. Matriz de Roles y Niveles de Acceso

El sistema cuenta con un control de acceso basado en el enum nativo `UserRole`:

### 👑 1. Administrador (`admin`)
- **Perfil:** Dueño de la clínica, director médico o gerente administrativo.
- **Acceso:** Total (100% del sistema).
- **Atribuciones clave:**
  - Control de usuarios y asignación de roles.
  - Creación y edición de doctores, horarios y comisiones.
  - Fijación de precios y catálogo de tratamientos.
  - Configuración general de la clínica (teléfonos, WhatsApp, dirección).
  - Gestión integral del CMS y contenido público.

---

### 📋 2. Recepción / Secretaría (`receptionist`)
- **Perfil:** Asistentes de recepción, secretarias o coordinadores de atención.
- **Acceso:** Operativo del día a día (Citas y Atención al Paciente).
- **Atribuciones clave:**
  - Agendamiento manual de citas para pacientes presenciales o telefónicos.
  - Confirmación, reagendamiento y cancelación de citas.
  - Envío directo de recordatorios y confirmaciones vía WhatsApp.
  - Consulta de disponibilidad y horarios de los doctores.
  - Revisión y atención de mensajes recibidos por el formulario de contacto web.
  - **Restricciones:** No puede eliminar registros permanentes, ni modificar precios, ni alterar configuraciones de la clínica, ni editar usuarios, ni tocar el CMS.

---

### 🩺 3. Odontólogo / Especialista (`dentist`)
- **Perfil:** Médicos odontólogos, cirujanos y especialistas del staff.
- **Acceso:** Su propia agenda clínica.
- **Atribuciones clave:**
  - Vinculado mediante su `dentist_id` a su perfil profesional.
  - **Filtro automático de seguridad:** Solo puede visualizar y gestionar las citas asignadas a su persona.
  - Actualización del estado de la cita en tiempo real (`En Consulta`, `Completada`).
  - Registro de notas clínicas internas del tratamiento.
  - **Restricciones:** No ve las citas ni pacientes de otros doctores. No tiene acceso a la gestión de usuarios, ni a configuración, ni al CMS.

---

### 📊 Tabla Comparativa de Permisos

| Módulo / Capacidad | 👑 Administrador | 📋 Recepción | 🩺 Odontólogo |
| :--- | :---: | :---: | :---: |
| **Citas: Ver Todas** | ✅ Sí | ✅ Sí | ❌ Solo las suyas |
| **Citas: Crear y Agendar** | ✅ Sí | ✅ Sí | ❌ No |
| **Citas: Cambiar Estado / Notas** | ✅ Sí | ✅ Sí | ✅ Solo en sus citas |
| **Citas: Eliminar Físicamente** | ✅ Sí | ❌ No | ❌ No |
| **Bandeja de Contactos Web** | ✅ Sí | ✅ Sí | ❌ No |
| **Especialistas: Crear / Editar** | ✅ Sí | 👁️ Solo Lectura | 👁️ Solo su horario |
| **Tratamientos: Modificar Precios** | ✅ Sí | 👁️ Solo Lectura | 👁️ Solo Lectura |
| **Gestión de Usuarios y Roles** | ✅ Sí | ❌ No | ❌ No |
| **Configuración de la Clínica** | ✅ Sí | ❌ No | ❌ No |
| **Módulo CMS (Gestor Web)** | ✅ Sí | ❌ No | ❌ No |

---

## 🏥 3. Detalle de los Módulos Operativos Clínicos

### 📅 A. Módulo de Citas y Agenda (Appointments)
- **Propósito:** Núcleo operativo de la clínica dental.
- **Funcionalidades:**
  - Código único de cita (`APT-XXXXXX`) para trazabilidad.
  - Visualización por fecha, hora, estado, paciente y doctor.
  - Acciones rápidas en un clic (botón de WhatsApp con mensaje estructurado).
  - Aislamiento de consultas mediante `AppointmentPolicy` y `AppointmentResource::getEloquentQuery()`.

### 🩺 B. Módulo de Especialistas (Dentists)
- **Propósito:** Gestión del equipo médico y sus jornadas.
- **Funcionalidades:**
  - Ficha profesional (especialidad, número de colegiatura, biografía, foto).
  - Configuración del horario semanal por día (turno matutino, pausa de almuerzo y turno vespertino).
  - Vinculación con los tratamientos específicos que cada doctor realiza.

### 🦷 C. Módulo de Tratamientos y Servicios (Services)
- **Propósito:** Catálogo oficial de prestaciones odontológicas.
- **Funcionalidades:**
  - Nombre del tratamiento, categoría clínica, descripción y protocolos.
  - Duración estimada en minutos (clave para el motor de reserva de turnos).
  - Precio base monetario.
  - Estado de activación en el catálogo.

### 📬 D. Módulo de Mensajes de Contacto (Contact Messages)
- **Propósito:** Centralización de solicitudes que ingresan desde la web.
- **Funcionalidades:**
  - Nombre, teléfono, correo y consulta del paciente.
  - Estado de atención (`Leído` / `Pendiente`).
  - Atención prioritaria por parte de Recepción.

### 🔐 E. Módulo de Administración de Usuarios (Users & Roles)
- **Propósito:** Seguridad y gobernanza del sistema.
- **Funcionalidades:**
  - Alta de usuarios con asignación de rol (`admin`, `receptionist`, `dentist`).
  - Vinculación obligatoria de `dentist_id` al seleccionar el rol Odontólogo.
  - Hashing seguro de contraseñas.
  - Protección activa contra auto-eliminación de la cuenta de administrador.

---

## 🌐 4. Módulo CMS (Visión General y Próxima Reestructuración)

> [!NOTE]  
> Este módulo está conceptualizado de forma generalizada para permitir la reconstrucción y rediseño completo del motor de contenidos sin ataduras a la estructura previa.

### Propósito del CMS
Permitir que el Administrador de la clínica pueda personalizar y gestionar la presencia digital del sitio web público sin necesidad de escribir código.

### Áreas contempladas dentro del CMS:
1. **Páginas y Secciones Dinámicas:**
   - Estructuración de la Landing Page principal (Héroes, bloques informativos, llamadas a la acción).
2. **Biblioteca Multimedia y Testimonios:**
   - Casos clínicos de antes y después, testimonios de pacientes y material audiovisual.
3. **Blog y Divulgación:**
   - Artículos educativos sobre salud bucal, prevención y novedades odontológicas.
4. **Preguntas Frecuentes e Información General:**
   - Respuestas a dudas habituales sobre seguros, financiamiento y preparación para tratamientos.

---

## 🔒 5. Capa de Seguridad y Políticas Activas

Cada módulo está protegido en la capa de datos y de interfaz mediante Laravel Policies:

- [`app/Policies/AppointmentPolicy.php`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/AppointmentPolicy.php)
- [`app/Policies/ServicePolicy.php`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/ServicePolicy.php)
- [`app/Policies/DentistPolicy.php`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/DentistPolicy.php)
- [`app/Policies/ContactMessagePolicy.php`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/ContactMessagePolicy.php)
- [`app/Policies/UserPolicy.php`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/UserPolicy.php)
- Políticas del CMS: [`BannerPolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/BannerPolicy.php), [`PostPolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/PostPolicy.php), [`FaqPolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/FaqPolicy.php), [`SmileCasePolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/SmileCasePolicy.php), [`TestimonialPolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/TestimonialPolicy.php), [`ClinicSettingPolicy`](file:///c:/Users/neida.DESKTOP-801C70S/Desktop/proyectos%20laravel/web%20dentista/app/Policies/ClinicSettingPolicy.php).

Cualquier usuario que intente acceder por URL a un recurso no autorizado recibirá de inmediato un error **403 Forbidden**, garantizando la integridad de los datos.
