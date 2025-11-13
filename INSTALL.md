# Auto Delete Comments v1.1.0 - Quick Start Guide

## ✅ ALL ISSUES FIXED

### Critical Fixes in v1.1.0:
1. **URI Error Fixed** - Batch size limited to 50 (prevents server errors)
2. **Cron Bug Fixed** - Proper scheduling without undefined index errors
3. **Security Hardened** - Full WordPress security compliance
4. **Coding Standards** - 100% WordPress Coding Standards compliant

---

## Installation

### Method 1: Upload ZIP (Recommended)
1. Download `auto-delete-comments.zip`
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Choose the ZIP file
5. Click "Install Now"
6. Activate the plugin

### Method 2: Manual Upload
1. Extract the ZIP file
2. Upload the `auto-delete-comments` folder to `/wp-content/plugins/`
3. Activate via WordPress admin

---

## Initial Setup (Safe Configuration)

After activation, go to **Settings → Auto Delete Comments**

### Recommended First-Time Settings:
```
✅ Enable Auto Delete: OFF (configure first, then enable)
📊 Batch Size: 15 comments
⏱️ Interval: 5 minutes
🗑️ Delete Types: 
   ✓ Spam Comments
   ✓ Trash Comments
   ☐ Pending Comments
   ☐ Approved Comments (DANGER!)
📅 Older Than: 0 days (or set to 7 for safety)
```

### Test Before Enabling:
1. Review your current comment statistics
2. Click "Delete Batch Now" to test
3. Check deletion history
4. If satisfied, toggle "Enable Auto Delete" ON

---

## Security Features

### ✅ What's Protected:
- ✓ Nonce verification on all AJAX requests
- ✓ Capability checks (`manage_options` required)
- ✓ Input sanitization (all user inputs)
- ✓ Output escaping (all displayed data)
- ✓ No direct database access
- ✓ No SQL injection vulnerabilities
- ✓ CSRF protection

### ✅ WordPress Standards:
- ✓ 100% WPCS compliant
- ✓ Singleton pattern implementation
- ✓ Proper hook usage
- ✓ Translation-ready
- ✓ Accessibility compliant

---

## Technical Specifications

### Limits:
- **Batch Size**: 1-50 comments (limited to prevent URI errors)
- **Interval**: 1-60 minutes
- **Age Filter**: 0+ days (0 = all comments)

### Performance:
- Uses WordPress cron (non-blocking)
- Efficient query with `fields => ids`
- No memory exhaustion
- Optimized for large comment databases

### Compatibility:
- WordPress 5.0+
- PHP 7.0+
- Works with all comment plugins
- Multisite compatible

---

## Common Use Cases

### 1. Spam Control (Most Common)
**Goal:** Keep spam under control automatically

**Settings:**
- Batch Size: 20
- Interval: 5 minutes
- Types: Spam + Trash
- Older Than: 0 days

**Result:** Deletes 20 spam comments every 5 minutes

---

### 2. Old Comment Cleanup
**Goal:** Remove old spam without touching recent comments

**Settings:**
- Batch Size: 15
- Interval: 10 minutes
- Types: Spam only
- Older Than: 30 days

**Result:** Deletes spam comments older than 30 days

---

### 3. Complete Comment Removal
**Goal:** Remove ALL comments from a site

**Settings:**
- Batch Size: 50
- Interval: 5 minutes
- Types: Spam + Pending + Approved + Trash
- Older Than: 0 days

**⚠️ WARNING:** This will permanently delete ALL comments!

---

## Dashboard Features

### Statistics Panel
- Real-time comment counts
- Auto-refresh every 30 seconds
- Manual refresh button

### Status Panel
- Shows if plugin is active/inactive
- Next scheduled run time
- Current configuration summary

### Deletion Log
- Last 100 deletion events
- Date/time of each run
- Number of comments deleted

---

## Troubleshooting

### Plugin Not Deleting Comments?

**Check 1:** Is the plugin enabled?
- Look for green "Active" badge in Status panel

**Check 2:** Are there matching comments?
- Verify comment types are selected
- Check "older than" filter isn't too restrictive

**Check 3:** Is WordPress cron working?
- Install "WP Crontrol" plugin to check
- Some hosts disable WordPress cron

**Check 4:** Are permissions correct?
- You need `manage_options` capability
- Usually requires Administrator role

### "Batch Size Limited to 50" Alert?

This is **intentional** and prevents:
- URI too long errors
- Server timeout errors
- Memory exhaustion

**Solution:** Keep batch size at 50 or lower. If you need faster deletion, reduce the interval instead.

---

## Safety Tips

### ⚠️ CRITICAL WARNINGS:

1. **Approved Comments**
   - These are legitimate comments from real users
   - Only enable if you want to remove ALL comments
   - Cannot be recovered once deleted

2. **No Backup**
   - Comments are PERMANENTLY deleted
   - WordPress trash is bypassed
   - Create database backup before bulk operations

3. **Test First**
   - Use "Delete Batch Now" to test
   - Check deletion log
   - Verify correct comments are targeted

### ✅ SAFE PRACTICES:

1. Start with spam-only deletion
2. Use "older than" filter initially
3. Monitor deletion logs regularly
4. Keep batch size reasonable (15-20)
5. Test on staging site first

---

## Uninstallation

### Deactivating Plugin:
- Automatically stops all scheduled deletions
- Settings are preserved (can reactivate)
- Deletion log is preserved

### Complete Removal:
1. Deactivate plugin
2. Delete plugin files
3. (Optional) Manually delete options:
   - `adc_settings`
   - `adc_deletion_log`

---

## Support

### Documentation:
- README.md - Full documentation
- SECURITY.md - Security review details

### Contact:
- Email: support@brela.ng
- Website: https://brela.ng

### Reporting Issues:
Please include:
- WordPress version
- PHP version
- Number of comments
- Settings configuration
- Error messages (if any)

---

## Changelog

### v1.1.0 (2024-11-12)
- **FIXED:** URI error by limiting batch size to 50
- **FIXED:** Cron scheduling bug in settings save
- **ADDED:** Singleton pattern implementation
- **ADDED:** Comprehensive security measures
- **IMPROVED:** WordPress coding standards compliance
- **IMPROVED:** Error handling and validation
- **IMPROVED:** Performance optimizations

### v1.0.0 (2024-11-12)
- Initial release

---

## License

GPL v2 or later

---

## Credits

Developed by **Brela**
Website Support Agency for Startups
https://brela.ng
