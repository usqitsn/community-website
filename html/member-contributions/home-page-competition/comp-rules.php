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
        <a href="/index.php"><img class="logo" src="/Resources/Images/logo2.png" alt="USQITSN Logo" width="180" height="194"/></a>
            <h1 class="project-name">USQ IT Students' Network</h1>
        </div>
      <nav>
          <a href="/members_area.php" class="btn">Members Area</a>
          <a href="/logout.php" class="btn">Log Out</a>
      </nav>
    </section>
    <section class="main-content padded-content larger">
    <h2 class="center page-title">Homepage Competition</h2>

    <p>Do you want to contribute to our website but don't know where to begin?</p>
    <p>Do you want real world experience building a webpage?</p>
    <p>Do you want something that you can put on your resume and potential employers can actually visit to see your work?</p>

    <p>If you have said yes to any of the above, then this competition is for you!</p>

    <h3>How it works</h3>
    <p>Currently, our homepage at <a href="https://usqitsn.org">usqitsn.org</a> is a little bland and uninspiring. We want something that turns heads, shows what we are about, showcases our members, their contributions and the club's projects. We also want to entice students and employers alike to become members, allowing us to synergise and add value to all of our members.</p>
    <p>Your mission, should you choose to accept it, is to create a homepage that delivers on these requirements. You can work as a team or individually, it is up to you. The winner will have their names in lights on the homepage showcasing their awsome contribution to the club.</p>

    <h3>Rules of engagement</h3>
    <ul>
        <li>The webpage should be constructed using only HTML5, CSS3 and JavaScript.</li>
        <li>The page should be constructed with a mobile first design.</li>
        <li>JavaScript should fail gracefully if users have it turned off.</li>
        <li>Code should be commented clearly, including CSS.</li>
        <li>Everything needed to make the page function should be included with the submission</li>
        <li>Use relative addresses</li>
        <li>The CSS should be easy to apply to the entire website, as it will likely be extended to theme the whole website</li>
        <li>No copyright material should be used. If images are sourced from the web, please include the url in a seperate text file so we can check copyright restrictions</li>
        <li>You may design/redesign the logo or use the one we have in place now.</li>
        <li>Please follow our <a href="">design breif.</a></li>
        <li>Competition closes on March 16th. We will then hold a vote among members to determine the winner.</li>
    </ul>

    <h3>Enough witrh the rules, how do I get started?</h3>
    <p>We thought you would never ask. We want every part of this competition to be about learning and experimentation, because the more technologies you have exposure to, the more comfortable you will be when an employer asks you about them. As such, we have chosen to use a version control and shared repository system, namely, Git and Github. Below you will find instructions on how to use these to prepare your submission. If you have any questions, or you cannot get something working, please feel free to ask.</p>
    <ol>
      <li>
        Create a <a href="https://github.com">Github</a> account, if you do not have one already.
      </li>
      <li>
        Fork <a href="https://github.com/usqitsn/community-website ">our repository</a> on Github. For assistance, please read <a href="https://help.github.com/articles/fork-a-repo/ ">this article.</a>
      </li>
      <li>
        Set up Git on the computer you wish to develop from. For assistance, please read <a href="https://help.github.com/articles/set-up-git/ ">this article.</a>
      </li>
      <li>
        Clone your forked repository to your computer.
        <pre>
          <code class="bash">
          git clone https://github.com/[YOURGITHUBACCOUNT]/community-website.git
        </code>
        </pre>
      </li>
      <li>
        Add an upstream remote
        <pre>
          <code class="bash">
          git remote add upstream https://github.com/usqitsn/community-website
        </code>
        </pre>
      </li>
      <li>
        Pull from the upstream remote
        <pre>
          <code class="bash">
          Git pull upstream master
        </code>
        </pre>
      </li>
      <li>
        Create a branch
        <pre>
          <code class="bash">
          git checkout -b [name_of_your_new_branch]
        </code>
      </pre>
      </li>
      <li>
        Create a folder inside HTML/home-page-competition/studentid and add your code
      </li>
      <li>
        Push your changes to your Github fork (do this as many times as you like until you are complete.)
        <pre>
          <code class="bash">
          git push origin [name_of_your_new_branch]
        </code>
      </pre>
      </li>
      <li>
        Once you are finished with your submission, create a pull request on your forked repository on Github to commit your changes to upstream. Don't forget to let us know who you are.
      </li>
    </ol>

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
  </body>
  <script>hljs.initHighlightingOnLoad();</script>
</html>
