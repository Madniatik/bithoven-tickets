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

        // ========= TICKET TEMPLATES =========
        
        // 1. Password Reset Request
        TicketTemplate::create([
            'name' => 'Solicitud de Restablecimiento de Contraseña',
            'subject' => 'No puedo acceder a mi cuenta',
            'description' => "No puedo iniciar sesión en mi cuenta. Necesito restablecer mi contraseña.\n\nEmail asociado: [ingresar email]\nÚltimo acceso exitoso: [fecha aproximada]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'medium',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 2. Bug Report
        TicketTemplate::create([
            'name' => 'Reporte de Error (Bug)',
            'subject' => 'Error en [Módulo/Funcionalidad]',
            'description' => "He encontrado un problema en el sistema.\n\n**Pasos para reproducir:**\n1. [Paso 1]\n2. [Paso 2]\n3. [Paso 3]\n\n**Resultado esperado:**\n[Describir qué debería suceder]\n\n**Resultado actual:**\n[Describir qué sucede realmente]\n\n**Navegador/Dispositivo:**\n[Chrome, Firefox, Safari, móvil, etc.]\n\n**Mensajes de error:**\n[Si los hay, copiar aquí]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'high',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 3. Feature Request
        TicketTemplate::create([
            'name' => 'Solicitud de Nueva Funcionalidad',
            'subject' => 'Solicitud: [Nombre de la funcionalidad]',
            'description' => "Me gustaría sugerir una nueva funcionalidad para el sistema.\n\n**Descripción de la funcionalidad:**\n[Explicar qué se necesita]\n\n**Problema que resuelve:**\n[Qué problema o necesidad aborda]\n\n**Beneficios esperados:**\n[Cómo mejoraría el sistema]\n\n**Casos de uso:**\n[Ejemplos de cuándo se usaría]",
            'category_id' => $generalCategory?->id,
            'priority' => 'low',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 4. Billing Issue
        TicketTemplate::create([
            'name' => 'Problema de Facturación',
            'subject' => 'Consulta sobre mi facturación',
            'description' => "Tengo una consulta/problema relacionado con mi facturación.\n\n**Número de factura (si aplica):**\n[Número]\n\n**Fecha de la transacción:**\n[Fecha]\n\n**Monto:**\n[Cantidad]\n\n**Descripción del problema:**\n[Explicar la situación]",
            'category_id' => $billingCategory?->id,
            'priority' => 'high',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 5. Account Access Problem
        TicketTemplate::create([
            'name' => 'Problema de Acceso a la Cuenta',
            'subject' => 'No puedo acceder a [módulo/sección]',
            'description' => "Tengo problemas para acceder a una parte del sistema.\n\n**Sección afectada:**\n[Nombre del módulo o sección]\n\n**Mensaje de error:**\n[Si aparece algún mensaje]\n\n**Intentos realizados:**\n[Qué has intentado hacer]\n\n**Rol/Permisos:**\n[Tu rol en el sistema]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'urgent',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 6. General Question
        TicketTemplate::create([
            'name' => 'Consulta General',
            'subject' => 'Consulta sobre [tema]',
            'description' => "Tengo una pregunta sobre el sistema.\n\n**Pregunta:**\n[Formular la pregunta]\n\n**Contexto adicional:**\n[Información relevante]",
            'category_id' => $generalCategory?->id,
            'priority' => 'low',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // 7. Performance Issue
        TicketTemplate::create([
            'name' => 'Problema de Rendimiento',
            'subject' => 'El sistema está lento',
            'description' => "El sistema está funcionando lento o con retrasos.\n\n**Secciones afectadas:**\n[Qué partes del sistema]\n\n**Cuándo ocurre:**\n[Horario, frecuencia]\n\n**Tipo de conexión:**\n[WiFi, ethernet, datos móviles]\n\n**Velocidad de internet:**\n[Aproximada]\n\n**Otros detalles:**\n[Información adicional]",
            'category_id' => $technicalCategory?->id,
            'priority' => 'medium',
            'is_active' => true,
            'usage_count' => 0,
        ]);

        // ========= CANNED RESPONSES =========

        // === Greetings & Initial Responses ===
        
        CannedResponse::create([
            'title' => 'Saludo Inicial',
            'shortcut' => '/greeting',
            'content' => "Hola {user_name},\n\nGracias por contactarnos. He recibido tu ticket y estoy revisando la información proporcionada.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Saludo Formal',
            'shortcut' => '/hello-formal',
            'content' => "Estimado/a {user_name},\n\nReciba un cordial saludo. Hemos recibido su solicitud y estamos trabajando en ella.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Investigation & Progress ===

        CannedResponse::create([
            'title' => 'Investigando el Problema',
            'shortcut' => '/investigating',
            'content' => "Estoy investigando este problema. Te mantendré informado/a sobre el progreso.\n\nTiempo estimado de respuesta: [especificar]",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Necesito Más Información',
            'shortcut' => '/need-info',
            'content' => "Para poder ayudarte mejor, necesito que me proporciones la siguiente información:\n\n- [punto 1]\n- [punto 2]\n- [punto 3]\n\nEn cuanto la reciba, continuaré con la resolución.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Escalado a Especialista',
            'shortcut' => '/escalated',
            'content' => "He escalado tu ticket a nuestro equipo especializado. Recibirás una respuesta en las próximas [timeframe] horas.\n\nReferencia: {ticket_number}",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Solutions & Closings ===

        CannedResponse::create([
            'title' => 'Problema Resuelto',
            'shortcut' => '/resolved',
            'content' => "El problema ha sido resuelto. Por favor, verifica que todo esté funcionando correctamente.\n\nSi el problema persiste o tienes alguna duda, no dudes en responder a este ticket.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Solución Aplicada - Confirmar',
            'shortcut' => '/solution-applied',
            'content' => "He aplicado la siguiente solución:\n\n[describir solución]\n\n¿Podrías confirmar que el problema está resuelto?",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Password Reset ===

        CannedResponse::create([
            'title' => 'Instrucciones de Restablecimiento de Contraseña',
            'shortcut' => '/reset-password',
            'content' => "Para restablecer tu contraseña:\n\n1. Ve a la página de inicio de sesión\n2. Haz clic en \"¿Olvidaste tu contraseña?\"\n3. Ingresa tu email registrado\n4. Recibirás un correo con las instrucciones\n5. El enlace expira en 60 minutos\n\nSi no recibes el correo, revisa tu carpeta de spam o contáctanos nuevamente.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Technical Responses ===

        CannedResponse::create([
            'title' => 'Cache del Navegador',
            'shortcut' => '/clear-cache',
            'content' => "Intenta limpiar el caché de tu navegador:\n\n**Chrome/Edge:**\nCtrl + Shift + Supr (Windows) o Cmd + Shift + Delete (Mac)\n\n**Firefox:**\nCtrl + Shift + Del (Windows) o Cmd + Shift + Delete (Mac)\n\n**Safari:**\nCmd + Opción + E\n\nLuego, reinicia el navegador e intenta nuevamente.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Navegador Compatible',
            'shortcut' => '/browser-support',
            'content' => "Para una mejor experiencia, te recomendamos usar:\n\n✅ Chrome (versión 90+)\n✅ Firefox (versión 88+)\n✅ Safari (versión 14+)\n✅ Edge (versión 90+)\n\nAsegúrate de tener tu navegador actualizado.",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Billing ===

        CannedResponse::create([
            'title' => 'Información de Facturación',
            'shortcut' => '/billing-info',
            'content' => "Puedes consultar tus facturas en:\n\nPanel de Control > Mi Cuenta > Facturación\n\nAhí encontrarás:\n- Historial de pagos\n- Facturas descargables\n- Métodos de pago\n- Próximas renovaciones",
            'category_id' => $billingCategory?->id,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Closings & Thanks ===

        CannedResponse::create([
            'title' => 'Agradecimiento y Cierre',
            'shortcut' => '/thanks',
            'content' => "Gracias por tu paciencia. Si necesitas ayuda adicional, no dudes en contactarnos.\n\n¡Que tengas un excelente día!",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => 'Cierre por Inactividad',
            'shortcut' => '/close-inactive',
            'content' => "Como no hemos recibido respuesta en los últimos días, procederemos a cerrar este ticket.\n\nSi aún necesitas ayuda, puedes reabrir este ticket o crear uno nuevo.\n\nGracias por contactarnos.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => true,
            'usage_count' => 0,
        ]);

        // === Internal Notes (is_public = false) ===

        CannedResponse::create([
            'title' => '[INTERNO] Requiere Aprobación',
            'shortcut' => '/internal-approval',
            'content' => "Este ticket requiere aprobación del supervisor antes de proceder.\nMotivo: [especificar]\nEscalado a: [nombre]",
            'category_id' => null,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => '[INTERNO] Problema Recurrente',
            'shortcut' => '/internal-recurring',
            'content' => "NOTA INTERNA: Este es un problema recurrente. Ver tickets relacionados:\n- {ticket_number_1}\n- {ticket_number_2}\n\nConsiderar solución permanente.",
            'category_id' => null,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]);

        CannedResponse::create([
            'title' => '[INTERNO] Verificar con Dev Team',
            'shortcut' => '/internal-dev',
            'content' => "NOTA INTERNA: Requiere revisión del equipo de desarrollo.\nComponente afectado: [especificar]\nPrioridad sugerida: [low/medium/high/urgent]",
            'category_id' => $technicalCategory?->id,
            'is_active' => true,
            'is_public' => false,
            'usage_count' => 0,
        ]);
    }
}
