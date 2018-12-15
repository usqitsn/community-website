<!--Original author: Ashley Rowe - Vice President - USQITSN
    Contributions:
    Contact: enquiries@usqitsn.org
-->
<?php
  session_start();
  if(isset($_SESSION['login_user'])){
    include "/var/www/components/htmlheadsignedin.html";
  }else{
    include "/var/www/components/htmlhead.html";
  }
?>

    <section class="main-content padded-content">
      <div class="center larger">
        <p>Hi, there! Thanks for checking out USQITSN. We exist to create and encourage <strong>community</strong>, <strong>connections</strong> and <strong>collaboration</strong> amongst IT students at USQ and the broader IT community. As a member, you will gain access to additional resources, networking oportunities, affiliations and personal development initiatives aimed at helping us all be the best we can be.
          Hoping we can all help each other explore everything IT while having some fun along the way!</p>

        <a href="/registration.php" class="big-btn">become a member</a>
      <br><br>
        <p>Once you become a member, don't forget to check out the discussion on our Facebook group. Our members are usually very active and you will find the conversation generally helpful.</p>
        <p>General enquiries: <a href="mailto:enquiries@usqitsn.org">enquiries@usqitsn.org</a></p>
        <p>Follow or tweet us: <a href="http://twitter.com/usqitsn">@usqitsn</a></p>
      <br><br>
        <p><em>What began as a <a href="https://www.facebook.com/groups/usqitsn/">Facebook group</a> of IT students developed into a USQ-affiliated student "club" that was founded in early 2018. In addition to bringing the wider community of USQ IT students together, we place a high priority on enhancing industry-readiness and providing professional development opportunities to students as we are able.</em></p>
      </div>
    </section>
    <?php
      include "/var/www/components/htmlfoot.html";
     ?>
