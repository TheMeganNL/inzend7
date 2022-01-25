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
            
            $test= "SELECT naam FROM lid WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $oanaam = $result->fetch_assoc()['naam'];

            $test= "SELECT postcode FROM lid WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $opostcode = $result->fetch_assoc()['postcode'];

            $test= "SELECT huisnummer FROM lid WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $ohuisnr = $result->fetch_assoc()['huisnummer'];

            $test= "SELECT telefoonnummer FROM telefoonnummers WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $otelnr = $result->fetch_assoc()['telefoonnummer'];

            $test= "SELECT emailadres FROM email WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $oemail = $result->fetch_assoc()['emailadres'];

            $test= "SELECT voornaam FROM lid WHERE lidnummer='$id';";    
            $result = $mysqli->query($test);
            $ovnaam = $result->fetch_assoc()['voornaam'];

            if (isset($_POST['ttoe'])){
                $telnr = sanitizeString($_POST['telnr']);
                $mysqli->query("INSERT INTO telefoonnummers VALUES ('$telnr','$id')");
            //   header("Location: ledenbestand.php");
                  header("Refresh:0");
            }

            if (isset($_POST['etoe'])){
                $email = sanitizeString($_POST['email']);
                $mysqli->query("INSERT INTO email(emailadres, lidnummer) VALUES ('$email','$id')");
            //    header("Location: ledenbestand.php");
                   header("Refresh:0");
            }
            
            if (isset($_POST['taan'])){                
                $ntelnr = sanitizeString($_POST['ntelnr']);
                $otelnr = sanitizeString($_POST['otelnr']);
                $mysqli->query("UPDATE telefoonnummers SET telefoonnummer='$ntelnr' WHERE telefoonnummer='$otelnr' && lidnummer='$id'");
               header("Refresh:0");
            }
            
            if (isset($_POST['eaan'])){
               $oemail = sanitizeString($_POST['oemail']);
                $nemail = sanitizeString($_POST['nemail']);
                $result = $mysqli->query("UPDATE email SET emailadres='$nemail' WHERE emailadres='$oemail' && lidnummer='$id'");
                if (!$result) die ("Database access failed");
                header("Refresh:0");
            }

            if (isset($_POST['lpas'])){
                $vnaam = sanitizeString($_POST['vnaam']);
                $anaam = sanitizeString($_POST['anaam']);
                $postcode = sanitizeString($_POST['postcode']);
                $huisnr = sanitizeString($_POST['huisnummer']);
                $telnr = sanitizeString($_POST['telnr']);
                $email = sanitizeString($_POST['email']);
                $mysqli->multi_query("UPDATE lid SET naam='$anaam', voornaam='$vnaam', postcode='$postcode', huisnummer='$huisnr' WHERE lidnummer='$id'");
                while ($mysqli->next_result()) {
                    if ($mysqli->more_results()) break;
                }
                $mysqli->query("UPDATE telefoonnummers SET telefoonnummer='$telnr', lidnummer='$id' WHERE lidnummer='$id'");
                $mysqli->query("UPDATE email SET emailadres='$email' WHERE emailadres='$oemail' && lidnummer='$id'");
                 header("Location: leden.php");
            }
            
            echo <<<_END
            <html><head><title>Lid verwijderen</title><style>
                a {
                    text-decoration: none;
                    color: black;
                }
            </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
            ----------------------------------------------------------------------------------------------------------------------    
                <h3>Jouw gegevens aanpassen. Als u alleen een emailadres of telefoonnummer wilt aanpassen dan kan dat onder dit formulier. </h3>                 
                    <form method="post" action="" form="aanpas">
                    Uw nieuwe gegevens(vul alle velden in)?
                    Voornaam <input type="text" name="vnaam" value="$ovnaam">
                    Achternaam <input type="text" name="anaam" value="$oanaam">
                    Postcode <input type="text" name="postcode" value="$opostcode">
                    Huisnummer <input type="text" name="huisnummer" value="$ohuisnr">
                    Telefoonnummer <input type="text" name="telnr" value="$otelnr">
                    Email <input type="text" name="email" value="$oemail">
                    <input type="hidden" name="lpas" value="yes">
                    <input  type="submit" value="Aanpassen"></button>        
                    </form >
            ----------------------------------------------------------------------------------------------------------------------      
                <h3>Wilt u uw email wijzigen dan kan dat hier.</h3>
                 Deze emailadressen zijn bekend bij ons:
            _END;
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
                echo "<td><button type='submit' value='veranderen' ><a href='verander.php?id=$id&email=$n'> Verander</a>";        
                echo "</tr>";
            }
            echo "</table>";            

            echo <<<_END
                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Oud Email adres <input type="text" name="oemail" value="$temail">
                    Nieuw Email adres<input type="text" name="nemail">
                    <input type="hidden" name="eaan" value="yes">
                    <input type="submit" value="Wijzigen">        
                    </form >  
            ----------------------------------------------------------------------------------------------------------------------    
            _END;
            
            echo " <h3>Wilt u uw telefoonnummer wijzigen dan kan dat hier.</h3>"
            . "Deze telefoonnummers zijn bekend bij ons:";
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
                echo "<td><button type='submit' value='veranderen' ><a href='verander.php?id=$id&telefoon=$n'> Verander</a>";        
                echo "</tr>";
            }
            echo "</table>";

            echo <<<_END
                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Oud Telefoon nummer <input type="text" name="otelnr" value="$ttelnr">
                    Nieuw Telefoon nummer <input type="text" name="ntelnr">
                    <input type="hidden" name="taan" value="yes">
                    <input type="submit" value="Wijzigen">        
                        </form >     

            ----------------------------------------------------------------------------------------------------------------------
                    <h3>Extra email adres toevoegen aan een lid.</h3>
                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Email <input type="text" name="email">
                    <input type="hidden" name="etoe" value="yes">
                    <input type="submit" value="Toevoegen">        
                        </form >      

            ----------------------------------------------------------------------------------------------------------------------
                    <h3>Extra telefoonnummer toevoegen aan een lid.</h3>
                    <form method="post" action="" form="verwijder">
                    Lidnummer: $id
                    Telefoon nummer <input type="text" name="telnr">
                    <input type="hidden" name="ttoe" value="yes">
                    <input type="submit" value="Toevoegen">        
                        </form >      </pre>
            _END;

            
    }
    else die("Invalid username/password combination");
			
}
else{
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die("Please enter your username and password");
}

