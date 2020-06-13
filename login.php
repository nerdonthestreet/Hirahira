<?php
 // Global database/session initialization.
 require_once "./config.php";
 
 // If we're already logged in, redirect back to the homepage.
 if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: index.php");
  exit;
 } 
 
 $username = "";
 $password = "";
 $username_err = "";
 $password_err = "";
 
 // Check the input if we POST to this file.
 if($_SERVER["REQUEST_METHOD"] == "POST"){

  // Check if username is empty.
  if(empty(trim($_POST["username"]))){
    $username_err = "Please enter a username.";
  } else{
   $username = trim($_POST["username"]);
  }
   
  // Check if password is empty.
  if(empty(trim($_POST["password"]))){
   $password_err = "Please enter a password.";
  } else{
   $password = trim($_POST["password"]);
  }
  
  // Check credentials against database.
  if(empty($username_err) && empty($password_err)){
   if ($stmt = $pdo->prepare("SELECT id, username, password, admin FROM accounts WHERE username = :username")){
    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
    $param_username = trim($_POST["username"]);
    if($stmt->execute()){
     // Check if the account exists. If it does, compare its password with the provided password.
     if($stmt->rowCount() == 1){
      if($row = $stmt->fetch()){
       $id = $row["id"];
       $username = $row["username"];
       $hashed_password = $row["password"];
       $admin = $row["admin"];
       if(password_verify($password, $hashed_password)){
        // If we made it here, the password is correct. Start a new session!
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $id;
        $_SESSION["username"] = $username;
        $_SESSION["admin"] = $admin;
        
        // Send us back to the home page.
        header("location: index.php");
       } else{
        // If we made it here, the password's not correct. Display an error message.
        $password_err = "The password you entered was not correct.";
       }
      }
     } else{
      // If we made it here, the username does not exist. Display an error message.
      $username_err = "The username you entered does not exist.";
     }
     // Close the statement.
     unset($stmt);
    }
   }
  }
 }
 unset($pdo);
?>

<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
 </head>
 
 <body>
 <?php echo $_SESSION["loggedin"]; ?>
  <h1 class="main-title"><?php echo $sitename ?></h1>
  <h2 class="login-title">Log In</h2>
  <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
   <div class="form-group login-input <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
    <label>Username:</label>
    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
    <span class="help-block"><?php echo $username_err; ?></span>
   </div>    
   <div class="form-group login-input <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
    <label>Password:</label>
    <input type="password" name="password" class="form-control">
    <span class="help-block"><?php echo $password_err; ?></span>
   </div>
   <div class="form-group">
    <input type="submit" class="button-orange" id="login-button" value="Login">
   </div>
  </form>
  
  <?php require_once "./footer.php"; ?>
 </body>
</html>
