<?php
# This file contains strings extracted from each module's module.json file. These strings are used in the Settings pages.
# BEGIN MODULE: admin-user
__( 'Admin User', 'it-l10n-ithemes-security-pro' );
__( 'An advanced tool that removes users with a username of “admin” or a user ID of “1”.', 'it-l10n-ithemes-security-pro' );
__( 'Change Admin User', 'it-l10n-ithemes-security-pro' );
__( 'Changes the username of the “admin” user.', 'it-l10n-ithemes-security-pro' );
__( 'Run this tool to change the username of a user with the “admin” username. This may prevent unsophisticated attacks that assume the “admin” user exists.', 'it-l10n-ithemes-security-pro' );
__( 'New Username', 'it-l10n-ithemes-security-pro' );
__( 'Enter the new username for the “admin” user.', 'it-l10n-ithemes-security-pro' );
__( 'Change User ID 1', 'it-l10n-ithemes-security-pro' );
__( 'Changes the user ID for the first WordPress user.', 'it-l10n-ithemes-security-pro' );
__( 'Run this tool to change the user ID of a user with a user ID of “1”. This may prevent unsophisticated attacks that assume the user with an ID of “1” is an administrator.', 'it-l10n-ithemes-security-pro' );
# END MODULE: admin-user

# BEGIN MODULE: backup
__( 'Database Backups', 'it-l10n-ithemes-security-pro' );
__( 'Manually create a database backup or schedule automatic database backups.', 'it-l10n-ithemes-security-pro' );
__( 'Database backups can help you restore your database in the case of data corruption. However, it is not a complete backup and will not include your site files.', 'it-l10n-ithemes-security-pro' );
__( 'Schedule Database Backups', 'it-l10n-ithemes-security-pro' );
__( 'Backup Interval', 'it-l10n-ithemes-security-pro' );
__( 'The number of days between database backups.', 'it-l10n-ithemes-security-pro' );
__( 'Backup Method', 'it-l10n-ithemes-security-pro' );
__( 'Select what we should do with your backup file. You can have it emailed to you, saved locally or both.', 'it-l10n-ithemes-security-pro' );
__( 'Save Locally and Email', 'it-l10n-ithemes-security-pro' );
__( 'Email Only', 'it-l10n-ithemes-security-pro' );
__( 'Save Locally Only', 'it-l10n-ithemes-security-pro' );
__( 'Backup Location', 'it-l10n-ithemes-security-pro' );
__( 'The path on your machine where backup files should be stored. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' );
__( 'Backups to Retain', 'it-l10n-ithemes-security-pro' );
__( 'Limit the number of backups stored locally (on this server). Any older backups beyond this number will be removed. Enter “0” to retain all backups.', 'it-l10n-ithemes-security-pro' );
__( 'Compress Backup Files', 'it-l10n-ithemes-security-pro' );
__( 'By default, iThemes Security will zip backup files to reduce file size. You may need to turn this off if you are having problems with backups.', 'it-l10n-ithemes-security-pro' );
__( 'Backup Tables', 'it-l10n-ithemes-security-pro' );
__( 'Specify which tables should be included or excluded from backups. WordPress Core tables are always included.', 'it-l10n-ithemes-security-pro' );
__( 'Last Run', 'it-l10n-ithemes-security-pro' );
__( 'Scheduling', 'it-l10n-ithemes-security-pro' );
__( 'Configuration', 'it-l10n-ithemes-security-pro' );
__( 'Backup Tables', 'it-l10n-ithemes-security-pro' );
__( 'Excluded Tables', 'it-l10n-ithemes-security-pro' );
__( 'List of tables to exclude from each backup.', 'it-l10n-ithemes-security-pro' );
__( 'Included Tables', 'it-l10n-ithemes-security-pro' );
__( 'List of tables to include in each backup.', 'it-l10n-ithemes-security-pro' );
# END MODULE: backup

# BEGIN MODULE: ban-users
__( 'Ban Users', 'it-l10n-ithemes-security-pro' );
__( 'Block specific IP addresses and user agents from accessing the site.', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security automatically adds an IP to the ban list once it meets the Ban Threshold requirements. The Ban Threshold setting can be adjusted in the [Global Settings](itsec://settings/configure/global). You can manually add IPs to the ban list from the Security Dashboard using the Banned Users card.', 'it-l10n-ithemes-security-pro' );
__( 'blacklist', 'it-l10n-ithemes-security-pro' );
__( 'Default Ban List', 'it-l10n-ithemes-security-pro' );
__( 'As a getting-started point you can include the HackRepair.com ban list developed by Jim Walker.', 'it-l10n-ithemes-security-pro' );
__( 'Enable Ban Lists', 'it-l10n-ithemes-security-pro' );
__( 'Limit Banned IPs in Server Configuration Files', 'it-l10n-ithemes-security-pro' );
__( 'Limiting the number of IPs blocked by the Server Configuration Files (.htaccess and nginx.conf) will help reduce the risk of a server timeout when updating the configuration file. If the number of IPs in the banned list exceeds the Server Configuration File limit, the additional IPs will be blocked using PHP. Blocking IPs at the server level is more efficient than blocking IPs at the application level using PHP.', 'it-l10n-ithemes-security-pro' );
__( 'Ban User Agents', 'it-l10n-ithemes-security-pro' );
__( 'Enter a list of user agents that will not be allowed access to your site. Add one user agent per-line.', 'it-l10n-ithemes-security-pro' );
__( 'Custom Bans', 'it-l10n-ithemes-security-pro' );
# END MODULE: ban-users

# BEGIN MODULE: brute-force
__( 'Local Brute Force', 'it-l10n-ithemes-security-pro' );
__( 'Protect your site against attackers that try to randomly guess login details to your site.', 'it-l10n-ithemes-security-pro' );
__( 'If one had unlimited time and wanted to try an unlimited number of password combinations to get into your site they eventually would, right? This method of attack, known as a brute force attack, is something that WordPress is acutely susceptible to as, by default, the system doesn’t care how many attempts a user makes to login. It will always let you try again. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshold has been reached.', 'it-l10n-ithemes-security-pro' );
__( 'Automatically ban “admin” user', 'it-l10n-ithemes-security-pro' );
__( 'Immediately ban a host that attempts to login using the “admin” username.', 'it-l10n-ithemes-security-pro' );
__( 'Max Login Attempts Per Host', 'it-l10n-ithemes-security-pro' );
__( 'The number of login attempts a user has before their host or computer is locked out of the system. Set to 0 to record bad login attempts without locking out the host.', 'it-l10n-ithemes-security-pro' );
__( 'Max Login Attempts Per User', 'it-l10n-ithemes-security-pro' );
__( 'The number of login attempts a user has before their username is locked out of the system. Note that this is different from hosts in case an attacker is using multiple computers. In addition, if they are using your login name you could be locked out yourself. Set to 0 to log bad login attempts per user without ever locking the user out (this is not recommended).', 'it-l10n-ithemes-security-pro' );
__( 'Minutes to Remember Bad Login (check period)', 'it-l10n-ithemes-security-pro' );
__( 'The number of minutes in which bad logins should be remembered.', 'it-l10n-ithemes-security-pro' );
__( 'Login Attempts', 'it-l10n-ithemes-security-pro' );
# END MODULE: brute-force

# BEGIN MODULE: content-directory
__( 'Change Content Directory', 'it-l10n-ithemes-security-pro' );
__( 'Advanced feature to rename the wp-content directory to a different name.', 'it-l10n-ithemes-security-pro' );
# END MODULE: content-directory

# BEGIN MODULE: core
__( 'Core', 'it-l10n-ithemes-security-pro' );
__( 'Set Encryption Key', 'it-l10n-ithemes-security-pro' );
__( 'Sets a secure key that iThemes Security uses to encrypt sensitive values like Two-Factor codes.', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security will add a constant to your website’s <code>wp-config.php</code> file named <code>ITSEC_ENCRYPTION_KEY</code>.', 'it-l10n-ithemes-security-pro' );
__( 'encryption', 'it-l10n-ithemes-security-pro' );
__( 'Confirm Reset Key', 'it-l10n-ithemes-security-pro' );
__( 'Confirm you want to reset the encryption key to a new value.', 'it-l10n-ithemes-security-pro' );
__( 'Rotate Encryption Key', 'it-l10n-ithemes-security-pro' );
__( 'Updates all encrypted values to use the new encryption key.', 'it-l10n-ithemes-security-pro' );
__( 'If you’ve manually updated the <code>ITSEC_ENCRYPTION_KEY</code> constant in your website’s <code>wp-config.php</code> file, use this tool to update any existing encrypted values.', 'it-l10n-ithemes-security-pro' );
__( 'encryption', 'it-l10n-ithemes-security-pro' );
__( 'Previous Key', 'it-l10n-ithemes-security-pro' );
__( 'Provide the previous value of <code>ITSEC_ENCRYPTION_KEY</code>.', 'it-l10n-ithemes-security-pro' );
# END MODULE: core

# BEGIN MODULE: dashboard
__( 'Security Dashboard', 'it-l10n-ithemes-security-pro' );
__( 'See a real-time overview of the security activity on your website with this dynamic dashboard.', 'it-l10n-ithemes-security-pro' );
__( 'Enable Dashboard Creation', 'it-l10n-ithemes-security-pro' );
__( 'Allow users to create new iThemes Security Dashboards.', 'it-l10n-ithemes-security-pro' );
# END MODULE: dashboard

# BEGIN MODULE: database-prefix
__( 'Change Database Table Prefix', 'it-l10n-ithemes-security-pro' );
__( 'Changes the database table prefix that WordPress uses.', 'it-l10n-ithemes-security-pro' );
__( 'By default, WordPress assigns the prefix wp_ to all tables in the database where your content, users, and objects exist. For potential attackers, this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% of sites are already known. Changing the wp_ prefix makes it more difficult for tools that are trying to take advantage of vulnerabilities in other places to affect the database of your site. Before using this tool, we strongly recommend creating a backup of your database.', 'it-l10n-ithemes-security-pro' );
# END MODULE: database-prefix

# BEGIN MODULE: email-confirmation
__( 'Email Confirmation', 'it-l10n-ithemes-security-pro' );
# END MODULE: email-confirmation

# BEGIN MODULE: feature-flags
__( 'Feature Flags', 'it-l10n-ithemes-security-pro' );
__( 'Feature Flags in iThemes Security allow you to try experimental features before they are released.', 'it-l10n-ithemes-security-pro' );
__( 'Enabled Features', 'it-l10n-ithemes-security-pro' );
__( 'Select which experimental features you’d like to enable.', 'it-l10n-ithemes-security-pro' );
# END MODULE: feature-flags

# BEGIN MODULE: file-change
__( 'File Change', 'it-l10n-ithemes-security-pro' );
__( 'Monitor the site for unexpected file changes.', 'it-l10n-ithemes-security-pro' );
__( 'Even the best security practices can fail. The key to quickly spotting a security breach is monitoring file changes on your website.<br>While the type of damage malware causes on your website varies greatly, what it does can be boiled down to adding, removing, or modifying files.<br>File Change Detection scans your website’s files and alerts you when changes occur on your website.', 'it-l10n-ithemes-security-pro' );
__( 'Excluded Files and Folders', 'it-l10n-ithemes-security-pro' );
__( 'Enter a list of file paths to exclude from each File Change scan.', 'it-l10n-ithemes-security-pro' );
__( 'Ignore File Types', 'it-l10n-ithemes-security-pro' );
__( 'File types listed here will not be checked for changes. While it is possible to change files such as images it is quite rare and nearly all known WordPress attacks exploit php, js and other text files.', 'it-l10n-ithemes-security-pro' );
__( 'Compare Files Online', 'it-l10n-ithemes-security-pro' );
__( 'When any WordPress core file or file in an iThemes plugin or theme has been changed on your system, this feature will compare it with the version on WordPress.org or iThemes (as appropriate) to determine if the change was malicious. Currently this feature only works with WordPress core files, plugins on the WordPress.org directory and iThemes plugins and themes (plugins and themes from other sources will be added as available).', 'it-l10n-ithemes-security-pro' );
__( 'Excluded Files', 'it-l10n-ithemes-security-pro' );
__( 'Online Files', 'it-l10n-ithemes-security-pro' );
# END MODULE: file-change

# BEGIN MODULE: file-permissions
__( 'File Permissions', 'it-l10n-ithemes-security-pro' );
__( 'Lists file and directory permissions of key areas of the site.', 'it-l10n-ithemes-security-pro' );
__( 'Check File Permissions', 'it-l10n-ithemes-security-pro' );
# END MODULE: file-permissions

# BEGIN MODULE: file-writing
__( 'File Writing', 'it-l10n-ithemes-security-pro' );
__( 'Server Config Rules', 'it-l10n-ithemes-security-pro' );
__( 'View or flush the generated Server Config rules.', 'it-l10n-ithemes-security-pro' );
__( 'The “Write to Files” setting must be enabled to automatically flush rules.', 'it-l10n-ithemes-security-pro' );
__( 'wp-config.php Rules', 'it-l10n-ithemes-security-pro' );
__( 'View or flush the generated wp-config.php rules.', 'it-l10n-ithemes-security-pro' );
__( 'The “Write to Files” setting must be enabled to automatically flush rules.', 'it-l10n-ithemes-security-pro' );
# END MODULE: file-writing

# BEGIN MODULE: global
__( 'Global Settings', 'it-l10n-ithemes-security-pro' );
__( 'Configure basic settings that control how iThemes Security functions.', 'it-l10n-ithemes-security-pro' );
__( 'Changes made to the Global Settings are applied globally throughout the plugin settings. For example, the Lockout & Lockout messages settings are used by all of the iThemes Security Lockout features.', 'it-l10n-ithemes-security-pro' );
__( 'Write to Files', 'it-l10n-ithemes-security-pro' );
__( 'Allow iThemes Security to write to wp-config.php and .htaccess automatically. If disabled, you will need to place configuration options in those files manually.', 'it-l10n-ithemes-security-pro' );
__( 'NGINX Conf File', 'it-l10n-ithemes-security-pro' );
__( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' );
__( 'Minutes to Lockout', 'it-l10n-ithemes-security-pro' );
__( 'The length of time a host or user will be locked out from this site after hitting the limit of bad logins. The default setting of 15 minutes is recommended as increasing it could prevent attackers from being banned.', 'it-l10n-ithemes-security-pro' );
__( 'Days to Remember Lockouts', 'it-l10n-ithemes-security-pro' );
__( 'How many days should iThemes Security remember a lockout. This does not affect the logs generated when creating a lockout.', 'it-l10n-ithemes-security-pro' );
__( 'Ban Repeat Offender', 'it-l10n-ithemes-security-pro' );
__( 'Should iThemes Security permanently add a locked out IP address to the “Ban Users” list after reaching the configured “Ban Threshold”.', 'it-l10n-ithemes-security-pro' );
__( 'Ban Threshold', 'it-l10n-ithemes-security-pro' );
__( 'The number of lockouts iThemes Security must remember before permanently banning the attacker.', 'it-l10n-ithemes-security-pro' );
__( 'Host Lockout Message', 'it-l10n-ithemes-security-pro' );
__( 'The message to display when a computer (host) has been locked out.', 'it-l10n-ithemes-security-pro' );
__( 'User Lockout Message', 'it-l10n-ithemes-security-pro' );
__( 'The message to display to a user when their account has been locked out.', 'it-l10n-ithemes-security-pro' );
__( 'Community Lockout Message', 'it-l10n-ithemes-security-pro' );
__( 'The message to display to a user when their IP has been flagged as bad by the iThemes network.', 'it-l10n-ithemes-security-pro' );
__( 'Automatically Temporarily Authorize Hosts', 'it-l10n-ithemes-security-pro' );
__( 'Whenever an administrator user accesses the website, iThemes Security will prevent locking out that computer for 24 hours.', 'it-l10n-ithemes-security-pro' );
__( 'Authorized Hosts', 'it-l10n-ithemes-security-pro' );
__( 'Enter a list of hosts that should not be locked out by iThemes Security.', 'it-l10n-ithemes-security-pro' );
__( 'whitelist', 'it-l10n-ithemes-security-pro' );
__( 'How should event logs be kept', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security can log events in multiple ways, each with advantages and disadvantages. Database Only puts all events in the database with your posts and other WordPress data. This makes it easy to retrieve and process but can be slower if the database table gets very large. File Only is very fast but the plugin does not process the logs itself as that would take far more resources. For most users or smaller sites Database Only should be fine. If you have a very large site or a log processing software then File Only might be a better option.', 'it-l10n-ithemes-security-pro' );
__( 'Database Only', 'it-l10n-ithemes-security-pro' );
__( 'File Only', 'it-l10n-ithemes-security-pro' );
__( 'Both', 'it-l10n-ithemes-security-pro' );
__( 'Days to Keep Database Logs', 'it-l10n-ithemes-security-pro' );
__( 'The number of days database logs should be kept.', 'it-l10n-ithemes-security-pro' );
__( 'Days to Keep File Logs', 'it-l10n-ithemes-security-pro' );
__( 'The number of days file logs should be kept. File logs will additionally be rotated once the file hits 10MB. Set to 0 to only use log rotation.', 'it-l10n-ithemes-security-pro' );
__( 'Path to Log Files', 'it-l10n-ithemes-security-pro' );
__( 'This path must be writable by your website. For added security, it is recommended you do not include it in your website root folder.', 'it-l10n-ithemes-security-pro' );
__( 'Proxy Detection', 'it-l10n-ithemes-security-pro' );
__( 'Determine how iThemes Security determines your visitor’s IP addresses. Choose the Security Check Scan to let iThemes Security identify malicious IPs attacking your website accurately.', 'it-l10n-ithemes-security-pro' );
__( 'Proxy Header', 'it-l10n-ithemes-security-pro' );
__( 'Select the header your Proxy Server uses to forward the client IP address. If you don’t know the header, you can contact your hosting provider or select the header that has your IP Address.', 'it-l10n-ithemes-security-pro' );
__( 'Allow Data Tracking', 'it-l10n-ithemes-security-pro' );
__( 'Allow iThemes to track plugin usage via anonymous data.', 'it-l10n-ithemes-security-pro' );
__( 'Hide Security Menu in Admin Bar', 'it-l10n-ithemes-security-pro' );
__( 'Remove the Security Messages Menu from the admin bar. Notifications will only appear on the iThemes Security dashboard and settings pages.', 'it-l10n-ithemes-security-pro' );
__( 'Show Error Codes', 'it-l10n-ithemes-security-pro' );
__( 'Each error message in iThemes Security has an associated error code that can help diagnose an issue. Changing this setting to “Yes” causes these codes to display. This setting should be left set to “No” unless iThemes Security support requests that you change it.', 'it-l10n-ithemes-security-pro' );
__( 'Lockouts', 'it-l10n-ithemes-security-pro' );
__( 'Lockout Messages', 'it-l10n-ithemes-security-pro' );
__( 'Authorized Hosts', 'it-l10n-ithemes-security-pro' );
__( 'Logging', 'it-l10n-ithemes-security-pro' );
__( 'IP Detection', 'it-l10n-ithemes-security-pro' );
__( 'UI Tweaks', 'it-l10n-ithemes-security-pro' );
__( 'Manage iThemes Security', 'it-l10n-ithemes-security-pro' );
__( 'Allow users to manage iThemes Security.', 'it-l10n-ithemes-security-pro' );
__( 'Identify Server IPs', 'it-l10n-ithemes-security-pro' );
__( 'Determines the list of IP addresses your server uses when making HTTP requests.', 'it-l10n-ithemes-security-pro' );
__( 'The correct list of server IPs is important to prevent erroneous Lockouts and Trusted Devices errors.', 'it-l10n-ithemes-security-pro' );
# END MODULE: global

# BEGIN MODULE: hibp
__( 'Refuse Compromised Passwords', 'it-l10n-ithemes-security-pro' );
__( 'Require users to use passwords which do not appear in any password breaches tracked by Have I Been Pwned. Plaintext passwords are never sent to Have I Been Pwned. Instead, 5 characters of the hashed password are sent over an encrypted connection to their API. ', 'it-l10n-ithemes-security-pro' );
# END MODULE: hibp

# BEGIN MODULE: hide-backend
__( 'Hide Backend', 'it-l10n-ithemes-security-pro' );
__( 'Change the login URL of your site.', 'it-l10n-ithemes-security-pro' );
__( 'The Hide Backend feature isn’t fool proof, and your new login URL could still be exposed by WordPress Core, Plugins, or Themes when printing links to the login page. For example Privacy Request Confirmations or front-end login forms. We recommend using more robust security features like Two-Factor Authentication to secure your WordPress login page.', 'it-l10n-ithemes-security-pro' );
__( 'Hide Backend', 'it-l10n-ithemes-security-pro' );
__( 'Enable the hide backend feature.', 'it-l10n-ithemes-security-pro' );
__( 'Login Slug', 'it-l10n-ithemes-security-pro' );
__( 'The login url slug cannot be “login”, “admin”, “dashboard”, or “wp-login.php” as these are use by default in WordPress.', 'it-l10n-ithemes-security-pro' );
__( 'Register Slug', 'it-l10n-ithemes-security-pro' );
__( 'Enable Redirection', 'it-l10n-ithemes-security-pro' );
__( 'Redirect users to a custom location on your site, instead of throwing a 403 (forbidden) error.', 'it-l10n-ithemes-security-pro' );
__( 'Redirection Slug', 'it-l10n-ithemes-security-pro' );
__( 'The slug to redirect users to when they attempt to access wp-admin while not logged in.', 'it-l10n-ithemes-security-pro' );
__( 'Custom Login Action', 'it-l10n-ithemes-security-pro' );
__( 'WordPress uses the “action” variable to handle many login and logout functions. By default this plugin can handle the normal ones but some plugins and themes may utilize a custom action (such as logging out of a private post). If you need a custom action please enter it here.', 'it-l10n-ithemes-security-pro' );
__( 'URLs', 'it-l10n-ithemes-security-pro' );
__( 'Redirection', 'it-l10n-ithemes-security-pro' );
__( 'Advanced', 'it-l10n-ithemes-security-pro' );
# END MODULE: hide-backend

# BEGIN MODULE: malware-scheduling
__( 'Site Scan Scheduling', 'it-l10n-ithemes-security-pro' );
__( 'Protect your site with automated site scans. When this feature is enabled, the site will be automatically scanned twice a day. If a problem is found, an email is sent to select users.', 'it-l10n-ithemes-security-pro' );
# END MODULE: malware-scheduling

# BEGIN MODULE: network-brute-force
__( 'Network Brute Force', 'it-l10n-ithemes-security-pro' );
__( 'Join a network of sites that reports and protects against bad actors on the internet.', 'it-l10n-ithemes-security-pro' );
__( 'If one had unlimited time and wanted to try an unlimited number of password combinations to get into your site they eventually would, right? This method of attack, known as a brute force attack, is something that WordPress is acutely susceptible to as, by default, the system doesn’t care how many attempts a user makes to login. It will always let you try again. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshold has been reached.', 'it-l10n-ithemes-security-pro' );
__( 'Ban Reported IPs', 'it-l10n-ithemes-security-pro' );
__( 'Automatically ban IPs reported as a problem by the network.', 'it-l10n-ithemes-security-pro' );
__( 'API Key', 'it-l10n-ithemes-security-pro' );
__( 'Email Address', 'it-l10n-ithemes-security-pro' );
__( 'Receive Email Updates', 'it-l10n-ithemes-security-pro' );
__( 'Get the weekly WordPress Vulnerability Report and more WordPress security updates sent to your inbox.', 'it-l10n-ithemes-security-pro' );
__( 'API Configuration', 'it-l10n-ithemes-security-pro' );
# END MODULE: network-brute-force

# BEGIN MODULE: notification-center
__( 'Notification Center', 'it-l10n-ithemes-security-pro' );
__( 'Manage and configure email notifications sent by iThemes Security related to various settings modules.', 'it-l10n-ithemes-security-pro' );
__( 'Using the Notification Center, you can set the default recipients, enable the security digest email, customize email notifications, and more.', 'it-l10n-ithemes-security-pro' );
__( 'From Email', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security will send notifications from this email address. Leave blank to use the WordPress default.', 'it-l10n-ithemes-security-pro' );
__( 'Default Recipients', 'it-l10n-ithemes-security-pro' );
__( 'Set the default recipients for any admin-facing notifications.', 'it-l10n-ithemes-security-pro' );
# END MODULE: notification-center

# BEGIN MODULE: password-requirements
__( 'Password Requirements', 'it-l10n-ithemes-security-pro' );
__( 'Requiring strong and refusing compromised passwords is the first step in securing your login page.', 'it-l10n-ithemes-security-pro' );
__( 'Brute force attacks rely on people reusing weak passwords. Password Requirements allow you to force selected users to create a strong password that hasn’t already been compromised.', 'it-l10n-ithemes-security-pro' );
__( 'Requirement Settings', 'it-l10n-ithemes-security-pro' );
# END MODULE: password-requirements

# BEGIN MODULE: privacy
__( 'Privacy', 'it-l10n-ithemes-security-pro' );
# END MODULE: privacy

# BEGIN MODULE: wordpress-salts
__( 'WordPress Salts', 'it-l10n-ithemes-security-pro' );
__( 'Change WordPress Salts', 'it-l10n-ithemes-security-pro' );
__( 'Changes the WordPress salts used to secure cookies and security tokens.', 'it-l10n-ithemes-security-pro' );
__( 'This shouldn’t be done periodically, but only if you suspect your site may have been compromised. This will force all users to login again.', 'it-l10n-ithemes-security-pro' );
# END MODULE: wordpress-salts

# BEGIN MODULE: security-check-pro
__( 'Security Check Pro', 'it-l10n-ithemes-security-pro' );
__( 'Detects the correct way to identify user IP addresses based on your server configuration by making an API request to iThemes.com servers. No user information is sent to iThemes. [Read our Privacy Policy](https://ithemes.com/privacy-policy/).', 'it-l10n-ithemes-security-pro' );
__( 'Detects the correct way to identify user IP addresses based on your server configuration.', 'it-l10n-ithemes-security-pro' );
# END MODULE: security-check-pro

# BEGIN MODULE: site-scanner
__( 'Site Scanner', 'it-l10n-ithemes-security-pro' );
# END MODULE: site-scanner

# BEGIN MODULE: ssl
__( 'Enforce SSL', 'it-l10n-ithemes-security-pro' );
__( 'Enforces that all connections to the website are made over SSL/TLS.', 'it-l10n-ithemes-security-pro' );
__( 'Require SSL', 'it-l10n-ithemes-security-pro' );
__( 'Redirect All HTTP Page Requests to HTTPS', 'it-l10n-ithemes-security-pro' );
__( 'Front End SSL Mode', 'it-l10n-ithemes-security-pro' );
__( 'Enables secure SSL connection for the front-end (public parts of your site). Turning this off will disable front-end SSL control, turning this on "Per Content" will place a checkbox on the edit page for all posts and pages (near the publish settings) allowing you to turn on SSL for selected pages or posts. Selecting "Whole Site" will force the whole site to use SSL.', 'it-l10n-ithemes-security-pro' );
__( 'SSL for Dashboard', 'it-l10n-ithemes-security-pro' );
__( 'Forces all dashboard access to be served only over an SSL connection.', 'it-l10n-ithemes-security-pro' );
# END MODULE: ssl

# BEGIN MODULE: strong-passwords
__( 'Strong Passwords', 'it-l10n-ithemes-security-pro' );
__( 'Force users to use strong passwords as rated by the WordPress password meter.', 'it-l10n-ithemes-security-pro' );
__( 'Strong Passwords', 'it-l10n-ithemes-security-pro' );
__( 'Force users to use strong passwords as rated by the WordPress password meter.', 'it-l10n-ithemes-security-pro' );
# END MODULE: strong-passwords

# BEGIN MODULE: sync-connect
__( 'Sync Connect', 'it-l10n-ithemes-security-pro' );
# END MODULE: sync-connect

# BEGIN MODULE: system-tweaks
__( 'System Tweaks', 'it-l10n-ithemes-security-pro' );
__( 'Make changes to the server configuration for this site.', 'it-l10n-ithemes-security-pro' );
__( 'Increase security by restricting file access and PHP execution on your server. This can help mitigate arbitrary file upload vulnerabilities from gaining complete control of your server.', 'it-l10n-ithemes-security-pro' );
__( 'Protect System Files', 'it-l10n-ithemes-security-pro' );
__( 'Prevent public access to readme.html, readme.txt, wp-config.php, install.php, wp-includes, and .htaccess. These files can give away important information on your site and serve no purpose to the public once WordPress has been successfully installed.', 'it-l10n-ithemes-security-pro' );
__( 'Disable Directory Browsing', 'it-l10n-ithemes-security-pro' );
__( 'Prevents users from seeing a list of files in a directory when no index file is present.', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP in Uploads', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP execution in the uploads directory. This blocks requests to maliciously uploaded PHP files in the uploads directory.', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP in Plugins', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP execution in the plugins directory. This blocks requests to PHP files inside plugin directories that can be exploited directly.', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP in Themes', 'it-l10n-ithemes-security-pro' );
__( 'Disable PHP execution in the themes directory. This blocks requests to PHP files inside theme directories that can be exploited directly.', 'it-l10n-ithemes-security-pro' );
__( 'File Access', 'it-l10n-ithemes-security-pro' );
__( 'PHP Execution', 'it-l10n-ithemes-security-pro' );
# END MODULE: system-tweaks

# BEGIN MODULE: two-factor
__( 'Two-Factor', 'it-l10n-ithemes-security-pro' );
__( 'Two-Factor Authentication greatly increases the security of your WordPress user account by requiring additional information beyond your username and password in order to log in.', 'it-l10n-ithemes-security-pro' );
__( 'Two-Factor authentication is a tried and true security method and will stop most automated bot attacks on the WordPress login. Once Two-Factor Authentication is enabled here, users can visit their profile to enable two-factor for their account.', 'it-l10n-ithemes-security-pro' );
__( '2fa', 'it-l10n-ithemes-security-pro' );
__( 'multi-factor', 'it-l10n-ithemes-security-pro' );
__( 'mfa', 'it-l10n-ithemes-security-pro' );
__( 'Authentication Methods Available to Users', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security supports multiple two-factor methods: mobile app, email, and backup codes. Selecting “All Methods” is highly recommended so that users can use the method that works the best for them.', 'it-l10n-ithemes-security-pro' );
__( 'All Methods (recommended)', 'it-l10n-ithemes-security-pro' );
__( 'All Except Email', 'it-l10n-ithemes-security-pro' );
__( 'Select Methods Manually', 'it-l10n-ithemes-security-pro' );
__( 'Select Available Methods', 'it-l10n-ithemes-security-pro' );
__( 'Disable on First Login', 'it-l10n-ithemes-security-pro' );
__( 'This simplifies the sign up flow for users that require two-factor to be enabled for their account.', 'it-l10n-ithemes-security-pro' );
__( 'On-board Welcome Text', 'it-l10n-ithemes-security-pro' );
__( 'Customize the text shown to users at the beginning of the Two-Factor On-Board flow.', 'it-l10n-ithemes-security-pro' );
__( 'Methods', 'it-l10n-ithemes-security-pro' );
__( 'Setup Flow', 'it-l10n-ithemes-security-pro' );
__( 'Skip Two-Factor Onboarding', 'it-l10n-ithemes-security-pro' );
__( 'By default, when a user logs in via the WordPress Login Page, iThemes Security will prompt them to setup Two-Factor. Optionally, you can skip the two-factor authentication on-boarding process for certain users. Users can still manually enroll in two-factor through their WordPress admin profile.', 'it-l10n-ithemes-security-pro' );
__( 'Application Passwords', 'it-l10n-ithemes-security-pro' );
__( 'Use Application Passwords to allow authentication without providing your actual password when using non-traditional login methods such as XML-RPC or the REST API. They can be easily revoked, and can never be used for traditional logins to your website.', 'it-l10n-ithemes-security-pro' );
# END MODULE: two-factor

# BEGIN MODULE: user-groups
__( 'User Groups', 'it-l10n-ithemes-security-pro' );
__( 'User Groups allow you to enable security features for specific sets of users.', 'it-l10n-ithemes-security-pro' );
__( 'User Groups allow you to view and manage the security settings that affect how people interact with your site. Enabling security features per group gives you the flexibility to apply the right level of security to the right people.</br>If a user belongs to multiple groups, all settings enabled in those groups will be applied to that user.', 'it-l10n-ithemes-security-pro' );
# END MODULE: user-groups

# BEGIN MODULE: wordpress-tweaks
__( 'WordPress Tweaks', 'it-l10n-ithemes-security-pro' );
__( 'Make changes to the default behavior of WordPress.', 'it-l10n-ithemes-security-pro' );
__( 'Increase the security of your website by removing the ability to edit files from the WordPress dashboard and limiting how APIs and users access your site.', 'it-l10n-ithemes-security-pro' );
__( 'Disable File Editor', 'it-l10n-ithemes-security-pro' );
__( 'Disables the WordPress file editor for plugins and themes. Once activated you will need to manually edit files using FTP or other tools.', 'it-l10n-ithemes-security-pro' );
__( 'XML-RPC', 'it-l10n-ithemes-security-pro' );
__( 'The WordPress XML-RPC API allows external services to access and modify content on the site. Common example of services that make use of XML-RPC are [the Jetpack plugin](https://jetpack.com), [the WordPress mobile apps](https://wordpress.org/mobile/), and [pingbacks](https://wpbeg.in/IiI0sh). If the site does not use a service that requires XML-RPC, select the “Disable XML-RPC” setting as disabling XML-RPC prevents attackers from using the feature to attack the site.', 'it-l10n-ithemes-security-pro' );
__( 'Disable XML-RPC', 'it-l10n-ithemes-security-pro' );
__( 'XML-RPC is disabled on the site. This setting is highly recommended if Jetpack, the WordPress mobile app, pingbacks, and other services that use XML-RPC are not used.', 'it-l10n-ithemes-security-pro' );
__( 'Disable Pingbacks', 'it-l10n-ithemes-security-pro' );
__( 'Only disable pingbacks. Other XML-RPC features will work as normal. Select this setting if you require features such as Jetpack or the WordPress Mobile app.', 'it-l10n-ithemes-security-pro' );
__( 'Enable XML-RPC', 'it-l10n-ithemes-security-pro' );
__( 'XML-RPC is fully enabled and will function as normal. Use this setting only if the site must have unrestricted use of XML-RPC.', 'it-l10n-ithemes-security-pro' );
__( 'Allow Multiple Authentication Attempts per XML-RPC Request', 'it-l10n-ithemes-security-pro' );
__( 'By default, the WordPress XML-RPC API allows hundreds of username and password guesses per request. Turn off this setting to prevent attackers from exploiting this feature.', 'it-l10n-ithemes-security-pro' );
__( 'REST API', 'it-l10n-ithemes-security-pro' );
__( 'The WordPress REST API is part of WordPress and provides developers with new ways to manage WordPress. By default, it could give public access to information that you believe is private on your site.', 'it-l10n-ithemes-security-pro' );
__( 'Default Access', 'it-l10n-ithemes-security-pro' );
__( 'Access to REST API data is left as default. Information including published posts, user details, and media library entries is available for public access.', 'it-l10n-ithemes-security-pro' );
__( 'Restricted Access', 'it-l10n-ithemes-security-pro' );
__( 'Restrict access to most REST API data. This means that most requests will require a logged in user or a user with specific privileges, blocking public requests for potentially-private data. We recommend selecting this option.', 'it-l10n-ithemes-security-pro' );
__( 'Login with Email Address or Username', 'it-l10n-ithemes-security-pro' );
__( 'By default, WordPress allows users to log in using either an email address or username. This setting allows you to restrict logins to only accept email addresses or usernames.', 'it-l10n-ithemes-security-pro' );
__( 'Email Address and Username', 'it-l10n-ithemes-security-pro' );
__( 'Allow users to log in using their user’s email address or username. This is the default WordPress behavior.', 'it-l10n-ithemes-security-pro' );
__( 'Email Address Only', 'it-l10n-ithemes-security-pro' );
__( 'Users can only log in using their user’s email address. This disables logging in using a username.', 'it-l10n-ithemes-security-pro' );
__( 'Username Only', 'it-l10n-ithemes-security-pro' );
__( 'Users can only log in using their user’s username. This disables logging in using an email address.', 'it-l10n-ithemes-security-pro' );
__( 'Force Unique Nickname', 'it-l10n-ithemes-security-pro' );
__( 'This forces users to choose a unique nickname when updating their profile or creating a new account which prevents bots and attackers from easily harvesting user’s login usernames from the code on author pages. Note this does not automatically update existing users as it will affect author feed urls if used.', 'it-l10n-ithemes-security-pro' );
__( 'Disable Extra User Archives', 'it-l10n-ithemes-security-pro' );
__( 'Disables a user’s author page if their post count is 0. This makes it harder for bots to determine usernames by disabling post archives for users that don’t write content for your site.', 'it-l10n-ithemes-security-pro' );
__( 'API Access', 'it-l10n-ithemes-security-pro' );
__( 'Users', 'it-l10n-ithemes-security-pro' );
# END MODULE: wordpress-tweaks

# BEGIN MODULE: dashboard-widget
__( 'Dashboard Widget', 'it-l10n-ithemes-security-pro' );
# END MODULE: dashboard-widget

# BEGIN MODULE: fingerprinting
__( 'Trusted Devices (Beta)', 'it-l10n-ithemes-security-pro' );
__( 'Trusted Devices identifies the devices users use to login and can apply additional restrictions to unknown devices.', 'it-l10n-ithemes-security-pro' );
__( 'Trusted Devices helps secure your website by requiring all or specific User Groups to confirm the devices they use to log in. Logins using a new device will trigger account restrictions and the Unrecognized Login email to send to the user’s email address. From the email, they can either approve the device or log it out and prevent future access.<br>A successful Session Hijacking attack allows an attacker to piggyback your login session. If a user’s device changes during a session, iThemes Security will automatically log the user out to prevent unauthorized activity on the user’s account, such as changing the user’s email address or uploading malicious plugins.', 'it-l10n-ithemes-security-pro' );
__( 'Restrict Capabilities', 'it-l10n-ithemes-security-pro' );
__( 'When a user is logged-in on an unrecognized device, restrict their administrator-level capabilities and prevent them from editing their login details. Enabling “Restrict Capabilities” requires the “[Unrecognized Login](itsec://settings/notification-center/unrecognized-login)” notification to be enabled.', 'it-l10n-ithemes-security-pro' );
__( 'Session Hijacking Protection', 'it-l10n-ithemes-security-pro' );
__( 'Help protect against session hijacking by verifying that a user’s device does not change during a session.', 'it-l10n-ithemes-security-pro' );
__( 'Enable Trusted Devices', 'it-l10n-ithemes-security-pro' );
__( 'Require users to approve logging in from new devices.', 'it-l10n-ithemes-security-pro' );
# END MODULE: fingerprinting

# BEGIN MODULE: geolocation
__( 'Geolocation', 'it-l10n-ithemes-security-pro' );
__( 'Improve Trusted Devices by connecting to an external location or mapping API.', 'it-l10n-ithemes-security-pro' );
__( 'MaxMind GeoLite2', 'it-l10n-ithemes-security-pro' );
__( 'The MaxMind Lite is a free database provided by MaxMind that allows for Geolocation lookups without connecting to an external API when geolocating IP addresses. [Sign up](https://www.maxmind.com/en/geolite2/signup) for a free MaxMind GeoLite2 account, generate a license key and enter it below.', 'it-l10n-ithemes-security-pro' );
__( 'API Key', 'it-l10n-ithemes-security-pro' );
__( 'MaxMind API', 'it-l10n-ithemes-security-pro' );
__( 'For the highest degree of accuracy, sign up for a [MaxMind GeoIP2 Precision: City](https://www.maxmind.com/en/geoip2-precision-city-service) account. Most users should find the lowest credit amount sufficient. The MaxMind API User and API Key can be found in the “Services > My License Key” section of the [account area](https://www.maxmind.com/en/account).', 'it-l10n-ithemes-security-pro' );
__( 'API User', 'it-l10n-ithemes-security-pro' );
__( 'API Key', 'it-l10n-ithemes-security-pro' );
__( 'Mapbox API Key', 'it-l10n-ithemes-security-pro' );
__( 'The MapBox Access Token can be found on the MapBox account page. Either provide the “Default public token” or create a new token with the styles:tiles scope.', 'it-l10n-ithemes-security-pro' );
__( 'MapQuest API (Consumer) Key', 'it-l10n-ithemes-security-pro' );
__( 'The MapQuest API Key can typically be found on the MapQuest Profile Page. If there is no key listed under the “My Keys” section, create a new application by clicking the “Manage Keys” button and then the “Create a New Key” button. Enter the name of your website for the “App Name” and leave the “Callback URL” blank.', 'it-l10n-ithemes-security-pro' );
__( 'Location', 'it-l10n-ithemes-security-pro' );
__( 'By default, a number of free GeoIP services are used. We strongly recommend enabling one of the MaxMind APIs for increased accuracy and reliability.', 'it-l10n-ithemes-security-pro' );
__( 'Mapping', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security uses static image maps to display the approximate location of an unrecognized login. We recommend using either the [Mapbox](https://www.mapbox.com) or [MapQuest](https://developer.mapquest.com) APIs. The free plan for both services should be sufficient for most users.', 'it-l10n-ithemes-security-pro' );
# END MODULE: geolocation

# BEGIN MODULE: import-export
__( 'Import Export', 'it-l10n-ithemes-security-pro' );
# END MODULE: import-export

# BEGIN MODULE: magic-links
__( 'Magic Links', 'it-l10n-ithemes-security-pro' );
__( 'The Magic Links bypass lockout option allows you to login while your username or IP is locked out.', 'it-l10n-ithemes-security-pro' );
# END MODULE: magic-links

# BEGIN MODULE: online-files
__( 'Online Files', 'it-l10n-ithemes-security-pro' );
# END MODULE: online-files

# BEGIN MODULE: password-expiration
__( 'Password Age', 'it-l10n-ithemes-security-pro' );
__( 'Strengthen the passwords on the site with automated password expiration.', 'it-l10n-ithemes-security-pro' );
__( 'Maximum Password Age', 'it-l10n-ithemes-security-pro' );
__( 'The maximum number of days a password may be kept before it is expired.', 'it-l10n-ithemes-security-pro' );
__( 'Force Password Change', 'it-l10n-ithemes-security-pro' );
# END MODULE: password-expiration

# BEGIN MODULE: passwordless-login
__( 'Passwordless Login', 'it-l10n-ithemes-security-pro' );
__( 'Log in without entering a password.', 'it-l10n-ithemes-security-pro' );
__( 'Passwordless login will send you an email with a magic link that will log you into WordPress with a click of a button.', 'it-l10n-ithemes-security-pro' );
__( 'Available Authentication Methods', 'it-l10n-ithemes-security-pro' );
__( 'iThemes Security supports multiple Passwordless Login methods.', 'it-l10n-ithemes-security-pro' );
__( 'Magic Link', 'it-l10n-ithemes-security-pro' );
__( 'Passkeys', 'it-l10n-ithemes-security-pro' );
__( 'Per-User Availability', 'it-l10n-ithemes-security-pro' );
__( 'By default, all users selected above will be able to use Passwordless Login. Change to “Disabled” if you prefer to have users opt-in on their individual profiles.', 'it-l10n-ithemes-security-pro' );
__( 'Enabled by Default', 'it-l10n-ithemes-security-pro' );
__( 'Disabled by Default', 'it-l10n-ithemes-security-pro' );
__( 'Passwordless Login Flow', 'it-l10n-ithemes-security-pro' );
__( 'Method First', 'it-l10n-ithemes-security-pro' );
__( 'Choose between the traditional and Passwordless Login methods before entering a username or email address.', 'it-l10n-ithemes-security-pro' );
__( 'Username First', 'it-l10n-ithemes-security-pro' );
__( 'Enter the username or email address first before selecting the login method.', 'it-l10n-ithemes-security-pro' );
__( 'Integrations', 'it-l10n-ithemes-security-pro' );
__( 'Enable', 'it-l10n-ithemes-security-pro' );
__( 'Enable Passwordless Login', 'it-l10n-ithemes-security-pro' );
__( 'Send an email with a secure link that allows users to login without entering a password.', 'it-l10n-ithemes-security-pro' );
__( 'Allow Two-Factor Bypass for Passwordless Login', 'it-l10n-ithemes-security-pro' );
__( 'Add an option to bypass two-factor authentication when using passwordless login in the WordPress user profile.', 'it-l10n-ithemes-security-pro' );
# END MODULE: passwordless-login

# BEGIN MODULE: privilege
__( 'Privilege Escalation', 'it-l10n-ithemes-security-pro' );
__( 'Allow administrators to temporarily grant extra access to a user of the site for a specified period of time.', 'it-l10n-ithemes-security-pro' );
# END MODULE: privilege

# BEGIN MODULE: pro-dashboard
__( 'Pro Dashboard', 'it-l10n-ithemes-security-pro' );
# END MODULE: pro-dashboard

# BEGIN MODULE: pro-two-factor
__( 'Vulnerable User Protection', 'it-l10n-ithemes-security-pro' );
__( 'Require user accounts that are considered vulnerable, such as having a weak password or recent brute force attacks, to use two-factor even if the account doesn’t already do so. Enabling this feature is highly recommended.', 'it-l10n-ithemes-security-pro' );
__( 'Vulnerable Site Protection', 'it-l10n-ithemes-security-pro' );
__( 'Require all users to use two-factor when logging in if the site is vulnerable, such as running outdated or software known to be vulnerable. Enabling this feature is highly recommended.', 'it-l10n-ithemes-security-pro' );
__( 'Protection', 'it-l10n-ithemes-security-pro' );
__( 'Require Two Factor', 'it-l10n-ithemes-security-pro' );
__( 'Require users in a group to use Two-Factor authentication. We highly recommended forcing any user that can make changes to the site to use two-factor authentication.', 'it-l10n-ithemes-security-pro' );
__( 'Force Two Factor', 'it-l10n-ithemes-security-pro' );
__( 'Allow Remembering Device', 'it-l10n-ithemes-security-pro' );
__( 'When logging in, users will be presented a “Remember this Device” option. If enabled, users won’t be prompted for a Two-Factor code in the next 30 days while using their current device.', 'it-l10n-ithemes-security-pro' );
# END MODULE: pro-two-factor

# BEGIN MODULE: recaptcha
__( 'CAPTCHA', 'it-l10n-ithemes-security-pro' );
__( 'Protect your site from bots by verifying that the person submitting comments or logging in is indeed human.', 'it-l10n-ithemes-security-pro' );
__( 'CAPTCHA challenges help keep bad bots from engaging in abusive activities on your website, such as attempting to break into your website using compromised passwords, posting spam, or fraudulently triggering password reset requests.<br>Legitimate users will be able to log in, make purchases, view pages, or create accounts. CAPTCHA APIs use advanced risk analysis techniques to tell humans and bots apart.', 'it-l10n-ithemes-security-pro' );
__( 'recaptcha', 'it-l10n-ithemes-security-pro' );
__( 'turnstile', 'it-l10n-ithemes-security-pro' );
__( 'hcaptcha', 'it-l10n-ithemes-security-pro' );
__( 'cloudflare', 'it-l10n-ithemes-security-pro' );
__( 'Provider', 'it-l10n-ithemes-security-pro' );
__( 'Select the CAPTCHA API you’d like to use.', 'it-l10n-ithemes-security-pro' );
__( 'Google', 'it-l10n-ithemes-security-pro' );
__( 'CloudFlare', 'it-l10n-ithemes-security-pro' );
__( 'hCaptcha', 'it-l10n-ithemes-security-pro' );
__( 'Type', 'it-l10n-ithemes-security-pro' );
__( 'Only select the type associated with the generated keys. If you are unsure which type was selected when generating the keys, you should [generate new keys](https://www.google.com/recaptcha/admin). For details about the different types, see [Google’s documentation](https://developers.google.com/recaptcha/docs/versions).', 'it-l10n-ithemes-security-pro' );
__( 'reCAPTCHA v2', 'it-l10n-ithemes-security-pro' );
__( 'Validate users with the “I’m not a robot” checkbox.', 'it-l10n-ithemes-security-pro' );
__( 'Invisible reCAPTCHA', 'it-l10n-ithemes-security-pro' );
__( 'Validate users in the background.', 'it-l10n-ithemes-security-pro' );
__( 'reCAPTCHA v3', 'it-l10n-ithemes-security-pro' );
__( 'Monitor visitors in the background on all pages.', 'it-l10n-ithemes-security-pro' );
__( 'Site Key', 'it-l10n-ithemes-security-pro' );
__( 'The generated site key from the [Google reCAPTCHA dashboard](https://www.google.com/recaptcha/admin).', 'it-l10n-ithemes-security-pro' );
__( 'Secret Key', 'it-l10n-ithemes-security-pro' );
__( 'The generated secret key from the [Google reCAPTCHA dashboard](https://www.google.com/recaptcha/admin).', 'it-l10n-ithemes-security-pro' );
__( 'Site Key', 'it-l10n-ithemes-security-pro' );
__( 'The generated site key from the [CloudFlare Turnstile dashboard](https://dash.cloudflare.com/?to=/:account/turnstile).', 'it-l10n-ithemes-security-pro' );
__( 'Secret Key', 'it-l10n-ithemes-security-pro' );
__( 'The generated secret key from the [CloudFlare Turnstile dashboard](https://dash.cloudflare.com/?to=/:account/turnstile).', 'it-l10n-ithemes-security-pro' );
__( 'Site Key', 'it-l10n-ithemes-security-pro' );
__( 'The generated site key from the [hCaptcha site dashboard](https://dashboard.hcaptcha.com/).', 'it-l10n-ithemes-security-pro' );
__( 'Secret Key', 'it-l10n-ithemes-security-pro' );
__( 'Your account’s secret key from the [hCaptcha account settings](https://dashboard.hcaptcha.com/settings).', 'it-l10n-ithemes-security-pro' );
__( 'Use on Login', 'it-l10n-ithemes-security-pro' );
__( 'Use on New User Registration', 'it-l10n-ithemes-security-pro' );
__( 'Use on Reset Password', 'it-l10n-ithemes-security-pro' );
__( 'Use on Comments', 'it-l10n-ithemes-security-pro' );
__( 'Include Script', 'it-l10n-ithemes-security-pro' );
__( 'Specify where the reCAPTCHA script should be loaded. Google recommends including the script on all pages to increase accuracy.', 'it-l10n-ithemes-security-pro' );
__( 'On All Pages', 'it-l10n-ithemes-security-pro' );
__( 'Only Required Pages', 'it-l10n-ithemes-security-pro' );
__( 'Force Language', 'it-l10n-ithemes-security-pro' );
__( 'Force the reCAPTCHA to render in the selected language.', 'it-l10n-ithemes-security-pro' );
__( 'Detect', 'it-l10n-ithemes-security-pro' );
__( 'Arabic', 'it-l10n-ithemes-security-pro' );
__( 'Afrikaans', 'it-l10n-ithemes-security-pro' );
__( 'Amharic', 'it-l10n-ithemes-security-pro' );
__( 'Armenian', 'it-l10n-ithemes-security-pro' );
__( 'Azerbaijani', 'it-l10n-ithemes-security-pro' );
__( 'Basque', 'it-l10n-ithemes-security-pro' );
__( 'Bengali', 'it-l10n-ithemes-security-pro' );
__( 'Bulgarian', 'it-l10n-ithemes-security-pro' );
__( 'Catalan', 'it-l10n-ithemes-security-pro' );
__( 'Chinese (Hong Kong)', 'it-l10n-ithemes-security-pro' );
__( 'Chinese (Simplified)', 'it-l10n-ithemes-security-pro' );
__( 'Chinese (Traditional)', 'it-l10n-ithemes-security-pro' );
__( 'Croatian', 'it-l10n-ithemes-security-pro' );
__( 'Czech', 'it-l10n-ithemes-security-pro' );
__( 'Danish', 'it-l10n-ithemes-security-pro' );
__( 'Dutch', 'it-l10n-ithemes-security-pro' );
__( 'English (UK)', 'it-l10n-ithemes-security-pro' );
__( 'English (US)', 'it-l10n-ithemes-security-pro' );
__( 'Estonian', 'it-l10n-ithemes-security-pro' );
__( 'Filipino', 'it-l10n-ithemes-security-pro' );
__( 'Finnish', 'it-l10n-ithemes-security-pro' );
__( 'French', 'it-l10n-ithemes-security-pro' );
__( 'French (Canadian)', 'it-l10n-ithemes-security-pro' );
__( 'Galician', 'it-l10n-ithemes-security-pro' );
__( 'Georgian', 'it-l10n-ithemes-security-pro' );
__( 'German', 'it-l10n-ithemes-security-pro' );
__( 'German (Austria)', 'it-l10n-ithemes-security-pro' );
__( 'German (Switzerland)', 'it-l10n-ithemes-security-pro' );
__( 'Greek', 'it-l10n-ithemes-security-pro' );
__( 'Gujarati', 'it-l10n-ithemes-security-pro' );
__( 'Hebrew', 'it-l10n-ithemes-security-pro' );
__( 'Hindi', 'it-l10n-ithemes-security-pro' );
__( 'Hungarain', 'it-l10n-ithemes-security-pro' );
__( 'Icelandic', 'it-l10n-ithemes-security-pro' );
__( 'Indonesian', 'it-l10n-ithemes-security-pro' );
__( 'Italian', 'it-l10n-ithemes-security-pro' );
__( 'Japanese', 'it-l10n-ithemes-security-pro' );
__( 'Kannada', 'it-l10n-ithemes-security-pro' );
__( 'Korean', 'it-l10n-ithemes-security-pro' );
__( 'Laothian', 'it-l10n-ithemes-security-pro' );
__( 'Latvian', 'it-l10n-ithemes-security-pro' );
__( 'Lithuanian', 'it-l10n-ithemes-security-pro' );
__( 'Malay', 'it-l10n-ithemes-security-pro' );
__( 'Malayalam', 'it-l10n-ithemes-security-pro' );
__( 'Marathi', 'it-l10n-ithemes-security-pro' );
__( 'Mongolian', 'it-l10n-ithemes-security-pro' );
__( 'Norwegian', 'it-l10n-ithemes-security-pro' );
__( 'Persian', 'it-l10n-ithemes-security-pro' );
__( 'Polish', 'it-l10n-ithemes-security-pro' );
__( 'Portuguese', 'it-l10n-ithemes-security-pro' );
__( 'Portuguese (Brazil)', 'it-l10n-ithemes-security-pro' );
__( 'Portuguese (Portugal)', 'it-l10n-ithemes-security-pro' );
__( 'Romanian', 'it-l10n-ithemes-security-pro' );
__( 'Russian', 'it-l10n-ithemes-security-pro' );
__( 'Serbian', 'it-l10n-ithemes-security-pro' );
__( 'Sinhalese', 'it-l10n-ithemes-security-pro' );
__( 'Slovak', 'it-l10n-ithemes-security-pro' );
__( 'Slovenian', 'it-l10n-ithemes-security-pro' );
__( 'Spanish', 'it-l10n-ithemes-security-pro' );
__( 'Spanish (Latin America)', 'it-l10n-ithemes-security-pro' );
__( 'Swahili', 'it-l10n-ithemes-security-pro' );
__( 'Swedish', 'it-l10n-ithemes-security-pro' );
__( 'Tamil', 'it-l10n-ithemes-security-pro' );
__( 'Telugu', 'it-l10n-ithemes-security-pro' );
__( 'Thai', 'it-l10n-ithemes-security-pro' );
__( 'Turkish', 'it-l10n-ithemes-security-pro' );
__( 'Ukrainian', 'it-l10n-ithemes-security-pro' );
__( 'Urdu', 'it-l10n-ithemes-security-pro' );
__( 'Vietnamese', 'it-l10n-ithemes-security-pro' );
__( 'Zulu', 'it-l10n-ithemes-security-pro' );
__( 'Use Dark Theme', 'it-l10n-ithemes-security-pro' );
__( 'reCAPTCHA Position', 'it-l10n-ithemes-security-pro' );
__( 'Bottom Left', 'it-l10n-ithemes-security-pro' );
__( 'Bottom Right', 'it-l10n-ithemes-security-pro' );
__( 'Widget Theme', 'it-l10n-ithemes-security-pro' );
__( 'Select the Turnstile widget style.', 'it-l10n-ithemes-security-pro' );
__( 'Use Browser Theme', 'it-l10n-ithemes-security-pro' );
__( 'Light', 'it-l10n-ithemes-security-pro' );
__( 'Dark', 'it-l10n-ithemes-security-pro' );
__( 'Widget Size', 'it-l10n-ithemes-security-pro' );
__( 'Select the Turnstile widget size.', 'it-l10n-ithemes-security-pro' );
__( 'Normal', 'it-l10n-ithemes-security-pro' );
__( 'Compact', 'it-l10n-ithemes-security-pro' );
__( 'Widget Theme', 'it-l10n-ithemes-security-pro' );
__( 'Select the hCaptcha widget style.', 'it-l10n-ithemes-security-pro' );
__( 'Light', 'it-l10n-ithemes-security-pro' );
__( 'Dark', 'it-l10n-ithemes-security-pro' );
__( 'Widget Size', 'it-l10n-ithemes-security-pro' );
__( 'Select the hCaptcha widget size.', 'it-l10n-ithemes-security-pro' );
__( 'Normal', 'it-l10n-ithemes-security-pro' );
__( 'Compact', 'it-l10n-ithemes-security-pro' );
__( 'Enable GDPR Opt-In', 'it-l10n-ithemes-security-pro' );
__( 'To assist with GDPR compliance, iThemes Security can prompt the user to accept the CAPTCHA provider’s Privacy Policy and Terms of Service before loading the challenge.', 'it-l10n-ithemes-security-pro' );
__( 'On Page Opt-in', 'it-l10n-ithemes-security-pro' );
__( 'Allow users to opt-in to CAPTCHA without refreshing the page.', 'it-l10n-ithemes-security-pro' );
__( 'Block Threshold', 'it-l10n-ithemes-security-pro' );
__( 'Google reCAPTCHA assigns a score between 0 and 1 describing the legitimacy of the request. A score of 1 is most likely a human, and a score of 0 is most likely a bot. Google recommends using a default value of 0.5 and to adjust the threshold based off the score distribution.', 'it-l10n-ithemes-security-pro' );
__( 'Lockout Error Threshold', 'it-l10n-ithemes-security-pro' );
__( 'The numbers of failed CAPTCHA entries that will trigger a lockout. Set to zero (0) to record errors without locking out users. This can be useful for troubleshooting content or other errors. The default is 7.', 'it-l10n-ithemes-security-pro' );
__( 'Lockout Check Period', 'it-l10n-ithemes-security-pro' );
__( 'How long the plugin will remember a bad CAPTCHA entry and count it towards a lockout.', 'it-l10n-ithemes-security-pro' );
__( 'API Keys', 'it-l10n-ithemes-security-pro' );
__( 'Protected Actions', 'it-l10n-ithemes-security-pro' );
__( 'Appearance', 'it-l10n-ithemes-security-pro' );
__( 'Lockout', 'it-l10n-ithemes-security-pro' );
# END MODULE: recaptcha

# BEGIN MODULE: user-logging
__( 'User Logging', 'it-l10n-ithemes-security-pro' );
__( 'Log user actions such as logging in, saving content and making changes to the site’s software.', 'it-l10n-ithemes-security-pro' );
__( 'Activity Monitoring', 'it-l10n-ithemes-security-pro' );
__( 'Track when a user logs in, saves content, and makes changes to the site’s software.', 'it-l10n-ithemes-security-pro' );
# END MODULE: user-logging

# BEGIN MODULE: user-security-check
__( 'User Security Check', 'it-l10n-ithemes-security-pro' );
__( 'Every user on your site affects overall security. See how your users might be affecting your security and take action when needed.', 'it-l10n-ithemes-security-pro' );
# END MODULE: user-security-check

# BEGIN MODULE: version-management
__( 'Version Management', 'it-l10n-ithemes-security-pro' );
__( 'Protect your site when outdated software is not updated quickly enough.', 'it-l10n-ithemes-security-pro' );
__( 'Even the strongest security measures will fail if you are running vulnerable software on your website. These settings help protect your site with options to update to new versions automatically or increase user security when the site’s software is outdated.', 'it-l10n-ithemes-security-pro' );
__( 'WordPress Updates', 'it-l10n-ithemes-security-pro' );
__( 'Automatically install the latest WordPress release. This should be enabled unless you actively maintain this site on a daily basis and install the updates manually shortly after they are released.', 'it-l10n-ithemes-security-pro' );
__( 'Plugin Updates', 'it-l10n-ithemes-security-pro' );
__( 'Automatically install the latest plugin updates. Enabling this setting will disable the WordPress auto-update plugins feature to prevent conflicts.', 'it-l10n-ithemes-security-pro' );
__( 'Custom', 'it-l10n-ithemes-security-pro' );
__( 'All', 'it-l10n-ithemes-security-pro' );
__( 'Theme Updates', 'it-l10n-ithemes-security-pro' );
__( 'Automatically install the latest theme updates. Enabling this setting will disable the WordPress auto-update themes feature to prevent conflicts.', 'it-l10n-ithemes-security-pro' );
__( 'Custom', 'it-l10n-ithemes-security-pro' );
__( 'All', 'it-l10n-ithemes-security-pro' );
__( 'Enabled', 'it-l10n-ithemes-security-pro' );
__( 'Delay', 'it-l10n-ithemes-security-pro' );
__( 'Disabled', 'it-l10n-ithemes-security-pro' );
__( 'Scan For Old WordPress Sites', 'it-l10n-ithemes-security-pro' );
__( 'Run a daily scan of the hosting account for old WordPress sites that could allow an attacker to compromise the server.', 'it-l10n-ithemes-security-pro' );
__( 'Auto Update If Fixes Vulnerability', 'it-l10n-ithemes-security-pro' );
__( 'Automatically update a plugin or theme if it fixes a vulnerability that was found by the Site Scanner.', 'it-l10n-ithemes-security-pro' );
__( 'WordPress', 'it-l10n-ithemes-security-pro' );
__( 'Plugins', 'it-l10n-ithemes-security-pro' );
__( 'Themes', 'it-l10n-ithemes-security-pro' );
__( 'Protection', 'it-l10n-ithemes-security-pro' );
# END MODULE: version-management

# BEGIN MODULE: webauthn
__( 'Passkeys', 'it-l10n-ithemes-security-pro' );
__( 'Allow users to login with biometrics like Face ID, Touch ID, Windows Hello, WebAuthn or any passkey their device supports. Enable Passwordless Login to check it out.', 'it-l10n-ithemes-security-pro' );
__( 'web authentication', 'it-l10n-ithemes-security-pro' );
__( 'fido', 'it-l10n-ithemes-security-pro' );
__( 'u2f', 'it-l10n-ithemes-security-pro' );
__( 'faceid', 'it-l10n-ithemes-security-pro' );
__( 'touchid', 'it-l10n-ithemes-security-pro' );
__( 'webauthn', 'it-l10n-ithemes-security-pro' );
# END MODULE: webauthn