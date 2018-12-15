<!--Author: Ashley Rowe - Vice President - USQITSN
    Contact: ashley@bitconception.com
-->
<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if(!isset($_SESSION['login_user'])){
        header('Location: /members_area.php');
    }
    require_once "/var/www/scripts/dbConfig.php";

    if($stmt = $mysqli->prepare("SELECT id FROM membership WHERE username = ?")){
        $stmt->bind_param("s", $param_username);
        $param_username = strtolower($_SESSION['login_user']);
        if($stmt->execute()){
          $result = $stmt->get_result();
            $user = $result->fetch_object();
            $uid = $user->id;
        }else{
            $error = $stmt->error;
            $stmt->close();
            die($error);
        }
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
      if(isset($_POST['comment']) && !empty($_POST['comment'] && strlen($_POST['comment'])<2000)){
          $sql = "INSERT INTO post_comments (post_id, comment_member_id, comment_text) VALUES (?, ?, ?)";

          if($stmt = $mysqli->prepare($sql)){
              // Bind variables to the prepared statement as parameters
              $stmt->bind_param("iis", $param_postid, $param_member_id, $param_comment_text);

              // Set parameters
              $param_postid = 1;
              $param_member_id = $uid;
              $param_comment_text = htmlspecialchars($_POST['comment']);

              // Attempt to execute the prepared statement
              if(!$stmt->execute()){
                  die($stmt->error);
              }
              // Close statement
              $stmt->close();
          }else{
            die("Something went wrong. Please try again later.");
          }
      }else{
        die("session not set");
      }

    }

?>

<!DOCTYPE html>
<html lang="{{ site.lang | default: 'en-US' }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/highlight/styles/monokai-sublime.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans|Quicksand" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/Style/siteStyle.css">
    <script src="../../highlight/highlight.pack.js"></script>
	<title>USQ IT Students' Network</title>
  </head>
  <body>
    <section class="header">
        <div class="main-header">
        <a href="index.php"><img class="logo" src="/Resources/Images/logo2.png" alt="USQITSN Logo" width="180" height="194"/></a>
            <h1 class="project-name">USQ IT Students' Network</h1>
        </div>
      <nav>
          <a href="/members_area.php" class="btn">Members Area</a>
          <a href="/logout.php" class="btn">Log Out</a>
      </nav>
    </section>
    <section class="main-content padded-content larger">
    <h3 class="center page-title">Know and Tell</h3>
    <h4 class="center page-title">HTML, PHP and MySQL - How to implement a user registration page</h4>

    <div class="post-info">
      <h5>Author: Ashley Rowe</h5>
      <h5>Publication Date: 14/12/2018</h5>
    </div>
    <div class="center">
      <h4>Table of Contents</h4>
      <ul class="no-bullet">
          <li><a href="#p1">Part 1: Intro and Server Setup</a></li>
          <li><a href="#p2">Part 2: Setting up the database</a></li>
          <li><a href="#p3">Part 3: Database Connection Script</a></li>
          <li><a href="#p4">Part 4: The Sign Up Script</a></li>
          <li><a href="#p4.1">4.1: The HTML Form</a></li>
          <li><a href="#p4.2">4.2 PHP Validation Functions</a></li>
          <li><a href="#p4.3">4.3 Creating Error Messages</a></li>
          <li><a href="#p4.4">4.4 Inserting into the Database</a></li>
          <li><a href="#commentDisplay">Member Comments</a></li>
      </ul>
    </div>
    <p>Welcome to the first members installment of <b>Know and Tell</b>. I envision this corner of the website to be dedicated to our members learning through teaching others. I have always believed the best way to learn it by teaching others, it allows you to fully digest whatever it is you are learning and put it into an easy to understand format which benefits both yourself and your audience. The eventual plan will be to implement a platform for members to write and post content in a simple way, but for now, if you are interested in contributing something, please email <a href="mailto:enquiries@usqitsn.org">enquiries@usqitsn.org</a> and we would be happy to feature your content. Please read our <a href="/member-contributions/contributing-content.php">contibuting content</a> page if you would like to learn more.</p>


    <h4 id="p1">Part 1: Intro and Server Setup</h4>
    <p>In this post I will be providing an end to end overview of how to implement an account creation and login system using only PHP, HTML, CSS and a little bit of bootstrap. This entire tutorial is based on how I implemented the USQITSN member registration on this website. Some of you may be wondering why you would bother implementing your own user login system when there are so many "good" content management systems out there. Whilst I do not want to start a philosophical debate, I will say that there are always trade offs when you choose an off-the-shelf system, and usually that trade off involves lower performance (due to the overhead of all the code needed for features you will never use), and also added complexity when you need some programatic feature added that someone has not provided a plug-in for. But in terms of why we are choosing to implement our own system for USQITSN, it is mainly in order to learn how it is done.</p>

    <p>You can play along at home, all you need is a <a href="https://httpd.apache.org/">Apache</a> web server, with PHP modules installed and <a href="https://www.mysql.com/">MySQL</a>. I find it best to start with a clean slate if you are not familiar with the LAMP stack (Linux, Apache, MySql, PHP). There are a few gotcha's that I will try to cover off on as we go through. For now, you could simply install <a href="https://www.debian.org/">Debian</a> in a virtual machine, then follow <a href="https://tecadmin.net/install-lamp-stack-debian-9-stretch/">these instructions</a> to install the LAMP stack. Make sure you note down the MySQL root password.</p>

    <p>There are two configuration changes that we will need to make before we get stuck into builing the scripts to make the magic happen.</p>
    <p>First of all we need to modify the php.ini file so that it will report any errors in the browser. This is extremely useful when debugging the website, but be mindful to have this function turned off in any production environment, to prevent giving malicous users information that they could use to breach your site security.</p>
    <p>The php.ini file is usually located in <em>/etc/php/7.2/apache2/</em>, you might need to open it as root to edit it.</p>

    <p>From the command line, run the following command to edit the php.ini file.</p>
    <pre>
      <code class="bash">
        sudo nano /etc/php/7.2/apache2/php.ini
      </code>
    </pre>
    <p>Then uncomment following lines to the php.ini file:</p>
    <pre>
      <code class="bash">
        error_reporting = E_ALL & ~E_NOTICE
        display_errors = On
      </code>
    </pre>
    <p>Save the php.ini file, then restart the apache web server with the following command:</p>
    <pre>
      <code class="bash">
          sudo /etc/init.d/apache2 restart
      </code>
    </pre>

    <p>Alternatively, if that does not work for you, you could always add the following 2 lines to each php script you want error messages displayed on:</p>
    <pre>
      <code class="php">
        session_start();
        error_reporting(E_ALL);
      </code>
    </pre>

    <p>The next step is to modify the MySQL config file so that the correct password encryption algorithm is used (which we will use to encrypt user passwords). This is necessary as it appears that PHP 7.2 does not support the <em>caching_sha2_password</em> plugin.</p>
    <p>From the command line, run the following command to edit the my.cnf file.</p>
    <pre>
      <code class="bash">
          sudo nano /etc/mysql/my.cnf
      </code>
    </pre>
    <p>Add the following lines to the bottom of the my.cnf file:</p>
    <pre>
      <code class="bash">
        [mysqld]
        default-authentication-plugin=mysql_native_password
      </code>
    </pre>

    <h4 id="p2">Part 2: Setting up the database</h4>
    <p>This section will describe how to set up a simple database and single table to store the user details. By using a database we can ensure that the website is extensible whilst also allowing concurrent user access. When it comes time to add additional functionality to the website, we can build relationhips among the tables so that we can link users to other data objects such as posts and comments.</p>

    <p>To begin with, we need to log into MySQL as the root user. Simply run the following command at the command line and enter the root password you set when installing MySQL.</p>
    <pre>
      <code class="bash">
          mysql -u root -p
      </code>
    </pre>

    <p>Now we want to create a database to store the tables for the website.</p>
    <pre>
      <code class="sql">
          CREATE DATABASE db_websiteName;
      </code>
    </pre>
    <p>Next, we want to create a user which we will be given specific permissions to access the database. It is not recommended to use the root user as it would provide anyone who is able to successfully deploy an SQL Injection attack unfettered access to the SQL server instance.</p>
    <pre>
      <code class="sql">
          CREATE USER 'webuser'@'localhost' IDENTIFIED BY 'password';
      </code>
    </pre>
    <p>Be sure to change the password to something secure and note down this password as we will be using it later.</p>

    <p>We then want to change into the newly created database so that any SQL queries we run will be run on the correct database.</p>
    <pre>
      <code class="sql">
          USE db_websiteName;
      </code>
    </pre>

    <p>Now that we have the configuration out of the way, we can actually create the table where the data will be stored. For this tutorial, we will be creating a simple table to store a user ID, username, first name, last name, email address, password, member type, and creation date.</p>
    <p>Run the following command in the MySQL prompt at the command line.</p>
    <pre>
      <code class="sql">
        CREATE TABLE membership(
        	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
        	fname VARCHAR(75) NOT NULL,
        	lname VARCHAR(75) NOT NULL,
        	email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
        	member_type VARCHAR(50) NOT NULL,
        	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
      </code>
    </pre>
    <p>Whilst I will not go into too much detail on the SQL query, I will explain a couple of things: the general syntax to create a table is the <em>CREATE TABLE</em> keyword, followed by a comma seperated list of column name, data type and options for each attribute. For the first column, we are asking for an unsigned integer which will be the primary key (must be unique and not null) and will auto increment as new entries are added. The rest are fairly easy to understand, with the exception of the last one. The created_at column will store the date and time that the row (or tuple) is created. The <em>DEFAULT CURRENT_TIMESTAMP</em> option tells the server to default to whatever the current date and time is when the row is created.</p>

    <p>Now that we have created the table, we will need to give our new user permissions to use the table. The below command provides the user with select, update and insert privelages on all tables within the database. We then flush the privelages to ensure they are applied correctly.</p>

    <pre>
      <code class="sql">
        GRANT SELECT, UPDATE, INSERT ON db_websiteName.* TO 'webuser'@'localhost';
        FLUSH PRIVILEGES;
      </code>
    </pre>

    <p>Congratulations! At this point you now have all of the prerequisites in place to start writing your PHP scripts which will store and retrieve information in this table.</p>

    <h4 id="p3">Part 3: Database Connection Script</h4>
    <p>Now we need to tell the PHP processor how to connect to the database. They need some way to talk to each other. The way to do this is with the <em>mysqli</em> class. Please note that there is an older <em>mysql</em> class which has been depreciated, as the newer version has extended capabilites. There is also an option to use the PDO class which is an abstraction layer to generalise the connection to a number of different databases, however I will be covering PDO in this tutorial.</p>

    <p>You will need to create a new file to hold the below script. Name it something meaningful such as <em>dbConnection.php</em> and save the file outside of the web root (above the html folder in the directory tree). The reason we save the file outside the web root is to ensure that the file cannot be accessed by website visitors, since it will store the database user password.</p>
    <p>The great thing about php is that you can compose documents from external components, such as this script, by using the <em>include</em> or <em>require</em> keywords. This means that the below script can live outside the document, and at runtime can be inlined into the referencing php document before being processed.</p>

    <pre>
      <code class="php">
          &lt?php
          /* Database credentials.*/
          define('DB_SERVER', 'localhost');
          define('DB_USERNAME', 'webuser');
          define('DB_PASSWORD', 'password');
          define('DB_NAME', 'db_websiteName');

          /*Attempt to connect to MySQL database*/
          $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

          // Check connection
          if($mysqli === false){
              die("ERROR: Could not connect. " . $mysqli->connect_error);
          }
          ?&gt
      </code>
    </pre>

    <p>The above code begins by defining 4 named constants which will be the credentials used to connect to the database. These constants are then passed to the mysqli constructor which will either return a valid connection object, or false if there was an error. We can then test the returned value to ensure that the connection succeeded. </p>

    <h4 id="p4">Part 4: The Sign Up Script</h4>

    <p>In this section we get to the nitty-gritty of creating the signup script. There are a number of things we need to do:</p>
    <ul>
      <li>Construct the HTML form to take user input.</li>
      <li>Create functions to validate each user input field.</li>
      <li>Create and capture error messages to report to the user.</li>
      <li>Insert the validated values into the database table.</li>
      <li>Redirect the user to the login page upon successful user creation.</li>
    </ul>

    <h5 id="p4.1">4.1: The HTML Form</h5>
    <p>Let's start with the HTML, as it will allow us to see what we are working with.</p>

    <pre>
      <code class="html">
        &lthtml lang="en"&gt
        &lthead&gt
           &ltmeta charset="UTF-8"&gt
           &ltmeta name="viewport" content="width=device-width, initial-scale=1"&gt
           &lttitle&gtSign Up&lt/title&gt
           &ltlink rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css"&gt
           &ltstyle type="text/css"&gt
               body{ font: 14px sans-serif; background-color: #32353a; color: #ffffff;}
               .wrapper{ width: 350px; padding: 20px; margin-left: auto;margin-right: auto;}
               img{ display: block; margin-left: auto; margin-right: auto;}
               h2,h5{text-align:center;}
           &lt/style&gt
        &lt/head&gt
      </code>
    </pre>

    <p>I am hoping that most readers are familiar with HTML, but if not then there are plenty of online resources to help you learn the language. I will point out a couple of things in this head section though.</p>
    <p>First of all, you may have noticed that I am linking to a stylesheet on a remote server. This linked stylesheet is refering to <a href="https://getbootstrap.com/">bootstrap</a>, which is essentially just a HTML, CSS and JavaScript library which allows you to construct modern looking pages through the use of predefined components. I have not worked with bootstrap much, so my knowledge on the matter is minimal. However I have found it helpful in reducing the amount of fiddling required to get the HTML to present well. The bootstrap.css file is hosted at a CDN (Content Distribution Network), which allows you to link to the page remotely rather than downloading and hosting the css file yourself.</p>
    <p>Additionally, the meta viewport line is required to allow for better handling of mobile devices. If you add this line, bootstrap will play nicely on mobile devices. You may also notice that there are some minor styling CSS that has been added. Feel free to change this to suit the style of your site.</p>

    <p>The body section is rather long, but it pays to look at the body as a whole so that you can see each component.</p>


    <pre>
      <code class="html">
        &ltbody&gt
           &ltdiv class="wrapper"&gt
             &ltimg class="logo" src="Resources/Images/logo2.png" alt="USQITSN Logo" width="120" height="134"/&gt
               &lth2&gtBecome a Member&lt/h2&gt
               &lth5&gtPlease fill this form to create an account.&lt/h5&gt
               &ltform action="&lt?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?&gt" method="post"&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtUsername&lt/label&gt
                       &ltinput type="text" name="username" required="true" pattern="[a-zA-Z0-9]{1,50}"
                       title="Username should only contain 1-50 alphanumeric characters" class="form-control"
                       value="&lt?php if(isset($_POST["username"])){echo $_POST["username"];} ?&gt"/&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["uname_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["uname_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtFirst Name&lt/label&gt
                       &ltinput type="text" name="fname" required="true" pattern="[a-zA-Z0-9]{1,75}"
                       title="First name should only contain 1-75 alphanumeric characters" class="form-control"
                       value="&lt?php if(isset($_POST["fname"])){echo $_POST["fname"];} ?&gt"/&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["fname_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["fname_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtLast Name&lt/label&gt
                       &ltinput type="text" name="lname" required="true" pattern="[a-zA-Z0-9]{1,75}"
                       title="Last name should only contain 1-75 alphanumeric characters" class="form-control"
                       value="&lt?php if(isset($_POST["lname"])){echo $_POST["lname"];} ?&gt"/&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["lname_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["lname_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtEmail&lt/label&gt
                       &ltinput type="email" name="email" required="true" class="form-control"
                       value="&lt?php if(isset($_POST["email"])){echo $_POST["email"];} ?&gt"&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["email_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["email_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtMembership Type&lt/label&gt
                       &ltselect name="member_type" class="form-control"&gt
                         &lt?php foreach ($member_types as $value) {
                           echo "&ltoption value='{$value}'&gt{$value}&lt/option&gt";
                         }
                         ?&gt
                       &lt/select&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["member_type_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["member_type_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtPassword&lt/label&gt
                       &ltinput type="password" name="pword" class="form-control"&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["pword_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["pword_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltlabel&gtConfirm Password&lt/label&gt
                       &ltinput type="password" name="pwordConf" class="form-control"&gt
                       &lt?php if($_SERVER["REQUEST_METHOD"] == "POST" && isset($errors["pword_match_error"])){
                         echo "&ltspan class='help-block'&gt{$errors["pword_match_error"]}&lt/span&gt";
                       }?&gt
                   &lt/div&gt
                   &ltdiv class="form-group"&gt
                       &ltinput type="submit" class="btn btn-primary" value="Submit"&gt
                       &ltinput type="reset" class="btn btn-default" value="Reset"&gt
                   &lt/div&gt
                   &ltp&gtAlready have an account? &lta href="login.php"&gtLogin here&lt/a&gt.&lt/p&gt
               &lt/form&gt
           &lt/div&gt
       &lt/body&gt
       &lt/html&gt
      </code>
    </pre>

    <p>Let's take a look at the form, the action attribute is set to <em>$_SERVER["PHP_SELF"]</em> which is essentially telling the page to post back to itself when the submit button is pressed. This allows us to process the form using PHP on the same page. The CSS classes are also required so that bootstrap can do it's magic.</em></p>

    <p>We will only look at a couple of the fields in detail, and we will start with the username. Essentially, we have a text input field named username and we are telling the HTML to perform some validation so that the user can get immediate feeback without requiring to post back to the server. In this case we are saying that the field is required and that is must match a specific regex pattern. If you don't know much about regex, the <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions"> MDN reference</a> (Mozilla Developers Network) provides a faily comprehensive overview of how they work. The pattern is simply saying we are looking for a string between 1 to 50 characters in length that is comprised only of lower and uppercase letters and/or numbers.</p>
    <p>A neat hack that I learned through writing the form is that if you want to use HTML validation rather than JavaScript but would like to have a custom error message displayed, you can use the title attribute, which gets displayed below the standard error message.</p>
    <p>Getting to the islands of PHP code held within the the html - the first of which is simply telling the page to display the previously entered username if, after a postback, the form is being displayed again (such as when an error occurs). The second PHP island will add a span element to display an error message if the postback to the server turned up an error with the field on the server side.</p>

    <p>The rest of the fields are fairly similar, the only one that stands out as being a little different is the membership-type field. This field uses php to construct the <em>option</em> elements. Finally, we have a submit button and reset button, as well as a link to the login page before closing off the HTML.</p>

    <h5 id="p4.2">4.2 PHP Validation Functions</h5>
    <p>Next we will discuss the PHP functions that will complete the validation on the server side. The rest of the PHP will be added to the code above the HTML so that it is processed first, though I beleive the functions can live anywhere on the page as they do not require forward declarations.</p>

    <pre>
      <code class="php">
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
        function checkUnique(	&amp;$_db, $_sql, $_val){
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
            if(!preg_match('/^[[:alpha:]]{1,75}$/',$_fname)){
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
            if(!preg_match('/^[[:alpha:]]{1,75}$/',$_lname)){
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
            } elseif(strlen(trim($_pword)) &lt; 6){
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
        * @param 	&amp;$member_type_array  A reference to an array containing valid member types
        * @return   True if valid, otherwise false
        */
        function validateMemberType($_member_type, 	&amp;$member_type_array){
          if(!in_array($_member_type,$member_type_array)){
            return false;
          }
          return true;
        }
      </code>
    </pre>

    <p>By moving the validation testing out of the body of the PHP and into functions, we can reuse the code and also have a cleaner code base which makes it more readable. Again, I will not go through each of the functions individually because my comments discribe their functionality, however, I will discusses how they are used and some specific points about their implementation.</p>
    <p>One of the questions you may be asking is why would we be validating the user input both in the HTML as well as in the server side PHP. The reason is that they serve two different functions. HTML or JavaScript validation are great for providing immediate feedback to the user as the input does not need to be passed back to the server. However, these types of validation can be bypassed. Users can turn off JavaScripts, and anyone with the know-how does not even need to be using a web browser to communicate with the server as values can be passed in via the data field in a http request. For these reasons, you should always validate input on the serverside. It is usefull, but less critical, that input be validated on the client side.</p>
    <p>The types of validation we are running are pretty simple. We are generally checking that the fields meet the min and max length requirements and meet a specified pattern or format. The main exception is the password confirmation field which is also checking that it matches the password field. Each function returns either true (valid) or false (invalid), we will use these return values to determin if we need to display an error message.</p>

    <h5 id="p4.3">4.3 Creating Error Messages</h5>

    <p>The next section we will look at is how the error checking and message setting is implemented.</p>
    <pre>
      <code class="php">
        &lt;?php
            require_once "/var/www/scripts/dbConfig.php";

            $errors = array();
            $member_types = array("USQ Student", "Alumni", "External", "USQ Staff");
            //Only process when form is submitted
            if($_SERVER["REQUEST_METHOD"] == "POST"){
              if(!validateUsername($_POST["username"])){
                $errors["uname_error"] = "Username must be between 1 and 50 characters long and only contain alphanumeric characters";
              }else{
                if(!checkUnique($mysqli, "SELECT id FROM membership WHERE username = ?", $_POST["username"])){
                  $errors["uname_error"] = "There is already a memeber with this username.";
                }
              }
              if(!validateFirstName($_POST["fname"])){
                $errors["fname_error"] = "First name must be between 1 and 75 characters long and only contain alpha characters";
              }
              if(!validateLastName($_POST["lname"])){
                $errors["lname_error"] = "Last name must be between 1 and 75 characters long and only contain alpha characters";
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
      </code>
    </pre>

    <p>First, we want to include the dbConnection script we wrote earlier so that we can connect to the database. Next we will be creating two arrays; one to hold the error messages and another to hold the valid member types. If we were going to be a little more general, we could opt to fill the member type array with values from a table containing valid member types but I have not done that just to keep things simple at this point.</p>
    <p>Now we move onto the error checking. We are going to use the post values that were submitted as a post back when the user clicked the submit button. We can all these by the names we set in the html name attributes of the form elements. The names and values are populated into a super-global associative array called <em>$_POST</em>. We pass these values to our functions and use if statements to check the boolean result of the validation. We are actually taking the inverse since we are using the NOT operator (!). So we are saying, if it is not valid, then create an key and value pair in the <em>$errors</em> associative array, where the value is the error message. You will also notice that some of the validation is nested, this is because it relys on the higher level test to return true before it makes sense to test the lower level value.</p>

    <h4 id="p4.4">4.4 Inserting into the Database</h4>
    <p>Once our error checking is complete, it is time to insert the values into the membership table.</p>

    <pre>
      <code class="php">
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
                    $stmt->close();
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
      </code>
    </pre>

    <p>Here we use array_filter to weed out any empty values in the errors array then test to see if the array itself is empty. If it is empty then we are assuming that everything passed validation and thus we can insert into the table.</p>
    <p>We will use prepared SQL statements to insert into the table. The reason for this are twofold, firstly it is much safer as we do not need to escape any user input, this is because the statements are precompiled and only the parameters are bound at runtime. This means that the sql query cannot be altered, making it safe from SQL injection attacks. Secondly, because the statements are precompiles, we will see a minor performance improvement. This probably would not be too noticable though unless we are running a large number of queries.</p>
    <p>To use prepared statements, we create a string containing the insert statement, but rather than including values or variables directly, we place a questionmark in their spot. These questionmarks will be replaced with the bound parameters later. We will be constructing a statement object by passing the SQL string to the mysqli member function. If for some reason the statement has failed to be created, it will return false, so it is important to test for this.</p>
    <p>Next, we bind the parameters to the statement. We have not set the values for these parameters yet, but it is perfectly legal to do so after binding to the statement. The "ssssss" string indicates that each of the 6 parameters will be a string ("s"). We then set the values for the parameters and execute the statement. Note the use of <em>password_hash</em> function, we are asking to use the default encryption algorithm which provides a one-way hash that is also salted (this means that even if an attacher were to get access to the hash value, it would be almost impossible to deduce the password through collisions). This function Again, the execution can fail, so we must test for it.</p>

    <p>If we have succeeded, we can close the statement, then redirect the user to the login page. Otherwise, we should tell the user something went wrong.</p>

    <p>That's it for this tutorial. I will post the next tutorial shortly which will explain how the member login works and how we can maintain state accross pages after a user logs in, so that they do not need to log in to each page they view.</p>

    <p>I would love to hear your comments. I am by no-means an expert, I just decided to have a go at creating these scripts and I am sure there are others out there that can provide some useful feedback on how we can improve the design. Also if you have any questions, ask them in the comments and I will do my best to answer them.</p>
    <p>Ash.</p>

    </section>
          <div class="row bootstrap snippets">
              <div class="col-md-6 col-md-offset-2 col-sm-12">
                  <div class="comment-wrapper" id="commentDisplay">
                      <div class="panel panel-info">
                          <div class="panel-heading">
                              Member Comments
                          </div>
                          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                          <div class="panel-body">
                              <textarea class="form-control" name="comment" id="comment" maxlength="2000" required="true" placeholder="write a comment..." rows="3"></textarea>
                              <br>
                              <button type="submit" value="Submit" class="btn btn-info pull-right">Post</button>
                              <div class="clearfix"></div>
                              <hr>
                              <ul class="media-list">
                                <?php
                                $sql = "SELECT m.username, p.created_at, p.comment_text FROM membership m JOIN post_comments p ON m.id = p.comment_member_id LIMIT 100";
                                  if($result = $mysqli->query($sql)){
                                    while ($row = $result->fetch_object()){
                                      echo "<li class='media'>";
                                      echo "<a href='#' class='pull-left'>";
                                      echo "<img src='/Resources/Images/vsblacklogo.png' alt='avitar' class='img-circle'></a>";
                                      echo "<div class='media-body'>";
                                      echo "<span class='text-muted pull-right'>";
                                      echo "<small class='text-muted'>".$row->created_at."</small>";
                                      echo "</span>";
                                      echo "<strong class='text-success'>".$row->username."</strong>";
                                      echo "<p>".$row->comment_text."</p>";
                                      echo "</div>";
                                      echo "</li>";
                                    }
                                  }else{
                                      die($result->error);
                                  }
                              ?>
                              </ul>
                          </div>
                        </form>
                      </div>
                  </div>
              </div>
          </div>
    <body/>
    <script>hljs.initHighlightingOnLoad();</script>
</html>
