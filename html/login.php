<!--Original author: Ashley Rowe - Vice President - USQITSN
    Contributions:
    Contact: enquiries@usqitsn.org
-->
      <?php
      session_start();
      if(isset($_SESSION['login_user'])){
        include "/var/www/components/htmlheadsignedin.html";
        echo "<p>You are already logged in!</p>";
      }else{
        include "/var/www/components/htmlhead.html";
        require_once "/var/www/scripts/dbConfig.php";
        if ( ! empty( $_POST ) ) {
          if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
            if(!login($mysqli, $_POST['username'], $_POST['password'])){
              $error = "Incorrect username or password";
            }else{
              header('Location: members_area.php');
            }
          }
        }
        echo   "<section class='main-content grey-back'>";
        require_once "/var/www/components/login_form.php";
      }


      function login(&$_db, $_user, $_pword){
          if($stmt = $_db->prepare("SELECT * FROM membership WHERE username = ?")){
              $stmt->bind_param("s", $param_username);
              $param_username = strtolower($_user);
              if($stmt->execute()){
                $result = $stmt->get_result();
                  $user = $result->fetch_object();
                  // Verify user password and set $_SESSION
          	       if ( password_verify( $_pword, $user->password) ) {
      		             $_SESSION['login_user'] = $user->username;
                       return true;
      	           }
                   return false;
              }else{
                  $error = $stmt->error;
                  $stmt->close();
                  die($error);
              }
          }else{
              die($_db->error);
          }
      }
       ?>

   </section>
     <footer class="site-footer">
         <span class="site-footer-credits"><a href="http://twitter.com/usqitsn">Twitter</a> | <a href="https://www.instagram.com/usqitsn/">Instagram</a> | <a href="http://usq.edu.au">USQ</a> | <a href="https://www.usq.edu.au/current-students/life/student-clubs/find-a-club/it-student-network">USQ-affiliated club page</a></span>
 	  </footer>
   </body>
 </html>
