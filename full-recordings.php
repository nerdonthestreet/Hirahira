<?php
 // Global database/session initialization.
 require_once "./config.php";
 
 $sort = trim($_GET["sort"]);
 if(empty($sort)){
  $sort = desc;
 }
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
 </head>

 <body>
  <h1 class="main-title"><?php echo $sitename ?></h1>
  
  <p class="top-nav">
   <a class="button-orange" href="./index.php">Home</a>
   <a class="button-blue" <?php if($sort == "asc"){echo "style=\"text-decoration: underline; text-underline-position: under;\"";} ?>href="./full-recordings.php?sort=asc">Oldest First</a>
   <a class="button-blue" <?php if($sort == "desc"){echo "style=\"text-decoration: underline; text-underline-position: under;\"";} ?>href="./full-recordings.php?sort=desc">Newest First</a>
  </p>
  
  <table class="full-recordings-list">
   <tr>
    <th>Preview</th>
    <th>Title</th>
    <th>Date</th>
    <th>Primary Game</th>
    <th style="text-align: center;">Secondary Game</th>
    <th>Length</th>
   </tr>
  
  <?php
   
   // SQL query for getting all full recordings, ascending.
   if($sort == "asc"){
    $stmt = $pdo->prepare("SELECT title, date, primary_game, secondary_game, length, id, thumb FROM full ORDER BY date ASC");
   }elseif($sort == "desc"){
    $stmt = $pdo->prepare("SELECT title, date, primary_game, secondary_game, length, id, thumb FROM full ORDER BY date DESC");
   }
   $stmt->execute();

   $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
   foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $result) {
    $x++;
    $resultColor = ($x%2 == 0)? 'darkResult': 'lightResult';
    echo "<tr class=\"" . $resultColor . "\" >";
     echo "<td><a href=\"./experience.php?id=" . $result['id'] . "\"><img class=\"full-preview\" src=\"" . $result['thumb'] . "\" /></a></td>";
     echo "<td><a class=\"episode-link\" href=\"./experience.php?id=" . $result['id'] . "\"> " . $result['title'] . "</a></td>";
     echo "<td>" . $result['date'] . "</td>";
     echo "<td>" . $result['primary_game'] . "</td>";
     echo "<td style=\"text-align: center;\">" . $result['secondary_game'] . "</td>";
     echo "<td>" . $result['length'] . "</td>";
    echo "</tr></a>";
   }
   
   unset($pdo);

  ?>
  </table>
  
  <p class="bottom-nav">
  <?php
    if($_SESSION["loggedin"] == false){
     echo '<a class="button-orange" href="./login.php">Log In</a>';
    }
    if($_SESSION["loggedin"] == true){
     if($_SESSION["admin"] == true){
      echo '<a class="button-blue" href="./experience-add.php">Add Recording</a>';
     }
     echo '<a class="button-orange" href="./logout.php">Log Out</a>';
    }
   ?>
  </p>
  
  <?php require_once "./footer.php"; ?>

 </body>
</html>
