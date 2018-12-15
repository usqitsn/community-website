<!--Original author: Ashley Rowe - Vice President - USQITSN
    Contributions:
    Contact: enquiries@usqitsn.org
-->
<!DOCTYPE html>
<html lang="{{ site.lang | default: "en-US" }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans|Quicksand" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./Style/siteStyle.css">
	<title>USQ IT Students' Network</title>
  </head>
  <body>
    <section class="header">
      <a href="index.html"><img class="logo" src="Resources/Images/logo2.png" alt="USQITSN Logo" width="180" height="194"/></a>
      <div class="header-text">
        <h1 class="project-name">USQ IT Students' Network</h1>
      </div>

      <nav>
          <a href="/logout.php" class="btn">Log Out</a>
      </nav>

    </section>
    <section class='main-content padded-content center larger'>
      <img class="infoImage" src="Resources/Images/underConstruction.png" alt="Under Construction Image" width="200" height="71"/>
    <?php
    session_start();
    if(isset($_SESSION['login_user'])){
      $memberUname = ucfirst($_SESSION['login_user']);
      print "<h3>Hi {$memberUname}, welcome to our member's area!</h3>";
      print "<p>This page is still under construction, but good things come to those who wait. Check back in again soon!</p>";
      echo <<<EOT

      <p>Please read our <a href="/member-contributions/know-and-tell/creating-a-user-registration-form.php">first member's post</a> to "Know and Tell", which details how the member registration form was created using PHP, HTML and CSS.</p>

      <h4>What you can expect to see here:</h4>
      <ul class='no-bullet'>
        <li>In the interest of sharing information: a full write-up of how the <a href="/member-contributions/know-and-tell/creating-a-user-registration-form.php">registration</a> and sign-on functionality has been implemented using pure PHP, HTML and CSS.</li>
        <li>Resources and info shared by our members.</li>
        <li>Benefits provided by our association with other clubs and entities</li>
        <li>Member's event information</li>
        <li>And much, much more! We are always looking for suggestions, so please email <a href='mailto:enquiries@usqitsn.org'>enquiries@usqitsn.org</a> if you have any.</li>
      </ul>
      <!-- Attention Grabber with information tiles -->
      <div class="info-tile-wrapper">
          <!-- Divider for centering the info tiles -->
          <div class="hz-centred">
              <!-- Information Tile -->

              <div class="info-tile">
                  <h3>Join the Discussion</h3>
                  <p>Our <a href='https://www.facebook.com/groups/usqitsn/'>Facebook Group</a> is the home for our member discussions. It provides a simple way for us all to connect and share our thoughts. Check out the group and add to the discussion.</p>
              </div>

              <!-- Information Tile -->
              <div class="info-tile">
                  <h3>Member Contributed Resources</h3>
                  <p>Archives of member contributed content including technical work, programs and tutorial posts.</p>
              </div>
              <div class="clr nospc">&#160;</div>
          </div>
      </div>
EOT;
    }else{
      print "<p>This page is for members only. Please <a href='login.php'>login here</a>.</p>";
      print "<p>If you are not already a member, why not <a href='registration.php'>register</a> to become a member. It is absolutely free!</p>";
    }
    ?>

    </section>
    <footer class="site-footer">
        <span class="site-footer-credits"><a href="http://twitter.com/usqitsn">Twitter</a> | <a href="https://www.instagram.com/usqitsn/">Instagram</a> | <a href="http://usq.edu.au">USQ</a> | <a href="https://www.usq.edu.au/current-students/life/student-clubs/find-a-club/it-student-network">USQ-affiliated club page</a> | <a href="logout.php">Log out</a></span>
	  </footer>
  </body>
</html>
