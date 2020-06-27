<?php
 // Global database/session initialization.
 require_once "./config.php";
 
 // If you're not an admin, go back to the regular view page.
 if($_SESSION["admin"] != true){
  header("location: /full-recordings.php");
  exit;
 }
 
 // Process POSTing to this file (but only if it's coming from an admin.)
 // Check the input if we POST to this file.
 if($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION["admin"] == true){
  $sql = "INSERT INTO full (embed_code, thumb, title, date, primary_game, secondary_game, length) VALUES (:embed_code, :thumb, :title, :date, :primary_game, :secondary_game, :length)";
  if($stmt = $pdo->prepare($sql)){
   // Plug variables into the SQL statement.
   $param_embed_code = trim($_POST["embed_code"]);
   $param_thumb = trim($_POST["thumb"]);
   $param_title = trim($_POST["title"]);
   $param_date = trim($_POST["date"]);
   $param_primary_game = trim($_POST["primary_game"]);
   $param_secondary_game = trim($_POST["secondary_game"]);
   $param_length = trim($_POST["length"]);
   $stmt->bindParam(":embed_code", $param_embed_code, PDO::PARAM_STR);
   $stmt->bindParam(":thumb", $param_thumb, PDO::PARAM_STR);
   $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
   $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
   $stmt->bindParam(":primary_game", $param_primary_game, PDO::PARAM_STR);
   $stmt->bindParam(":secondary_game", $param_secondary_game, PDO::PARAM_STR);
   $stmt->bindParam(":length", $param_length, PDO::PARAM_STR);
   if($stmt->execute()){
    // Redirect to episode list
    header("location: full-recordings.php");
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
 </head>
 <body>
  <div class="experienceContainer">
  <article class="videoContainer">
   <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
    <textarea name="embed_code" class="edit-field">&lt;source src=&quot;autoURL&quot; type=&quot;application/x-mpegURL&quot; res=&quot;9999&quot; label=&quot;auto&quot; /&gt;&#13;&lt;source src=&quot;720pURL&quot; type=&quot;video/mp4&quot; res=&quot;720&quot; label=&quot;720p&quot; /&gt;&#13;&lt;source src=&quot;540pURL&quot; type=&quot;video/mp4&quot; res=&quot;540&quot; label=&quot;540p&quot; /&gt;&#13;&lt;source src=&quot;360pURL&quot; type=&quot;video/mp4&quot; res=&quot;360&quot; label=&quot;360p&quot; /&gt;&#13;&lt;track id=&quot;entrack-1&quot; kind=&quot;captions&quot; src=&quot;https://kushking.tips/chat-logs/unavailable.vtt&quot; srclang=&quot;en&quot; label=&quot;Chat&quot; type=&quot;text/vtt&quot; default&gt;</textarea>
    <textarea style="min-height: 1em; width: 50%;" name="thumb" class="edit-field">ThumbnailURL</textarea>
    <textarea style="min-height: 1em; width: 50%;" name="title" class="edit-field">Title</textarea>
    <textarea style="min-height: 1em; width: 50%;" name="date" class="edit-field">DateTime</textarea>
    <textarea style="min-height: 1em; width: 50%;" name="primary_game" class="edit-field">PrimaryGame</textarea>
	<textarea style="min-height: 1em; width: 50%;" name="secondary_game" class="edit-field">SecondaryGame</textarea>
    <textarea style="min-height: 1em; width: 50%;" name="length" class="edit-field">Runtime</textarea>
    <input type="submit" class="btn btn-primary" value="Save">
   </form>
  </article>
  <article class="chatContainer" id="admin-instructions">
   <h2>Instructions:</h2>
   <p><span style="font-weight: bold;">Sources:</span> Enter a list of VideoJS sources (for each resolution of video, followed by the chat log.)
   <p>The chat log defaults to "unavailable.vtt", where "unavailable" should be changed to the ID of this episode (as seen in your URL bar) if a chat log is available.</p>
   <p><span style="font-weight: bold;">Thumbnail:</span> Enter a direct link to this recording's thumbnail.</p>
   <p><span style="font-weight: bold;">Title:</span> Enter the human-readable title of this recording/stream.</p>
   <p><span style="font-weight: bold;">Date/time:</span> Enter the date and time that this recording began (in Mountain time): YYYY-MM-DD HH:MM:SS</p>
   <p><span style="font-weight: bold;">Primary game:</span> Enter the primary game played during this stream.</p>
   <p><span style="font-weight: bold;">Secondary games:</span> Enter a comma-separated list of secondary games played during this stream.</p>
   <p><span style="font-weight: bold;">Runtime:</span> Enter the runtime of this stream: HH:MM:SS</p>
  </article>
  </div>
  <?php require_once "./footer.php"; ?>
 </body>
</html>
