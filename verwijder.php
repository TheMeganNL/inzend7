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
            if (isset ($_GET["email"])) $temail = sanitizeString($_GET["email"]);
                else $temail= "";
            if (isset ($_GET["telefoon"])) $ttelnr = sanitizeString($_GET["telefoon"]);
                    else $ttelnr =  "";

            if (isset($_POST['lver'])){  
                $mysqli->multi_query("DELETE  FROM email WHERE lidnummer='$id'");
                while ($mysqli->next_result()) {
                    if ($mysqli->more_results()) break;
                }
                $mysqli->query("DELETE  FROM telefoonnummers WHERE lidnummer='$id'");
                $mysqli->query("DELETE  FROM lid WHERE lidnummer='$id'");
                $mysqli->query("DELETE  FROM teamlid WHERE lidnummer='$id'");
                header("Location: leden.php");
            }

            if (isset($_POST['tver'])){
                $telnr = sanitizeString($_POST['telnr']);
                 $result = $mysqli->query("DELETE  FROM telefoonnummers WHERE lidnummer='$id' && telefoonnummer='$ttelnr'");
                if (!$result) die ("tabase access failed");
                if ($result) echo "Dit telefoonnummer is verwijderd. <br><br>";
                header("Location: leden.php");
            }

            if (isset($_POST['ever'])){
                $email = sanitizeString($_POST['email']);
                $result =$mysqli->query("DELETE  FROM email WHERE lidnummer='$id' && emailadres='$temail'");
                if (!$result) die ("tabase access failed");
                if ($result) echo "Dit emailadres is verwijderd. <br><br>";
                header("Location: leden.php");
            }

            echo "<html><head><title>Lid verwijderen</title><style>
                a {
                    text-decoration: none;
                    color: black;
                }
            </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
            ----------------------------------------------------------------------------------------------------------------------
            <h3>Wilt u uw email verwijderen dan kan dat hier.</h3>"
            . "Deze emailadressen zijn bekend bij ons";
            $lzien = "SELECT email.emailadres FROM email WHERE lidnummer=$id";
            $result = $mysqli->query($lzien);
            if (!$result) die ("Database access failed");
            $rows = $result->num_rows;
            echo "<table><tr><th>Emailadres</th></tr>";
            for ($j=0;$j<$rows; ++$j){
                $row = $result->fetch_array(MYSQLI_NUM);    
                $n=$row[0];
                echo "<tr>";
                for ($k =0; $k<1;++$k){           
                    echo "<td>". htmlspecialchars($row[$k]). "</td> ";
                }
                echo "<td><button type='submit' value='veranderen' ><a href='verwijder.php?id=$id&email=$n'>Selecteren</a>";        
                echo "</tr>";
            }
            echo "</table>";

            echo <<<_END

                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Te verwijderen emailadres <input type="text" name="oemail" value="$temail">
                    <input type="hidden" name="ever" value="yes">
                    <input type="submit" value="Verwijderen">        
                        </form >  
            ----------------------------------------------------------------------------------------------------------------------                
            <h3>Wilt u uw telefoonnummer verwijderen dan kan dat hier.</h3>
            Deze telefoonnummers zijn bekend bij ons:
            _END;
            $lzie = "SELECT telefoonnummers.telefoonnummer FROM telefoonnummers WHERE lidnummer=$id";
            $result = $mysqli->query($lzie);
            if (!$result) die ("Database access failed");
            $rows = $result->num_rows;
            echo "<table><tr><th>Telefoonnumer</th></tr>";
            for ($j=0;$j<$rows; ++$j){
                $row = $result->fetch_array(MYSQLI_NUM);    
                $n=$row[0];
                echo "<tr>";
                for ($k =0; $k<1;++$k){           
                    echo "<td>". htmlspecialchars($row[$k]). "</td> ";
                }
                echo "<td><button type='submit' value='veranderen' ><a href='verwijder.php?id=$id&telefoon=$n'>Selecteren</a>";        
                echo "</tr>";
            }
            echo "</table>";

            echo <<<_END

                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Telefoonnummer <input type="text" name="telnr" value="$ttelnr">
                    <input type="hidden" name="tver" value="yes">
                    <input type="submit" value="Verwijderen">        
                        </form >      
            ----------------------------------------------------------------------------------------------------------------------                     
                    <h3>Wilt u uw gegevens graag verwijderen uit het systeem vul dan hier uw lidnummer en  achternaam in.
                    Zorg dat je het zeker weet want u kunt deze informatie niet meer terughalen.
                    </h3>

                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    <input type="hidden" name="lver" value="yes">
                    <input type="submit" value="Verwijderen">        
                    </form >        
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