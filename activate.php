<?php
//session_start();
//Setup database and connections
//Database should already be setup from initDB
function setupConnection()
{
  //Establish connection
  $connection = $users = new mysqli("localhost:3306", "root", "");
  //Select database
  $dbSelection = $users->select_db("users");
  return $users;
}


/*function findUser($users, $username)
{
   return $users->query("SELECT Username FROM login_info WHERE Username=$username");
}
*/


//If logging in, search database for user
//Returns an array indicating whether the username was found and whether the password was correct
if($_POST['type'] == "Login")
{
   //Setup connection
   $users = new mysqli("localhost:3306", "root", "");
   $users->select_db("users");
   if($users->error != "") //Returns an empty string if no error
   {
      $errArr = array($users->error);
      echo json_encode($errArr);
      $users->close();
      return;
   }

   //An array that stores information about the success of the login
   //1 if username/password matches
   $userInfo = array(0, 0);

   //Check to see if there exists a user with the given username
   $username = $users->query("SELECT Username FROM login_info WHERE Username='$_POST[username]'");
   if(!$username)
   {
      $errArr = array($users->error);
      echo json_encode($errArr);
      $users->close();
      return;
   }
   
   if(sizeof($username->fetch_all()) > 0)
   {
      //Username does exist, set the array's first element to 1
       $userInfo[0] = 1;
   }
   else
   {
      //Username does not exist
      echo json_encode($userInfo);
      $users->close();
      return;
   }

   //Check password
   $password = $users->query("SELECT Passcode FROM login_info WHERE Username='$_POST[username]'");
   if(!$password)
   {
      $errArr = array($users->error);
      echo json_encode($errArr);
      $users->close();
      return;
   }
   $passArr = $password->fetch_all();
   if($passArr[0][0] == $_POST['password'])
   {
      $userInfo[1] = 1;
   }

   $users->close();
   echo json_encode($userInfo);
   
}

if($_POST['type'] == "Sign Up")
{
    //Setup connection
    $users = new mysqli("localhost:3306", "root", "");
    $users->select_db("users");
    if($users->error != "") //Returns an empty string if no error
    {
       $errArr = array($users->error);
       echo json_encode($errArr);
       $users->close();
       return;
    }
 
    //An array that stores information about the success of the login
    //1 if username/password matches
    $userInfo = array(0, 0);
 
    //Check to see if there exists a user with the given username
    $username = $users->query("SELECT Username FROM login_info WHERE Username='$_POST[username]'");
    if(!$username)
    {
       $errArr = array($users->error);
       echo json_encode($errArr);
       $users->close();
       return;
    }
    
    if(sizeof($username->fetch_all()) > 0)
    {
       //There already exists someone with that username
       echo json_encode($userInfo);
       return;
    }

    $userInfo[0] = 1;
   
    //Verify that password was correctly confirmed
    if($_POST['password'] == $_POST['confirmation'])
    {
      $insertUser = $users->query("INSERT INTO login_info (Username, Passcode) VALUES ('$_POST[username]', '$_POST[password]')");
      if(!$insertUser)
      {
         $errArr = array($users->error);
         echo json_encode($errArr);
         $users->close();
         return;
      }
      $userInfo[1] = 1;
    }
    
    echo json_encode($userInfo);

}

/*if($_POST['type'] == "Sign Up")
{
   //1 if username does not exist, 1 if password is successfully confirmed
   $userInfo = array(0, 0);
   $users = setupConnection("users", "login_info");
   $username = $users->query("SELECT Username FROM loginInfo WHERE Username='$_POST[username]'");

   //$username = findUser($_POST['username'])
   
      /*if(!$username)
      {
	      echo json_encode($userInfo);
	      return;     
      }
      */

      //$userInfo[0] = $username;
      //Check password
      /*if($_POST['password'] != $_POST['confirmation'])
      {
	      echo json_encode($userInfo);
	      return;
      }
      */

      //$userInfo[1] = 1;
      /*$queryString = "INSERT INTO loginInfo VALUES ('admin', 'password')";
      $users->query(queryString);
      $userInfo[1] = $queryString;
      /*if($users->query($queryString) == FALSE)
      {
         $userInfo[0] = 0;
         $userInfo[1] = 1;
      }
      */
      //$users->close();
      //echo json_encode($userInfo);
//}

?>	
   
