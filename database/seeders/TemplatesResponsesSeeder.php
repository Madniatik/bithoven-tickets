<?php

namespace Bithoven\Tickets\Database\Seeders;

use Bithoven\Tickets\Models\TicketTemplate;
use Bithoven\Tickets\Models\CannedResponse;
use Bithoven\Tickets\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TemplatesResponsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías existentes
        $categories = TicketCategory::all();
        $technicalCategory = $categories->where('slug', 'tecnico')->first();
        $billingCategory = $categories->where('slug', 'facturacion')->first();
        $generalCategory = $categories->where('slug', 'general')->first();
        $accountCategory = $categories->where('slug', 'cuenta')->first();
        $featureCategory = $categories->where('slug', 'funcionalidad')->first();
        $securityCategory = $categories->where('slug', 'seguridad')->first();
        $integrationCategory = $categories->where('slug', 'integracion')->first();

        // ========= TICKET TEMPLATES =========
        
        // 1. Password Reset Request
        TicketTemplate::updateOrCreate(
            ['id' => 1],
            [
            'name' => 'Solicitud de Restablecimiento de Contraseña',
            'subject' => 'No puedo acceder a mi cuenta',
            'description' => "No puedo iniciar sesión en mi cuenta. Necesito restablecer mi contraseña.\n\nEmail asociado: [ingresar email]\nÚltimo acceso exitoso: [fecha aproximada]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'medium',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 2. Bug Report
        TicketTemplate::updateOrCreate(
            ['id' => 2],
            [
            'name' => 'Reporte de Error (Bug)',
            'subject' => 'Error en [Módulo/Funcionalidad]',
            'description' => "He encontrado un problema en el sistema.\n\n**Pasos para reproducir:**\n1. [Paso 1]\n2. [Paso 2]\n3. [Paso 3]\n\n**Resultado esperado:**\n[Describir qué debería suceder]\n\n**Resultado actual:**\n[Describir qué sucede realmente]\n\n**Navegador/Dispositivo:**\n[Chrome, Firefox, Safari, móvil, etc.]\n\n**Mensajes de error:**\n[Si los hay, copiar aquí]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'high',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 3. Feature Request
        TicketTemplate::updateOrCreate(
            ['id' => 3],
            [
            'name' => 'Solicitud de Nueva Funcionalidad',
            'subject' => 'Solicitud: [Nombre de la funcionalidad]',
            'description' => "Me gustaría sugerir una nueva funcionalidad para el sistema.\n\n**Descripción de la funcionalidad:**\n[Explicar qué se necesita]\n\n**Problema que resuelve:**\n[Qué problema o necesidad aborda]\n\n**Beneficios esperados:**\n[Cómo mejoraría el sistema]\n\n**Casos de uso:**\n[Ejemplos de cuándo se usaría]",
            'category_id' => $generalCategory?->id,
            'priority' => 'low',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 4. Billing Issue
        TicketTemplate::updateOrCreate(
            ['id' => 4],
            [
            'name' => 'Problema de Facturación',
            'subject' => 'Consulta sobre mi facturación',
            'description' => "Tengo una consulta/problema relacionado con mi facturación.\n\n**Número de factura (si aplica):**\n[Número]\n\n**Fecha de la transacción:**\n[Fecha]\n\n**Monto:**\n[Cantidad]\n\n**Descripción del problema:**\n[Explicar la situación]",
            'category_id' => $billingCategory?->id,
            'priority' => 'high',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 5. Account Access Problem
        TicketTemplate::updateOrCreate(
            ['id' => 5],
            [
            'name' => 'Problema de Acceso a la Cuenta',
            'subject' => 'No puedo acceder a [módulo/sección]',
            'description' => "Tengo problemas para acceder a una parte del sistema.\n\n**Sección afectada:**\n[Nombre del módulo o sección]\n\n**Mensaje de error:**\n[Si aparece algún mensaje]\n\n**Intentos realizados:**\n[Qué has intentado hacer]\n\n**Rol/Permisos:**\n[Tu rol en el sistema]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'urgent',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 6. General Question
        TicketTemplate::updateOrCreate(
            ['id' => 6],
            [
            'name' => 'Consulta General',
            'subject' => 'Consulta sobre [tema]',
            'description' => "Tengo una pregunta sobre el sistema.\n\n**Pregunta:**\n[Formular la pregunta]\n\n**Contexto adicional:**\n[Información relevante]",
            'category_id' => $generalCategory?->id,
            'priority' => 'low',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 7. Performance Issue
        TicketTemplate::updateOrCreate(
            ['id' => 7],
            [
            'name' => 'Problema de Rendimiento',
            'subject' => 'El sistema está lento',
            'description' => "El sistema está funcionando lento o con retrasos.\n\n**Secciones afectadas:**\n[Qué partes del sistema]\n\n**Cuándo ocurre:**\n[Horario, frecuencia]\n\n**Tipo de conexión:**\n[WiFi, ethernet, datos móviles]\n\n**Velocidad de internet:**\n[Aproximada]\n\n**Otros detalles:**\n[Información adicional]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'medium',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 8. API Integration Issue
        TicketTemplate::updateOrCreate(
            ['id' => 8],
            [
            'name' => 'Problema con Integración API',
            'subject' => 'Error en integración API',
            'description' => "Tengo un problema con la integración de API.\n\n**Endpoint afectado:**\n[URL del endpoint]\n\n**Método HTTP:**\n[GET, POST, PUT, DELETE]\n\n**Código de error:**\n[Código HTTP recibido]\n\n**Mensaje de error:**\n[Mensaje completo]\n\n**Payload enviado (si aplica):**\n```json\n[pegar payload]\n```\n\n**Respuesta recibida:**\n```json\n[pegar respuesta]\n```",
            'category_id' => $integrationCategory?->id,
            'priority' => 'high',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 9. Security Concern
        TicketTemplate::updateOrCreate(
            ['id' => 9],
            [
            'name' => 'Reporte de Seguridad',
            'subject' => '[CONFIDENCIAL] Posible problema de seguridad',
            'description' => "**⚠️ IMPORTANTE: Este es un reporte confidencial de seguridad**\n\n**Tipo de problema:**\n[Vulnerabilidad, acceso no autorizado, exposición de datos, etc.]\n\n**Descripción:**\n[Explicar el problema de forma detallada]\n\n**Severidad estimada:**\n[Baja / Media / Alta / Crítica]\n\n**Evidencia:**\n[Screenshots, logs, etc.]\n\n**Pasos para reproducir:**\n1. [Paso 1]\n2. [Paso 2]\n\n**Impacto potencial:**\n[Qué datos o funcionalidades están afectadas]",
            'category_id' => $securityCategory?->id,
            'priority' => 'urgent',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // 10. Data Export Request
        TicketTemplate::updateOrCreate(
            ['id' => 10],
            [
            'name' => 'Solicitud de Exportación de Datos',
            'subject' => 'Necesito exportar mis datos',
            'description' => "Necesito exportar mis datos del sistema.\n\n**Tipo de datos:**\n[Qué información necesitas exportar]\n\n**Formato preferido:**\n[CSV, Excel, PDF, JSON, etc.]\n\n**Período de tiempo:**\n[Fechas específicas o todo el historial]\n\n**Motivo (opcional):**\n[Razón de la exportación]",
            'category_id' => $accountCategory?->id,
            'priority' => 'medium',
            'is_active' => true,
            'usage_count' => 0,
        ]
        );

        // ========= CANNED RESPONSES =========

        // === Greetings & Initial Responses ===
        
        CannedResponse::updateOrCreate(
            ['id' => 1],
            [
            'shortcut' => '/greeting',
            'title' => 'Saludo Inicial',
            'content' => "Hola {user_name},\n\nGracias por contactarnos. He recibido tu ticket y estoy revisando la información proporcionada.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 2],
            [
            'shortcut' => '/hello-formal',
            'title' => 'Saludo Formal',
            'content' => "Estimado/a {user_name},\n\nReciba un cordial saludo. Hemos recibido su solicitud y estamos trabajando en ella.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Investigation & Progress ===

        CannedResponse::updateOrCreate(
            ['id' => 3],
            [
            'shortcut' => '/investigating',
            'title' => 'Investigando el Problema',
            'content' => "Estoy investigando este problema. Te mantendré informado/a sobre el progreso.\n\nTiempo estimado de respuesta: [especificar]",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 4],
            [
            'shortcut' => '/need-info',
            'title' => 'Necesito Más Información',
            'content' => "Para poder ayudarte mejor, necesito que me proporciones la siguiente información:\n\n- [punto 1]\n- [punto 2]\n- [punto 3]\n\nEn cuanto la reciba, continuaré con la resolución.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 5],
            [
            'shortcut' => '/escalated',
            'title' => 'Escalado a Especialista',
            'content' => "He escalado tu ticket a nuestro equipo especializado. Recibirás una respuesta en las próximas [timeframe] horas.\n\nReferencia: {ticket_number}",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Solutions & Closings ===

        CannedResponse::updateOrCreate(
            ['id' => 6],
            [
            'shortcut' => '/resolved',
            'title' => 'Problema Resuelto',
            'content' => "El problema ha sido resuelto. Por favor, verifica que todo esté funcionando correctamente.\n\nSi el problema persiste o tienes alguna duda, no dudes en responder a este ticket.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 7],
            [
            'shortcut' => '/solution-applied',
            'title' => 'Solución Aplicada - Confirmar',
            'content' => "He aplicado la siguiente solución:\n\n[describir solución]\n\n¿Podrías confirmar que el problema está resuelto?",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Password Reset ===

        CannedResponse::updateOrCreate(
            ['id' => 8],
            [
            'shortcut' => '/reset-password',
            'title' => 'Instrucciones de Restablecimiento de Contraseña',
            'content' => "Para restablecer tu contraseña:\n\n1. Ve a la página de inicio de sesión\n2. Haz clic en \"¿Olvidaste tu contraseña?\"\n3. Ingresa tu email registrado\n4. Recibirás un correo con las instrucciones\n5. El enlace expira en 60 minutos\n\nSi no recibes el correo, revisa tu carpeta de spam o contáctanos nuevamente.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Technical Responses ===

        CannedResponse::updateOrCreate(
            ['id' => 9],
            [
            'shortcut' => '/clear-cache',
            'title' => 'Cache del Navegador',
            'content' => "Intenta limpiar el caché de tu navegador:\n\n**Chrome/Edge:**\nCtrl + Shift + Supr (Windows) o Cmd + Shift + Delete (Mac)\n\n**Firefox:**\nCtrl + Shift + Del (Windows) o Cmd + Shift + Delete (Mac)\n\n**Safari:**\nCmd + Opción + E\n\nLuego, reinicia el navegador e intenta nuevamente.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 10],
            [
            'shortcut' => '/browser-support',
            'title' => 'Navegador Compatible',
            'content' => "Para una mejor experiencia, te recomendamos usar:\n\n✅ Chrome (versión 90+)\n✅ Firefox (versión 88+)\n✅ Safari (versión 14+)\n✅ Edge (versión 90+)\n\nAsegúrate de tener tu navegador actualizado.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Billing ===

        CannedResponse::updateOrCreate(
            ['id' => 11],
            [
            'shortcut' => '/billing-info',
            'title' => 'Información de Facturación',
            'content' => "Puedes consultar tus facturas en:\n\nPanel de Control > Mi Cuenta > Facturación\n\nAhí encontrarás:\n- Historial de pagos\n- Facturas descargables\n- Métodos de pago\n- Próximas renovaciones",
            'category_id' => $billingCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Closings & Thanks ===

        CannedResponse::updateOrCreate(
            ['id' => 12],
            [
            'shortcut' => '/thanks',
            'title' => 'Agradecimiento y Cierre',
            'content' => "Gracias por tu paciencia. Si necesitas ayuda adicional, no dudes en contactarnos.\n\n¡Que tengas un excelente día!",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 13],
            [
            'shortcut' => '/close-inactive',
            'title' => 'Cierre por Inactividad',
            'content' => "Como no hemos recibido respuesta en los últimos días, procederemos a cerrar este ticket.\n\nSi aún necesitas ayuda, puedes reabrir este ticket o crear uno nuevo.\n\nGracias por contactarnos.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        // === Internal Notes (is_public = false) ===

        CannedResponse::updateOrCreate(
            ['id' => 14],
            [
            'shortcut' => '/internal-approval',
            'title' => '[INTERNO] Requiere Aprobación',
            'content' => "Este ticket requiere aprobación del supervisor antes de proceder.\nMotivo: [especificar]\nEscalado a: [nombre]",
            'category_id' => null,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 15],
            [
            'shortcut' => '/internal-recurring',
            'title' => '[INTERNO] Problema Recurrente',
            'content' => "NOTA INTERNA: Este es un problema recurrente. Ver tickets relacionados:\n- {ticket_number_1}\n- {ticket_number_2}\n\nConsiderar solución permanente.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 16],
            [
            'shortcut' => '/internal-dev',
            'title' => '[INTERNO] Verificar con Dev Team',
            'content' => "NOTA INTERNA: Requiere revisión del equipo de desarrollo.\nComponente afectado: [especificar]\nPrioridad sugerida: [low/medium/high/urgent]",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]
        );

        // === Quick Fixes ===

        CannedResponse::updateOrCreate(
            ['id' => 17],
            [
            'shortcut' => '/logout-login',
            'title' => 'Reiniciar Sesión',
            'content' => "Por favor, intenta lo siguiente:\n\n1. Cierra sesión completamente\n2. Cierra el navegador\n3. Abre nuevamente el navegador\n4. Inicia sesión otra vez\n\nEsto debería resolver problemas de caché de sesión.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 18],
            [
            'shortcut' => '/incognito',
            'title' => 'Modo Incógnito',
            'content' => "Prueba abrir el sistema en modo incógnito/privado:\n\n**Chrome/Edge:** Ctrl + Shift + N\n**Firefox:** Ctrl + Shift + P\n**Safari:** Cmd + Shift + N\n\nSi funciona ahí, el problema es con extensiones del navegador o caché.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 19],
            [
            'shortcut' => '/need-screenshot',
            'title' => 'Screenshot Solicitado',
            'content' => "Para ayudarte mejor, ¿podrías enviarme un screenshot del problema?\n\n**Captura de pantalla:**\n- Windows: Win + Shift + S\n- Mac: Cmd + Shift + 4\n\nAsegúrate de que se vea claramente el mensaje de error o problema.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 20],
            [
            'shortcut' => '/refund-process',
            'title' => 'Solicitud de Reembolso',
            'content' => "He iniciado el proceso de reembolso.\n\n**Tiempo estimado:** 5-10 días hábiles\n**Método de devolución:** Mismo método de pago original\n**Referencia:** {ticket_number}\n\nRecibirás un correo de confirmación cuando se procese.",
            'category_id' => $billingCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 21],
            [
            'shortcut' => '/change-email',
            'title' => 'Cambio de Email',
            'content' => "Para cambiar el email de tu cuenta:\n\n1. Ve a Mi Cuenta > Configuración\n2. Sección 'Email'\n3. Introduce el nuevo email\n4. Recibirás un código de verificación en el email nuevo\n5. Introduce el código para confirmar\n\nSi tienes problemas, puedo ayudarte desde aquí.",
            'category_id' => $accountCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 22],
            [
            'shortcut' => '/api-docs',
            'title' => 'API Documentation',
            'content' => "Puedes encontrar la documentación completa de la API en:\n\n🔗 [URL]/api/documentation\n\n**Incluye:**\n- Endpoints disponibles\n- Parámetros requeridos\n- Ejemplos de requests/responses\n- Rate limits\n- Autenticación\n\n¿Necesitas ayuda con algo específico?",
            'category_id' => $integrationCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 23],
            [
            'shortcut' => '/roadmap',
            'title' => 'Feature en Roadmap',
            'content' => "¡Gracias por la sugerencia!\n\nEsta funcionalidad está en nuestro roadmap para implementación futura. Te mantendremos informado sobre su desarrollo.\n\nPuedes ver nuestro roadmap público en: [URL]\n\n¿Hay algo más en lo que pueda ayudarte mientras tanto?",
            'category_id' => $featureCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]
        );

        CannedResponse::updateOrCreate(
            ['id' => 24],
            [
            'shortcut' => '/internal-bug',
            'title' => '[INTERNO] Bug Confirmado',
            'content' => "NOTA INTERNA: Bug confirmado y reproducido.\nSeveridad: [low/medium/high/critical]\nAsignado a: Dev Team\nEstimación de fix: [timeframe]\nRelease planeado: [version]",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]
        );
    }
}
