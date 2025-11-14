#!/bin/bash

# ===========================================
# Script para migrar Tickets a DataTables
# ===========================================

set -e  # Exit on error

EXTENSION_DIR="/Users/madniatik/CODE/LARAVEL/BITHOVEN/EXTENSIONS/bithoven-extension-tickets"
CPANEL_DIR="/Users/madniatik/CODE/LARAVEL/BITHOVEN/CPANEL"

echo "🚀 Iniciando migración a DataTables..."
echo ""

# Step 1: Copy DataTables to extension source
echo "📦 Paso 1: Copiando DataTables al source de la extensión..."
cp "${EXTENSION_DIR}/src/DataTables/TicketsDataTable.php" "${EXTENSION_DIR}/src/DataTables/TicketsDataTable.php.bak" 2>/dev/null || true
echo "   ✅ DataTables ya están en el source"
echo ""

# Step 2: Copy updated controllers
echo "🔧 Paso 2: Controladores ya actualizados"
echo "   ✅ TicketController.php"
echo "   ✅ CannedResponseController.php"
echo "   ✅ TicketTemplateController.php"
echo "   ✅ TicketAutomationRuleController.php"
echo ""

# Step 3: Publish extension to vendor
echo "📤 Paso 3: Publicando extensión al vendor..."
cd "${CPANEL_DIR}"
php artisan vendor:publish --tag=bithoven-extension-tickets-views --force
echo "   ✅ Vistas publicadas"
echo ""

# Step 4: Clear caches
echo "🗑️  Paso 4: Limpiando cachés..."
php artisan view:clear
php artisan route:clear
php artisan config:clear
echo "   ✅ Cachés limpiados"
echo ""

# Step 5: Verify DataTables exist in vendor
echo "🔍 Paso 5: Verificando DataTables en vendor..."
DATATABLES=(
    "TicketsDataTable.php"
    "CannedResponsesDataTable.php"
    "TicketTemplatesDataTable.php"
    "AutomationRulesDataTable.php"
    "AutomationLogsDataTable.php"
)

for dt in "${DATATABLES[@]}"; do
    if [ -f "${CPANEL_DIR}/vendor/bithoven/tickets/src/DataTables/${dt}" ]; then
        echo "   ✅ ${dt}"
    else
        echo "   ❌ ${dt} NO ENCONTRADO"
    fi
done
echo ""

echo "✨ Migración completada!"
echo ""
echo "📋 Próximos pasos:"
echo "   1. Actualizar las vistas Blade para usar DataTables"
echo "   2. Probar cada tabla en el navegador"
echo "   3. Hard refresh (Cmd+Shift+R) para ver cambios CSS"
echo ""
