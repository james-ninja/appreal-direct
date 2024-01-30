=== WP Engine Site Migration ===
Contributors: wpengine, ahmedgeek, philwebs, ianmjones, eriktorsner, dalewilliams, tysonreeder, kevinwhoffman
Tags: migrate, push, transfer, wordpress migration plugin, move site, database migration, site migration
Requires at least: 5.0
Tested up to: 6.4.2
Requires PHP: 5.6
Stable tag: 1.0.0-beta.21
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Migrate any WordPress site to WP Engine or Flywheel.

== Description ==

Migrate any WordPress site to WP Engine or Flywheel. Copy all database tables and site files or customize the migration to include only what you need.

== Changelog ==

= WP Engine Site Migration 1.0.0-beta.21 - 2024-01-10 =
* Added: An alternative migration method is now suggested when an error occurs
* Fixed: Recursive scanning performance is now improved due to ignoring excluded directories
* Fixed: Connecting to a multisite no longer causes an error when the primary domain is `wpenginepowered.com`
* Fixed: Errors while saving the remote manifest are now surfaced
* Fixed: Focus rings on the plugin settings page are now fully visible

= WP Engine Site Migration 1.0.0-beta.20 - 2023-12-22 =
* Fixed: Excluded media files defined in the UI are once again excluded from the migration

= WP Engine Site Migration 1.0.0-beta.19 - 2023-12-20 =
* Fixed: Excluded directories defined in the UI are now correctly excluded from the migration
* Fixed: Plugins that contain multiple files with header comments no longer cause migrations to fail

= WP Engine Site Migration 1.0.0-beta.18 - 2023-12-14 =
* Changed: Failed migration logs once again provide insight when troubleshooting a migration
* Fixed: Stuck migrations are now terminated after 4 minutes without progress
* Fixed: Third-party admin notices no longer appear on plugin pages
* Fixed: "Unsupported operand types" no longer cause errors when scanning a directory during initialization

= WP Engine Site Migration 1.0.0-beta.17 - 2023-12-11 =
* Removed: Changes from Beta 16 have been reverted to result in more stable migrations

= WP Engine Site Migration 1.0.0-beta.16 - 2023-12-07 =
* Changed: Failed migration logs now provide more insight when troubleshooting a migration
* Fixed: "Unsupported operand types" no longer cause errors when scanning a directory during initialization

= WP Engine Site Migration 1.0.0-beta.15 - 2023-11-29 =
* Added: PHP memory limit is now available in the diagnostic log to aid in troubleshooting memory issues
* Changed: The option to "Load Select Plugins for Migration Requests" has been removed as it was often used to unnecessarily load plugins during a migration, thereby increasing the likelihood of errors
* Fixed: PHP warnings related to 'open_basedir' restrictions no longer occur
* Fixed: The number of background polling requests that fetch data from the server has been reduced
* Fixed: Error handling of manifest files is now more robust

= WP Engine Site Migration 1.0.0-beta.14 - 2023-11-16 =
* Added: An alternative file transfer method is now attempted when encountering `cURL error 28`
* Fixed: Elementor CSS and references to these files in the database are now properly regenerated after a migration

= WP Engine Site Migration 1.0.0-beta.13 - 2023-11-14 =
* Fixed: Compatibility with Widgets for Google Reviews and other plugins which mix `utf8` and `utf8mb4` character sets has been improved
* Fixed: Temporary files (i.e. `tmpchunk` files) from previous migrations no longer cause errors in subsequent attempts
* Fixed: Failure to create temporary tables used in background processing now displays an error and immediately stops the migration
* Fixed: Telemetry now includes a more accurate representation of each migration based on current task data

= WP Engine Site Migration 1.0.0-beta.12 - 2023-11-08 =
* Changed: The User-Agent request header is now set to `wpe-site-migration/<version>` for all migration requests
* Changed: Migration status updates are now sent to WP Engine at regular intervals throughout the migration for better support and troubleshooting

= WP Engine Site Migration 1.0.0-beta.11 - 2023-10-31 =
* Added: cURL version is now available in the site diagnostics to provide more complete information for suppport
* Changed: All `.htaccess` files are now excluded as they are not supported on WP Engine or Flywheel
* Changed: Temporary directories on the source site are now located in the `uploads` directory where the plugin is more likely to have write permissions

= WP Engine Site Migration 1.0.0-beta.10 - 2023-10-25 =
* Changed: Connection information in the plugin Settings tab now uses the `wpengine.com` URL for WP Engine sites
* Fixed: Errors caused by file size differences are now less likely to occur as the size is reverified just before transferring
* Fixed: Errors that occur while initializing the migration are now more likely to be caught
* Fixed: Compatibility with WP Go Maps has been further improved through better handling of empty `POINT` fields

= WP Engine Site Migration 1.0.0-beta.9 - 2023-10-18 =
* Fixed: Compatibility with WP Go Maps has been improved through support for the MySQL `POINT` data type
* Fixed: Themes in nested directories no longer cause errors when finalizing the migration

= WP Engine Site Migration 1.0.0-beta.8 - 2023-10-16 =
* Changed: UpdraftPlus directory `/wp-content/updraft` is now excluded to avoid migrating backups that significantly increase size and duration
* Fixed: Only tables with the site's prefix are selected by default when customizing the selected tables

= WP Engine Site Migration 1.0.0-beta.7 - 2023-10-12 =
* Changed: Wordfence directory `/wp-content/wflogs` is now excluded to avoid errors caused by files changing in the middle of a migration
* Changed: Elementor directory `/wp-content/uploads/elementor/css` is now excluded so that CSS with the correct URLs is generated at the destination
* Fixed: Temporary tables generated by a migration no longer cause missing table errors
* Fixed: The calculated migration size no longer disappears after returning to a paused migration

= WP Engine Site Migration 1.0.0-beta.6 - 2023-10-04 =
* Added: External links that open in a new tab now have visual indicators
* Changed: Disabling the WP REST API now surfaces a warning
* Changed: Minimum WordPress and PHP requirements now deactivate the plugin if they are not met
* Fixed: Temporary directories starting with `wpmdb-tmp` are now automatically excluded from file transfers
* Fixed: Toggle buttons no longer disappear when panels are open
* Fixed: The WP Admin footer no longer moves up the page when customizing a migration
* Fixed: Error messages no longer render HTML, which could cause the surrounding page layout to break
* Fixed: Error messages related to the folder name of a theme now clarify which theme caused the error

= WP Engine Site Migration 1.0.0-beta.5 - 2023-09-28 =
* Added: WP Engine Site Migration is now available for download and testing from the User Portal.
