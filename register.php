<?php
 session_start();
 
 // If we're already logged in, redirect back to the homepage.
 if(isset($SESSION["loggedin"]) && $SESSIOIN["loggedin"] === true){
  header("location: index.php");
  exit;
 }
 
 // Open the database connection.
 require_once "./config.php";
 
 $username = "";
 $password = "";
 $confirm_password = "";
 $username_err = "";
 $password_err = "";
 $confirm_password_err = "";
 
 // Check the input if we POST to this file.
 if($_SERVER["REQUEST_METHOD"] == "POST"){

  // Check if username is empty.
  if(empty(trim($_POST["username"]))){
    $username_err = "Please enter a username.";
  } else{
   // Check if this username already exists or not.
   $sql = "SELECT id FROM accounts WHERE username = :username";
   if($stmt = $pdo->prepare($sql)){
    // Plug input into the SQL statement.
    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
    $param_username = trim($_POST["username"]);
    if($stmt->execute()){
     // If the username already exists...
     if($stmt->rowCount() == 1){
      $username_err = "This username already exists.";
     } else{
      $username = trim($_POST["username"]);
     }
    } else{
     echo "The username check failed to process.";
    }
    unset($stmt);
   }
  }

  // Check if the password is empty.
  if(empty(trim($_POST["password"]))){
   $password_err = "Please enter a password.";
  } elseif(strlen(trim($_POST["password"])) < 6){
   $password_err = "Your password must be at least 6 characters.";
  } else{
   $password = trim($_POST["password"]);
  }
  
  // Check the password confirmation.
  if(empty(trim($_POST["confirm_password"]))){
   $confirm_password_err = "Please confirm your password.";
  } else{
   $confirm_password = trim($_POST["confirm_password"]);
   if(empty($password_err) && ($password != $confirm_password)){
    $confirm_password_err = "Passwords do not match.";
   }
  }
  
  if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
   $sql = "INSERT INTO accounts (username, password) VALUES (:username, :password)";
   if($stmt = $pdo->prepare($sql)){
    // Plug variables into the SQL statement.
    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
    $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
    $param_username = $username;
    $param_password = password_hash($password, PASSWORD_DEFAULT);
    if($stmt->execute()){
     // Redirect to login page
     header("location: login.php");
    } else{
     echo "Registration failed.";
    }
    unset($stmt);
   }
  }
  unset($pdo);
 }
?>

<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="./styles.css">
 </head>
 
 <body>
  <h1 class="main-title"><?php echo $sitename ?></h1>
  <h2 class="page-title">Register a New User</h2>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
   <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
    <label>Username</label>
    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
    <span class="help-block"><?php echo $username_err; ?></span>
   </div>    
   <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
    <label>Password</label>
    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
    <span class="help-block"><?php echo $password_err; ?></span>
   </div>
   <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
    <span class="help-block"><?php echo $confirm_password_err; ?></span>
   </div>
   <div class="form-group">
    <input type="submit" class="btn btn-primary" value="Register">
   </div>
  </form>
 </body>
</html>
