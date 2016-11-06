<?php die();?>
Akeeba Backup 4.2.4
================================================================================
~ ANGIE: Improve memory efficiency of the database engine
~ Switching the default log level to All Information and Debug
+ Support utf8mb4 in CRON jobs
# [LOW] Desktop notifications for backup resume showed "%d" instead of the time to wait before resume
# [LOW] Push notifications should not be enabled by default
# [MEDIUM] Dropbox integration would not work under many PHP 5.5 and 5.6 servers due to a PHP issue. Workaround applied.
# [HIGH] Restoring on MySQL would be impossible unless you used the MySQL PDO driver

Akeeba Backup 4.2.3
================================================================================
! Packaging error leading to immediate backup failure

Akeeba Backup 4.2.2
================================================================================
+ Push notifications with Pushbullet
# [HIGH] ANGIE: Restoration may fail or corrupt text on some servers due to UTF8MB4 support

Akeeba Backup 4.2.1
================================================================================
~ Updated Import from S3 to use the official Amazon AWS SDK for PHP
~ OneDrive: refresh the tokens if they expire in the middle of an upload
+ You can set the backup profile name directly from the Configuration page
+ You can create new backup profiles from the Configuration page using the Save & New button
+ Desktop notifications for backup start, finish, warning and error on compatible browsers (Chrome, Safari, Firefox)
+ UTF8MB4 (UTF-8 multibyte) support in restoration scripts, allows you to correctly restore content using multibyte Unicode characters (Emoji, Traditional Chinese, etc)
~ The logs and log directories at the site's root are forcibly excluded if present (some developers hardcode them)
~ ANGIE (restoration script): Reset OPcache after the restoration is complete (NB! Only if you use ANGIE's Remove Installation Directory feature)
# [HIGH] Restoration might be impossible if your database passwords contains a double quote character
# [MEDIUM] Dropbox and iDriveSync: could not upload under PHP 5.6 and some versions of PHP 5.5
# [MEDIUM] OneDrive upload could fail in CLI CRON jobs if the upload of all parts takes more than 3600 seconds
# [MEDIUM] Possible crash when the legacy MySQL driver is not available
# [MEDIUM] Reupload from Manage Backups failed when the post-processing engine is configured to use chunked uploads
# [LOW] White page when the ak_stats database table is broken
# [LOW] Some installations didn't have the correct embedded installer selected. Now forcing the correct default if an invalid value is detected.

Akeeba Backup 4.2.0
================================================================================
- Removed Joomla! 2.5 support
- Removed post-setup page; these messages are now handled through Joomla!'s Post-Installation Messages feature
- Removed the Site Transfer Wizard. You can use the DirectFTP/DirectSFTP engines; or use Upload to FTP/SFTP and check the Upload Kickstart option (preferred method)
- Removed the System Restore Point feature; please make a full site backup before updating your extensions. Joomla! 3.x requires full backups due to the way ACL, Categories, Tags and Content History work; that's why the SRP feature was removed.
- Removed Extension Filters. For the same reasons we removed SRP: this cannot work as expected in Joomla! 3.x.
- Removed Lite Mode in the front-end. Smartphones are now the norm and Joomla! 3.x offers a responsive back-end. Just use it from your smartphone!
+ Check for obsolete PHP versions and printing a warning
+ gh-528 Native support for Microsoft Live OneDrive
+ gh-529 Added support for uploading archives in subdirectories while using FTP post-processing engine
+ gh-530 Show profile title in a tooltip when hovering over the profile ID in Manage Backups page
+ gh-530 Profile filter in the Manage Backups page
+ Always exclude the configured Joomla! log directory, no matter where it is
# [LOW] ANGIE would throw a memory outage error on extreme circumstances when the database server connected and disconnected immediately
# [LOW] Prevent usage of negative profile ID in CLI backups
# [MEDIUM] Import from S3 could be a byte off

Akeeba Backup 4.1.2
================================================================================
+ Added "Apply to all" button in Files and Directories Exclusion page
+ ANGIE for Wordpress: Db collation set to "utf8_general_ci" by default
# [HIGH] Missing interface options on bad hosts which disable the innocent parse_ini_file PHP function
# [HIGH] ANGIE (restoration): Some bad hosts disable the innocent parse_ini_file PHP function resulting in translation and functional issues during the restoration
# [MEDIUM] ANGIE for Wordpress: Site url was not replaced when moving to a different server
# [LOW] On some hosts you wouldn't get the correct installer included in the backup
# [LOW] ANGIE for Wordpress: fixed changing Admin access details while restoring
# [LOW] Configuration Wizard: detected minimum execution time was ignored; default value of 2 seconds always applied

Akeeba Backup 4.1.1
================================================================================
- Removed the obsolete "Upgrade profiles to ANGIE" post-installation message
# [LOW] Tooltips for backup comments not shown in Manage Backup page on Joomla! 3.x
# [HIGH] Control Panel icons not shown on some extremely low quality hosts which disable the innocuous parse_ini_file function. If you were affected SWITCH HOSTS, IMMEDIATELY!
# [HIGH] gh-523 Fatal error on Joomla! 2.5 when logging in as Administrator (NOT Super User!) and you have published our Quick Icon plugin
# [HIGH] Old PHP 5.3 versions have a bug regarding Interface implementation, causing a PHP fatal error

Akeeba Backup 4.1.0
================================================================================
+ Brand new icon set in the Control Panel page by Helvecio da Silva (http://hlvcdesign.com.br)
+ Warning added when Joomla!'s com_postinstall component is broken (with instructions to fix it)
~ Less intrusive display of the file integrity check results
# [HIGH] System Restore Points didn't really work
# [HIGH] The Quick Icon - Akeeba Backup Notification plugin was broken since 4.1.0.rc1 (thanks Camden!)
# [HIGH] Stack filters wouldn't load. As a result, voluminous and unwanted data of Joomla!'s Finder tables would always be included.
# [MEDIUM] The backup ID was not returned to JSON API requests, making download after backup with Akeeba Remote CLI impossible (thanks Mikkel)
# [LOW] Work around Joomla!'s bug causing it to not load its library translation strings while showing the installation status messages when System Restore Points are enabled
# [LOW] Piecon could throw Javascript errors on some sites
# [LOW] Upload to Dropbox may not work on servers without a global cacert.pem file

Akeeba Backup 4.1.0.rc3
================================================================================
! DirectoryIterator::getExtension is not compatible with PHP 5.3.4 and 5.3.5
! As we announced 18 months ago, we require PHP 5.3.4 or later. If your site doesn't meet this requirement a very stern warning will be issued.
- Removed the (broken) multipart upload from the legacy S3 post-processing engine. Please use the new "Upload to Amazon S3" option for multipart uploads.
~ Removing references to JParameter (it was removed in Joomla! 3.4 alpha)
# [HIGH] Some Pro features (restore SRPs, site transfer wizard) not working because their files are deleted on installation
# [HIGH] Bug in third party Guzzle library causes Amazon S3 multipart uploads of archives larger than the remaining RAM size to fail due to memory exhaustion.
# [MEDIUM] The backup would halt if the upload to S3 failed instead of simply raising a warning
# [MEDIUM] System Restore Points were throwing a fatal error while applying size quota
# [MEDIUM] Fatal error on sites with open_basedir restrictions on the site's root
# [LOW] System Restore Points throwing warnings

Akeeba Backup 4.1.0.rc2
================================================================================
! Settings would be lost when upgrading to 4.1.0.rc1 due to the loss of the settings' encryption key on upgrade
# [LOW] 500 error when a specified engine (scanner, archiver, post-processing, dump) doesn't exist

Akeeba Backup 4.1.0.rc1
================================================================================
+ New and improved backup engine
+ Installation integrity check automatically spots missing, out of date or tampered files and warns you
+ ALICE will check if Additional Database information are correct [PRO]
+ ANGIE: Add warning about Live site URL on Windows
+ You can now sort and search entries in the Profiles Management page
+ Using advanced selects (Chosen) for backup profile lists under Joomla! 3
- Removed inconsistent submenus at the top of some pages
~ The following features are deprecated and will be removed in a later version: site transfer wizard, system restore points, extension filters, lite mode, the old Amazon S3 integration (there is a new Amazon S3 integration which has replaced it)
~ Changed the .htaccess files to be compatible with Apache 2.4
~ Improved styling for detected issues
~ Set the commonly used ports for FTP/FTPS and SFTP transfers in the Site Transfer Wizard [PRO]
# [MEDIUM] ANGIE: The option "No auto value on zero" was not working
# [MEDIUM] The data file pointer can be null sometimes when using multipart archives causing backup failures
# [MEDIUM] Fixed error while trying to fetch Super Administrators email during frontend backup
# [MEDIUM] ALICE: The "Backup engine state saving issues" and "Timeout while backing up" tests returned wrong results [PRO]
# [MEDIUM] Upload to remote storage from the Manage Backups page was broken for Amazon S3 multipart uploads [PRO]
# [MEDIUM] ae/gh-11 Race condition could prevent the reliable creation of JPS (encrypted) archives [PRO]
# [MEDIUM] gh-522 "Back to standard installer" still results in a System Restore Point backup being taken [PRO]
# [LOW] Tooltips not showing for engine parameters when selecting a different engine (e.g. changing the Archiver Engine from JPA to ZIP)
# [LOW] The lib_joomla translation might not be loaded when an extension is installed and the System - System Restore Points plugin is enabled [PRO]
# [LOW] ANGIE: Fixed table name abstraction when no table prefix is given
# [LOW] ANGIE: Fixed loading of translations
# [LOW] SFTP post-processing engine did not mark successfully uploaded backup as Remote [PRO]
# [LOW] SFTP post-processing engine could not fetch the archive back to the server [PRO]
# [LOW] ANGIE: Fixed .htaccess parsing while restoring a WordPress site
# [LOW] ANGIE: Fixed removing installation directory while restoring a WordPress site

Akeeba Backup 4.0.5
================================================================================
# [HIGH] The integrated restoration is broken after the last security update

Akeeba Backup 4.0.4
================================================================================
! [SECURITY: Medium] Possibility of arbitrary file writing while a backup archive is being extracted by the integrated restoration feature

Akeeba Backup 4.0.3
================================================================================
+ Warn the user if the post-processing engine requires cURL extension and it's not enabled
! Backup failure on certain Windows hosts and PHP versions due to the way these versions handle file pointers
! Failure to post-process part files immediately on certain Windows hosts and PHP versions due to the way these versions handle file pointers

Akeeba Backup 4.0.2
================================================================================
! Dangling file pointer causing backup failure on certain Windows hosts
~ CloudFiles implementation changed to authentication API version 2.0, eliminating the need to choose your location
~ Old MySQL versions (5.1) would return randomly ordered rows when dumping MyISAM tables when the MySQL database is corrupt up to the kazoo and about to come crashing down in flames
~ Some servers seem to try and load the AkeebaUsagestats class twice
# [LOW] Database table exclusion table blank and backup errors when your db user doesn't have adequate privileges to show procedures, triggers or stored procedures in MySQL
# [LOW] Could not back up triggers, procedures and functions

Akeeba Backup 4.0.1
================================================================================
! A bug in SRP prevented updates unless you disabled it. Moreover, the data of the extensions were not saved in the SRP backup (also affects all 3.11.x versions of Akeeba Backup).

Akeeba Backup 4.0.0
================================================================================
+ Support for iDriveSync accounts created in 2014 or later
+ A different log file is created per backup attempt (and automatically removed when the backup archives are deleted by quotas or by using Delete Files in the interface)
+ You can now run several backups at the same time
+ The minimum execution time can now be enforced in the client side for backend backups, leading to increased stability on certain hosts
+ Back-end backups will resume after an AJAX error, allowing you to complete backups even on very hosts with very tight resource usage limits
+ The Dropbox chunked upload can now work on files smaller than 150Mb and will work across backup steps, allowing you to upload large files to Dropbox without timeout errors
+ Greatly improve the backup performance on massive tables as long as they have an auto_increment column
+ Work around the issues caused by some servers' error pages which contain DOM-modifying JavaScript
+ Support for the new MySQL (PDO) driver in Joomla! 3.4
+ Akeeba Backup now uses Joomla!'s Post-installation Messages on Joomla! 3.2.0 and later instead of showing its own post-installation page after a new install / upgrade to a new version
+ Anonymous reporting of PHP, MySQL and CMS versions (opt-out through the options)
- Removed obsolete ABI (Akeeba Backup Installer)
~ Even better workaround for very badly written system plugins which output Javascript without a trailing semicolon and/or newline, leading to Javascript errors. This is not a bug in our software, it's a bug in those badly written plugins and WE have to work around THEIR bad code!
~ Work around for the  overreaching password managers in so-called modern browsers which fill random, irrelevant passwords in the JPS and ANGIE password fields, without asking you and without notifying you, without letting developers tell them "no, do not autocomplete this field because you're doing it wrong and screwing with my clients, Mr. Stupid Browser".
# [HIGH] Dropbox upload would enter an infinite loop when using chunked uploads
# [MEDIUM] ANGIE: Restoring off-site directories would lead to errors
# [MEDIUM] Front-end backup failure on multilingual sites or when used in combination with certain third party plugins
# [MEDIUM] Joomla! caches plugin information, leading to unexpected behaviour in various places
# [MEDIUM] ANGIE for Joomla!, cannot detect Joomla! version, leading to Two Factor Auth data not being re-encoded with the new secret key
# [MEDIUM] ANGIE (all flavours): cannot restart db restoration after a database error has occurred.
# [LOW] ANGIE for WordPress, phpBB and PrestaShop: escape some special characters in passwords
# [LOW] ANGIE for Joomla!, array values in configuration.php were corrupted