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
        
        if (isset($_POST['ltoe'])){        
            $vnaam = sanitizeString($_POST['vnaam']);
            $anaam = sanitizeString($_POST['anaam']);
            $postcode = sanitizeString($_POST['postcode']);
            $huisnr = sanitizeString($_POST['huisnummer']);
            $telnr = sanitizeString($_POST['telnr']);
            $email = sanitizeString($_POST['email']);
            $team = sanitizeString($_POST['team']); 

            $ltoe = "INSERT INTO lid (`naam`, `voornaam`, `postcode`, `huisnummer`) VALUES ('$anaam','$vnaam','$postcode','$huisnr')";
            $mysqli->multi_query($ltoe);
            $lidnr = $mysqli->insert_id;

            while ($mysqli->next_result()) {
                if ($mysqli->more_results()) break;
            }
//            echo $lidnr;
            $mysqli->query("INSERT INTO email VALUES ('$email', '$lidnr')");
            $mysqli->query("INSERT INTO telefoonnummers VALUES ('$telnr','$lidnr')");
            $result = $mysqli->query("INSERT INTO `teamlid`(`teamnaam`, `lidnummer`) VALUES ('$team','$lidnr')");
            if (!$result) die ("tabase access failed");
            if ($result) echo "Dit lid is toegevoegd <br><br>";
        }

        echo <<<_END
            <html><head><title>Voeg lid toe</title><style>
            a {
                text-decoration: none;
                color: black;
            }
        </style></head><body>
        <pre> <button type='button'><a href='leden.php'>Terug</a></button>
        ----------------------------------------------------------------------------------------------------------------------   

                <h3>Een lid toevoegen:</h3>
                <form method="post" action="">

                Voornaam <input type="text" name="vnaam">
                Achternaam <input type="text" name="anaam">
                Postcode <input type="text" name="postcode">
                Huisnummer <input type="text" name="huisnummer">
                Telefoonnummer <input type="text" name="telnr">
                Email <input type="text" name="email">
                Team <select name="team">
        _END;

        $lzien = "SELECT teamnaam FROM teams;";
        $result = $mysqli->query($lzien);
        if (!$result) die ("Database access failed");

        $rows = $result->num_rows;                
        for ($j=0;$j<$rows; ++$j){
            $row = $result->fetch_array(MYSQLI_NUM);  
            $n=$row[0];    
            echo "<option value='$n'";                            
            echo "> $n</option><br>";                                    
        }

        echo<<<_END
                </select>    
                <input type="hidden" name="ltoe" value="yes">
                <input type="submit" value="Toevoegen">        
                    </form>    </pre></html>
        _END;
    }
    else die("Invalid username/password combination");
			
}

else{
     header('WWW-Authenticate: Basic realm="Restricted Area"');
     header('HTTP/1.0 401 Unauthorized');
     die("Please enter your username and password");
}
