<!DOCTYPE html>
<html>
    <head>
        <script src="/jquery/jquery-3.5.1.js"></script>
    <head>
    <?php
        if(isset($_POST['Login']))
        {
            echo "<h1>Login</h1>";
            echo "<h5>Please enter your credentials to continue</h5>";
        }

        if(isset($_POST['Sign_Up']))
        {
            echo "<h1>Sign Up</h1>";
            echo "<h5>Please choose a username and password</h5>";
        }
        //Quick way of getting registration type (login or sign up)
        function getRegType()
        {
            if(isset($_POST['Sign_Up'])) { return "Sign Up"; }
            else { return "Login"; }
        }
    ?>
    <body>
        <form method="post" onsubmit="verifyInput(); return false">
            Username/email: <input type="text" id="username"></br>
            Password: <input type="password" id="password"></br>
            <?php if(getRegType() == "Sign Up"){ echo "Confirm password: <input type='password' id='confirmation'></br>";} ?>
            <input type="submit" name="<?php echo getRegType(); ?>" value="<?php echo getRegType(); ?>">
        </form>
        <script type="text/javascript">
            function verifyInput()
            {
                //Store the type of the request (login or signup) in a variable
                var reqType = "<?php echo getRegType(); ?>"
                if(reqType == "Login")
                {
                    $.ajax({
                        type: 'POST',
                        url: './activate.php',
                        data: {type: "Login", username: $("#username").val(), password: $("#password").val()},
                        success: (result) => 
                        {
                            //console.log(result)
                            console.log("[0]: " + result[0] + " [1]: " + result[1] + "\n")
                            if(result[0] == 0 || result[1] == 0)
                            {
                                //Make red
                                $("body").append($("<h3 id='logErr'>Either the username or password was incorrect</h3>"))
                            }
                            else
                            {
                                <?php
                                if(!session_start())
                                {
                                    echo "Could not start session";
                                }
                                else
                                {
                                    $_SESSION['key'] = "keyboardCat";
                                   //echo var_dump($_SESSION);
                                    ?>
                                    window.location.replace('./menuScreen.php');
                                    console.log("Success!")
                                <?php } ?>
                            }
                        },
                        dataType: "json"
                    })
                }
               //Something wrong in the activate.php file with sign up (probably with login too)
                if(reqType == "Sign Up")
                {
                    $.ajax({
                        type: 'POST',
                        url: './activate.php',
                        data: {type: "Sign Up", username: $("#username").val(), password: $("#password").val(),
                                confirmation: $("#confirmation").val()},
                        success: (result) =>
                        {
                            console.log("[0]: " + result[0] + ", [1]: " + result[1] + ", [2]: " + result[2] +
                            " , [3]: " + result[3] )
                            if(result[0] == 0)
                            {
                                $("body").append($("<h3 id=usernameExists>Please pick a username that is not already registered.</h3>"))
                            }

                            else if(result[1] == 0)
                            {
                                $("body").append($("<h3 id=passwordWrong>Password entered incorrectly.</h3>"))
                            }

                            else
                            {
                                //START SESSIONS LATER
                                session_start();
                                $_SESSION['key'] = "keyboardCat";
                                window.location.replace('./menuScreen.php');
                                //console.log("Success");
                            }
                        },
                        dataType: "json"
                    })
                }
                
            }
        
        </script>

    </body>
</html>