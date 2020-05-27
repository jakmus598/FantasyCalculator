<?php
//Idea:
//Call method that obtains playerArrays and all their links
//Then, pass that back to user and have user choose which player they want
//Finally, pass the choices back to this code, which will do the same exact thing as before except
//it will have the link array
//Initial domain string
//$domain = "https://www.pro-football-reference.com/players/";
$counter = 0;
function getPoints($name, $week, $year, $isKicker)
{
    $domain = "https://www.pro-football-reference.com/players/";
    $homeStream = fopen($domain, "r");
    $srcCode = stream_get_contents($homeStream);
    fclose($homeStream);
    //Find player
    $nameArray = explode(" ", $name); //Splits name into first and last
    $lastNameFirstLetter = ($nameArray[1])[0];
    $domain = $domain . $lastNameFirstLetter . "/";

    //Find player on the last name page (page of all players whose last name starts with a given letter)
    
    $lastNameStream = fopen($domain, "r");
    $srcCode = stream_get_contents($lastNameStream);
    fclose($lastNameStream);
    $playerLink = findLink($srcCode, $name, $year, $domain);
    $stats = findStats($playerLink, $week, $year, $isKicker);
    return $stats;
    //$officialURL = findStats($playerLink, 12, 2012);
    //fclose($lastNameStream);
    //return findStats($playerLink, $week, $year);
    //$domain = $domain . $playerLink;
    //return findStats($playerLink, $week, $year);
    //return $playerLink;
    //Go back 
}


//Finds the link to the specific player's page
//srcArray = array of source code, name = player's name, domain = domain to add to
function findLink($srcCode, $name, $year, $domain)
{
    //Array of all the players with the given name
    $nameArray = Array();
    //Replace each ">" and "<" with & so that html doesn't render
    $srcCode = str_replace("<", "&", $srcCode);
    $srcCode = str_replace(">", "&", $srcCode);
    $srcArray = explode("&", $srcCode);
    //echo var_dump($srcArray);
    $playerURL = "";
    for($i = 0; $i < sizeof($srcArray); $i++)
    {
        //print $srcArray[$i] . "</br>";
        if($srcArray[$i] == $name)
        {
            //$j = 0;
            $playerURL = $srcArray[$i - 1];
            break;
            /*$yearsPlayedString = explode(") ", $srcArray[$i + 2]);
            $yearsPlayed = Array();
            if(sizeof($yearsPlayedString) > 1)
            {
                $yearsPlayed = explode("-", $yearsPlayedString[1]);
            }
            else
            {
                $yearsPlayed = explode("-", $yearsPlayedString[0]);
            }
            if($year < $yearsPlayed[0] || $year > $yearsPlayed[1])
            {
                $j = $i + 9;
                //Player names are stored at offsets of 9
                while($srcArray[$j] == $name)
                {
                    $yearsPlayedString = explode(") ", $srcArray[$j + 2]);
                    $yearsPlayed = Array();
                    if(sizeof($yearsPlayedString) > 1)
                    {
                        $yearsPlayed = explode("-", $yearsPlayedString[1]);
                    }
                    else
                    {
                        $yearsPlayed = explode("-", $yearsPlayedString[0]);
                    }
                
                    //Parses srcArray[$j + 2] into numbers
                    $playerURL = $srcArray[$j - 1];
                    if($year < $yearsPlayed[0] || $year > $yearsPlayed[1])
                    {
                        $j = $i + 9;
                    }
                    else
                    {
                        break;
                    }
                }
            }
            */
        }
    }
            //array_push$nameArray, $name);
            //$playerInfo = array("Years played" => $srcArray[$i + 2], $srcArray[$i - 1]);
            //array_push($nameArray, $playerInfo);
            //If multiple players with same name, make sure you choose the correct one
        
    
    if($playerURL == "")
    {
        return FALSE;
    }
    //Parse player URL
    //Format (for any player) is four letters of last name, two letters of first name, 
    //, two digits, and 3 more letters for .htm
    $playerURL = substr($playerURL, 19, 8);
    $domain = $domain . "" . $playerURL . "/";
    return $domain;
}

function findStats($playerLink, $week, $year, $isKicker)
{
    static $counter = 0;
    $counter = $counter + 1;
    //Go to the player's gamelog for the specific season
    $playerURL = $playerLink . "gamelog/" . $year;
    //Check to see if year is valid
    //echo $playerURL;
    $statsStream = fopen($playerURL, "r");
    $srcCode = stream_get_contents($statsStream);
    //$srcCode1 = stream_get_contents($statsStream);
    fclose($statsStream);
    
    $srcCode = str_replace("<", "&", $srcCode);
    $srcCode = str_replace(">", "&", $srcCode);

    
    if(!strpos($srcCode, '"game_num"'))
    {
        //echo var_dump($playerURL);
        //echo var_dump($srcCode);
        return 0;
    }
    //Now, check to make sure week is valid
    if(!strpos($srcCode, '"week_num" &' . $week))
    {
        return 0;
    }
    
    
    //return $srcCode;

    //Parse at desired week
    //An array of the stats for all the weeks
    $weekStatsAll = explode('"week_num"', $srcCode);
    //0 and 1 contain irrelevant html code
    //If the game is before the bye
    $weekStats = Array();
    $weekIndex = 0;
    if(substr($weekStatsAll[$week], 0, 3) == " &" . ($week - 1))
    {
        $weekIndex = $week + 1;
        $weekStats = $weekStatsAll[$weekIndex];
        if(substr($weekStats, 0, 3) == " &" . ($week + 1))
        {
            return 0;
        }
    }
    else
    {
        $weekIndex = $week;
        $weekStats = $weekStatsAll[$weekIndex];
    }

    //return $weekStats;

    //Determine stats
    //data_stat is used to identify each category
    //$statsArray = explode("data-stat=", $weekStats);
    //return $statsArray;
    $statArray = Array();
    //Get game location
    $gameLocationPos = strpos($weekStats, '"game_location" &');
    $gameLocation = $weekStats[$gameLocationPos + strlen('"game_location" &')];
    $statArray['gameLoc'] = $gameLocation; //the string containing the stats that will be returned

    //Get opposing team
    /*$oppTeamPos = strpos($weekStats, '"opp" &&a href="/teams/');
    $oppTeamStart = $oppTeamPos + strlen('"opp" &&a href="/teams/');
    $oppTeamArr = explode("/", substr($weekStats, $oppTeamStart)); */
    $oppTeamLowercase = parseStat($weekStats, '"opp" &&a href="/teams/', "/");
    $oppTeam = strtoupper($oppTeamLowercase);
    $statArray['oppTeam'] = $oppTeam;

    //Get passing stats
    //Completions
    $passCmps = parseStat($weekStats, '"pass_cmp" &', "&");
    $statArray['passCmps'] = $passCmps;
    /*$passCmpsPos = strpos($weekStats, '"pass_cmp" &');
    if(!$passCmpsPos)
    {
        $statArray['passCmps'] = 0;
    }
    else
    {
        $passCmpsStart = $passCmpsPos + strlen('"pass_cmp" &');
        $passCmpsArr = explode("&", substr($weekStats, $passCmpsStart));
        $passCmps = $passCmpsArr[0];
        $statArray['passCmps'] = $passCmps;
    }
    */

    //Pass attempts
    $statArray['passAtts'] = parseStat($weekStats, '"pass_att" &', "&");
    $statArray['passCmps'] = parseStat($weekStats, '"pass_cmp" &', "&");
    $statArray['passYds'] = parseStat($weekStats, '"pass_yds" &', "&");
    $statArray['passTDs'] = parseStat($weekStats, '"pass_td" &', "&");
    $statArray['passINTs'] = parseStat($weekStats, '"pass_int &"', "&");
    
    //Rushing stats
    $statArray['rushAtts'] = parseStat($weekStats, '"rush_att" &', "&");
    $statArray['rushYds'] = parseStat($weekStats, '"rush_yds" &', "&");
    $statArray['rushTDs'] = parseStat($weekStats, '"rush_td" &', "&");

    //Receiving stats
    $statArray['rec'] = parseStat($weekStats, '"rec" &', "&");
    $statArray['recYds'] = parseStat($weekStats, '"rec_yds" &', "&");
    $statArray['recTDs'] = parseStat($weekStats, '"rec_td" &', "&");

    //Special teams
    $statArray['kickRetTDs'] = parseStat($weekStats, '"kick_ret_tc" &', "&");
    $statArray['puntRetTDs'] = parseStat($weekStats, '"punt_ret_td" &', "&");
    //Must calculate the kicker's stats based on the fantasy points they earned


    if($isKicker)
    {
        $fantasyURL = $playerLink . "fantasy/" . $year;
        $fantasyStream = fopen($fantasyURL, "r");
        $fantasyCode = stream_get_contents($fantasyStream);
        fclose($fantasyStream);
        
        $fantasyCode = str_replace("<", "&", $fantasyCode);
        $fantasyCode = str_replace(">", "&", $fantasyCode);
        $allFantasyStats = explode('"week_num"', $fantasyCode);
        $weekFantasyStats = $allFantasyStats[$weekIndex];

        $statArray['kickPts'] = parseStat($weekFantasyStats, '"fantasy_points" &', "&");
    }
    else
    {
        $statArray['kickPts'] = 0;
    }


    //Misc
    $statArray['fumbles'] = parseStat($weekStats, '"fumbles_lost" &', "&"); //Only lost fumbles
    //echo "fumbles_lost: " . $statArray['fumbles'];
    $statArray['fumblesRecYds'] = parseStat($weekStats, "fumbles_rec_yds", "&");
    //echo "fumblesRecYds: " . $statArray['fumblesRecYds'];
    $statArray['fumblesRecTDs'] = parseStat($weekStats, "fumbles_rec_td", "&");
    //echo "fumblesRecTDs: " . $statArray['fumblesRecTDs'];

    //$statString = $statString . " " . $oppTeam;
    //$oppTeam = $oppTeamAr
    return $statArray;
}
    
//startString = string to begin parsing at
//dataString = string that contains all the data (e.g. weekString) 
function parseStat($dataString, $startString, $explodeStr)
{
    $startPos= strpos($dataString, $startString);
    if(!$startPos)
    {
        return 0;
    }
    $statPos = $startPos + strlen($startString);
    $statArr = explode($explodeStr, substr($dataString, $statPos));
    return $statArr[0];
    //$oppTeam = strtoupper($oppTeamArr[0]);
    //$statArray['oppTeam'] = $oppTeam;
}

//echo $playerLink;
//Eventually use POST for custom scoring; for now just use standard scoring (0.5 ppr)
function calculatePoints($statArray)
{
    //Pass through another function that sets all non-numeric values to numeric ones
    foreach($statArray as $stat => $value)
    {
        if($value == '" ')
        {
            $statArray[$stat] = 0;
        }
    }
    //echo var_dump($statArray);
    $passingPoints = $statArray['passYds']/25 + 6*$statArray['passTDs'] + (-2)*$statArray['passINTs'];
    $rushPoints = $statArray['rushYds']/10 + 6*$statArray['rushTDs'];
    $recPoints = $statArray['rec']/2 + $statArray['recYds']/10 + 6*$statArray['recTDs'];
    $specialTeams = 6*$statArray['kickRetTDs'] + 6*$statArray['puntRetTDs'];
    //echo var_dump($statArray['fumbles']);
    //echo var_dump($statArray['fumblesRecYds']);
    //echo var_dump($statArray['fumblesRecTDs']);
    //echo empty($statArray);
    $fumbles = (-2)*$statArray['fumbles'] + $statArray['fumblesRecYds']/10 + 6*$statArray['fumblesRecTDs'];
    return $passingPoints + $rushPoints + $recPoints + $specialTeams + $fumbles + $statArray['kickPts'];

}



function returnStats()
{
    $posKeys = Array("QB", "RB", "WR", "TE", "Flex", "DST", "K");
    $valuesArray = Array();
    $stringArray = Array();
    $totalPoints = 0;
    for($i = 0; $i < sizeof($posKeys); $i++)
    {
        for($j = 0; $j < sizeof($_POST); $j++)
        {
            $entryName = $posKeys[$i] . "" . ($j + 1);
            //echo $entryName;
            if(array_key_exists($entryName, $_POST))
            {
                    $ptsArr = Array();
                    if($posKeys[$i] == "K")
                    {
                        $ptsArr = getPoints($_POST[$entryName], 5, 2012, TRUE);
                    }
                    else
                    {
                        $ptsArr = getPoints($_POST[$entryName], 5, 2012, FALSE);
                    }
                    if($ptsArr == 0 || $ptsArr == 1.1 || $ptsArr == 2.2)
                    {
                        array_push($stringArray, ($_POST[$entryName] . ": " . $ptsArr . " (BYE)"));
                    }
                    else
                    {
                        $playerPoints = calculatePoints($ptsArr);
                        array_push($stringArray, ($_POST[$entryName] . ": " . $playerPoints));
                        $totalPoints += $playerPoints;
                    }
                }
                //Get final string
                //Calculate points, then add view full stats button

                /*if($posKeys[$i] == "QB")
                {
                    $statArray = getPoints($_POST[$entryName], 5, 2012, FALSE);
                    $cmpToAttRatio = $statArray['passCmps'] . "/" $statArray['passAtts'] . ", ";
                    $passYds = $statArray['passYds'] . " yds, ";
                    $tdAndINT = $statArray['passTDs'] . " TD, " . $statArray['passINTs'] . "INT";
                    $finalString = $_POST[$entryName] . ": " . $statArray['passAtts']
                }
                */
                             
        }

    }

    array_push($stringArray, "Total points: " . $totalPoints);
    return $stringArray;
}

function getChoices($name)
{
    //Prepare stream
    $domain = "https://www.pro-football-reference.com/players/";
    $homeStream = fopen($domain, "r");
    $srcCode = stream_get_contents($homeStream);
    fclose($homeStream);
    
    //Use player's last name to find corresponding link
    $nameArray = explode(" ", $name); //Splits name into first and last
    $lastNameFirstLetter = ($nameArray[1])[0];
    $domain = $domain . $lastNameFirstLetter . "/";

    //Replace each ">" and "<" with & so that html doesn't render
    $srcCode = str_replace("<", "&", $srcCode);
    $srcCode = str_replace(">", "&", $srcCode);
    $srcArray = explode("&", $srcCode);
    
    //Find all the players with the given name
    $nameArray = Array();
    for($i = 0; $i < sizeof($srcArray); $i++)
    {
        //print $srcArray[$i] . "</br>";
        if($srcArray[$i] == $name)
        {
            //Parse playerURL
            $playerURL = $srcArray[$i - 1];
            $playerURL = substr($playerURL, 19, 8);
            $URL = $domain . "" . $playerURL . "/";
            $playerInfo = array("name" => $name, "Years played" => $srcArray[$i + 2], "URL" => $URL);
            array_push($nameArray, $playerInfo);
            //If multiple players with same name, make sure you choose the correct one
            
        }
    }

    return $nameArray;

}

function getResponseCodes()
{
    //global $domain;
    $initLink = getPoints("Michael Turner", 12, 2008);
    echo $initLink;
    $nextLink = findStats($initLink, 12, 2008);
    echo(var_dump($nextLink));
    /*$nextLink = findStats($initLink, 12, 2008); 
    $headers = get_headers($initLink);
    echo "initLink: " . substr($headers[0], 9, 3);
    $headers = get_headers($nextLink);
    echo "nextLink: " . substr($headers[0], 9, 3);
    //echo var_dump(get_http_response_code($initLink));
    //echo var_dump(get_http_response_code($nextLink));
    */
}
//getPoints("Jimmy Graham", 12, 2012);
/*if(isset($_POST['name']))
{
    //$linkArray = Array();
    /*foreach($_POST as $type => $name)
    {
        if($type != 'links')
        {
            array_push($linkArray, getChoices($name));
        }
    }
    */
    //return getChoices($_POST['name']);

    //return $linkArray;
//}

    $outputArr = returnStats();
    for($i = 0; $i < sizeof($outputArr); $i++)
    {
        echo $outputArr[$i] . "</br>";
    }


/*
Process:
Go to https://www.pro-football-reference.com/players/$first_letter_of_last_name/
//Search corresponding HTML document for player's name
//Right before the player's name there should be a relative link to his page. Add to the following link:
/gamelog/season_year
//At that link, find data-stat="week-num" - right next to it should be the proper week number
//Following that, there should be the following stats:
//rush_att, rush_yards, rush_td, targets, rec, rec_yards, rec_td, fumbles_lost, fumbles_rec_yds
*/
?>