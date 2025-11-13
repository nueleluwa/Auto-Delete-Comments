=== Auto Delete Comments ===
Contributors: luwie93
Donate link: https://buymeacoffee.com/nueleluwa
Tags: comments, spam, auto-delete, cleanup, moderation, batch-delete, automation
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically delete spam, pending, approved, or trash comments in configurable batches with advanced scheduling, analytics, and REST API support.

== Description ==

**Auto Delete Comments** is a modern, production-ready WordPress plugin that helps you maintain a clean comments database by automatically deleting unwanted comments in batches.

= Key Features =

* **Automatic Deletion** - Schedule batch deletions every 1-60 minutes
* **Flexible Selection** - Choose which comment types to delete (spam, pending, approved, trash)
* **Age Filtering** - Delete only comments older than specified days
* **Batch Processing** - Process 1-50 comments per run to prevent server overload
* **Real-Time Statistics** - Live comment counts with auto-refresh
* **Advanced Analytics** - 30-day tracking with interactive charts
* **Activity Logging** - Track last 100 deletion activities
* **Manual Controls** - Delete batch immediately with one click
* **REST API** - Full API access for headless WordPress setups
* **Professional Interface** - Clean, WordPress-native admin design
* **Security Hardened** - WordPress VIP coding standards compliant

= Perfect For =

* **High-Traffic Sites** - Automatically manage spam comments
* **Blogs & News Sites** - Keep comment sections clean
* **E-commerce Stores** - Remove spam from product reviews
* **Membership Sites** - Maintain clean discussion areas
* **Database Optimization** - Regular cleanup for better performance

= How It Works =

1. Install and activate the plugin
2. Go to Settings → Auto Delete Comments
3. Configure your preferences (batch size, interval, comment types)
4. Enable automatic deletion
5. Monitor activity through the dashboard

Comments are permanently deleted using WordPress's native `wp_delete_comment()` function, ensuring all associated metadata is properly cleaned up.

= Security & Performance =

* **Security Rating:** A+ (WordPress VIP Standards)
* **Nonce verification** on all AJAX requests
* **Capability checks** for all admin operations
* **Input sanitization** using WordPress functions
* **Output escaping** throughout
* **Optimized queries** using `fields => 'ids'`
* **Batch processing** prevents timeouts and server overload
* **Limited batch size** (max 50) prevents URI errors

= REST API Endpoints =

The plugin provides REST API endpoints for headless WordPress setups:

* `GET /wp-json/auto-delete-comments/v1/stats` - Get current comment statistics
* `GET /wp-json/auto-delete-comments/v1/analytics` - Get deletion analytics

All endpoints require `manage_options` capability for security.

= Support =

For support, bug reports, or feature requests:

* [GitHub Repository](https://github.com/nueleluwa/Auto-Delete-Comments)
* [GitHub Issues](https://github.com/nueleluwa/Auto-Delete-Comments/issues)
* [Documentation](https://github.com/nueleluwa/Auto-Delete-Comments/blob/main/README.md)

= Professional Services =

Need custom WordPress development? Visit [Brela.ng](https://brela.ng) for professional WordPress solutions.

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins → Add New
3. Search for "Auto Delete Comments"
4. Click "Install Now" then "Activate"
5. Go to Settings → Auto Delete Comments to configure

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New → Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"
6. Go to Settings → Auto Delete Comments to configure

= Configuration =

1. **Enable Auto Delete** - Toggle to activate automatic deletion
2. **Batch Size** - Set number of comments to delete per run (1-50)
3. **Interval** - Choose how often to run (1-60 minutes)
4. **Comment Types** - Select which types to delete:
   * Spam Comments (Recommended)
   * Trash Comments (Safe)
   * Pending Comments (Use with caution)
   * Approved Comments (Dangerous - use carefully!)
5. **Older Than** - Set minimum age in days (0 = delete all matching)
6. Click "Save Changes"

== Frequently Asked Questions ==

= Is it safe to delete approved comments? =

Deleting approved comments is risky as these are legitimate user contributions. Only enable this if you're certain you want to remove them. Always test with a small batch first.

= Can I recover deleted comments? =

No. Comments are permanently deleted using WordPress's native deletion function. They cannot be recovered. Always backup your database before enabling automatic deletion of approved comments.

= How does the batch size affect performance? =

Smaller batch sizes (10-20) are safer and have minimal impact on server resources. Larger batches (30-50) process more comments faster but may use more resources. The plugin limits batch size to 50 to prevent server errors.

= What happens if I disable the plugin? =

When you disable the plugin, all scheduled cron jobs are automatically removed. Your existing comments remain unchanged. Settings are preserved and will be restored if you reactivate the plugin.

= Does this work with custom comment types? =

The plugin works with WordPress's native comment statuses: spam, pending (hold), approved, and trash. It uses WordPress's standard comment system and should work with most comment-related plugins.

= How often should I run the deletion? =

For most sites, running every 5-10 minutes with a batch size of 15-20 is ideal. High-traffic sites with lots of spam may benefit from more frequent runs (3-5 minutes). Low-traffic sites can use longer intervals (10-30 minutes).

= Does this affect site performance? =

No. The plugin uses WordPress cron for background processing and optimized queries. Deletion runs asynchronously and has minimal impact on site performance.

= Can I use this with WP-CLI? =

Yes! You can trigger manual deletion using WordPress's cron system:
`wp cron event run adc_delete_comments_batch`

= Is this compatible with multisite? =

Yes, the plugin is compatible with WordPress multisite. Each site in the network has its own independent configuration.

= Does this delete comment metadata? =

Yes. The plugin uses WordPress's native `wp_delete_comment()` function with the force delete parameter, which removes all associated metadata properly.

== Screenshots ==

1. **Main Dashboard** - Real-time comment statistics, settings panel, and status information
2. **Statistics Cards** - Live counts for spam, pending, approved, trash, and total comments
3. **Settings Form** - Easy configuration with batch size, interval, and comment type selection
4. **Manual Actions** - One-click batch deletion for testing before enabling automatic mode
5. **Deletion History** - Activity log showing last 100 deletion events with timestamps
6. **Analytics Chart** - Visual 7-day trend showing deletion patterns
7. **Status Panel** - Current configuration, next run time, and plugin status
8. **Author Section** - Professional plugin branding with developer information

== Changelog ==

= 2.0.1 (2024-11-13) =
**Critical Bug Fixes & Improvements**
* Fixed: Template variable scope issues causing PHP warnings
* Fixed: Cron scheduling reliability (intervals now registered early)
* Fixed: Missing output escaping (XSS vulnerability eliminated)
* Added: Settings validation (prevents enabling without comment types)
* Added: Professional author section with photo and social links
* Added: Comprehensive error logging throughout
* Improved: WordPress-native design for author section
* Added: Extensive documentation (10+ guides)
* Added: Code quality validation script
* Updated: All GitHub URLs to correct repository
* Security: A+ rating (all vulnerabilities fixed)
* Quality: WordPress VIP coding standards compliant

= 2.0.0 (2024-11-12) =
**Major Update - Modern Interface**
* Added: Real-time statistics dashboard with auto-refresh
* Added: Advanced analytics with Chart.js visualization
* Added: REST API endpoints for headless WordPress
* Added: Modern card-based UI design
* Added: Manual batch deletion controls
* Added: 30-day analytics tracking
* Added: Activity logging (last 100 runs)
* Enhanced: User interface and experience
* Improved: Data visualization
* Updated: Comprehensive documentation

= 1.1.0 (2024-11-12) =
**Security & Bug Fixes**
* Fixed: URI error by limiting batch size to 50
* Fixed: Cron scheduling reliability issues
* Added: Comprehensive security measures
* Improved: WordPress Coding Standards compliance (100%)
* Optimized: Performance improvements
* Enhanced: Error handling throughout

= 1.0.0 (2024-11-12) =
**Initial Release**
* Basic automatic comment deletion
* Configurable scheduling (1-60 minutes)
* Comment type selection
* Age filtering
* Activity logging
* WordPress cron integration

== Upgrade Notice ==

= 2.0.1 =
Critical bug fixes and security improvements. All users should upgrade immediately. This version fixes template variable issues, improves cron reliability, and eliminates a potential XSS vulnerability.

= 2.0.0 =
Major update with modern interface, REST API, and advanced analytics. Recommended upgrade for all users.

= 1.1.0 =
Important bug fixes and security enhancements. Recommended upgrade for all users.

== Additional Information ==

= Requirements =
* WordPress 5.8 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher

= Browser Support =
* Chrome/Edge (latest)
* Firefox (latest)
* Safari (latest)
* Mobile browsers

= Languages =
* English (default)
* Translation-ready with .pot file included

= Credits =
* Developed by Emmanuel Eluwa
* Chart.js for analytics visualization
* WordPress Community for feedback and support

= Privacy Policy =
This plugin does not collect, store, or transmit any user data. All operations are performed locally on your WordPress installation. The plugin only accesses your site's comment data to perform deletions as configured.

= GDPR Compliance =
The plugin is GDPR compliant as it does not collect any personal data from users. It only processes existing WordPress comments based on your configuration.

== Developer Notes ==

= Code Standards =
* WordPress VIP Coding Standards compliant
* PHPCS validated
* Follows WordPress plugin best practices
* Fully documented inline code

= Hooks & Filters =
The plugin uses WordPress's standard hook system. Developers can extend functionality using:
* `adc_delete_comments_batch` - Action hook for custom processing
* `adc_settings` - Filter for modifying default settings

= REST API Usage =

**Get Statistics:**
```
GET /wp-json/auto-delete-comments/v1/stats
Authorization: Required (manage_options capability)

Response:
{
  "spam": 42,
  "pending": 5,
  "approved": 1250,
  "trash": 8,
  "total": 1305
}
```

**Get Analytics:**
```
GET /wp-json/auto-delete-comments/v1/analytics
Authorization: Required (manage_options capability)

Response:
{
  "total_deleted": 1234,
  "avg_per_run": 15.2,
  "total_runs": 81,
  "last_7_days": {
    "2024-11-06": 50,
    "2024-11-07": 45,
    ...
  }
}
```

= GitHub Repository =
* Source code: https://github.com/nueleluwa/Auto-Delete-Comments
* Issues: https://github.com/nueleluwa/Auto-Delete-Comments/issues
* Contributions welcome!

= Professional Support =
For custom development or professional WordPress services, visit [Brela.ng](https://brela.ng)
