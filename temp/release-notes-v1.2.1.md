## 🐛 v1.2.1 - Livewire Fix

### 🔧 Bug Fixes
- **Fixed Duplicate Form Submission**: Resolved issue where items were being created twice
- **Livewire Compatibility**: Fixed event listener duplication caused by Livewire navigation
- **Improved Form Handling**: Enhanced submit button state management during processing

### 🛠️ Technical Changes
- Implemented **event delegation** pattern instead of direct element event listeners
- Added `window.dummyItemsInitialized` guard to prevent multiple script initialization
- Submit button now properly disables during form processing
- Modal marked with `wire:ignore.self` for better Livewire integration

### 📖 Background
This release addresses a common issue when using vanilla JavaScript event listeners in Livewire applications. Livewire's navigation system can cause event listeners to be registered multiple times, leading to duplicate form submissions.

**Solution implemented:**
```javascript
// Event delegation at document level
document.addEventListener('submit', function(e) {
    if (e.target.id === 'createItemForm') {
        // Handle submission
    }
});
```

This pattern ensures events are only registered once, regardless of Livewire's DOM updates.

### 📥 Update from v1.2.0
This is a **patch release** - safe to update immediately:
```bash
composer update bithoven/dummy
php artisan view:clear
```

No database migrations required.

---

**Full Changelog**: https://github.com/Madniatik/bithoven-extension-dummy/compare/v1.2.0...v1.2.1
