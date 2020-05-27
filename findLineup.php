<?php
session_start();
if(!isset($_SESSION['key']))
{
    echo "You must be logged in to view this pageblahablash";
}
else
{ ?>
    <!DOCTYPE html>
    <html>
        <head>
            <script src="/jquery/jquery-3.5.1.js"></script>
            <script type="text/javascript"> var numPositions = {QB: 0, RB: 0, WR: 0, TE: 0, Flex: 0, DST: 0, K: 0}; </script>
        </head>
        <body>
            <select id="new_player" value="Add Player" name="Add player" onchange="addLine(value)">Add player
                <option value="Select position">Select position</option>
                <option value="QB">QB</option>
                <option value="RB">RB</option>
                <option value="WR">WR</option>
                <option value="TE">TE</option>
                <option value="Flex">Flex</option>
                <option value="DST">D/ST</option>
                <option value="K">K</option>
            </select> 
            <form id="playerText" action="./findStats.php" method="post">
                <div id="textLines"></div>
                <input type="submit" value="Submit">
            </form>
            <script>
                function addLine(value)
                {
                    if(value=="Select position")
                    {
                        return;
                    }

                    numPositions[value]++
                    var addSlash = value
                    if(value == "DST")
                    {
                        addSlash = "D/ST"
                    }
                    //Name of element (not of input tag)
                    var name = addSlash + "" + numPositions[value]
                
                    //Add a new line of text
                    var newLine = name + ": <input type='text' name='" + (value + "" + numPositions[value]) + "'/></br>"
                    $("#textLines").append(newLine)
                    console.log(value)
                }
                
            </script>
        </body>
    </html>
     <?php } ?>
        