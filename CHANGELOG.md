# Changelog

All notable changes to the Auto Delete Comments plugin will be documented in this file.

## [2.0.1] - 2024-11-13

### Fixed
- Fixed `$this->cron_hook` variable reference in admin template that could cause undefined variable errors
- Fixed `$this->option_name` variable references in admin template for better compatibility
- Fixed potential issue where cron schedules might not be registered in time
- Added proper escaping for CSS class names in status badge display
- Improved error handling in `delete_comments_batch()` method
- Added logging for failed comment deletions
- Added validation to ensure at least one comment type is selected when enabling auto-delete
- Fixed cron interval registration to use a dedicated method called early in initialization

### Added
- Comprehensive validation script (`validate-plugin.sh`) for code quality checks
- Better error messages and logging throughout the plugin
- Empty status array check to prevent unnecessary processing
- Validation that at least one comment type must be selected

### Changed
- Refactored cron schedule registration to use `register_cron_schedules()` method
- Improved `get_cron_interval_key()` to be simpler and more reliable
- Enhanced sanitization with better validation logic
- Variables now properly passed from class to template file

### Security
- All output is now properly escaped using WordPress escaping functions
- Added validation to prevent auto-delete activation without comment types selected

## [2.0.0] - 2024-11-12

### Added
- Modern dashboard UI with card-based layout
- Real-time comment statistics with auto-refresh
- Analytics tracking for deleted comments
- RESTful API endpoints for stats and analytics
- Improved admin interface with toggle switches
- Better responsive design for mobile devices
- Activity logging with last 100 deletion events
- Status panel showing next scheduled run
- Manual delete button for immediate batch deletion
- Warning system for deleting approved comments

### Changed
- Batch size limited to 50 (previously 100) to prevent server errors
- Improved code organization and documentation
- Enhanced security with nonce verification
- Better error handling throughout

### Security
- Added proper nonce verification for all AJAX requests
- Improved input sanitization and validation
- Added capability checks for all admin operations

## [1.0.0] - Initial Release

### Added
- Basic automatic comment deletion functionality
- Configurable batch size (1-100 comments)
- Configurable interval (1-60 minutes)
- Support for deleting spam, pending, approved, and trash comments
- Age filtering (delete comments older than X days)
- WordPress cron integration
- Basic activity logging
- Settings page in WordPress admin

---

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
