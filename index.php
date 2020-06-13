<?php
 // Global database/session initialization.
 require_once "./config.php";
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
 </head>

 <body>
  <h1 class="main-title"><?php echo $sitename ?></h1>
  <div class="index-nav">
   <a class="button-orange" href="./full-recordings.php">Full Recordings</a>
  </div>
  <?php
   $stmt = $pdo->prepare("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(length))) FROM full");
   $stmt->execute();
   $result = $stmt->fetch();
   echo "<p><span style=\"font-weight: bold;\">Total runtime:</span> " . $result[0];
  ?>
  
  <?php require_once "./footer.php"; ?>
 </body>
</html>
