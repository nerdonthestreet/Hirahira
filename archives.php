<?php
 // Global database/session initialization.
 require_once "./config.php";

 // Page-specific functions.
 require_once("./content/thumbnail.php");
 require_once("./content/url.php");

 // Only allow $page_sort to be set to ASC or DESC.
 if (!empty($_GET["sort"])) {
  if ($_GET["sort"] == "asc") {
   $page_sort = "ASC";
  } elseif ($_GET["sort"] == "desc") {
   $page_sort = "DESC";
  }
 }
 if (!isset($page_sort)) {
  $page_sort = "DESC";
 }

 // Do some format validation for the year so we don't pass
 // unreasonable user input to an SQL query.
 if (!empty($_GET["year"])) {
  if (is_numeric(substr($_GET["year"], 0, 4))) {
   $page_year = substr($_GET["year"], 0, 4);
  }
 }

 // Validate game names.
 if (!empty($_GET["game"]) && strcmp($_GET["game"],"all") != 0) {
  $get_game_list = $pdo->prepare("SELECT name FROM games");
  $get_game_list->execute();
  $game_list = $get_game_list->fetchAll();
  if (in_array($_GET["game"], array_column($game_list, "name"))) {
   $page_game = $_GET["game"];
  }
 }
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
   <a class="button-blue" href="./index.php">Home</a>
   <a class="button-orange" href="./archives.php">Archives</a>
   <a class="button-blue" href="./clips.php">Clips</a>
  </div>
  
  <div class="browse-filters">
   <div class="browse-filter-row">
    <a <?php if($page_sort == "ASC"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('sort', 'asc') . "'"; ?>>Oldest First</a>
    <a <?php if($page_sort == "DESC"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('sort', 'desc') . "'"; ?>>Newest First</a>
   </div>
   <div class="browse-filter-row">
    <a <?php if($page_year == "2013"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2013') . "'"; ?>>2013</a>
    <a <?php if($page_year == "2014"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2014') . "'"; ?>>2014</a>
    <a <?php if($page_year == "2015"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2015') . "'"; ?>>2015</a>
    <a <?php if($page_year == "2016"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2016') . "'"; ?>>2016</a>
    <a <?php if($page_year == "2017"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2017') . "'"; ?>>2017</a>
    <a <?php if($page_year == "2018"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2018') . "'"; ?>>2018</a>
   </div><div class="browse-filter-row">
    <a <?php if($page_year == "2019"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2019') . "'"; ?>>2019</a>
    <a <?php if($page_year == "2020"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2020') . "'"; ?>>2020</a>
    <a <?php if($page_year == "2021"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2021') . "'"; ?>>2021</a>
    <a <?php if($page_year == "2022"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2022') . "'"; ?>>2022</a>
    <a <?php if($page_year == "2023"){echo "class=button-orange";}else{echo "class=button-blue";} ?> <?php echo "href='./archives.php?" . updateUrl('year', '2023') . "'"; ?>>2023</a>
    <a <?php if(empty($page_year) or $page_year == "all"){echo "class='button-orange'";}else{echo "class='button-blue'";} ?> <?php echo "href='./archives.php?" . updateUrl('year', 'all') . "'"; ?>>All</a>
   </div>
   <div class="browse-filter-row">
    <script>
     function updateGame(newgame) {
      filters = new URLSearchParams(window.location.search);
      filters.set('game', newgame);
      var newUrl = window.location.origin + window.location.pathname + '?' + filters.toString();
      window.location.href = newUrl;
     }
    </script>
    <select name="game" id="game" onchange="updateGame(this.value)" >
     <option value='all'<?php if(empty($_GET['game']) or strcmp($_GET['game'],'all') == 0){echo "selected";} ?>>Select a game...</option>
     <optgroup label="Primary games:" />
     <?php
      $get_games_primary = $pdo->prepare("SELECT name FROM games WHERE type = 'primary' ORDER BY name ASC");
      $get_games_primary->execute();
      foreach(new RecursiveArrayIterator($get_games_primary->fetchAll()) as $game) {
       if ($_GET['game'] == $game[0]) {
        $selected = " selected";
        echo "<script>console.log('TRUE')</script>";
       } else {
        $selected = "";
       }
       echo "<option value='" . $game[0] .  "'" . $selected . ">" . $game[0] . "</option>";
      }
     ?>
     <optgroup label="Secondary games:" />
     <?php
      $get_games_secondary = $pdo->prepare("SELECT name FROM games WHERE type = 'secondary' ORDER BY name ASC");
      $get_games_secondary->execute();
      foreach(new RecursiveArrayIterator($get_games_secondary->fetchAll()) as $game) {
       echo "<option value='" . $game[0] . "'>" . $game[0] . "</option>";
      }
     ?>
    </select>
   </div>
   <?php if(!empty($_GET)) {echo "<div class='browse-filter-row'><a class='button-blue' href='./archives.php'>Reset Filters</a></div>";} ?>
  </div>

  <div class="archive-list">
  
  <?php
   
   // SQL query for retrieving the specified full recordings:
   if (isset($page_year)) {
    $year_clause = " WHERE date_time BETWEEN '" . $page_year . "-01-01' AND '" . $page_year . "-12-31'";
   } else { $year_clause = ""; }
   if (isset($page_game) && strcmp($page_game, 'all') != 0) {
    if (isset($page_year)) { $page_game_prefix = " AND"; } else { $page_game_prefix = " WHERE"; }
    $game_clause = $page_game_prefix . " games.name = '" . $page_game . "'";
   } else { $game_clause = ""; }
   $stmt = $pdo->prepare("SELECT date_time, DATE(date_time) AS date, TIME(date_time) AS time, length, archives.id, vimeo_id, youtube_id, thumb, games.name FROM archives INNER JOIN archives_games ON archives.id = archives_games.archive_id INNER JOIN games ON archives_games.game_id = games.id"
                         . $year_clause . $game_clause . " GROUP BY archives.id ORDER BY date_time " . $page_sort);
   $stmt->execute();

   // $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
   foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $result) {
    $x++;
    $resultColor = ($x%2 == 0)? 'darkResult': 'lightResult';

    echo "<a class='archive-link' href=\"./experience.php?id=" . $result['id'] . "\"><div class=\"archive-entry " . $resultColor . "\" >";

    echo "<div class='archive-thumbnail-container'><img class=\"archive-thumbnail\" src=\"" . getThumbnail($result['thumb'], $result['vimeo_id'], $result['youtube_id']) . "\" /></div>";

    echo "<div class='archive-entry-details'>";

    $stmt2 = $pdo->prepare("SELECT games.name, games.type, archives.id FROM archives INNER JOIN archives_games ON archives.id = archives_games.archive_id INNER JOIN games ON archives_games.game_id = games.id WHERE archives.id = " . $result['id']);
    $stmt2->execute();
    $gameresult = $stmt2->fetchAll();

    echo "<p class='archive-date'><span style='font-weight: bold;'>Date:</span> " . $result['date'] . " @ " . $result['time'] .  "</p>";

    if (count(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "primary")) == 0; })) > 0 && count(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "secondary")) == 0; })) > 0) {
        echo "<p class='archive-game-type-label'>Primary game" . (count(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "primary")) == 0; })) > 1 ? "s" : '' ) .  ":</p>";
        echo "<ul class='archive-game-list'>";
        foreach(new RecursiveArrayIterator(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "primary")) == 0; })) as $game) {
            echo "<li class='archive-game-name'><span class='archive-game-name-inner'>" . $game[0] . "</span></li>";
        }
        echo "</ul>";
        echo "<p class='archive-game-type-label'>Secondary game" . (count(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "secondary")) == 0; })) > 1 ? "s" : '' ) .  ":</p>";
        echo "<ul class='archive-game-list'>";
        foreach(new RecursiveArrayIterator(array_filter($gameresult, function ($gameresult) { return (strcmp($gameresult[1], "secondary")) == 0; })) as $game) {
            echo "<li class='archive-game-name'><span class='archive-game-name-inner'>" . $game[0] . "</span></li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='archive-game-type-label'>Game" . (count($gameresult) > 1 ? "s" : "") . ":</p>";
        echo "<ul class='archive-game-list'>";
        foreach (new RecursiveArrayIterator($gameresult) as $game) {
            echo "<li class='archive-game-name'><span class='archive-game-name-inner'>" . $game[0] . "</span></li>";
        }
        echo "</ul>";
    }

    echo "</div>";

    echo "</div></a>";


   }
   
   unset($pdo);

  ?>
  </div>
  
  <p class="bottom-nav">
  <?php
    if($_SESSION["loggedin"] == false){
     echo '<a class="button-blue" href="./login.php">Log In</a>';
    }
    if($_SESSION["loggedin"] == true){
     if($_SESSION["admin"] == true){
      echo '<a class="button-blue" href="./experience-add.php">Add Recording</a>';
     }
     echo '<a class="button-blue" href="./logout.php">Log Out</a>';
    }
   ?>
  </p>
  
  <?php require_once "./footer.php"; ?>

 </body>
</html>
