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
        if (isset ($_GET["vteam"])) $vteam = sanitizeString($_GET["vteam"]);
            else $vteam= "";
            
        if (isset($_POST['ttoe'])){
            $tnaam = sanitizeString($_POST['naam']);
            $ptoe = "INSERT INTO `teamlid`(`teamnaam`, `lidnummer`) VALUES ('$tnaam','$id')";
            $result = $mysqli->query($ptoe);
            //header("Refresh:0");
            if(!$result) die ("Database access failed");
            if($result) echo "Dit lid is toegevoegd aan het Team<br><br>";
        }

        echo <<<_END
            <html><head><title>Lid verwijderen</title><style>
            a {
                text-decoration: none;
                color: black;
            }
            </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
            ----------------------------------------------------------------------------------------------------------------------

            <h3>Deze teams zijn momenteel in gebruik:</h3>
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
                echo "<td><button type='submit' value='veranderen' ><a href='tltoe.php?id=$id&vteam=$n'>Selecteren</a>";      
                echo "</tr>";
            }
            echo "</table>";

            echo<<<_END

                <h3>Voeg hier een team toe:</h3>
                <form method="post" action="" >
                Lidnummer:  $id
                Teamnaam <input type="text" name="naam" value="$vteam">
                <input type="hidden" name="ttoe" value="yes">
                <input type="submit" value="Toevoegen aan team">        
                        </form > </pre></html>
            _END;
    }
    else die("Invalid username/password combination");
			
}
else{
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die("Please enter your username and password");
}
