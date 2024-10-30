<?php
# File: login_register_admin.php
# vim: nowrapscan ic
#
# Purpose: Admin console for Login, Register.
#
# History:
# 11-May-11 fhk; Init
#--------------------------------------------------

# If admin options update ..
if ( $_POST['operation'] == 'saveit' ) {
  update_option('login_register_logout_post', trim($_POST['login_register_logout_post']));
  update_option('login_register_login_post', trim($_POST['login_register_login_post']));
  update_option('login_register_lostpassword_post', trim($_POST['login_register_lostpassword_post']));
  update_option('login_register_resetpassword_post', trim($_POST['login_register_resetpassword_post']));
  update_option('login_register_tell_admin_when_password_recovered', trim($_POST['login_register_tell_admin_when_password_recovered']));
  update_option('login_register_pullcssandjavascriptintoview', trim($_POST['login_register_pullcssandjavascriptintoview']));
  update_option('login_register_ajaxonlyfeatures', trim($_POST['login_register_ajaxonlyfeatures']));
  update_option('login_register_emailislogin', trim($_POST['login_register_emailislogin']));
  update_option('login_register_minpassword_length', trim($_POST['login_register_minpassword_length']));
  update_option('login_register_userregistration', trim($_POST['login_register_userregistration']));
  update_option('login_register_userregistration_post', trim($_POST['login_register_userregistration_post']));
  update_option('login_register_expiredpassword_errorcode', trim($_POST['login_register_expiredpassword_errorcode']));
  update_option('login_register_invitation_codes', trim($_POST['login_register_invitation_codes']));
  $message = '<span id="login_register_admin_menu_message" style="font-weight:bold;display:block;padding:5px;color:green">Update completed.</span>';
}

# Load settings, etc
$login_register_logout_post = get_option('login_register_logout_post');
$login_register_login_post = get_option('login_register_login_post');
$login_register_lostpassword_post = get_option('login_register_lostpassword_post');
$login_register_resetpassword_post = get_option('login_register_resetpassword_post');
$login_register_userregistration = get_option('login_register_userregistration');
$login_register_userregistration_post = get_option('login_register_userregistration_post');
$login_register_expiredpassword_errorcode = get_option('login_register_expiredpassword_errorcode');
$login_register_tell_admin_when_password_recovered = get_option('login_register_tell_admin_when_password_recovered','yes');
$login_register_pullcssandjavascriptintoview = get_option('login_register_pullcssandjavascriptintoview','yes');
$login_register_ajaxonlyfeatures = get_option('login_register_ajaxonlyfeatures','yes');
$login_register_emailislogin = get_option('login_register_emailislogin','no');
$login_register_minpassword_length = get_option('login_register_minpassword_length','7');
$login_register_invitation_codes = get_option('login_register_invitation_codes','');

?>
<html>
<head>
</head>
<body>

<div class="banner"><h1>Login, Register Settings</h1></div>

<?php
if ( ! class_exists('ReallySimpleCaptcha') ) { ?>
<div style="color:red">
Be sure to enable Captcha on your control forms with Really Simple CAPTCHA plugin ( <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">http://wordpress.org/extend/plugins/really-simple-captcha/</a> )
</div>
<?php } else { ?>
<div style="color:green">
* Really Simple CAPTCHA plugin is active and will be used on the registration page.
</div>
<?php
}
?>

<div onclick="try{ document.getElementById('login_register_admin_menu_message').style.display='none';}catch(e){}">
<form style="display::inline;" method="post">
<div style="border:1px solid black;display:block;padding:5px;margin:5px">
<p>

  <label for="login_register_login_post" style="font-weight:bold">LOGIN Page Wrapper ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_login_post" id="login_register_login_post" type="text" value="<?php echo $login_register_login_post; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the login page, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it.</span><br />
  <br />

  <label for="login_register_logout_post" style="font-weight:bold">LOGOUT Page Wrapper ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_logout_post" id="login_register_logout_post" type="text" value="<?php echo $login_register_logout_post; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the logout page, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it.</span><br />
  <br />

  <label for="login_register_lostpassword_post" style="font-weight:bold">LOSTPASSWORD Page Wrapper ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_lostpassword_post" id="login_register_lostpassword_post" type="text" value="<?php echo $login_register_lostpassword_post; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the lost password page, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it.</span><br />
  <br />

  <label for="login_register_userregistration" style="font-weight:bold">USER REGISTRATION Page Wrapper ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_userregistration" id="login_register_userregistration" type="text" value="<?php echo $login_register_userregistration; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the user registration page, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it.</span><br />
  <br />

  <label for="login_register_userregistration_post" style="font-weight:bold">USER REGISTRATION Page Wrapper (post) ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_userregistration_post" id="login_register_userregistration_post" type="text" value="<?php echo $login_register_userregistration_post; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the user registration page upon successful registration, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it.</span><br />
  <br />

  <label for="login_register_resetpassword_post" style="font-weight:bold">RESET Password Page Wrapper ID:</label><br />
  <span style="color:green">http://<?php echo $_SERVER['HTTP_HOST']; ?>/?p=</span><input name="login_register_resetpassword_post" id="login_register_resetpassword_post" type="text" value="<?php echo $login_register_resetpassword_post; ?>" size="10" /><br />
  <span>Enter the post or page ID to be used as a wrapper for the reset password page, the token {LOGINREGISTER_CONTENT} will be expanded to the control data. Leave this field blank or set to 0 to disable using a page/post wrapper. The identified page or post does not need to be publish publically for this plugin to utilize it. See also the <i>login_register_resetpassword.php_sample file</i> file provided in the plugin folder for optional customized control of the password reset control.</span><br />
  <br /><hr>

  <label for="login_register_tell_admin_when_password_recovered" style="font-weight:bold">Email to admin when a user password is reset:</label><br />
  <input name="login_register_tell_admin_when_password_recovered" id="login_register_tell_admin_when_password_recovered" value="yes" type="checkbox" <?php echo ( ( $login_register_tell_admin_when_password_recovered.'x' == 'yesx' ) ? 'checked="checked"' : "" ); ?>" /><br />
  <span>When a user resets a password, send an email to the admin account of this site. See also the <i>login_register_mailer.php_sample</i> file provided in the plugin folder for optional customized control of email sending.</span><br />
  <br /><hr>

  <label for="login_register_pullcssandjavascriptintoview" style="font-weight:bold">Pull CSS and Javascript in to control pages from Active Theme:</label><br />
  <input name="login_register_pullcssandjavascriptintoview" id="login_register_pullcssandjavascriptintoview" value="yes" type="checkbox" <?php echo ( ( $login_register_pullcssandjavascriptintoview.'x' == 'yesx' ) ? 'checked="checked"' : "" ); ?>" /><br />
  <span>The control pages are standalone CSS and HTML content. Enable this to pull CSS and Javascript resources in to these control pages. This feature works by calling <i>bloginfo('stylesheet_url')</i> and <i>wp_head()</i> functions.</span><br />
  <br /><hr>

  <label for="login_register_emailislogin" style="font-weight:bold">Treat the Email address one in the same as the User Login name:</label><br />
  <input name="login_register_emailislogin" id="login_register_emailislogin" value="yes" type="checkbox" <?php echo ( ( $login_register_emailislogin.'x' == 'yesx' ) ? 'checked="checked"' : "" ); ?>" /><br />
  <span>Typically the user login name is a unique way to identify a user, as well so is the user email address. Enable this to engage logic to work on the idea the usernames and email addresses are one in the same, and use only the email address to identify users.</span><br />
  <br /><hr>

  <label for="login_register_minpassword_length" style="font-weight:bold">Minimum password length:</label><br />
  <input name="login_register_minpassword_length" id="login_register_minpassword_length" type="text" value="<?php echo $login_register_minpassword_length; ?>" size="5" /><br />
  <span>Enter the integer value (>=7 and <=30) that is the minimum length for generating/reseting user passwords.</span><br />
  <br /><hr>

  <label for="login_register_expiredpassword_errorcode" style="font-weight:bold">Expired Password Error Code:</label> &nbsp; <span style="color:green"><?php echo ( $login_register_expiredpassword_errorcode.'x' != 'x' && file_exists( WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php' ) ) ? '(currently enabled)' : '<span style="color:black">(currently disabled)</span>'; ?></span><br />
  <input name="login_register_expiredpassword_errorcode" id="login_register_expiredpassword_errorcode" type="text" value="<?php echo $login_register_expiredpassword_errorcode; ?>" size="40" /><br />
  <span>Enter the string or integer error code to engage expired password management interface. Leave empty to disable expired password management. When a code is provided here, the file <i>login_register_expiredpassword.php</i> will be engaged to augment the login form with expired password form controls. See the <i>login_register_expiredpassword.php_sample</i> file provided in the plugin folder for example setup of this feature.  Reference also the LDAP-LPRM plugin <a href="http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/">http://wordpress.org/extend/plugins/ldap-login-password-and-role-manager/</a> for an example login module that has password expiration management built-in and can be easily integrated with this plugin.</span><br />
  <br /><hr>

  <label for="login_register_ajaxonlyfeatures" style="font-weight:bold">AJAX only:</label><br />
  <input name="login_register_ajaxonlyfeatures" id="login_register_ajaxonlyfeatures" value="yes" type="checkbox" <?php echo ( ( $login_register_ajaxonlyfeatures.'x' == 'yesx' ) ? 'checked="checked"' : "" ); ?>" /><br />
  <span>This plugin will engage when the URL path is <i>/wp-login.php</i>, or </i>/wp-register.php</i>. To disable this and only use the AJAX features, enabled this option. This will disengage the logic on the standard login and register URLs.</span><br />
  <br /><hr>

  <label for="login_register_invitation_codes" style="font-weight:bold">Invitation Codes:</label> &nbsp; <span style="color:green"><?php echo ( $login_register_invitation_codes.'x' != 'x' ) ? '(currently enabled)' : '<span style="color:black">(currently disabled)</span>'; ?></span><br />
  <textarea name="login_register_invitation_codes" id="login_register_invitation_codes" style="width:400px"><?php echo $login_register_invitation_codes; ?></textarea><br />
  <span>For the purpose of registration, provide single words or short sentances (less than 100 characters each), one invitation code per line. New user registration will require to enter one of these codes to register. Leave this field blank to disable this feature.</span><br />
  <br /><hr>

</p>
<input type="hidden" name="operation" value="saveit" />
<input type="submit" name="button_submit" value="<?php _e('Update Options', 'login_register_admin'); ?>" />
</form>
<?php echo $message; ?>

</div>
</div>

<div class="help">
Use the shortcode <b>[login_register]</b> in any page or post to embed the login form in any page directly. Also with attributes [login_register style="" class="" title=""]
</div>

</body>
</html>
