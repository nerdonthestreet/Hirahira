<?php
 // Global database/session initialization.
 require_once "./config.php";

 // Page-specific functions:
 require_once "./content/runtime.php";
?>
<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
  <link rel="icon" type="image/png" href="/favicon.png">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
 </head>

 <body>
  <h1 class="main-title"><?php echo $sitename ?></h1>
  <div class="top-nav">
   <a class="button-orange" href="./index.php">Home</a>
   <a class="button-blue" href="./archives.php">Archives</a>
   <a class="button-blue" href="./clips.php">Clips</a>
  </div>

  <div class="introduction">
   <h2>Welcome!</h2>
   <?php echo $welcome_text; ?>
  </div>

  <div class="stats">
   <h2>Stats:</h2>
   <ul>
   <?php
    $stmt = $pdo->prepare("SELECT SUM(TIME_TO_SEC(length)), COUNT(*) FROM archives");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<li><span style='font-weight: bold;'>Archived streams:</span> " . $result[1] . "</li>";
    echo "<li><span style='font-weight: bold;'>Total runtime:</span> " . formatRuntime($result[0]) . "</li>";
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM games");
    $stmt2->execute();
    $result2 = $stmt2->fetch();
    echo "<li><span style='font-weight: bold;'>Games streamed:</span> " . $result2[0] . "</li>";
   ?>
   </ul>
  </div>

  <?php require_once "./footer.php"; ?>
 </body>
</html>
