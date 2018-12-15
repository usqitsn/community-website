<div class='form-wrapper'>
    <h2>Member Login</h2>
    <h5>Please login to access the members-only area.</h5>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required="true" pattern="[a-zA-Z0-9]{1,50}" title="Please enter a valid username" class="form-control" value="<?php if(isset($_POST['username'])){echo $_POST["username"];} ?>"/>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($error)){
              echo "<span class='help-block'>{$error}</span>";
            }?>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>
        <p>Don't have an account yet? <a href="registration.php">Register here</a>.</p>
      </form>
</div>
