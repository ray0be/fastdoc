<form method="post" action="">
    {if isset($change_password)}
    <div class="form-group">
        <label for="apassword">Actual password</label>
        <input class="form-control" type="password" id="apassword" name="apassword" value="">
    </div>
    {/if}
    <blockquote>
        <p>We recommand you to choose a password with lots of complexity. In addition, you'll be able to activate the reCAPTCHA system in the settings to prevent bruteforce actions.</p>
        <p><a class="btn btn-default" onclick="generate_pass();return false;">Generate a good password</a>&nbsp;<span id="generated_password" style="font-family:Courier,Monospace,sans-serif;letter-spacing:2px;"></span></p>
    </blockquote>

    <div class="form-group">
        <label for="password">New password</label>
        <input class="form-control" type="password" id="password" name="password" value="">
    </div>
    <div class="form-group">
        <label for="password2">New password (again)</label>
        <input class="form-control" type="password" id="password2" name="password2" value="">
    </div>
    
    <input type="submit" class="btn btn-success" value="Continue">
</form>

<script>
{literal}
function generate_pass()
{
    var chars = 'azertyupqsdfghjkmwxcvbn23456789AZERTYUPQSDFGHJKMWXCVBN&#()[]{}@+/%!';
    var pass = '';
    var length = 12;
    for(var i=0; i<length; i++) {
        var wpos = Math.round(Math.random()*chars.length);
        pass += chars.substring(wpos,wpos+1);
    }
    $('#generated_password').text(pass);
}
{/literal}
</script>