<?php

namespace Bithoven\Tickets\Database\Seeders;

use Bithoven\Tickets\Models\TicketCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Técnico',
                'slug' => 'tecnico',
                'description' => 'Problemas técnicos, bugs, errores del sistema, acceso, rendimiento',
                'icon' => 'ki-wrench',
                'color' => '#3b82f6', // blue-500
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Facturación',
                'slug' => 'facturacion',
                'description' => 'Consultas sobre pagos, facturas, suscripciones, reembolsos',
                'icon' => 'ki-credit-cart',
                'color' => '#10b981', // green-500
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Consultas generales, preguntas, sugerencias, información',
                'icon' => 'ki-information',
                'color' => '#6b7280', // gray-500
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'name' => 'Cuenta',
                'slug' => 'cuenta',
                'description' => 'Gestión de cuenta, perfil, configuración, contraseñas, permisos',
                'icon' => 'ki-user',
                'color' => '#8b5cf6', // violet-500
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Funcionalidad',
                'slug' => 'funcionalidad',
                'description' => 'Solicitud de nuevas funcionalidades, mejoras, feedback',
                'icon' => 'ki-rocket',
                'color' => '#f59e0b', // amber-500
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'id' => 6,
                'name' => 'Seguridad',
                'slug' => 'seguridad',
                'description' => 'Reportes de seguridad, vulnerabilidades, accesos no autorizados',
                'icon' => 'ki-shield-tick',
                'color' => '#ef4444', // red-500
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'id' => 7,
                'name' => 'Documentación',
                'slug' => 'documentacion',
                'description' => 'Dudas sobre documentación, tutoriales, guías de uso',
                'icon' => 'ki-book',
                'color' => '#06b6d4', // cyan-500
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'id' => 8,
                'name' => 'Integración',
                'slug' => 'integracion',
                'description' => 'API, webhooks, integraciones con terceros, desarrollo',
                'icon' => 'ki-code',
                'color' => '#14b8a6', // teal-500
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }
}
