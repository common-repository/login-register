<?php
if ( ! class_exists('login_register_ajax') ) {

  class login_register_ajax {
  
    public static function getloginform($args=array()) { extract($args);
      if ( ! defined('LOGIN_REGISTER_ENABLED') ) return 'Error: login_register plugin must be enabled.';
      ob_start(); ?>
      <script src="/wp-content/plugins/login-register/login_register_ajax.js" type="text/javascript">
      <script type="text/javascript">
        window.login_register_configs_minpassword_length = <?php echo (int)get_option('login_register_minpassword_length','7'); ?>;
        window.login_register_configs_fadeonlogin = <?php echo ( ( $fadeoutonlogin.'x' == 'yesx' ) ? 'true' : 'false' ); ?>;
        window.login_register_configs_siteurl = '<?php echo str_replace("'",'',get_option('siteurl')); ?>';
        jQuery(document).ready(function() {
          var script = document.createElement('script');
          script.src = '/wp-content/plugins/login-register/login_register_ajax.js';
          script.type = 'text/javascript';
          script.defer = false;
          script.id = 'login_register_ajax_jso_script';
          var head = document.getElementsByTagName('head').item(0);
          //head.appendChild(script);
        });
      </script>
      @@TOP@@
      <div id="login_register_div" Xsiteurl="<?php echo str_replace('"','',get_option('siteurl')); ?>" >
      <div id="login_register_div1" class="login_register_div login" Xdiv="login_register">
      <?php if ( $titlebar.'x' != 'x' ) echo '<h2>' . htmlspecialchars($titlebar) . '</h2>'; ?>
      <div class="error" id="login_error"></div>
      <form name="loginform_ajax" id="loginform_ajax">
        <?php if ( get_option('login_register_emailislogin').'x' != 'yesx' ) { ?>
          <p>
            <label class="login_register" for="user_login"><?php _e('Username:') ?></label>
            <input type="text" id="user_login_id" name="user_login" id="user_login" maxlength="160" value="<?php echo wp_specialchars($user_login); ?>" /><br />
          </p>
        <?php } else { ?>
        <p>
          <label class="login_register" for="email"><?php _e('E-mail:') ?></label>
          <input type="text" id="user_login_id" name="user_email" id="user_email" value="<?php echo sanitize_email(stripslashes($user_login)); ?>" />
        </p>
        <?php } ?>
        <p>
          <label class="login_register" for="password"><?php _e('Password:') ?></label>
          <input type="password" name="login_password" id="login_password" value="" />
          <div style="display:none" name="passwordexpired">
            <b>Your password has expired, please enter a new password of minimum length of <?php echo get_option("login_register_minpassword_length","7"); ?> characters.</b><br>
            <label class="login_register" for="newpassword">New Password:</label><input type="password" maxlength="40" id="newpassword" name="newpassword"><br>
            <label class="login_register" for="confpassword">Confirm Password:</label><input type="password" maxlength="40" id="newpassword2" name="newpassword2"><br>
          </div>
        </p>
        <p>
          <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php echo ( $_COOKIE['login_rememberme'].'x' == 'yesx' || $rememberme == 'forever' ) ? ' checked' : ''; ?> tabindex="3" />
          <label class="login_register" for="rememberme"><?php _e('Remember me'); ?></label>
        </p>
        <p>
          <input type="button" name="loginform_submitbutton" id="loginform_submitbutton" value="<?php _e('Login'); ?>" onclick="window.login_register_ajax_jso.login(this)" tabindex="4" />
          <input type="hidden" name="redirect_to" value="<?php echo wp_specialchars($_POST['redirect_to']); ?>" />
        </p>
      </form>
      <ul>
        <li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showlostpasswordform()" title="<?php _e('Lost Password') ?>"><?php _e('Lost your password?') ?></a></li>
        <?php if (get_settings('users_can_register')) { ?><li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showregisterform()" title="<?php _e('Register') ?>"><?php _e('Register') ?></a></li><?php } ?>
      </ul>
      @@AFTER_LOGIN@@
      </div>

      <div id="login_register_div2" class="login_register_div lostpassword" Xdiv="login_register" style="display:none">
        <p><?php _e('Please enter your information here. A new password will be emailed to you.') ?></p>
        <div class="error" id="login_register_lostpassword_error"></div>
        <form id="login_register_passwordreset" name="login_register_passwordreset">
          <p>
            <label class="login_register" for="email"><?php _e('E-mail:') ?></label>
            <input type="text" name="email" id="email" value="" tabindex="1" />
          </p>
          <?php
             if ( class_exists('ReallySimpleCaptcha') ) {
               $captcha_instance = new ReallySimpleCaptcha();
               $captcha_instance->bg = array(230, 230, 230);
               $captcha_instance->fg = array(13, 13, 13);
               $captchaword = $captcha_instance->generate_random_word();
               $captcha_prefix = mt_rand();
               echo '<img src="/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image($captcha_prefix, $captchaword) . '" /><input type="hidden" name="captcha_prefix1" id="captcha_prefix1" value="' . $captcha_prefix . '" />';
          ?>
          <p>
            <label class="login_register" for="simple_captcha"><?php _e('Enter Code:') ?></label>
            <input type="text" name="simple_captcha1" id="simple_captcha1" maxlength="20" value="" />
          </p>
          <?php } ?>
          <p>
            <input type="button" name="login_register_lostpassword_submitbutton" id="login_register_lostpassword_submitbutton" value="<?php _e('Retrieve Password'); ?>" onclick="if(this.form.email.value.replace(/\s/,'').length>0){this.value=' ... processing ... ';this.disabled=true;document.getElementById('login_register_div2').style.cursor='wait';window.login_register_ajax_jso.submitlostpassword()}" tabindex="2" />
          </p>
        </form>
        <ul>
          <?php if (get_settings('users_can_register')) { ?><li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showregisterform()" title="<?php _e('Register') ?>"><?php _e('Register') ?></a></li><?php } ?>
          <li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showloginform()" title="<?php _e('Login') ?>"><?php _e('Login') ?></a></li>
        </ul>
        @@AFTER_LOSTPASSWORD@@
      </div>

      <div id="login_register_div3" class="login_register_div register" Xdiv="login_register" style="display:none">
        <div class="error" id="login_register_register_error"></div>
        <form id="login_register_register" name="login_register_register">
          <textarea style="display:none" name="login_register_ajax_custompayload" id="login_register_ajax_custompayload"></textarea>
          <?php if ( get_option('login_register_emailislogin').'x' != 'yesx' ) { ?>
            <p>
              <label class="login_register" for="user_login"><?php _e('Username:') ?></label>
              <input type="text" name="register_user_login" id="register_user_login" maxlength="160" value="<?php echo wp_specialchars($user_login); ?>" /><br />
            </p>
          <?php } ?>
          <p>
            <label class="login_register" for="user_email"><?php _e('E-mail:') ?></label>
            <input type="text" name="register_user_email" id="register_user_email" maxlength="160" value="<?php echo sanitize_email($user_email); ?>" />
          </p>
          <p>
            <label class="login_register" for="user_pass"><?php _e('Password:') ?></label>
            <input type="password" name="register_user_pass" id="register_user_pass" maxlength="100" value="" /><br>
            <span>Please enter a new password of at least <?php echo get_option("login_register_minpassword_length","7"); ?> characters.</span><br>
          </p>
          <p>
            <label class="login_register" for="confirm_user_pass"><?php _e('Confirm Password:') ?></label>
            <input type="password" name="register_confirm_user_pass" id="register_confirm_user_pass" maxlength="100" value="" />
          </p>
          <?php
             echo $registrationcustom;

             if ( class_exists('ReallySimpleCaptcha') ) {
               $captcha_instance = new ReallySimpleCaptcha();
               $captcha_instance->bg = array(230, 230, 230);
               $captcha_instance->fg = array(13, 13, 13);
               $captchaword = $captcha_instance->generate_random_word();
               $captcha_prefix = mt_rand();
               echo '<img src="/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_instance->generate_image($captcha_prefix, $captchaword) . '" /><input type="hidden" name="captcha_prefix2" id="captcha_prefix2" value="' . $captcha_prefix . '" />';
          ?>
          <p>
            <label class="login_register" for="simple_captcha"><?php _e('Enter Captcha Code:') ?></label>
            <input type="text" name="simple_captcha2" id="simple_captcha2" maxlength="20" value="" />
          </p>
               <?php
             }
             $login_register_invitation_codes = get_option('login_register_invitation_codes');
             if ( trim($login_register_invitation_codes).'x' != 'x' ) {
          ?>
          <p>
            <label class="login_register" for="invitation_code"><?php _e('Invitation Code:') ?></label>
            <input type="text" name="invitation_code" id="invitation_code" maxlength="100" value="" />
          </p>
               <?php
             } ?>
          <input type="button" name="login_register_registerform_submitbutton" id="login_register_registerform_submitbutton" value="<?php _e('Register'); ?>" onclick="this.value=' ... processing ... ';this.disabled=true;document.getElementById('login_register_div3').style.cursor='wait';window.login_register_ajax_jso.submitregister()" tabindex="4" />
        </form>
        <ul>
          <li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showloginform()" title="<?php _e('Login') ?>"><?php _e('Login') ?></a></li>
          <li><a style="cursor:pointer" onclick="window.login_register_ajax_jso.showlostpasswordform()" title="<?php _e('Lost Password') ?>"><?php _e('Lost your password?') ?></a></li>
        </ul>
        @@AFTER_REGISTER@@
      </div>

      </div> <!-- login_register_div -->
      @@BOTTOM@@
      <?php

      $html = ob_get_contents(); ob_end_clean();
      return $html;
    }

    public static function login() {
      $returnvalue['error'] = '';
      if( $_POST ) {
        $user_login = ( get_option('login_register_emailislogin').'x' != 'yesx' ) ? $_POST['user_login'] : sanitize_email($_POST['user_email']);
        $user_login = sanitize_user( $user_login );
        $user_pass  = $_POST['login_password'];
        $new_pass  = $_POST['newpassword'];
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

      if ($user_login == '') {
        global $wpdb;
        $user_email = sanitize_email($_POST['user_email']);
        if (is_email($user_email)) {
          $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_email = '$user_email'");
          if ($user) $user_login = $user->user_login;
        }
      }

      if ($user_login == '') {
        $returnvalue['error'] = ( get_option('login_register_emailislogin').'x' == 'yesx' ) ? 'Invalid User Email, please try again.' : 'Missing login name.';
        return $returnvalue;
      }

      $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $user_pass , 'remember' => ( $rememberme == 'forever' ) ), false );
      if ( is_wp_error($login_result) ) $returnvalue['error'] = $login_result->get_error_message();
      else {

        $user = new WP_User(0, $user_login);

        if ( wp_login($user_login, $user_pass, $using_cookie) ) {
          wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
          login_register_object::logger( array('message' => 'login user: '. $user_login . ' rememberme: ' . ( ( ! empty($rememberme) ) ? $rememberme : 'no' ) . ' redirect to: ' . $_POST['redirect_to']) );
          if ( $rememberme == 'forever' ) setcookie("login_rememberme", 'yes', time()+31536000); else setcookie("login_rememberme", 'no', 0); /* expire in 1 year */
        } else {
          if ( $using_cookie ) $returnvalue['error'] = __('Your session has expired.');
        }
      }

      if ( is_wp_error($login_result) ) {
        $login_register_expiredpassword_errorcode = trim(get_option('login_register_expiredpassword_errorcode'));
        if ( $login_register_expiredpassword_errorcode.'x' != 'x' && $login_result->get_error_code().'x' == $login_register_expiredpassword_errorcode.'x' && file_exists( WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php') ) {
          require_once WP_PLUGIN_DIR . '/login-register/login_register_expiredpassword.php';
          $returnvalue['passwordisexpired'] = true;
          $o = null; if ( class_exists( 'login_register_expiredpassword' ) ) $o = new login_register_expiredpassword();
          if ( $o != null && method_exists ( $o , 'passwordreset' ) ) {
            $status = $o->passwordreset($user_login,$user_pass,$new_pass);
            if ( $o->passwordwasreset === true ) {
              $returnvalue['passwordisexpired'] = false;
              $returnvalue['error'] = '';
              $login_result = wp_signon( array('user_login' => $user_login, 'user_password' => $new_pass , 'remember' => ( $rememberme == 'forever' ) ), false );
            }
          }
        }
      }

      return $returnvalue;
    }

  }

}
