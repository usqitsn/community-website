<?php
  class Post{

    //object properties
    private $post_id;
    private $post_name;
    private $post_category;
    private $post_author_id;
    private $post_location;
    private $created_at;

    //constructor
    public function __construct($_post_id=0, $_post_name=0, $_post_category=0, $_post_author_id=0, $_post_location=0, $_created_at=0){
      if($_post_id !== 0){
        $this->validatePostID($_post_id) OR die("Invalid PostID:".$_post_id);
      }
      if($_post_name !== 0){
        $this->validatePostName($_post_name) OR die("Invalid Post Name");
      }
      if($_post_id !== 0){
        $this->validatePostCategory($_post_category) OR die("Invalid Post Category");
      }
      if($_post_author_id !== 0){
        $this->validatePostAuthorID($_post_author_id) OR die("Invalid Post Author ID");
      }
      if($_post_location !== 0){
        $this->validatePostLocation($_post_location) OR die("Invalid Post Location");
      }
      //TO DO: Create validation function for dates
      if($_created_at !== 0){
        $created_at = $_created_at;
      }
    }

    public function getPostID(){
      return $this->post_id;
    }
    public function getPostName(){
      return $this->post_name;
    }
    public function getPostCategory(){
      return $this->post_category;
    }
    public function getPostAuthorID(){
      return $this->post_author_id;
    }
    public function getPostLocation(){
      return $this->post_location;
    }
    public function getPostCreatedAt(){
      return $this->created_at;
    }

    //Function to  insert the post into the db
    public function insertIntoDB($_db){

    }

    //Validates the post id to only contain an unsinged integer or string equivelant
    private function validatePostID($_post_id){
      if(is_numeric($_post_id) && $_post_id>0 && $_post_id<65535){
        $post_id = intval($_post_id);
        return true;
      }else{
        return false;
      }
    }

    //Validates the post name
    private function validatePostName($_post_name){
      $this->post_name = htmlspecialchars($_post_name);
      if($this->post_name === '' or strlen($this->post_name) > 100){
        return false; //Was not a string, or an error occured
      }
      return true;
    }

    //Validates the post name
    private function validatePostCategory($_post_category){
      $this->post_category = htmlspecialchars($_post_category);
      if($this->post_category === '' or strlen($this->post_category) > 50){
        return false; //Was not a string, or an error occured
      }
      return true;
    }

    //Validates the post author id to only contain an unsinged integer or string equivelant
    private function validatePostAuthorID($_post_author_id){
      if(is_numeric($_post_author_id) && $_post_author_id>0 && $_post_author_id<65535){
        $this->post_author_id = intval($_post_author_id);
        return true;
      }else{
        return false;
      }
    }

    //Validate the post location
    private function validatePostLocation($_post_location){
      $url = "https://usqitsn.org".$_post_location;
      if(strlen($url)>19 && strlen($url)<255 && filter_var($url, FILTER_VALIDATE_URL)){
        $this->post_location = $_post_location;
        return true;
      }else{
        return false;
      }
    }
  }
