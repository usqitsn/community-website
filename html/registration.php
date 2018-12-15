<!--Original author: Ashley Rowe - Vice President - USQITSN
    Contributions:
    Contact: enquiries@usqitsn.org
-->
<?php
    require_once "/var/www/scripts/dbConfig.php";

    $errors = array();
    $member_types = array("USQ Student", "Alumni", "External", "USQ Staff");
    //Only process when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
      if(!validateUsername($_POST["username"])){
        $errors["uname_error"] = "Username must be between 1 and 50 characters long and only contain alphanumeric characters and hyphens";
      }else{
        if(!checkUnique($mysqli, "SELECT id FROM membership WHERE username = ?", $_POST["username"])){
          $errors["uname_error"] = "There is already a memeber with this username.";
        }
      }
      if(!validateFirstName($_POST["fname"])){
        $errors["fname_error"] = "First name must be between 1 and 75 characters long and only contain alpha characters and hyphens";
      }
      if(!validateLastName($_POST["lname"])){
        $errors["lname_error"] = "Last name must be between 1 and 75 characters long and only contain alpha characters and hyphens";
      }
      if(!validateEmail($_POST["email"])){
        $errors["email_error"] = "Email address is not valid";
      }else{
        if(strtolower($_POST["member_type"]) === "usq student"){
          if(!validateUsqEmail($_POST["email"])){
            $errors["email_error"] = "Student members must use a valid USQ email address";
          }
        }
        if(!checkUnique($mysqli, "SELECT id FROM membership WHERE email = ?", $_POST["email"])){
          $errors["email_error"] = "There is already a member with this email address.";
        }
      }
      if(!validatePassword($_POST["pword"])){
        $errors["pword_error"] = "Password must contain at least 6 characters";
      }
      if(!validateConfirmPassword($_POST["pword"],$_POST["pwordConf"])){
        $errors["pword_match_error"] = "Passwords do not match";
      }
      if(!validateMemberType($_POST["member_type"], $member_types)){
        $errors["member_type_error"] = "You must select a valid member type";
      }

      // Check input errors before inserting in database
      $errors = array_filter($errors);
      if(empty($errors)){
          // Prepare an insert statement
          $sql = "INSERT INTO membership (username, fname, lname, email, password, member_type) VALUES (?, ?, ?, ?, ?, ?)";

          if($stmt = $mysqli->prepare($sql)){
              // Bind variables to the prepared statement as parameters
              $stmt->bind_param("ssssss", $param_username, $param_fname, $param_lname, $param_email, $param_password, $param_member_type);

              // Set parameters
              $param_username = strtolower($_POST["username"]);
              $param_fname = strtolower($_POST["fname"]);
              $param_lname = strtolower($_POST["lname"]);
              $param_email = strtolower($_POST["email"]);
              $param_password = password_hash($_POST["pword"], PASSWORD_DEFAULT); // Creates a password hash
              $param_member_type =  strtolower($_POST["member_type"]);

              // Attempt to execute the prepared statement
              if($stmt->execute()){
                  // Redirect to login page
                  header("location: login.php");
              } else{
                  echo "Something went wrong. Please try again later.";
              }
              // Close statement
              $stmt->close();
          }else{
            echo "Something went wrong. Please try again later.";
          }

      }
      // Close connection
      $mysqli->close();
    }

    /*Check that the username is the right size and constitution
    * @param $_uname  The username to check
    * @return   True if valid, otherwise an error message
    */
    function validateUsername($_uname){
        //Check the username is within the size range and only consists of alphanumeric characters
        if(!preg_match('/^[[:alnum:]]{1,50}$/',$_uname)){
            return false;
        }else{
            return true;
        }
    }

    /*Check if a value is unique within a relation for a given sql HttpQueryString
    * @param $_db   The database connection
    * @param $_sql  The sql Query
    * @param $_val  The value to check
    * @return   True if unique, otherwise false or error message
    */
    function checkUnique(&$_db, $_sql, $_val){
        if($stmt = $_db->prepare($_sql)){
            $stmt->bind_param("s", $param_username);
            $param_username = strtolower($_val);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows >0){
                    $stmt->close();
                    return false;
                }
                return true; //Unique
            }else{
                $error = $stmt->error;
                $stmt->close();
                die($error);
            }
        }else{
            die($_db->error);
        }
    }

    /*Check that the first name is the right size and constitution
    * @param $_fname  The username to check
    * @return   True if valid, otherwise false
    */
    function validateFirstName($_fname){
        //Check the first name is within the size range and only consists of alphanumeric characters
        if(!preg_match('/^[a-zA-Z-]{1,75}$/',$_fname)){
            return false;
        }else{
            return true;
        }
    }
    /*Check that the last name is the right size and constitution
    * @param $_lname  The username to check
    * @return   True if valid, otherwise false
    */
    function validateLastName($_lname){
        //Check the last name is within the size range and only consists of alphanumeric characters
        if(!preg_match('/^[a-zA-Z-]{1,75}$/',$_lname)){
            return false;
        }else{
            return true;
        }
    }

    /*Check that the password is not empty nor less than 6 characters long
    * @param $_pword  The password to check
    * @return   True if valid, otherwise false
    */
    function validatePassword($_pword){
        if(empty(trim($_pword))){
            return false;
        } elseif(strlen(trim($_pword)) < 6){
            return false;
        } else{
            return true;
        }
    }

    /*Check that the password confirmation is not empty and matches the password field
    * @param $_pword  The original password
    * @param $_pwordConf  The confirmation password to check
    * @return   True if valid, otherwise false
    */
    function validateConfirmPassword($_pword, $_pwordConf){
            // Validate confirm password
        if(empty(trim($_pwordConf))){
            return false;
        } else{
            if($_pword != $_pwordConf){
                return false;
            }else{
                return true;
            }
        }
    }

    /*Check for a valid email address
    * @param $_email  the string to test
    * @return   True if valid, otherwise false
    */
    function validateEmail($_email){
        if (!filter_var($_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }else{
            return true;
        }
    }

    /*Check that string matches a valid USQ email address
    * @param $_email  the string to test
    * @return   True if valid, otherwise false
    */
    function validateUsqEmail($_email){
        if (!preg_match('/^[a-zA-Z0-9_.+-]+@(?:(?:[a-zA-Z0-9-]+\.)?[a-zA-Z]+\.)?umail\.usq\.edu\.au$/',$_email)) {
            return false;
        }else{
            return true;
        }
    }

    /*Check that the string matches a valid member type
    * @param $_member_type  the string to test
    * @param &$member_type_array  A reference to an array containing valid member types
    * @return   True if valid, otherwise false
    */
    function validateMemberType($_member_type, &$member_type_array){
      if(!in_array($_member_type,$member_type_array)){
        return false;
      }
      return true;
    }

 ?>

 <!DOCTYPE html>

 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans|Quicksand" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 1.1em; background-color: #32353a; color: #ffffff;}
        .wrapper{ width: 350px; padding: 20px; margin-left: auto;margin-right: auto;}
        img{ display: block; margin-left: auto; margin-right: auto;}
        h2,h5{text-align:center;}
    </style>
 </head>
 <body>
    <div class="wrapper">
      <img class="logo" src="Resources/Images/logo2.png" alt="USQITSN Logo" width="120" height="134"/>
        <h2>Become a Member</h2>
        <h5>Please fill this form to create an account.</h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required="true" pattern="[a-zA-Z0-9-]{1,50}" title="Username should only contain 1-50 alphanumeric characters and hyphens" class="form-control" value="<?php if(isset($_POST["username"])){echo $_POST["username"];} ?>"/>
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["uname_error"])){
                  echo "<span class='help-block'>{$errors["uname_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="fname" required="true" pattern="[a-zA-Z-]{1,75}" title="First name should only contain 1-75 alpha characters and hyphens" class="form-control" value="<?php if(isset($_POST["fname"])){echo $_POST["fname"];} ?>"/>
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["fname_error"])){
                  echo "<span class='help-block'>{$errors["fname_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lname" required="true" pattern="[a-zA-Z-]{1,75}" title="Last name should only contain 1-75 alpha characters and hyphens" class="form-control" value="<?php if(isset($_POST["lname"])){echo $_POST["lname"];} ?>"/>
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["lname_error"])){
                  echo "<span class='help-block'>{$errors["lname_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required="true" class="form-control" value="<?php if(isset($_POST["email"])){echo $_POST["email"];} ?>">
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["email_error"])){
                  echo "<span class='help-block'>{$errors["email_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>Membership Type</label>
                <select name="member_type" class="form-control">
                  <?php foreach ($member_types as $value) {
                    echo "<option value='{$value}'>{$value}</option>";
                  }
                  ?>
                </select>
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["member_type_error"])){
                  echo "<span class='help-block'>{$errors["member_type_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pword" class="form-control">
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["pword_error"])){
                  echo "<span class='help-block'>{$errors["pword_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="pwordConf" class="form-control">
                <?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["pword_match_error"])){
                  echo "<span class='help-block'>{$errors["pword_match_error"]}</span>";
                }?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
