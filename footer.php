<?php
 echo "<hr/><div class=\"footer\">";
 if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  echo "User: " . $_SESSION["username"] . ". ";
 }
 echo "Session: " . session_id() . ".<br/>Powered by Hirahira!</div>";
?>
