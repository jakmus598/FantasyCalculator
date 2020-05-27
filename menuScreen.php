<!DOCTYPE html>
<html>
    <head>
        <script src="/jquery/jquery-3.5.1.js"></script>
    </head>
    <body>
    <?php
    //Ensure that a session exists
    session_start();
    if(!isset($_SESSION['key']))
    {
        echo "You must login or sign up to view this page";
    }
    else
    {
    ?>
        <form method="post">
            <input type="submit" value="Search a Lineup" onclick="location.href='./findLineup.php'" />
        </form> 
</body>
</html>
    <?php } ?>
    
