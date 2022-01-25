<?php

$username = 'LOIDocent';
$password = 'mysqlphp';

setcookie('date',time());
setcookie('gebruiker',$username);

if (isset($_COOKIE['date'])) $date = $_COOKIE['date'];
if (isset($_COOKIE['gebruiker'])) $gebruiker = $_COOKIE['gebruiker'];



if (isset($_SERVER['PHP_AUTH_USER']) &&
    isset($_SERVER['PHP_AUTH_PW'])) 
{
    
    if($_SERVER['PHP_AUTH_USER'] === $username &&
        $_SERVER['PHP_AUTH_PW'] === $password){

        require_once 'login.php';
        require_once 'sanitize.php';
        require_once 'dev-settings.php';
        $mysqli = new mysqli($hn, $un, $pw, $db);
        if ($mysqli->connect_error) die("fatal error");

        $id= sanitizeString($_GET["id"]);
        
        if(isset($_GET["team"])) $nteam = sanitizeString($_GET["team"]);
            else $nteam = "";
           
        if (isset($_POST['tpas'])){
            $hteamnaam = sanitizeString($_POST['hteamnaam']);
            $nteam = sanitizeString($_POST['nteam']);
            $result= $mysqli->query("UPDATE `teamlid` SET `teamnaam`='$nteam' WHERE lidnummer='$id'");
            if($result) echo "Het team is veranderd";
            if(!$result) die ("Database failure");    
        }

        $test= "SELECT teamnaam FROM teamlid WHERE lidnummer='$id';";    
        $result = $mysqli->query($test);
        $thuidig = $result->fetch_assoc()['teamnaam']; 


        echo <<<_END
            <html><head><title>Lid verwijderen</title><style>
            a {
                text-decoration: none;
                color: black;
            }
            </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
            ----------------------------------------------------------------------------------------------------------------------
            <h3>Huidige teams </h3>
            _END;

        $lzien = "SELECT teams.teamnaam, teams.omschrijving FROM teams;";
        $result = $mysqli->query($lzien);
        if (!$result) die ("Database access failed");
        $rows = $result->num_rows;
        echo "<table><tr><th>Teamnaam</th><th>Omschrijving</th>";
        for ($j=0;$j<$rows; ++$j){
            $row = $result->fetch_array(MYSQLI_NUM);  
            $n=$row[0]; 
            echo "<tr>";
            for ($k =0; $k <2;++$k){           
                echo "<td>". htmlspecialchars($row[$k]). "</td> ";
            }
            echo "<td><button type='submit' value='veranderen' ><a href='tverander.php?id=$id&team=$n'>Dit team selecteren</a></button></td>";
        //                                . "<td><button type='submit' value='verwijderen'><a href='tverwijder.php?id=$n'>Wijzig team omschrijving</a></button></td> ";        
            echo "</tr>";
        }
        echo "</table>";
        
        echo <<<_END
                <form method="post" action="" form="aanpas">
                Lidnummer: $id
                Huidig Team: <input type="text" name="hteamnaam" value="$thuidig">
                Nieuw Team: <input type="text" name="nteam" value="$nteam">        
                <input type="hidden" name="tpas" value="yes">
                <input type="submit" value="Aanpassen"></button>        
                    </form >
        _END;
    }
    else die("Invalid username/password combination");
			
}
else{
     header('WWW-Authenticate: Basic realm="Restricted Area"');
     header('HTTP/1.0 401 Unauthorized');
     die("Please enter your username and password");
}