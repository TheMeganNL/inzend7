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

        if (isset($_POST['ptoe'])){

            $postcode = sanitizeString($_POST['postcode']);
            $adres = sanitizeString($_POST['straat']);
            $woonplaats = sanitizeString($_POST['plaats']);
            $ptoe = "INSERT INTO postcode VALUES ('$postcode','$adres','$woonplaats')";
            $result = $mysqli->query($ptoe);
            if (!$result) die ("Database access failed");
            if($result) echo "Deze postcode is toegevoegd<br><br>"; 
        }

        echo <<<_END
        <html><head><title>Lid verwijderen</title><style>
            a {
                text-decoration: none;
                color: black;
            }
        </style></head><body> <pre> <button type='button'><a href='leden.php'>Terug</a></button>
        ----------------------------------------------------------------------------------------------------------------------

                            <h2>Deze postcodes zijn bij ons bekend.</h2>
        _END;

        $pcheck = " SELECT * FROM postcode";
        $result = $mysqli->query($pcheck);
        if(!$result) die ("Database access failed");
                        
        $rows = $result->num_rows;
        echo "<table><tr><th>Postcode</th><th>Straatnaam</th><th>Plaats</th></tr>";
        for ($j=0;$j<$rows; ++$j){
            $row = $result->fetch_array(MYSQLI_NUM);    
            echo "<tr>";
            for ($k =0; $k <3;++$k){
                echo "<td>". htmlspecialchars($row[$k]). "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        echo <<<_END
        ---------------------------------------------------------------------------------------------------------------------- 
                <h3>Staat uw postcode niet in ons systeem dan kunt u die hier toevoegen:</h3>
                <form method="post" action="" form="postcode">
                Postcode <input type="text" name="postcode">
                Straat <input type="text" name="straat">
                Woonplaats <input type="text" name="plaats">
                <input type="hidden" name="ptoe" value="yes">
                <input type="submit" value="Postcode toevoegen">        
                    </form > </pre>
        _END;
    }
    else die("Invalid username/password combination");
			
}
else{
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    die("Please enter your username and password");
}

