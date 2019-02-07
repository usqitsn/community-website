<?php

//A class for containing a list of all member posts
class Member_Posts{
  private $conn; // Database connection
  private const MEMBER_POSTS_TABLE = "member_posts";

  //object properties
  public $posts;

  //Constructor
  public function __construct(&$_db){
    $posts = array();
  }

  public function queryPosts(&$_db){
    $query = "SELECT * FROM ". Member_Posts::MEMBER_POSTS_TABLE . ";";

    if($result = $_db->query($query)){

      if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
          $posts[] = new Post($row["post_id"],$row["post_name"],$row["post_category"],$row["post_author_id"],$row["post_location"],$row["created_at"]);
        }
        return $posts;
      }else{
        return $_db->error;
      }
    }else{
      return $_db->error;
    }
  }
}
