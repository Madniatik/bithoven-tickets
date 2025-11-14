## 🎯 v1.2.0 - Priority System

### ✨ New Features
- 🎯 **Priority System**: Categorize items by priority (Low, Normal, High, Critical)
- 🎨 **Color-Coded Badges**: Visual priority indicators with automatic color coding
  - Low: Blue (info)
  - Normal: Primary (default)
  - High: Orange (warning)
  - Critical: Red (danger)
- 📊 **Enhanced Table**: Priority column added to items table for better organization

### 🔧 Improvements
- Better data organization with priority-based sorting capabilities
- Improved visual hierarchy in items listing
- Enhanced form validation for priority field
- Database optimization with indexed priority column

### 📚 Technical Details
- **Migration**: `add_priority_to_dummy_items`
  - Adds ENUM column with values: low, normal, high, critical
  - Default value: normal
  - Indexed for query performance
  - Fully reversible with down() method
- **Model**: Added `priority` to fillable attributes
- **Controller**: Enhanced validation in store() and update() methods
- **Views**: Updated items table and create modal with priority selector

### 🧪 Perfect for Testing
This release is ideal for testing the Bithoven Extension Update System:
- **Automatic Backup**: System will create backup before update
- **Database Migration**: Tests migration execution during update
- **Rollback Safety**: Can rollback to v1.1.0 if issues occur
- **Data Preservation**: All existing items get 'normal' priority by default

### 📥 Update from v1.1.0
```bash
# Via Extension Manager UI (Recommended)
1. Navigate to Extensions page
2. Click "Update" on Dummy extension
3. Review changelog in modal
4. Confirm update
5. Automatic backup + migration will execute

# Via Artisan (Manual)
composer update bithoven/dummy
php artisan migrate
```

### 🔄 Rollback Instructions
If needed, rollback is automatic on failure, or manual:
```bash
php artisan bithoven:extension:rollback dummy [backup-id]
```

### 📖 Documentation
- [CHANGELOG.md](https://github.com/Madniatik/bithoven-extension-dummy/blob/main/CHANGELOG.md)
- [README.md](https://github.com/Madniatik/bithoven-extension-dummy/blob/main/README.md)

---

**Full Changelog**: https://github.com/Madniatik/bithoven-extension-dummy/compare/v1.1.0...v1.2.0
