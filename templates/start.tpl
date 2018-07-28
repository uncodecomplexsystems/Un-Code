{include file="head.public.tpl" title="Start"}

<script type="text/javascript">
$(document).ready(function() {

	// Login
    var dialog_login, form_login,
	  login_name = $( "#login_name" ),
      login_password = $( "#login_password" ),
      allFieldsLogin = $( [] ).add( login_name ).add( login_password ),
      tipsLogin = $( ".validateTipsLogin" );
 
    function checkLengthLogin( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTipsLogin( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

     function updateTipsLogin( t ) {
      tipsLogin
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tipsLogin.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
  
     function doLogin() {
      var valid = true;
      allFieldsLogin.removeClass( "ui-state-error" );
 
      valid = valid && checkLengthLogin( login_name, "Account Name", 3, 40 );
      valid = valid && checkLengthLogin( login_password, "Password", 6, 40 );
 
      if ( valid ) {
		
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok')
					{
						dialog_login.dialog( "close" );
						location.href = '?';
					}
					else updateTipsLogin('Error logging in. Please recheck your entered credentials. If the problem persists, please contact support.');
				}
			}
			
			var sendData = serialize(document.forms[0]);
			xmlhttp.open("POST","?p=login",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);
		
      }
      return valid;
    }
 
    dialog_login = $( "#login" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Log in": doLogin,
        Cancel: function() {
          dialog_login.dialog( "close" );
        }
      },
	  
	  
      close: function() {
        form_login[ 0 ].reset();
        allFieldsLogin.removeClass( "ui-state-error" );
      }
    });
 
    form_login = dialog_login.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      createLogin;
    });
 
    $( "#toggle_login" ).button().on( "click", function() {
	  updateTipsLogin('Please enter your UN-CODE account name and password.');
      dialog_login.dialog( "open" );
    });
	
	
	
	
	
	 var dialog_register, form_register,
 
	  register_name = $( "#register_name" ),
      register_email = $( "#register_email" ),
	  register_password = $( "#register_password" ),
      allFieldsRegister = $( [] ).add( register_name ).add( register_email ).add( register_password ),
      tipsRegister = $( ".validateTipsRegister" );
 
    function updateTipsRegister( t ) {
      tipsRegister
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tipsRegister.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLengthRegister( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTipsRegister( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
     function doRegister() {
      var valid = true;
      allFieldsRegister.removeClass( "ui-state-error" );
 
      valid = valid && checkLengthRegister( register_name, "Account Name", 3, 40 );
      valid = valid && checkLengthRegister( register_email, "Email Adress", 5, 40 );
	  valid = valid && checkLengthRegister( register_password, "Password", 6, 40 );
 
      if ( valid ) {
		xmlhttp=new XMLHttpRequest();
	
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					if (xmlhttp.responseText == 'ok')
					{
						dialog_register.dialog( "close" );
						updateTipsLogin('Your account has been created successfully. Welcome to UN-CODE! You can now log in with your credentials to start the science.');
						dialog_login.dialog( "open" );
					}
					else if (xmlhttp.responseText == 'reg_name_exists') updateTipsRegister('The chosen Account Name already exists. Please pick another one.');
					else if (xmlhttp.responseText == 'reg_email_exists') updateTipsRegister('The entered Email Adress already is used by someone. Please pick another one. If you have trouble restoring an old account, please contact support.');
					else if (xmlhttp.responseText == 'reg_email_invalid') updateTipsRegister('The entered Email Adress seems to be invalid. Please check again.');
					else updateTipsRegister('A problem has occured during registration. If the problem persists, please contact support.');
				}
			}
			
			var sendData = serialize(document.forms[1]);
			xmlhttp.open("POST","?p=register",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(sendData);		
      }
      return valid;
    }
 
    dialog_register = $( "#register" ).dialog({
      autoOpen: false,
      height: 450,
      width: 600,
	  	show: {
				effect: "puff",
				duration: 200
			},
		hide: {
				effect: "puff",
				duration: 200
			},
      modal: true,
      buttons: {
        "Create Account": doRegister,
        Cancel: function() {
          dialog_register.dialog( "close" );
        }
      },
      close: function() {
        form_register[ 0 ].reset();
        allFieldsRegister.removeClass( "ui-state-error" );
      }
    });
 
    form_register = dialog_register.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      createRegister;
    });
	
	$( "#toggle_register" ).button().on( "click", function() {
	  updateTipsRegister('Please enter your desired account name, password and e-mail adress (important for password restauration). We will never send you spam email.');
      dialog_register.dialog( "open" );
    });
	
});
</script>

<div id="main">
	<div id="content">

	<div id="postheader">
		<div class="entry-header">
		</div>
		
		<div class="entry-content">
			<h3>Welcome to the UN-CODE App! Ready to do some science?</h3>
			<p>Let's go! Please log in with your account credentials.
			<br><button id="toggle_login">Log in with existing account</button></p>
			<br>
			<p>Don't have an account, yet? No problem! UN-CODE is a non-profit project and is totally free. Just create a new account and be ready to start within seconds. 
			<br><button id="toggle_register">Create a new account</button>
			<br><br><br>
			<div id="demo">Just want to have a sneek peak? Use our demo account!<br><b>Account:</b> demo<br><b>Password:</b> uncode</div>
			</p>
		</div>
	</div>
	</div>
</div>





<div id="login" style="font-size: 70%" title="Ready to do some science?">
  <p class="validateTipsLogin">Please enter your UN-CODE account name and password.</p><br>
  <form>
    <fieldset>
      <label for="login_name">Your Account</label>
      <br><input class="text ui-widget-content ui-corner-all" type="text" id="login_name" name="login_name" value="" maxlength="40">
	  <br><label for="login_password">Your Password</label>
      <br><input class="text ui-widget-content ui-corner-all" type="password" id="login_password" name="login_password" value="" maxlength="40">
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<div id="register" style="font-size: 70%" title="Create an account. It's free.">
  <p class="validateTipsRegister">Please enter your desired account name, password and e-mail adress (important for password restauration). We will never send you spam email.</p><br>
  <form>
    <fieldset>
      <label for="register_name">Account Name</label>
      <br><input class="text ui-widget-content ui-corner-all" type="text" id="register_name" name="register_name" value="" maxlength="40">
	  <br><label for="register_password">Password</label>
      <br><input class="text ui-widget-content ui-corner-all" type="password" id="register_password" name="register_password" value="" maxlength="40">
	  <br><label for="register_email">Email Adress</label>
      <br><input class="text ui-widget-content ui-corner-all" type="text" id="register_email" name="register_email" value="" maxlength="40">
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>








	
{include file="foot.tpl"}