<!--Original author: Ashley Rowe - Vice President - USQITSN
    Contributions:
    Contact: enquiries@usqitsn.org
-->
<?php
  session_start();
  if(isset($_SESSION['login_user'])){
    include "/var/www/components/htmlheadsignedin.html";
    $memberUname = ucfirst($_SESSION['login_user']);
    echo <<<EOT
    <div class="content">
      <div class="sidebar">
        <nav>
          <h4>Recent Posts</h4>
          <ul class='vert-links'>
EOT;
            require_once "/var/www/scripts/dbConfig.php";
            require_once "/var/www/scripts/Objects/post.php";
            require_once "/var/www/scripts/Objects/member_posts.php";
            $member_posts = new Member_Posts($mysqli);
            $posts = $member_posts->queryPosts($mysqli);
            foreach ($posts as $post) {
              print "<a href='".$post->getPostLocation()."' >".$post->getPostName()."</a>";
            }
    echo <<<EOT
          </ul>
        </nav>
      </div>
    <section class='main-content padded-content center larger'>
      <h3>Hi {$memberUname}, welcome to our member's area!</h3>
      <p>Here you will find exclusive content for our members. We hope to update this page regularly with new content, so please check back frequently.</p>

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
      include "/var/www/components/htmlhead.html";
      print "<p>This page is for members only. Please <a href='login.php'>login here</a>.</p>";
      print "<p>If you are not already a member, why not <a href='registration.php'>register</a> to become a member. It is absolutely free!</p>";
    }
    ?>

    </section>
  </div>
    <footer class="site-footer">
        <span class="site-footer-credits"><a href="http://twitter.com/usqitsn">Twitter</a> | <a href="https://www.instagram.com/usqitsn/">Instagram</a> | <a href="http://usq.edu.au">USQ</a> | <a href="https://www.usq.edu.au/current-students/life/student-clubs/find-a-club/it-student-network">USQ-affiliated club page</a> | <a href="logout.php">Log out</a></span>
	  </footer>
  </body>
</html>
