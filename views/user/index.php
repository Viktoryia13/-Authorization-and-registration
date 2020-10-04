<div class="login-box">
  <div class="lb-header">
    <a class="active" id="login-box-link">Login</a>
    <a id="signup-box-link">Sign Up</a>
  </div>
  <form onsubmit="return false;" class="signIn">
 <!-- <form action="/users/signIn" method="post" class="signIn"> -->
  <div class="u-form-group">
        <input type="text" placeholder="Login" name="login" />
        <span class="input-errors" id="login-errors"></span>
      </div>
      <div class="u-form-group">
        <input type="password" placeholder="Password" name="password" />
        <span class="input-errors" id="password-errors"></span>
      </div>
      <div class="u-form-group">
        <label>Запомнить меня</label>
      <input name='remember' type='checkbox' value='1' >
      </div>

    <div class="u-form-group">
      <button id="signIn">Log in</button>
    </div>

  </form>

  <form onsubmit="return false;" class="signUp">
    <!-- <form action="/users/signUp" method="post" class="signUp"> -->
    <div class="signUp-inputs">
      <div class="u-form-group">
        <input type="text" placeholder="Login" name="login" />
        <span class="input-errors login-errors" id="sign-up-login-errors"></span>
      </div>
      <div class="u-form-group">
        <input type="password" placeholder="Password" name="password" />
        <span class="input-errors" id="sign-up-password-errors"></span>
      </div>
      <div class="u-form-group">
        <input type="password" placeholder="Confirm Password" name="confirm_password" />
        <span class="input-errors" id="sign-up-confirm-password-errors"></span>
      </div>
      <div class="u-form-group">
        <input type="email" placeholder="Email" name="email" />
        <span class="input-errors" id="sign-up-email-errors"></span>
      </div>
      <div class="u-form-group">
        <input type="text" placeholder="Name" name="name" />
        <span class="input-errors" id="sign-up-name-errors"></span>
      </div>
      <div class="u-form-group">
        <button id="signUp">Sign Up</button>
      </div>
    </div>
    <span class="sign-up-success"></span>
  </form>

  <div class="alert display-none" id="sign-up-errors">
  </div>
  <div class="alert display-none" id="sign-up-success">
  </div>
</div>