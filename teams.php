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

        echo <<<_END
                <html>
                <head>
                <title>Informatie ledenbestand</title>
                <style>a {
                        text-decoration: none;
                        color: black;
                    }
                </style>
                </head>
                <body>
                <pre> 
                <b><h1>De teams van VV Boxtel.</h1></b>
                <button type='button'><a href='leden.php'>Leden pagina</a></button> <button type='button'><a href='ttoevoeg.php'>Team toevoegen</a></button><br><br>
        _END;

                        

        $lzien = "SELECT lid.lidnummer, lid.voornaam, lid.naam, teams.teamnaam, "
               . "teams.omschrijving, teamlid.tl_ID FROM lid INNER JOIN teamlid ON "
               . "teamlid.lidnummer=lid.lidnummer INNER JOIN teams ON teams.teamnaam=teamlid.teamnaam "
               . "ORDER BY lidnummer ASC;";
        $result = $mysqli->query($lzien);
        if (!$result) die ("Database access failed");
        $rows = $result->num_rows;
        echo "<table><tr><th>Lidnummer</th><th>Voornaam</th><th>Achternaam</th><th>Teamnaam</th><th>Omschrijving</th><th>Teamlid Id</th>";
         for ($j=0;$j<$rows; ++$j){              
            $row = $result->fetch_array(MYSQLI_NUM);  
            $n=$row[0];   
            echo "<tr>";
            for ($k =0; $k <6;++$k){           
                echo "<td>". htmlspecialchars($row[$k]). "</td> ";
            }
            echo "<td><button type='submit' value='veranderen' ><a href='tverander.php?id=$n'> Verander van team</a></button></td>"
               . "<td><button type='submit' value='verwijderen'><a href='tverwijder.php?id=$n'>Verwijder uit een team</a></button></td> "
               . "<td><button type='submit' value='toevoegen'><a href='tltoe.php?id=$n'>Toevoegen aan een team</a></button></td> ";          
            echo "</tr>";
        }
        echo "</table>";
    }
else die("Invalid username/password combination");
}
else{
     header('WWW-Authenticate: Basic realm="Restricted Area"');
     header('HTTP/1.0 401 Unauthorized');
     die("Please enter your username and password");
}