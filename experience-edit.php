<?php
 // Global database/session initialization.
 require_once "./config.php";
 
 // If you're not an admin, go back to the regular view page.
 if($_SESSION["admin"] != true){
  header("location: /experience.php?id=" . trim($_GET["id"]));
  exit;
 }
 
 // Process POSTing to this file (but only if it's coming from an admin.)
 // Check the input if we POST to this file.
 if($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION["admin"] == true){
  $sql = "UPDATE full SET embed_code = :embed_code, thumb = :thumb, title = :title, date = :date, primary_game = :primary_game, secondary_game = :secondary_game, length = :length WHERE id = :id";
  if($stmt = $pdo->prepare($sql)){
   // Plug variables into the SQL statement.
   $param_embed_code = trim($_POST["embed_code"]);
   $param_thumb = trim($_POST["thumb"]);
   $param_title = trim($_POST["title"]);
   $param_date = trim($_POST["date"]);
   $param_primary_game = trim($_POST["primary_game"]);
   $param_secondary_game = trim($_POST["secondary_game"]);
   $param_length = trim($_POST["length"]);
   $param_id = trim($_GET["id"]);
   $stmt->bindParam(":embed_code", $param_embed_code, PDO::PARAM_STR);
   $stmt->bindParam(":thumb", $param_thumb, PDO::PARAM_STR);
   $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
   $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
   $stmt->bindParam(":primary_game", $param_primary_game, PDO::PARAM_STR);
   $stmt->bindParam(":secondary_game", $param_secondary_game, PDO::PARAM_STR);
   $stmt->bindParam(":length", $param_length, PDO::PARAM_STR);
   $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
   if($stmt->execute()){
    // Redirect to login page
    header("location: experience.php?id=" . trim($_GET["id"]));
   } else{
    echo "Database update failed.";
   }
   unset($stmt);
  }
  unset($pdo);
  exit;
 }
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
  <link rel="stylesheet" type="text/css" href="./player/video-js.css">
  <script src="./player/video.js"></script>
 </head>
 <body>
  <div class="experienceContainer">
  <article class="videoContainer">
   <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
    <?php
     $stmt = $pdo->prepare("SELECT title, date, primary_game, secondary_game, embed_code, thumb, length FROM full WHERE id = :id");
     $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
     $param_id = trim($_GET["id"]);
     $stmt->execute();
     $result = $stmt->fetch();
     echo "<textarea name=\"embed_code\" class=\"edit-field\">" . $result['embed_code'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"thumb\" class=\"edit-field\">" . $result['thumb'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"title\" class=\"edit-field\">" . $result['title'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"date\" class=\"edit-field\">" . $result['date'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"primary_game\" class=\"edit-field\">" . $result['primary_game'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"secondary_game\" class=\"edit-field\">" . $result['secondary_game'] . "</textarea>";
     echo "<textarea style=\"min-height: 1em; width: 50%;\" name=\"length\" class=\"edit-field\">" . $result['length'] . "</textarea>";
    ?>
    <input type="submit" class="btn btn-primary" value="Save">
   </form>
  </article>
  <article class="chatContainer">
   chat test
  </article>
  </div>
  <?php require_once "./footer.php"; ?>
 </body>
</html>
