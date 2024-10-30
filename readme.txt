=== Plugin Name ===
Plugin Name: login_register
Plugin URI: http://www.frankkoenen.com/2011/06/wordpress-plugin-login-register/
Description: Provide replacement User Interface for Login, Register and Lost Password with option settings.
Version: 1.2.0
Author: Frank Koenen
Author URI: http://frankkoenen.com
License: GPL2
Contributors: frank.wordpressplugins@feweb.net
Donate link: http://www.frankkoenen.com/2011/06/wordpress-plugin-login-register/
Tags: authentication, login, password, password reset, register, registration, invite, captcha, password recovery, expire, password, expirary, login_register, login-register, login/register, registration invitation, ajax
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 1.2.0

Provides a replacement for login and register and lost password and provides feature for password expiration and optional invitation codes.

== Description ==

This plugin provides a complete replacement for the standard WP login, registration and lost password interfaces.
Provides a password recovery system, adds password expired feature, integrates with Really Simple Captcha and provides optional invitation code to register.

Simply download and install the plugin, activate it.

Customizable to integrate with LDAP-LPRM plugin ( <a href="http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/">http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/</a>).

Captcha integration with Really Simple CAPTCHA ( <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">http://wordpress.org/extend/plugins/really-simple-captcha/</a> )

Configurations include assigning page or post ID to provide content as wrapper to the login, registration and lost password forms.

Configure one or more invitations codes to force new regristraints to supply the code to register.

== Features ==

* CSS ready for 100% customization to customize the cleaned forms for integration with your theme and the rest of your site.
* Include or exclude active theme Javascript and CSS resources.
* Replacement Login form.
* Replacement for the Registration form.
* Replacement for the Lost Password form.
* Uses the standard WP Password recovery interface.
* Optionally enable password expirary management.
* Optionally enable registration invitation code verification.
* Full system logging via syslog.
* Fully customizable expired password reset and password recovery and user registration procedures.
* All form control pages are customizable via blog-posts; assign posts for display with the specific forms as desired.
* Can be AJAX enabled to embed the forms on any page, using shortcodes

== Installation ==

Dowload the plugin to your WP site.
Enabled the plugin.
All configurations are optional at Settings >> Login/Register Settings

== Frequently Asked Questions ==

= Can I customize the look and feel of the forms? =

Yes. The HTML in the forms is designed for easy CSS selection and isolation of styling as needed. All the forms pull in the active theme header and footer.

= How does the plugin handle password recovery? =

The password recovery feature uses the WP built in "user_activation_key" system to generate a key based off the users email address, then routes an email with URL click to recover the password at the WP site. By default, the plugin will generate a new password for the user at this point, or will call a custom password recovery procedure, see login_register_resetpassword.php_sample file provided in the plugin folder.

= Can I provide my own email sending code? =

Yes. If your hosting service has restrictions on routing outbound emails, which is needed for the password recovery feature, this plug in allows you to enable your own PHP logic to send emails. See the login_register_mailer.php_sample file provided in the plugin folder.

= Does the plugin supercede the base WP login? =

Yes. But only the user interface portions of login. If you have other plugins that handle login authentication, these plugins will remain in effect. See also the LDAP-LPRM plugin <a href="http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/">http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/</a>

= Can I embed the user login and registration forms into my theme? =

Yes. Since you control the HTML content of these forms, you can easily model the pages to bare bones content, then use some JQuery and Iframe techniques to embed the forms on any portion of your theme.

= I installed the plugin, but I'm not seeing the register links. What am I forgetting? =

The plugin will follow the setting for "Membership" registration found in the Settings >> General section of the admin console. Be sure to enable "Anyone can register" to enable the registration aspects of this plugin.

= How do I embedd my form using AJAX? =

See the sample code in the plugin named README.ajax.sample.txt

== Changelog ==

= 1.0 =
* June-2011, Initial release

= 1.0.1 =
* June-2011, Patched include path errors.

= 1.0.2 =
* June-2011, patches to home pages references.

= 1.0.3 =
* June-2011, added default activation to ensure settings.

= 1.0.4 =
* June-2011, patches to ensure $redirect_to is honored on login.

= 1.0.5 =
* June-2011, added AJAX login form embedding code.

= 1.0.6 =
* June-2011, patches to expired password, password reset in AJAX realm.

= 1.0.7 =
* Dec-2011, patches to get-theme header, left login only showing '__BODY_CONTENT__'

= 1.2.0 =
* Mar-2012, added shortcode for AJAX support async login.
