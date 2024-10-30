window.login_register_ajax_jso = {
  redirect_to: null,
  callback: null,
  loginname: null,
  login: function(o) {
    o.blur();
    o.value=' ... processing ... ';
    o.disabled=true;
    document.getElementById('login_register_div').style.cursor='wait';
    window.login_register_ajax_jso.loginname = document.getElementById('user_login_id').value.replace(/^\s*/,'').replace(/\s*$/,'');
    var pw = document.getElementById('login_password').value.replace(/^\s*/,'').replace(/\s*$/,'');
    var pw1 = document.getElementById('newpassword').value.replace(/^\s*/,'').replace(/\s*$/,'');
    var pw2 = document.getElementById('newpassword2').value.replace(/^\s*/,'').replace(/\s*$/,'');
    var md = document.getElementById('login_error');
    if ( pw1 != '' && ( pw1 != pw2 || pw1.length < window.login_register_configs_minpassword_length || pw1 == pw ) ) {
      md.innerHTML = 'New password is too short, or confirm new password do not match, or you haven\'t provided a new password.';
      return false;
    }
    md.innerHTML = '';
    o.disabled = true;
    o.value = ' processing ... ';
    var siteurl = document.getElementById('login_register_div').getAttribute('Xsiteurl');
    jQuery.ajax({
      type: 'POST',
      data: jQuery('#loginform_ajax').serialize(),
      url: siteurl + '/wp-admin/admin-ajax.php?action=login_register_dologin',
      dataType: 'json',
      success: function(result) {
        var md = document.getElementById('login_error');
        var b = document.getElementById('loginform_submitbutton');
        var d = document.getElementById('login_register_div');
        d.style.cursor = 'auto';

        if ( result.password_expired ) {
          b.disabled = false;
          b.value = 'login';
          jQuery('[name="passwordexpired"]').show();
          return;
        }

        if ( result.errormessage ) {
          md.innerHTML = result.errormessage;
          b.disabled = false;
          b.value = 'login';
          return;
        }

        if ( result.allow_access == 'ok' ) {
          jQuery(md).css('color','green');
          md.innerHTML = 'Login was successful';
          if ( window.login_register_configs_fadeonlogin ) setTimeout("jQuery('#login_register_div').fadeOut(800)",3000);
          if ( window.login_register_ajax_jso.redirect_to ) document.location.href = window.login_register_ajax_jso.redirect_to;
          if ( window.login_register_ajax_jso.callback ) eval(window.login_register_ajax_jso.callback + "('" + window.login_register_ajax_jso.loginname.replace(/'/,'') + "')");
          return;
        }

      }
    });
  },

  submitregister: function() {
    var u1 = document.getElementById('register_user_email').value.replace(/ /,'');
    var p1 = document.getElementById('register_user_pass').value.replace(/^ */,'').replace(/ *$/,'');
    var p2 = document.getElementById('register_confirm_user_pass').value.replace(/^ */,'').replace(/ *$/,'');
    var b = document.getElementById('login_register_registerform_submitbutton');
    if ( u1 == '' || p1 == '' || p1 != p2 || p1.length < window.login_register_configs_minpassword_length ) {
      document.getElementById('login_register_register_error').innerHTML = 'Please check user name and passwords.';
      b.disabled = false;
      b.value = 'Register';
      jQuery('#login_register_div DIV.register[Xdiv="login_register"]').css({cursor: 'auto'});
      return;
    }

    if ( window.login_register_ajax_jso.callback_registeronsubmit ) eval(window.login_register_ajax_jso.callback_registeronsubmit + '()');
    var siteurl = document.getElementById('login_register_div').getAttribute('Xsiteurl');
    jQuery.ajax({
      type: 'POST',
      data: jQuery('#login_register_register').serialize(),
      url: siteurl + '/wp-admin/admin-ajax.php?action=login_register_doregister',
      dataType: 'json',
      success: function(result) {
        jQuery('#login_register_div DIV.register[Xdiv="login_register"]').css({cursor: 'auto'});
        if ( result.errors && result.errors.length > 0 ) {
          document.getElementById('login_register_register_error').innerHTML = result.errors;
          b.disabled = false;
          b.value = 'Register';
          return;
        }
        jQuery('#login_register_div DIV[Xdiv="login_register"].register').html('Registration Completed. Thank you.');
      }
    });
  },

  submitlostpassword: function() {
    var siteurl = document.getElementById('login_register_div').getAttribute('Xsiteurl');
    jQuery.ajax({
      type: 'POST',
      data: jQuery('#login_register_passwordreset').serialize(),
      url: siteurl + '/wp-admin/admin-ajax.php?action=login_register_dolostpassword',
      dataType: 'json',
      success: function(result) {
        document.getElementById('login_register_passwordreset').style.display = 'none';
        jQuery('#login_register_passwordreset').before('<p>Please check your email.  New password has been sent to you.</p>');
      }
    });
  },

  showloginform: function() {
    jQuery('#login_register_div DIV[Xdiv="login_register"]:not(.login)').slideUp(100 , function() { jQuery('#login_register_div1').fadeIn(800); });
    if ( window.login_register_ajax_jso.callback_loginview ) eval(window.login_register_ajax_jso.callback_loginview + '()');
  },
  showlostpasswordform: function() {
    jQuery('#login_register_div DIV[Xdiv="login_register"]:not(.lostpassword)').slideUp(100 , function() { jQuery('#login_register_div2').fadeIn(800); });
    if ( window.login_register_ajax_jso.callback_lostpasswordview ) eval(window.login_register_ajax_jso.callback_lostpasswordview + '()');
  },
  showregisterform: function() { jQuery('#login_register_div DIV[Xdiv="login_register"]:not(.register)').slideUp(100 , function() {
    jQuery('#login_register_div3').fadeIn(800); });
    if ( window.login_register_ajax_jso.callback_registerview ) eval(window.login_register_ajax_jso.callback_registerview + '()');
  },

  'end': 'end'
}

