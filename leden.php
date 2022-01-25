<?php

$username = 'LOIDocent';
$password = 'mysqlphp';

if (isset($_COOKIE['date'])) $date = $_COOKIE['date'];
if (isset($_COOKIE['gebruiker'])) $gebruiker = $_COOKIE['gebruiker'];

setcookie('date',date("l jS \of F Y h:i:s A e O"));
setcookie('gebruiker',$username);


if (isset($_SERVER['PHP_AUTH_USER']) &&
        isset($_SERVER['PHP_AUTH_PW'])) 
    {
    
    if($_SERVER['PHP_AUTH_USER'] === $username &&
       $_SERVER['PHP_AUTH_PW'] === $password)
       {

        require_once 'login.php';
        require_once 'sanitize.php';
        require_once 'dev-settings.php';
        $mysqli = new mysqli($hn, $un, $pw, $db);
        if ($mysqli->connect_error) die("fatal error");

        echo <<<_END
                <html>
                <head>
                <title>Informatie ledenbestand</title>
                    <style>
                        a {
                            text-decoration: none;
                            color: black;
                        }
                    </style>
                </head>
                <body>
                <pre> 
                <b><h1>Welkom op bij het ledenbestand van VV Boxtel.</h1></b>
                <button type='button'><a href='lidtoe.php'>Lid toevoegen</a></button> <button type="button"><a href="teams.php">Teams</a></button><br><br> 
        _END;

                        

        $lzien = "SELECT lid.*, email.emailadres, t.telefoonnummer, postcode.*, teamlid.tl_ID, teamlid.teamnaam FROM lid 
                  LEFT JOIN email ON lid.lidnummer=email.lidnummer
                  LEFT JOIN telefoonnummers t ON lid.lidnummer=t.lidnummer
                  LEFT JOIN postcode ON postcode.postcode=lid.postcode
                  LEFT JOIN teamlid ON teamlid.lidnummer=lid.lidnummer ORDER BY lidnummer ASC;";
        $result = $mysqli->query($lzien);
        if (!$result) die ("Database access failed");

        $rows = $result->num_rows;
        echo "<table><tr><th>Lidnummer</th><th>Achternaam</th><th>Voornaam</th><th>Postcode</th><th>Huisnummer</th><th>Emailadres</th><th>Telefoonnummer</th><th>Postcode</th><th>Straat</th><th>Woonplaats</th><th>Team-ID</th><th>teamnaam</th></tr>";
        for ($j=0;$j<$rows; ++$j){                                
            $row = $result->fetch_array(MYSQLI_NUM); 
            $n=$row[0];   
            echo "<tr>";
            for ($k =0; $k <12;++$k){           
                echo "<td>". htmlspecialchars($row[$k]). "</td> ";
            }
            echo "<td><button type='submit' value='veranderen' ><a href='verander.php?id=$n'> Verander</a></button></td><td><button type='submit' value='verwijderen'><a href='verwijder.php?id=$n'>Verwijderen</a></button></td> ";        
            echo "</tr>";
        }
        echo "</table>";
                            
        echo <<<_END
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

        echo "<br><br><button type='button'><a href='toevoeg.php'>Postcode toevoegen</a></button>";
    }
    else die("Invalid username/password combination");
}


    
else{
header('WWW-Authenticate: Basic realm="Restricted Area"');
header('HTTP/1.0 401 Unauthorized');
die("Please enter your username and password");
}
