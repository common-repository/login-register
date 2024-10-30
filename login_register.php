<?php
/*
Plugin Name: login_register
Plugin URI: http://www.frankkoenen.com/2011/06/wordpress-plugin-login-register/
Description: Login and Registration.
Author: Frank Koenen
Version: 1.2.0
Author URI: http://frankkoenen.com
*/

define('LOGIN_REGISTER_ENABLED', true);

add_action('wp_ajax_nopriv_login_register_dologin', 'login_register_ajax_dologin');
add_action('wp_ajax_login_register_dologin', 'login_register_ajax_dologin');
add_action('wp_ajax_nopriv_login_register_doregister', 'login_register_ajax_doregister');
add_action('wp_ajax_login_register_doregister', 'login_register_ajax_doregister');
add_action('wp_ajax_nopriv_login_register_dolostpassword', 'login_register_ajax_dolostpassword');
add_action('wp_ajax_login_register_dolostpassword', 'login_register_ajax_dolostpassword');
add_action('init', array('login_register','Init'), 99);
add_action('admin_menu','login_register_admin_menu_action');
add_action('wp_head', 'login_register_head');
add_shortcode('login_register','login_register_shortcode');
add_filter('widget_text', 'do_shortcode');
add_filter('plugin_action_links','login_register_action_links', 10, 2 );
register_activation_hook(__FILE__,'login_register_activate');

define('LOGIN_REGISTER_LOCALFILE', 'local.php');

define('LOGIN_REGISTER_ISAJAXCALL', ( $_GET['action'].'x' != 'x' && $_SERVER['PHP_SELF'].'x' == '/wp-admin/admin-ajax.phpx' ) );

if ( (int)LOGIN_REGISTER_ISAJAXCALL == 1 ) {
  $lf = WP_PLUGIN_DIR . '/login-register/' . LOGIN_REGISTER_LOCALFILE;
  if ( file_exists( $lf ) ) require_once $lf;
}

function login_register_activate() {
  $login_register_emailislogin = get_option('login_register_emailislogin','no');
  $login_register_expiredpassword_errorcode = get_option('login_register_expiredpassword_errorcode','');
  $login_register_login_post = get_option('login_register_login_post',0);
  $login_register_logout_post = get_option('login_register_logout_post',0);
  $login_register_lostpassword_post = get_option('login_register_lostpassword_post',0);
  $login_register_minpassword_length = get_option('login_register_minpassword_length',7);
  $login_register_pullcssandjavascriptintoview = get_option('login_register_pullcssandjavascriptintoview','yes');
  $login_register_ajaxonlyfeatures = get_option('login_register_ajaxonlyfeatures','no');
  $login_register_resetpassword_post = get_option('login_register_resetpassword_post',0);
  $login_register_tell_admin_when_password_recovered = get_option('login_register_tell_admin_when_password_recovered','yes');
  $login_register_userregistration = get_option('login_register_userregistration',0);
  $login_register_userregistration_post = get_option('login_register_userregistration_post',0);
  $login_register_invitation_codes = get_option('login_register_invitation_codes','');

  update_option('login_register_emailislogin', $login_register_emailislogin );
  update_option('login_register_expiredpassword_errorcode', $login_register_expiredpassword_errorcode );
  update_option('login_register_login_post', $login_register_login_post );
  update_option('login_register_logout_post', $login_register_logout_post );
  update_option('login_register_lostpassword_post', $login_register_lostpassword_post );
  update_option('login_register_minpassword_length', $login_register_minpassword_length );
  update_option('login_register_pullcssandjavascriptintoview', $login_register_pullcssandjavascriptintoview );
  update_option('login_register_ajaxonlyfeatures', $login_register_ajaxonlyfeatures );
  update_option('login_register_resetpassword_post', $login_register_resetpassword_post );
  update_option('login_register_tell_admin_when_password_recovered', $login_register_tell_admin_when_password_recovered );
  update_option('login_register_userregistration', $login_register_userregistration );
  update_option('login_register_userregistration_post', $login_register_userregistration_post );
  update_option('login_register_invitation_codes', $login_register_invitation_codes );
}

function login_register_action_links($links, $file) {
  if ( $file != 'login-register/login_register.php' ) return $links;
  $settings_link = '<a href="' . get_option('siteurl') . '/wp-admin/options-general.php?page=login_register_admin">' . esc_html( __( 'Settings') ) . '</a>';
  array_unshift( $links, $settings_link );
  return $links;
}

function login_register_admin_menu_action() {
  add_options_page('Login and Register', 'Login/Register', 10, 'login_register_admin', 'login_register_admin_menu');
}

function login_register_head() {
  wp_enqueue_script('jqueryui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.js');
  wp_enqueue_style('jqueryui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css');
}

function login_register_admin_menu() {
  require_once WP_PLUGIN_DIR . '/login-register/login_register_admin.php';
}

class login_register {
  public static function Init() {
    global $pagenow,$login_register_object;

    $login_register_ajaxonlyfeatures = get_option('login_register_ajaxonlyfeatures','yes');
    if ( $login_register_ajaxonlyfeatures.'x' == 'yesx' ) return;

    switch ($pagenow) {
      case 'wp-login.php':
      if ($_REQUEST['action'] == 'register') {
        wp_redirect('wp-register.php');
        return;
      } else {
        $login_register_object = new login_register_object();
        $login_register_object->do_login();
      }
      break;

      case 'wp-register.php':
        $login_register_object = new login_register_object();
        $login_register_object->do_register();
      break;

    }

  }
}

# This object is instantiated in global space as $login_register_object
class login_register_object {

  # called in place of the wp-login.php page ...
  public function do_login() {
    global $wpdb;

    switch($_REQUEST["action"]) {

      case "logout": # logout
        $redirect_to = '/wp-login.php?login_register_trigger=loggedout';
        if ( isset($_REQUEST['redirect_to']) ) $redirect_to = $_REQUEST['redirect_to'];
        $current_user = wp_get_current_user();
        login_register_object::logger( array('message' => 'logout user: '. $current_user->user_login . ' redirect to: ' . $redirect_to) );
        wp_clearcookie();
        do_action('wp_logout');
        nocache_headers();
        wp_redirect($redirect_to);
        exit;
        break;

      case 'lostpassword': # lost password
        do_action('lost_password');
        ob_start();
        ?>

        <div id="login_register_div" class="login_register_div lostpassword">
          <p><?php _e('Please enter your information here. We will send you a new password.') ?></p>
          <?php if ($error) echo '<div id="login_register_login_error">'.$error.'</div>'; ?>
          <form id="login_register_passwordreset" name="login_register_passwordreset" action="/wp-login.php?action=retrievelostpassword" method="post">
            <p>
              <label for="email"><?php _e('E-mail:') ?></label>
              <input type="text" name="email" id="email" value="" tabindex="1" />
            </p>
            <?php
               if ( class_exists('ReallySimpleCaptcha') ) {
                 $captcha_instance = new ReallySimpleCaptcha();
                 $captcha_instance->bg = array(230, 230, 230);
                 $captcha_instance->fg = array(13, 13, 13);
                 $captchaword = $captcha_instance->generate_random_word();
                 $captcha_prefix = mt_rand();
                 echo '<img src="/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image($captcha_prefix, $captchaword) . '" /><input type="hidden" name="captcha_prefix" id="captcha_prefix" value="' . $captcha_prefix . '" />';
            ?>
            <p>
              <label for="simple_captcha"><?php _e('Enter Code:') ?></label>
              <input type="text" name="simple_captcha" id="simple_captcha" maxlength="20" value="" />
            </p>
                 <?php
               }
            ?>
            <p>
              <input type="button" name="login_register_lostpassword_submitbutton" id="login_register_lostpassword_submitbutton" value="<?php _e('Retrieve Password'); ?>" onclick="if(this.form.email.value.replace(/\s/,'').length>0){this.value=' ... processing ... ';this.disabled=true;document.getElementById('login_register_div').style.cursor='wait';this.form.submit()}" tabindex="2" />
            </p>
          </form>
          <ul>
            <?php if (get_settings('users_can_register')) { ?><li><a href="/wp-register.php"><?php _e('Register') ?></a></li><?php } ?>
            <li><a href="/wp-login.php"><?php _e('Login') ?></a></li>
          </ul>
        </div>

        <?php
        $html = ob_get_contents(); ob_end_clean();

        $login_register_lostpassword_post = get_option("login_register_lostpassword_post");
        if ( (int)$login_register_lostpassword_post > 0 ) {
          $postcontent = wp_get_single_post( (int)$login_register_lostpassword_post, ARRAY_A);
          $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
        }

        echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );

        exit;
        break;

      case 'retrievelostpassword': # retrieve lost password

        $captchaok = true;
        if ( class_exists('ReallySimpleCaptcha') ) {
          $captcha_instance = new ReallySimpleCaptcha();
          $captchaok = $captcha_correct = $captcha_instance->check($_POST['captcha_prefix'], trim($_POST['simple_captcha']));
          $captcha_instance->remove($captcha_prefix);
        }

        if ( $captchaok ) {
          $user_data = get_userdatabylogin($_POST['email']);
          if ( is_object($user_data) ) {
            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;
          }

          $user_emailok = ( ! $user_email || $user_email != $_POST['email'] ) ? false : true;

          if ( $user_emailok ) {
         
            do_action('retreive_password', $user_login); # Misspelled and deprecated.
            do_action('retrieve_password', $user_login);
         
            $key = substr( md5( uniqid( microtime() ) ), 0, 50);
            $wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$key' WHERE user_login = '$user_login'");
            $message  = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
            $message .= get_option('siteurl') . "\r\n\r\n";
            $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
            $message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
            $message .= get_settings('siteurl') . "/wp-login.php?action=resetpass&key=$key\r\n";
         
            if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_mailer.php') ) require WP_PLUGIN_DIR . '/login-register/login_register_mailer.php';
            $o = null; if ( class_exists( 'login_register_mailer' ) ) $o = new login_register_mailer();
            if ( $o != null && method_exists ( $o , 'sendmail' ) )
              $m = $o->sendmail($user_email, sprintf(__('[%s] Password Reset'), get_settings('blogname')), $message);
            else
              $m = wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_settings('blogname')), $message);

          }

        }
         
        ob_start();
        echo '<div id="login_register_div" class="login_register_div lostpassword">';
        if ( ! $captchaok ) {
          echo '<p>' . __('Captcha error.') . '<br /></p>';
        } elseif ( ! $user_emailok ) {
          echo '<p>' . __('Please enter a valid email address.') . '<br /></p>';
        } elseif ($m == false) {
          echo '<p>' . __('The e-mail could not be sent.') . '<br />';
          echo __('Possible reason: your host may have disabled the mail() function...') . '</p>';
        } else {
          echo '<p>' . sprintf(__('The e-mail was sent successfully to %s\'s e-mail address. Please check the email message for your instructions to recover your password.'), $user_login) . '<br /></p>';
        } ?>
        <ul>
          <li><a href="/wp-login.php?action=lostpassword" title="<?php _e('Lost Password') ?>"><?php _e('Lost your password?') ?></a></li>
          <?php if (get_settings('users_can_register')) { ?><li><a href="/wp-register.php"><?php _e('Register') ?></a></li><?php } ?>
        </ul><?php
        echo '</div>';
        
        $html = ob_get_contents(); ob_end_clean();
        
        $login_register_lostpassword_post = get_option("login_register_lostpassword_post");
        if ( (int)$login_register_lostpassword_post > 0 ) {
          $postcontent = wp_get_single_post( (int)$login_register_lostpassword_post, ARRAY_A);
          $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
        }
        
        echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );
        
        login_register_object::logger( array('message' => 'retrievelostpassword user: '. $user_login ) );
        
        exit;
        break;

      case 'resetpass': # reset password

        ob_start();

        echo '<div id="login_register_div" class="login_register_div resetpassword">';

        $key = preg_replace('/a-z0-9/i', '', $_GET['key']);

        $err = false;

        if ( ! $err ) {
          if ( empty($key) )
          {
            _e('Sorry, that key does not appear to be valid.');
            echo '<a href="/wp-login.php?action=lostpassword" title="' . __('Lost Password') . '"><' . __('Lost your password?') . '</a>';
            $err = true;
          }
        }

        if ( ! $err ) {
          $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key = '$key' AND user_activation_key <> ''");
          if ( ! $user ) {
            _e('Sorry, that key does not appear to be valid.');
            echo '<a href="/wp-login.php?action=lostpassword" title="' . __('Lost Password') . '"><' . __('Lost your password?') . '</a>';
            $err = true;
          }
        }

        if ( ! $err ) {

          login_register_object::logger( array('message' => 'resetpass user: '. $user->user_login ) );

          $pwlength = get_option("login_register_minpassword_length","7");
          if ( $pwlength < 7 ) $pwlength = 7; else if ( $pwlength > 30 ) $pwlength = 30;

          if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_resetpassword.php') ) require WP_PLUGIN_DIR . '/login-register/login_register_resetpassword.php';
          $o = null; if ( class_exists( 'login_register_resetpassword' ) ) $o = new login_register_resetpassword();
          if ( $o != null && method_exists ( $o , 'recoverpassword' ) ) {

            $o->recoverpassword($user->user_login,$key,$pwlength);

          } else {
  
            $new_pass = substr( md5( uniqid( microtime() ) ), 0, $pwlength);
            $wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$new_pass'), user_activation_key = '' WHERE user_login = '" . $user->user_login . "'");
            wp_cache_delete($user->ID, 'users');
            wp_cache_delete($user->user_login, 'userlogins');
            $message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
            $message .= sprintf(__('Password: %s'), $new_pass) . "\r\n";
            $message .= get_settings('siteurl') . "/wp-login.php\r\n";

            if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_mailer.php') ) require WP_PLUGIN_DIR . '/login-register/login_register_mailer.php';
            $o = null; if ( class_exists( 'login_register_mailer' ) ) $o = new login_register_mailer();
            if ( $o != null && method_exists ( $o , 'sendmail' ) )
              $m = $o->sendmail($user->user_email, sprintf(__('[%s] Your new password'), get_settings('blogname')), $message);
            else
              $m = wp_mail($user->user_email, sprintf(__('[%s] Your new password'), get_settings('blogname')), $message);

            if ($m == false) {
              echo '<p>' . __('The e-mail could not be sent.') . "<br />\n";
              echo __('Possible reason: your host may have disabled the mail() function...') . '</p>';
            } else {
              echo '<p>' . sprintf(__('Your new password is in the mail.'), $user_login) . '<br />';
              echo "<a href='/wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
            }

          }

          if ( $m == true && get_option("login_register_tell_admin_when_password_recovered").'x' == 'yesx' ) {
            # send a copy of password change notification to the admin
            $message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
            if ( $o != null && method_exists ( $o , 'sendmail' ) )
              $o->sendmail( get_settings('admin_email') , sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), $message);
            else
              wp_mail(get_settings('admin_email'), sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), $message);
          }

        }

        echo "</div>";

        $html = ob_get_contents(); ob_end_clean();

        $login_register_resetpassword_post = get_option("login_register_resetpassword_post");
        if ( (int)$login_register_resetpassword_post > 0 ) {
          $postcontent = wp_get_single_post( (int)$login_register_resetpassword_post, ARRAY_A);
          $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
        }

        echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );

        exit;
        break;

      case 'login': # login and default action
      default:
        $user_login = '';
        $user_pass = '';
        $using_cookie = false;
        if ( ! isset( $_REQUEST['redirect_to'] ) ) $redirect_to = ( $_POST['redirect_to'].'x' != 'x' ) ? $_POST['redirect_to'] : get_option('siteurl') . '/wp-admin/'; else $redirect_to = $_REQUEST['redirect_to'];

        if( $_POST ) {
          $user_login = ( get_option('login_register_emailislogin').'x' != 'yesx' ) ? trim($_POST['user_login']) : ( ( trim($_POST['user_email']).'x' != 'adminx' ) ? sanitize_email(trim($_POST['user_email'])) : trim($_POST['user_email']) );
          $user_login = sanitize_user( $user_login );
          $user_pass  = $_POST['login_password'];
          $rememberme = $_POST['rememberme'];
        } else {
          if (function_exists('wp_get_cookie_login')) # This check was added in version 1.0 to make the plugin compatible with WP2.0.1
          {
            $cookie_login = wp_get_cookie_login();
            if ( ! empty($cookie_login) ) {
              $using_cookie = true;
              $user_login = $cookie_login['login'];
              $user_pass = $cookie_login['password'];
            }
          }
          elseif ( ! empty($_COOKIE) ) # This was added in version 1.0 to make the plugin compatible with WP2.0.1
          {
            if ( ! empty($_COOKIE[USER_COOKIE]) )
              $user_login = $_COOKIE[USER_COOKIE];
            if ( ! empty($_COOKIE[PASS_COOKIE]) ) {
              $user_pass = $_COOKIE[PASS_COOKIE];
              $using_cookie = true;
            }
          }
        }

        # get user_login from email
        if ($user_login == "") {
          global $wpdb;
          $user_email = sanitize_email(trim($_POST['user_email']));
          if (is_email($user_email)) {
            $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_email = '$user_email'");
            if ($user) $user_login = $user->user_login;
          }
        }

        $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $user_pass , 'remember' => ( $rememberme == 'forever' ) ), false );
        if ( is_wp_error($login_result) ) $error = $login_result->get_error_message();
        else {

          $user = new WP_User(0, $user_login);

          # If the user can't edit posts, send them to their profile.
          if ( ! $user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == '/wp-admin/' || $redirect_to == get_option('siteurl') . '/wp-admin/' ) ) $redirect_to = get_option('siteurl') . '/wp-admin/profile.php';

          if ( wp_login($user_login, $user_pass, $using_cookie) ) {
            wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
            wp_redirect($redirect_to);
            login_register_object::logger( array('message' => 'login user: '. $user_login . ' rememberme: ' . ( ( ! empty($rememberme) ) ? $rememberme : 'no' ) . ' redirect to: ' . $redirect_to) );
            if ( $rememberme == 'forever' ) setcookie("login_register_login_rememberme", 'yes', time()+31536000); else setcookie("login_register_login_rememberme", 'no', 0); /* expire in 1 year */
            exit;
          } else {
            if ( $using_cookie ) $error = __('Your session has expired.');
          }
        }

        $expirepassword = '';
        if ( is_wp_error($login_result) ) {
          $login_register_expiredpassword_errorcode = trim(get_option('login_register_expiredpassword_errorcode'));
          if ( $login_register_expiredpassword_errorcode.'x' != 'x' && $login_result->get_error_code().'x' == $login_register_expiredpassword_errorcode.'x' && file_exists( WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php') ) {
            require WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php';
            $o = null; if ( class_exists( 'login_register_expiredpassword' ) ) $o = new login_register_expiredpassword();
            $pwlength = get_option("login_register_minpassword_length","7");
            if ( $pwlength < 7 ) $pwlength = 7; else if ( $pwlength > 30 ) $pwlength = 30;
            if ( $o != null && method_exists ( $o , 'loginform' ) ) $expirepassword = $o->loginform($user_login,$user_pass,$pwlength);
          }
        }

        ob_start();
        ?>

        <div id="login_register_div" class="login_register_div login">
        <?php if ( $error ) echo '<div id="login_register_login_error">' . $error . '</div>'; ?>
        <form name="login_register_loginform" id="login_register_loginform" action="/wp-login.php" method="post">
          <?php if ( get_option('login_register_emailislogin').'x' != 'yesx' ) { ?>
            <p>
              <label for="user_login"><?php _e('Username:') ?></label>
              <input type="text" Xid="user_login_id" name="user_login" id="user_login" maxlength="160" value="<?php echo wp_specialchars($user_login); ?>" /><br />
            </p>
          <?php } else { ?>
          <p>
            <label for="email"><?php _e('E-mail:') ?></label>
            <input type="text" Xid="user_login_id" name="user_email" id="user_email" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" />
          </p>
          <?php } ?>
          <p>
            <label for="password"><?php _e('Password:') ?></label>
            <input type="password" name="login_password" id="login_password" value="" />
          </p>
          <p>
            <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php echo ( $_COOKIE['login_register_login_rememberme'].'x' == 'yesx' || $rememberme == 'forever' ) ? ' checked' : ''; ?> tabindex="3" />
            <label for="rememberme"><?php _e('Remember me'); ?></label>
          </p>
          <?php echo $expirepassword; ?>
          <p>
            <input type="button" name="login_register_loginform_submitbutton" id="login_register_loginform_submitbutton" value="<?php _e('Login'); ?>" onclick="this.value=' ... processing ... ';this.disabled=true;document.getElementById('login_register_div').style.cursor='wait';this.form.submit()" tabindex="4" />
            <input type="hidden" name="redirect_to" value="<?php echo wp_specialchars($redirect_to); ?>" />
          </p>
        </form>
        <ul>
          <li><a href="/wp-login.php?action=lostpassword" title="<?php _e('Lost Password') ?>"><?php _e('Lost your password?') ?></a></li>
          <?php if (get_settings('users_can_register')) { ?><li><a href="/wp-register.php"><?php _e('Register') ?></a></li><?php } ?>
        </ul>
        </div>

        <?php
        $html = ob_get_contents(); ob_end_clean();

        switch($_REQUEST['login_register_trigger']) {

          case 'loggedout':
            $login_register_logout_post = get_option('login_register_logout_post');
            if ( (int)$login_register_logout_post > 0 ) {
              $postcontent = wp_get_single_post( (int)$login_register_logout_post, ARRAY_A);
              $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
            }
            break;

          default:
            $login_register_login_post = get_option('login_register_login_post');
            if ( (int)$login_register_login_post > 0 ) {
              $postcontent = wp_get_single_post( (int)$login_register_login_post, ARRAY_A);
              $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
            }
            break;

        }

        echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );

        exit;
        break;
    } # end switch
  }

  # called in place of the wp-register.php page ...
  public function do_register() {
    global $wpdb, $wp_query;

    if (!is_array($wp_query->query_vars)) $wp_query->query_vars = array();

    switch( $_POST['action'] ) {

      case 'register':

        $pwlength = get_option('login_register_minpassword_length','7');
        if ( $pwlength < 7 ) $pwlength = 7; else if ( $pwlength > 30 ) $pwlength = 30;

        require_once( ABSPATH . WPINC . '/registration-functions.php');

        $user_pass = '';
        $confirm_user_pass = '';
        $user_login = sanitize_user( $_POST['user_login'] );
        $user_email = sanitize_email(trim($_POST['user_email']));
        if ( isset( $_POST['user_pass'] )) $user_pass = $_POST['user_pass'];
        if ( isset( $_POST['confirm_user_pass'] )) $confirm_user_pass = $_POST['confirm_user_pass'];

        $captchaok = true;
        if ( class_exists('ReallySimpleCaptcha') ) {
          $captcha_instance = new ReallySimpleCaptcha();
          $captchaok = $captcha_correct = $captcha_instance->check($_POST['captcha_prefix'], trim($_POST['simple_captcha']));
          $captcha_instance->remove($captcha_prefix);
        }

        $inviteok = true;
        $login_register_invitation_codes = get_option('login_register_invitation_codes');
        if ( trim($login_register_invitation_codes).'x' != 'x' ) {
          $a = null;$a = explode("\n",strtolower(trim(preg_replace('//','',$login_register_invitation_codes))));
          $a  = ' ' . implode(' ',$a) . ' ';
          $c = ' ' . strtolower(trim($_POST['invitation_code'])) . ' ';
          if ( $c.'x' == 'x' || stristr($a,$c).'x' == 'x' ) $inviteok = false;
        }

        $errors = array();
        if ( get_option('login_register_emailislogin').'x' == 'yesx' ) $user_login = $user_email;

        if ( $user_login == '' ) $errors['user_login'] = __('<strong>ERROR</strong>: Please enter a username.');

        if ($user_email == '') {
          $errors['user_email'] = __('<strong>ERROR</strong>: Please type your e-mail address.');
        } else if ( ! is_email($user_email) ) {
          $errors['user_email'] = __('<strong>ERROR</strong>: The email address isn&#8217;t correct.');
          $user_email = '';
        }

        $customproc = false;
        if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php') ) {
          require WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php';
          $o = null; if ( class_exists( 'login_register_registerprocedure' ) ) $o = new login_register_registerprocedure();
          if ( $o != null && method_exists ( $o , 'processregister' ) ) {
            $customproc = true;
            $r = $o->processregister($user_login,$user_email,$user_pass,$pwlength,$captchaok,$inviteok);
            $errors = $r['errors'];
            $user_id = $r['user_id'];
            unset($r);
            unset($o);
          }

        }

        if ( ! $customproc ) {

          if ( ! $captchaok ) $errors['captcha'] = __('<strong>ERROR</strong>: captcha error.');
          if ( ! $inviteok ) $errors['invite'] = __('<strong>ERROR</strong>: invitation code in error.');

          if ( username_exists( $user_login ) )
            $errors['user_login'] = __('<strong>ERROR</strong>: This username is already registered, please choose another one.');
         
          $email_exists = $wpdb->get_row("SELECT user_email FROM $wpdb->users WHERE user_email = '$user_email'");
          if ( $email_exists)
            die (__('<strong>ERROR</strong>: This email address is already registered, please supply another.'));
         
          if ($user_pass == '')
            $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter a password.');
          elseif ( strpos( " ".$user_pass, "\\" ) )
            $errors['user_pass'] = __('<strong>ERROR</strong>: Passwords may not contain the character "\\".');
          elseif ( strlen($user_pass) < $pwlength )
            $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter a password of at least ' . $pwlength . ' characters.');
          elseif ( $user_pass.'x' != $confirm_user_pass.'x' )
            $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter password password twice, exactly the same.');
         
          if ( @count($errors) == 0 ) {
            $user_id = wp_create_user( $user_login, $user_pass, $user_email );
            if ( ! $user_id )
              $errors['user_id'] = sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email'));
            else
              wp_new_user_notification($user_id, $user_pass);
          }

        }

        if ( @count($errors) == 0 ) {
          $_REQUEST["action"] = "login";
          $_POST['login_password'] = $user_pass;
          $_POST['user_email'] = $user_email;
          $_POST['rememberme'] = true;
          ob_start();
          ?>
          <div id="login_register_div" class="login_register_div register">
            <h2><?php _e('Registration Complete') ?></h2>
            <?php if ( get_option('login_register_emailislogin').'x' != 'yesx' ) { ?>
              <p>
                <?php printf(__('Username: %s'), '<strong>' . wp_specialchars($user_login) . '</strong>'); ?>
              </p>
            <?php } ?>
            <p>
              <?php printf(__('E-mail: %s'), '<strong>' . wp_specialchars($user_email) . '</strong>'); ?>
            </p>
            <ul>
              <li><a href="/wp-login.php"><?php _e('Login') ?></a></li>
            </ul>
          </div>
          <?php
          $html = ob_get_contents(); ob_end_clean();

          $login_register_userregistration_post = get_option("login_register_userregistration_post");
          if ( (int)$login_register_userregistration_post > 0 ) {
            $postcontent = wp_get_single_post( (int)$login_register_userregistration_post, ARRAY_A);
            $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
          }
  
          echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );
  
          login_register_object::logger( array('message' => 'userregistration user: '. $user_login . ( ( trim($_POST['invitation_code']).'x' != 'x' )? ', invitation code: ' . substr(trim($_POST['invitation_code']),0,50) :'') ) );

          exit;
        }

        # fall thru to default ...

      default: # show registration form

        $pwlength = get_option('login_register_minpassword_length','7');
        if ( $pwlength < 7 ) $pwlength = 7; else if ( $pwlength > 30 ) $pwlength = 30;

        $registrationcustom = '';
        if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php') ) {
          require WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php';
          $o = null; if ( class_exists( 'login_register_registerprocedure' ) ) $o = new login_register_registerprocedure();
          if ( $o != null && method_exists ( $o , 'registrationform' ) ) $registrationcustom = $o->registrationform($user_login,$user_email,$pwlength);
        }

        ob_start();
        ?>

        <div id="login_register_div" class="login_register_div register">
          <?php if ( isset($errors) ) { ?>
            <div class="error">
              <ul>
              <?php
              foreach($errors as $error) echo "<li>$error</li>";
              ?>
              </ul>
            </div>
          <?php } ?>
          <form id="login_register_register" name="login_register_register" method="post" action="/wp-register.php">
            <?php if ( get_option('login_register_emailislogin').'x' != 'yesx' ) { ?>
              <p>
                <label for="user_login"><?php _e('Username:') ?></label>
                <input type="text" name="user_login" id="user_login" maxlength="160" value="<?php echo wp_specialchars($user_login); ?>" /><br />
              </p>
            <?php } ?>
            <p>
              <label for="user_email"><?php _e('E-mail:') ?></label>
              <input type="text" name="user_email" id="user_email" maxlength="160" value="<?php echo wp_specialchars($user_email); ?>" />
            </p>
            <p>
              <label for="user_pass"><?php _e('Password:') ?></label>
              <input type="password" name="user_pass" id="user_pass" maxlength="100" value="" />
              <span>Please enter a new password of at least <?php echo $pwlength; ?> characters.</span><br>
            </p>
            <p>
              <label for="confirm_user_pass"><?php _e('Confirm Password:') ?></label>
              <input type="password" name="confirm_user_pass" id="confirm_user_pass" maxlength="100" value="" />
            </p>
            <?php
               echo $registrationcustom;

               if ( class_exists('ReallySimpleCaptcha') ) {
                 $captcha_instance = new ReallySimpleCaptcha();
                 $captcha_instance->bg = array(230, 230, 230);
                 $captcha_instance->fg = array(13, 13, 13);
                 $captchaword = $captcha_instance->generate_random_word();
                 $captcha_prefix = mt_rand();
                 echo '<img src="/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image($captcha_prefix, $captchaword) . '" /><input type="hidden" name="captcha_prefix" id="captcha_prefix" value="' . $captcha_prefix . '" />';
            ?>
            <p>
              <label for="simple_captcha"><?php _e('Enter Captcha Code:') ?></label>
              <input type="text" name="simple_captcha" id="simple_captcha" maxlength="20" value="" />
            </p>
                 <?php
               }
               $login_register_invitation_codes = get_option('login_register_invitation_codes');
               if ( trim($login_register_invitation_codes).'x' != 'x' ) {
            ?>
            <p>
              <label for="invitation_code"><?php _e('Invitation Code:') ?></label>
              <input type="text" name="invitation_code" id="invitation_code" maxlength="100" value="" />
            </p>
                 <?php
               } ?>
            <input type="button" name="login_register_registerform_submitbutton" id="login_register_registerform_submitbutton" value="<?php _e('Register'); ?>" onclick="this.value=' ... processing ... ';this.disabled=true;document.getElementById('login_register_div').style.cursor='wait';this.form.submit()" tabindex="4" />
            <input type="hidden" name="action" id="action" value="register" />
          </form>
          <ul>
            <li><a href="/wp-login.php"><?php _e('Login') ?></a></li>
            <li><a href="/wp-login.php?action=lostpassword" title="<?php _e('Lost Password') ?>"><?php _e('Lost your password?') ?></a></li>
          </ul>
        </div>

        <?php
        $html = ob_get_contents(); ob_end_clean();

        $login_register_userregistration = get_option("login_register_userregistration");
        if ( (int)$login_register_userregistration > 0 ) {
          $postcontent = wp_get_single_post( (int)$login_register_userregistration, ARRAY_A);
          $html = preg_replace('/\{LOGINREGISTER_CONTENT\}/',$html, preg_replace('/\n/',' ',trim( $postcontent['post_content'] ) ) );
        }

        echo preg_replace('/__BODY_CONTENT__/',$html, $this->_getthemeheader() );

        exit;
        break;

    }
  }

  private function _getthemeheader() {
    $login_register_pullcssandjavascriptintoview = get_option('login_register_pullcssandjavascriptintoview');
    if ( $login_register_pullcssandjavascriptintoview.'x' != 'yesx' ) {
      ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11"><title><?php wp_title(); ?></title><meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset'); ?>" /></head><body><div class="entry-content login_register_wrapper">__BODY_CONTENT__</div></body></html><?php
      $html = ob_get_contents(); ob_end_clean();
      return $html;
    }

    ob_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11"><title><?php wp_title(); ?></title><meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset'); ?>" /><link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" /><?php wp_enqueue_script('jquery');wp_head(); ?></head><body><div class="entry-content login_register_wrapper">__BODY_CONTENT__</div><?php wp_footer(); ?></body></html><?php
    $html = ob_get_contents(); ob_end_clean();
    return $html;
  }

  public static function logger($arr=array()) {
    $message = $arr['message']; # message should be a short/decisive one line message.
    $priority = $arr['priority']; if ( $priority.'x' == 'x' ) $priority = 'local0.notice';
    $tag = $arr['tag']; if ( $tag.'x' == 'x' ) $tag = basename(__FILE__);

    if ( @trim($message).'x'=='x' ) return;

    $message = preg_replace('/\n/',' ',trim($message)); # make sure message is one line.

    if ( $priority.'x' == 'local0.noticex' ) $priority = LOG_LOCAL0;

    @openlog($tag,null,$priority);
    @syslog($priority,$message);
    @closelog();
  }

}

function login_register_shortcode($attrs, $content=null, $code=''){
  require WP_PLUGIN_DIR . '/login-register/login_register_ajax.php';

  $lf = WP_PLUGIN_DIR . '/login-register/' . LOGIN_REGISTER_LOCALFILE;
  $current_user = wp_get_current_user();
  if ( (int)$current_user->ID > 0 ) {
    $html = '';
    if ( file_exists( $lf ) ) {
      require $lf;
      if ( class_exists('login_register_local') ) {
        $o = new login_register_local();
        if ( method_exists($o,'loggedinhtml') ) $html = $o->loggedinhtml(array('user_id'=>(int)$current_user->ID,'attrs'=>$attrs,'code'=>$code));
      }
    }
    return $html;
  }

  $html = login_register_ajax::getloginform(array('fadeoutonlogin'=>'yes','titlebar'=>$attrs['title']));
  $html .= $content;

  if ( file_exists( $lf ) ) {
    require $lf;
    if ( class_exists('login_register_local') ) {
      $o = new login_register_local();
      if ( method_exists($o,'top') ) $html = str_replace('@@TOP@@',$o->top(array('attrs'=>$attrs,'code'=>$code)),$html);
      if ( method_exists($o,'login_after') ) $html = str_replace('@@AFTER_LOGIN@@',$o->login_after(array('attrs'=>$attrs,'code'=>$code)),$html);
      if ( method_exists($o,'register_after') ) $html = str_replace('@@AFTER_REGISTER@@',$o->register_after(array('attrs'=>$attrs,'code'=>$code)),$html);
      if ( method_exists($o,'lostpassword_after') ) $html = str_replace('@@AFTER_LOSTPASSWORD@@',$o->lostpassword_after(array('attrs'=>$attrs,'code'=>$code)),$html);
      if ( method_exists($o,'bottom') ) $html = str_replace('@@BOTTOM@@',$o->bottom(array('attrs'=>$attrs,'code'=>$code)),$html);
    }
  }

  $html = str_replace('@@TOP@@','',$html);
  $html = str_replace('@@AFTER_LOGIN@@','',$html);
  $html = str_replace('@@AFTER_LOSTPASSWORD@@','',$html);
  $html = str_replace('@@AFTER_REGISTER@@','',$html);
  $html = str_replace('@@BOTTOM@@','',$html);

  if ( $attrs['title'].'x' != 'x' || $attrs['style'].'x' != 'x' || $attrs['class'].'x' != 'x' )
    $html = '<div class="' . $attrs['class'] . '" style="' . $attrs['style'] . '">' . $html . '</div>' ;

  return $html;
}

if ( ! function_exists('login_register_ajax_dologin') ) {
function login_register_ajax_dologin() {

  if ( $_POST ) {
    $user_login = ( get_option('login_register_emailislogin').'x' != 'yesx' ) ? $_POST['user_login'] : sanitize_email($_POST['user_email']);
    $user_login = sanitize_user( $user_login );
    $user_pass  = $_POST['login_password'];
    $new_pass  = $_POST['newpassword'];
    $rememberme = $_POST['rememberme'];
  } else {
    if (function_exists('wp_get_cookie_login')) {
      $cookie_login = wp_get_cookie_login();
      if ( ! empty($cookie_login) ) {
        $using_cookie = true;
        $user_login = $cookie_login['login'];
        $user_pass = $cookie_login['password'];
      }
    } elseif ( ! empty($_COOKIE) ) {
      if ( ! empty($_COOKIE[USER_COOKIE]) )
        $user_login = $_COOKIE[USER_COOKIE];
      if ( ! empty($_COOKIE[PASS_COOKIE]) ) {
        $user_pass = $_COOKIE[PASS_COOKIE];
        $using_cookie = true;
      }
    }
  }
  
  if ($user_login == '') {
    global $wpdb;
    $user_email = sanitize_email($_POST['user_email']);
    if (is_email($user_email)) {
      $user = $wpdb->get_row('SELECT * FROM ' . $wpdb->users . ' WHERE user_email = \'' . $user_email . '\'');
      if ($user) $user_login = $user->user_login;
    }
  }
  
  if ($user_login == '') {
    $r['errormessage'] = ( get_option('login_register_emailislogin').'x' == 'yesx' ) ? 'Invalid User Email, please try again' : 'Missing login name.';
    Header('Content-Type: application/json');
    echo json_encode($r);
    exit;
  }
  
  $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $user_pass , 'remember' => ( $rememberme == 'forever' ) ), false );
  if ( is_wp_error($login_result) ) $r['errormessage'] = $login_result->get_error_message();
  else {
  
    $user = new WP_User(0, $user_login);
  
    if ( wp_login($user_login, $user_pass, $using_cookie) ) {
      wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
      login_register_object::logger( array('message' => 'login user: '. $user_login . ' rememberme: ' . ( ( ! empty($rememberme) ) ? $rememberme : 'no' ) . ' redirect to: ' . $_POST['redirect_to']) );
      if ( $rememberme == 'forever' ) setcookie("login_rememberme", 'yes', time()+31536000); else setcookie("login_rememberme", 'no', 0); /* expire in 1 year */
    } else {
      if ( $using_cookie ) $r['errormessage'] = __('Your session has expired.');
    }
  }
  
  if ( is_wp_error($login_result) ) {
    $login_register_expiredpassword_errorcode = trim(get_option('login_register_expiredpassword_errorcode'));
    if ( $login_register_expiredpassword_errorcode.'x' != 'x' && $login_result->get_error_code().'x' == $login_register_expiredpassword_errorcode.'x' && file_exists( WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php') ) {
      require_once WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php';
      $r['passwordisexpired'] = true;
      $o = null; if ( class_exists( 'login_register_expiredpassword' ) ) $o = new login_register_expiredpassword();
      if ( $o != null && method_exists ( $o , 'passwordreset' ) ) {
        $status = $o->passwordreset($user_login,$user_pass,$new_pass);
        if ( $o->passwordwasreset === true ) {
          $r['passwordisexpired'] = false;
          $r['errormessage'] = '';
          $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $new_pass , 'remember' => ( $rememberme == 'forever' ) ), false );
        }
      }
    }
  }

  $lf = WP_PLUGIN_DIR . '/login-register/' . LOGIN_REGISTER_LOCALFILE;
  if ( file_exists( $lf ) ) {
    require $lf;
    if ( class_exists('login_register_local') ) {
      $o = new login_register_local();
      if ( method_exists($o,'customlogin') ) $r['customlogin'] = $o->customlogin(array('user_login'=>$user_login,'user_password'=>$user_pass,'user_id'=>$user->ID));
    }
  }
  
  $r['allow_access'] = ( $r['errormessage'].'x' == 'x' ) ? 'ok' : 'no';
  $r['status'] = 'ok';
  Header('Content-Type: application/json');
  echo json_encode($r);
  exit;
}}

if ( ! function_exists('login_register_ajax_doregister') ) {
function login_register_ajax_doregister() {
  $pwlength = get_option('login_register_minpassword_length','7');
  if ( $pwlength < 7 ) $pwlength = 7; else if ( $pwlength > 30 ) $pwlength = 30;
  
  require_once( ABSPATH . WPINC . '/registration-functions.php');

  $user_pass = '';
  $confirm_user_pass = '';
  $user_login = sanitize_user( $_POST['register_user_login'] );
  $user_email = sanitize_email(trim($_POST['register_user_email']));
  if ( isset( $_POST['register_user_pass'] )) $user_pass = $_POST['register_user_pass'];
  if ( isset( $_POST['register_confirm_user_pass'] )) $confirm_user_pass = $_POST['register_confirm_user_pass'];
  
  $captchaok = true;
  if ( class_exists('ReallySimpleCaptcha') ) {
    $captcha_instance = new ReallySimpleCaptcha();
    $captchaok = $captcha_correct = $captcha_instance->check($_POST['captcha_prefix2'], trim($_POST['simple_captcha2']));
    $captcha_instance->remove($_POST['captcha_prefix2']);
  }
  
  $inviteok = true;
  $login_register_invitation_codes = get_option('login_register_invitation_codes');
  if ( trim($login_register_invitation_codes).'x' != 'x' ) {
    $a = null;$a = explode("\n",strtolower(trim(preg_replace('//','',$login_register_invitation_codes))));
    $a  = ' ' . implode(' ',$a) . ' ';
    $c = ' ' . strtolower(trim($_POST['invitation_code'])) . ' ';
    if ( $c.'x' == 'x' || stristr($a,$c).'x' == 'x' ) $inviteok = false;
  }
  
  $errors = array();
  if ( get_option('login_register_emailislogin').'x' == 'yesx' ) $user_login = $user_email;
  
  if ( $user_login == '' && get_option('login_register_emailislogin').'x' != 'yesx' ) $errors['user_login'] = __('<strong>ERROR</strong>: Please enter a username.');
  
  if ($user_email == '') {
    $errors['user_email'] = __('<strong>ERROR</strong>: Please type your e-mail address.');
  } else if ( ! is_email($user_email) ) {
    $errors['user_email'] = __('<strong>ERROR</strong>: The email address isn&#8217;t correct.');
    $user_email = '';
  }
  
  $customproc = false;
  if ( file_exists( WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php') ) {
    require WP_PLUGIN_DIR . '/login-register/login_register_registerprocedure.php';
    $o = null; if ( class_exists( 'login_register_registerprocedure' ) ) $o = new login_register_registerprocedure();
    if ( $o != null && method_exists ( $o , 'processregister' ) ) {
      $customproc = true;
      $r = $o->processregister($user_login,$user_email,$user_pass,$pwlength,$captchaok,$inviteok);
      $errors = $r['errors'];
      $user_id = $r['user_id'];
      unset($r);
      unset($o);
    }
  }

  global $wpdb;
  
  if ( ! $customproc ) {
    if ( ! $captchaok ) $errors['captcha'] = __('<strong>ERROR</strong>: captcha error.');
    if ( ! $inviteok ) $errors['invite'] = __('<strong>ERROR</strong>: invitation code in error.');
  
    if ( username_exists( $user_login ) )
      $errors['user_login'] = __('<strong>ERROR</strong>: This username is already registered, please choose another one.');
   
    $email_exists = $wpdb->get_row("SELECT user_email FROM $wpdb->users WHERE user_email = '$user_email'");
    if ( $email_exists || $user_email.'x' == 'x' ) $errors['user_email'] = __('<strong>ERROR</strong>: This email address is already registered, please supply another.');
   
    if ($user_pass == '')
      $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter a password.');
    elseif ( strpos(' '.$user_pass,"\\"))
      $errors['user_pass'] = __('<strong>ERROR</strong>: Passwords may not contain the character "\\".');
    elseif ( strlen($user_pass) < $pwlength )
      $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter a password of at least ' . $pwlength . ' characters.');
    elseif ( $user_pass.'x' != $confirm_user_pass.'x' )
      $errors['user_pass'] = __('<strong>ERROR</strong>: Please enter password password twice, exactly the same.');
   
    if ( @count($errors) == 0 ) {
      $user_id = wp_create_user( $user_login, $user_pass, $user_email );
      if ( ! $user_id )
        $errors['user_id'] = sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email'));
      else wp_new_user_notification($user_id, $user_pass);
    }
  }
  
  if ( @count($errors) == 0 ) {
    $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $user_pass ));
    login_register_object::logger( array('message' => 'userregistration user: '. $user_login . ( ( trim($_POST['register_invitation_code']).'x' != 'x' )? ', invitation code: ' . substr(trim($_POST['register_invitation_code']),0,50) :'') ) );

    if ( $_POST['login_register_ajax_custompayload'].'x' != 'x' ) {
      $lf = WP_PLUGIN_DIR . '/login-register/' . LOGIN_REGISTER_LOCALFILE;
      if ( file_exists( $lf ) ) {
        require $lf;
        if ( class_exists('login_register_local') ) {
          $o = new login_register_local();
          if ( method_exists($o,'customregistration') ) $r['customregistration'] = $o->customregistration(array('user_login'=>$user_login,'user_password'=>$user_pass,'user_id'=>$user_id,'login_register_ajax_custompayload'=>$_POST['login_register_ajax_custompayload']));
        }
      }
    }

  }
  
  Header('Content-Type: application/json');
  $r['status'] = 'ok';
  $r['errors'] = implode('<br>',$errors);
  echo json_encode($r);
  exit;
  
}}

if ( ! function_exists('login_register_ajax_dolostpassword') ) {
function login_register_ajax_dolostpassword() {

  $r['i'] = 0;
  if ( $_POST ) {
    $email = sanitize_email($_POST['email']);
    $password = wp_generate_password(7);
    global $wpdb;
    $sql = 'SELECT ID FROM ' . $wpdb->users . ' WHERE user_email = \'' . mysql_real_escape_string($email) . '\' LIMIT 1';
    $id = $wpdb->get_var($sql);
    if ( (int)$id > 1 ) {
      $r['i'] = 1;
      wp_set_password($password,$id);
      wp_mail( $email, 'Password reset', 'Your new password is: ' . $password);
    }
  }

  $r['status'] = 'ok';
  Header('Content-Type: application/json');
  echo json_encode($r);
  exit;
}}

# vim: nowrapscan ic
