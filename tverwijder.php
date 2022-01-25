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
        
        
        if (isset($_POST['tver'])){
            $vteam = sanitizeString($_POST['vteam']);   
            $mysqli->query("DELETE FROM teamlid WHERE lidnummer='$id' AND teamnaam='$vteam'");
            header("Location: leden.php");
        }

        echo "<html><head><title>Lid verwijderen</title><style>
            a {
                text-decoration: none;
                color: black;
            }
        </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
        ----------------------------------------------------------------------------------------------------------------------
        <h3>Wilt u iemand uit een team verwijderen dan kan dat hier.</h3>"
        . "Deze persoon zit in deze teams:<br><br>";
        $lzien = "SELECT teamnaam FROM teamlid WHERE lidnummer=$id";
        $result = $mysqli->query($lzien);
        if (!$result) die ("Database access failed");

        $rows = $result->num_rows;
        echo "<table><tr><th>Teamnaam</th><th>Omschrijving</th></tr>";
        for ($j=0;$j<$rows; ++$j){
            $row = $result->fetch_array(MYSQLI_NUM);    
            $n=$row[0];
            echo "<tr>";
            for ($k =0; $k<1;++$k){           
                echo "<td>". htmlspecialchars($row[$k]). "</td> ";
            }
            echo "<td><button type='submit' value='veranderen' ><a href='tverwijder.php?id=$id&vteam=$n'>Selecteren</a>";        
            echo "</tr>";
        }
        echo "</table>";
             
        echo <<<_END
                <form method="post" action="" form="verwijder">
                Lidnummer: $id
                Te verwijderen team <input type="text" name="vteam" value="$vteam">
                <input type="hidden" name="tver" value="yes">
                <input type="submit" value="Verwijderen">        
                    </form >  
        ----------------------------------------------------------------------------------------------------------------------    

                </pre>
            </body>
        </html>
        _END;
    }
    else die("Invalid username/password combination");
			
}
else{
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die("Please enter your username and password");

}
