/* Kirjautumisen session-käsittely Slackista*/
..
if(password_verify($password, $hashed_password)){
// Password is correct, so start a new session
 if (!session_id()) session_start();
 // Store data in session variables
 $_SESSION["loggedin"] = true;
 // $_SESSION["id"] = $id;
 // $_SESSION["username"] = $username;                            
 // Redirect user to welcome page
 if (isset($_SESSION['next_page']){
   $next_page = $_SESSION['next_page'];
   unset($_SESSION['next_page']);
   header("location: $next_page");
   exit;
   }
 header("location: welcome.php");
