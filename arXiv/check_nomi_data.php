<?php

#funzione per la verifica se ci sono sessioni attive

function sessioneavviata() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $var = True;
    $a = date("Ymd", time());
    $datas = datasessione();
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT attivo FROM sessione";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if (($row['attivo'] == 0) or ( $datas < $a - 1)) {
        $var = False;
    }
    mysql_close($db_connection);
    return $var;
}

#funzione di avvio della sessione

function avviasessione() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $a = date("Ymd", time());
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "UPDATE sessione SET attivo='1'";
    $result = mysql_query($sql) or die(mysql_error());
    $sql = "UPDATE sessione_data SET data='" . $a . "'";
    $result = mysql_query($sql) or die(mysql_error());
    mysql_close($db_connection);
}

#funzione per terminare la sessione

function chiudisessione() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "UPDATE sessione SET attivo='0'";
    $result = mysql_query($sql) or die(mysql_error());
    mysql_close($db_connection);
}

#funzione verifica nuovo nome

function nomiprec($nome) {
    #cerca se il nome se era stato gia cercato...
    $nome = trim($nome);
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT * FROM AUTORI_BACKUP WHERE nome='" . $nome . "'";
    $query = mysql_query($sql) or die(mysql_error());
    $array = mysql_fetch_row($query);
    if ($array[0] == $nome) {
        mysql_close($db_connection);
        return True;
    } else {
        mysql_close($db_connection);
        return False;
    }
}

# funzione lettura dei preprint

function searchpreprint() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dmipreprints/' . 'authorization/sec_sess.php';
    sec_session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 86400)) {
        if ($_SESSION['logged_type'] === "mod") {
            $cred = 1;
        } else {
            $cred = 0;
        }
    }
    #rilevazione del browser in uso
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (strlen(strstr($agent, "Firefox")) > 0) {
        $browser = 'Firefox';
    }
    #verifica ordine risultati
    if ($_GET['o'] == "dated") {
        $order = "data_pubblicazione DESC";
        $orstr = "decreasing date";
    } else if ($_GET['o'] == "datec") {
        $order = "data_pubblicazione ASC";
        $orstr = "increasing date";
    } else if ($_GET['o'] == "named") {
        $order = "autori DESC";
        $orstr = "decreasing name";
    } else if ($_GET['o'] == "namec") {
        $order = "autori ASC";
        $orstr = "increasing name";
    } else if ($_GET['o'] == "idd") {
        $order = "id_pubblicazione DESC";
        $orstr = "decreasing ID";
    } else {
        $order = "id_pubblicazione ASC";
        $orstr = "increasing ID";
    }
    $cat = 0;
    #verifica parametri ricerca
    if ($_GET['all'] != "1") {
        if ($_GET['h'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."authors, ";
        }
        if ($_GET['t'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."title, ";
        }
        if ($_GET['a'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."abstract, ";
        }
        if ($_GET['y'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."category, ";
        }
        if ($_GET['c'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."comments, ";
        }
        if ($_GET['j'] == "1") {
            $query = $query . "SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat++;
            $cat3=$cat3."journal-ref, ";
        }
    } else {
        $query = " 
    	SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
        $cat = "ALL RECORDS";
    }
    $query = substr($query, 0, -7);
    $cat3 = substr($cat3, 0, -2);
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    $copia = $_SERVER['DOCUMENT_ROOT'] . '/dmipreprints' . "/pdf/";
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    #risultati visualizzati per pagina
    $risperpag = 5;
    #recupero pagina
    $p = $_GET['p'];
    #limite risultati
    $limit = $risperpag * $p - $risperpag;
    #query di ricerca
    $querytotale = mysql_query($query);
    $ristot = mysql_num_rows($querytotale);
    if($cat != "ALL RECORDS"){
    	echo $ristot . " RESULTS ON " . $cat . " FIELD(".$cat3.") FOR: '" . $_GET['r'] . "' (results ordered by ".$orstr.")";
    }else{
    	echo $ristot . " RESULTS ON " . $cat . " FOR: '" . $_GET['r'] . "' (results ordered by ".$orstr.")";
    }
    $npag = ceil($ristot / $risperpag);
    $query = $query . " ORDER BY " . $order . " LIMIT " . $limit . "," . $risperpag . "";
    $result = mysql_query($query) or die(mysql_error());
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    #impostazione della paginazione dei risultati
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="view_preprints.php?p=1&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysql_fetch_array($result)) {
        $i++;
        if ($cred == 1) {
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:90%;'>";
            if ($_GET['w'] == "0") {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a>&nbsp&nbsp&nbsp<a title='Change this preprint' style='color:#007897;' href='./manual_edit.php?id=" . $row['id_pubblicazione'] . "' onclick='window.open(this.href); return false'>edit</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            } else {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a title='Change this preprint' style='color:#007897;' href='./manual_edit.php?id=" . $row['id_pubblicazione'] . "' onclick='window.open(this.href); return false'>edit</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            }
        } else {
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:90%;'>";
            if ($_GET['w'] == "0") {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            }
        }
        echo "<p><h1>Title:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['titolo']) . "</div>";
        echo "<p><h1>Date of pubblication:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['data_pubblicazione']) . "</div>";
        echo "<p><h1>Author/s:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['autori']) . "</div>";
        echo "<p><h1>Journal reference:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['referenze']) . "</div>";
        echo "<p><h1>Comments:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['commenti']) . "</div>";
        echo "<p><h1>Category:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['categoria']) . "</div>";
        echo "<p><h1>Abstract:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['abstract']) . "</div>";
        $na = $row['Filename'];
        $na = substr($na, -3, 3);
        #controllo se il file é un pdf
        if ($na == "pdf") {
            if ($_GET['w'] == "1") {
                #visualizzazione integrata del pdf
                echo "<p><h1>pdf:</h1></p><center><embed style='display: block; border: 1px; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;' width='850px' height='600px' src='./pdf/" . $row['Filename'] . "'></center>";
            }
        } else {
            if ($_GET['w'] == "1") {
                echo "<p><h1>document:</h1></p><div style='margin-left:2%; margin-right:2%;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>LINK</a> (On page view disabled for this file)</div>";
            }
        }
        echo "</div><hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    #impostazioni della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="view_preprints.php?p=1&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $x = $limit + 1;
    echo "RESULTS FROM " . $x . " TO " . ($p * 5) . "<br/><br/>";
    mysql_close($db_connection);
}

# funzione filtro e lettura dei preprint

function filtropreprint() {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/dmipreprints/' . 'authorization/sec_sess.php';
    sec_session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 86400)) {
        if ($_SESSION['logged_type'] === "mod") {
            $cred = 1;
        } else {
            $cred = 0;
        }
    }
    #rilevazione del browser in uso
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (strlen(strstr($agent, "Firefox")) > 0) {
        $browser = 'Firefox';
    }
    #verifica ordine risultati
    if ($_GET['o'] == "dated") {
        $order = "data_pubblicazione DESC";
        $orstr = "decreasing date";
    } else if ($_GET['o'] == "datec") {
        $order = "data_pubblicazione ASC";
        $orstr = "increasing date";
    } else if ($_GET['o'] == "named") {
        $order = "autori DESC";
        $orstr = "decreasing name";
    } else if ($_GET['o'] == "namec") {
        $order = "autori ASC";
        $orstr = "increasing name";
    } else if ($_GET['o'] == "idd") {
        $order = "id_pubblicazione DESC";
        $orstr = "decreasing ID";
    } else {
        $order = "id_pubblicazione ASC";
        $orstr = "increasing ID";
    }
    #verifica filtro
    if ($_GET['f'] == "author") {
        $argom = "autori";
    } else if ($_GET['f'] == "category") {
        $argom = "categoria";
    } else if ($_GET['f'] == "year") {
        $argom = "data_pubblicazione";
    } else if ($_GET['f'] == "id") {
        $argom = "id_pubblicazione";
    }
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    $copia = $_SERVER['DOCUMENT_ROOT'] . '/dmipreprints' . "/pdf/";
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    #risultati visualizzati per pagina
    $risperpag = 5;
    #recupero pagina
    $p = $_GET['p'];
    #limite risultati
    $limit = $risperpag * $p - $risperpag;
    #query di ricerca
    if (isset($argom)) {
        $querytotale = mysql_query("SELECT * FROM PREPRINTS WHERE " . $argom . " LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1'");
        $ristot = mysql_num_rows($querytotale);
        if ($ristot != 0) {
            echo $ristot." RESULTS FOR: '" . ($_GET['r']) . "'(" . $_GET['f'] . " filter) (results ordered by " . $orstr . ")";
        } else {
            echo "SEARCHED " . strtoupper($_GET['f']) . " '" . ($_GET['r']) . "' NOT FOUND!";
            echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
            break;
        }
        $npag = ceil($ristot / $risperpag);
        $sql = "SELECT * FROM PREPRINTS WHERE " . $argom . " LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' ORDER BY " . $order . " LIMIT " . $limit . "," . $risperpag . "";
        $result = mysql_query($sql) or die(mysql_error());
    } else {
        $querytotale = mysql_query("SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1'");
        $ristot = mysql_num_rows($querytotale);
        echo "PREPRINTS APPROVED: " . $ristot;
        $npag = ceil($ristot / $risperpag);
        $sql = "SELECT * FROM PREPRINTS WHERE autori LIKE '%" . $_GET['r'] . "%' AND checked='1' ORDER BY data_pubblicazione DESC LIMIT " . $limit . "," . $risperpag . "";
        $result = mysql_query($sql) or die(mysql_error());
    }
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    #impostazione della paginazione dei risultati
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="view_preprints.php?p=1&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysql_fetch_array($result)) {
        $i++;
        if ($cred == 1) {
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:90%;'>";
            if ($_GET['w'] == "0") {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a>&nbsp&nbsp&nbsp<a title='Change this preprint' style='color:#007897;' href='./manual_edit.php?id=" . $row['id_pubblicazione'] . "' onclick='window.open(this.href); return false'>edit</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            } else {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a title='Change this preprint' style='color:#007897;' href='./manual_edit.php?id=" . $row['id_pubblicazione'] . "' onclick='window.open(this.href); return false'>edit</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            }
        } else {
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:90%;'>";
            if ($_GET['w'] == "0") {
                echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a></div><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            }
        }
        echo "<p><h1>Title:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['titolo']) . "</div>";
        echo "<p><h1>Date of pubblication:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['data_pubblicazione']) . "</div>";
        echo "<p><h1>Author/s:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['autori']) . "</div>";
        echo "<p><h1>Journal reference:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['referenze']) . "</div>";
        echo "<p><h1>Comments:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['commenti']) . "</div>";
        echo "<p><h1>Category:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['categoria']) . "</div>";
        echo "<p><h1>Abstract:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['abstract']) . "</div>";
        $na = $row['Filename'];
        $na = substr($na, -3, 3);
        #controllo se il file é un pdf
        if ($na == "pdf") {
            if ($_GET['w'] == "1") {
                #visualizzazione integrata del pdf
                echo "<p><h1>pdf:</h1></p><center><embed style='display: block; border: 1px; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;' width='850px' height='600px' src='./pdf/" . $row['Filename'] . "'></center>";
            }
        } else {
            if ($_GET['w'] == "1") {
                echo "<p><h1>document:</h1></p><div style='margin-left:2%; margin-right:2%;'><a style='color:#007897;' href=./pdf/" . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>LINK</a> (On page view disabled for this file)</div>";
            }
        }
        echo "</div><hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    #impostazioni della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="view_preprints.php?p=1&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&w=' . $_GET['w'] . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $x = $limit + 1;
    echo "RESULTS FROM " . $x . " TO " . ($p * 5) . "<br/><br/>";
    mysql_close($db_connection);
}

#funzione lettura dei preprint archiviati

function leggipreprintarchiviati() {
    $copia = $_SERVER['DOCUMENT_ROOT'] . '/dmipreprints/' . "arXiv/pdf/";
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $risperpag = 5;
    $p = $_GET['p'];
    $limit = $risperpag * $p - $risperpag;
    $querytotale = mysql_query("SELECT * FROM PREPRINTS_ARCHIVIATI WHERE checked='1'");
    $ristot = mysql_num_rows($querytotale);
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    echo "PREPRINTS ARCHIVED: " . $ristot . "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    $npag = ceil($ristot / $risperpag);
    #impostazione della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="archived_preprints.php?p=1&r=' . $_GET['r'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="archived_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    #verifica se i preprint devono essere rimossi definitivamente
    if ($_GET['c'] != "Remove all") {
        $sql = "SELECT * FROM PREPRINTS_ARCHIVIATI WHERE checked='1' ORDER BY data_pubblicazione DESC LIMIT " . $limit . "," . $risperpag . "";
        $result = mysql_query($sql) or die(mysql_error());
        $i = $limit;
        #recupero info e visualizzazione
        while ($row = mysql_fetch_array($result)) {
            $i++;
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:90%;'>";
            echo "<p><h1>Id of pubblication:</h1></p><div style='margin-left:2%;'>" . $row['id_pubblicazione'] . "</div>";
            echo "<p><h1>Title:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['titolo']) . "</div>";
            echo "<p><h1>Date of pubblication:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['data_pubblicazione']) . "</div>";
            echo "<p><h1>Author/s:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['autori']) . "</div>";
            echo "<p><h1>Journal reference:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['referenze']) . "</div>";
            echo "<p><h1>Comments:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['commenti']) . "</div>";
            echo "<p><h1>Category:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['categoria']) . "</div>";
            echo "<p><h1>Abstract:</h1></p><div style='margin-left:2%; margin-right:2%;'>" . stripslashes($row['abstract']) . "</div>";

            echo "</div><hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
        }
        #visualizzazione della navigazione per pagine
        if ($ristot != 0) {
            if ($p != 1) {
                echo '<a style="color:#007897; text-decoration: none;" title="First page" href="archived_preprints.php?p=1&r=' . $_GET['r'] . '"> &#8656 </a>';
                if ($p >= 3 && $t3 > 0) {
                    echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '"> ' . " " . $t3 . " " . ' </a>';
                }
                if ($p >= 2 && $t2 > 0) {
                    echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '"> ' . " " . $t2 . " " . ' </a>';
                }
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '"> ' . " " . $t1 . " " . ' </a>';
            }
            echo " " . $p . " ";
            if ($p != $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '"> ' . " " . $t4 . " " . ' </a>';
                if ($p < $npag && $t5 <= $npag) {
                    echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '"> ' . " " . $t5 . " " . ' </a>';
                }
                if ($p < $npag && $t6 <= $npag) {
                    echo '<a style="color:#007897; text-decoration: none;" href="archived_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '"> ' . " " . $t6 . " " . ' </a>';
                }
                echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="archived_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '"> &#8658 </a>';
            }
            echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
        }
    } else {
        #controllo di preprint da rimuovere
        if ($ristot != 0) {
            cancellapreprint();
            echo '<script type="text/javascript">alert("Preprints deleted from database!");</script>';
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=./archived_preprints.php?p=1">';
        } else {
            $limit = 0;
            echo '<script type="text/javascript">alert("No archived preprint!");</script>';
        }
    }
    $x = $limit + 1;
    echo "RESULTS FROM " . $x . " TO " . ($p * 5) . "<br/><br/>";
    mysql_close($db_connection);
}

# funzione cancellazione preprint

function cancellaselected($id) {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "DELETE FROM PREPRINTS WHERE id_pubblicazione='" . $id . "'";
    $result = mysql_query($sql) or die(mysql_error());
    mysql_close($db_connection);
}

# funzione cancellazione preprint archiviati

function cancellapreprint() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "DELETE FROM PREPRINTS_ARCHIVIATI WHERE checked='1'";
    $result = mysql_query($sql) or die(mysql_error());
    echo "PREPRINTS DELETED!<br/><br/><br/>";
    mysql_close($db_connection);
}

#funzione che cerca se il preprint è stato già scaricato nell'esecuzione in corso

function preprintscaricati($id) {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $var = False;
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT id FROM temp";
    $result = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_array($result)) {
        if ($row['id'] == $id) {
            $var = True;
        }
    }
    mysql_close($db_connection);
    return $var;
}

#funzione per l'inserimento dell'id dentro temp

function aggiornapreprintscaricati($id) {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "INSERT INTO temp (id) VALUES ('" . $id . "') ON DUPLICATE KEY UPDATE id = VALUES(id)";
    $result = mysql_query($sql) or die(mysql_error());
    mysql_close($db_connection);
}

#funzione per la cancellazione del contenuto temp

function azzerapreprint() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT id FROM temp";
    $result = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_array($result)) {
        $sql = "DELETE FROM temp WHERE id='" . $row['id'] . "'";
        $query = mysql_query($sql) or die(mysql_error());
    }
    mysql_close($db_connection);
}

#funzione che cerca se il preprint se è presente

function cercapreprint($id) {
    $id = trim($id);
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $id . "'";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ($row['nome'] == $nome) {
        $var[0] = $row['id_pubblicazione'];
        $var[1] = stripslashes($row['titolo']);
        $var[2] = stripslashes($row['data_pubblicazione']);
        $var[3] = stripslashes($row['autori']);
        $var[4] = stripslashes($row['referenze']);
        $var[5] = stripslashes($row['commenti']);
        $var[6] = stripslashes($row['categoria']);
        $var[7] = stripslashes($row['abstract']);
    }
    mysql_close($db_connection);
    return $var;
}

#funzione che cerca se il nome è presente

function cercanome($nome) {
    #cerca se il nome se era stato gia cercato...
    $nome = trim($nome);
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $var = False;
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT * FROM AUTORI WHERE nome='" . $nome . "'";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    if ($row['nome'] == $nome) {
        $var = True;
    }
    mysql_close($db_connection);
    return $var;
}

#funzione aggiornamento nomi_ultimo_lancio

function aggiornanomi() {
    #leggo i nuovi nomi e li inserisco in array...
    $array = legginomi();
    #cerca se il nome se era stato gia cercato...
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT nome FROM AUTORI_BACKUP";
    $result = mysql_query($sql) or die(mysql_error());
    $nl2 = count($array);
    while ($row = mysql_fetch_array($result)) {
        $sql = "DELETE FROM AUTORI_BACKUP WHERE nome='" . $row['nome'] . "'";
        $query = mysql_query($sql) or die(mysql_error());
    }
    #aggiorno i nomi...
    for ($i = 0; $i < $nl2; $i++) {
        $sql = "INSERT INTO AUTORI_BACKUP (nome) VALUES ('" . $array[$i] . "')";
        $query = mysql_query($sql) or die(mysql_error());
    }
    mysql_close($db_connection);
}

# funzione lettura nomi

function legginomi() {
    #leggo i nuovi nomi e li inserisco in array...
    #cerca se il nome se era stato gia cercato...
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT nome FROM AUTORI";
    $result = mysql_query($sql) or die(mysql_error());
    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        $array[$i] = $row['nome'];
        $i++;
    }
    mysql_close($db_connection);
    return $array;
}

#funzione scrittura nomi

function scrivinomi($nomi) {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT nome FROM AUTORI";
    $result = mysql_query($sql) or die(mysql_error());
    $nl2 = count($nomi);
    while ($row = mysql_fetch_array($result)) {
        $sql = "DELETE FROM AUTORI WHERE nome='" . $row['nome'] . "'";
        $query = mysql_query($sql) or die(mysql_error());
    }
    #aggiorno i nomi...
    for ($i = 0; $i < $nl2; $i++) {
        $sql = "INSERT INTO AUTORI (nome) VALUES ('" . $nomi[$i] . "') ON DUPLICATE KEY UPDATE nome = VALUES(nome)";
        $query = mysql_query($sql) or die(mysql_error());
    }
    mysql_close($db_connection);
}

#funzione inserimento nuovo utente

function aggiungiutente($nome, $a) {
    #leggo i nuovi nomi e li inserisco in array...
    $array = legginomi();
    while (strpos($nome, "  ") !== FALSE) {
        echo '<script type="text/javascript">alert("NAME NOT VALID! DETECTED CONSECUTIVE SPACE INSIDE FIELD NAME!");</script>';
        return;
    }
    $array2 = explode(",", $nome);
    $nl = count($array2);
    $l = count($array);
    for ($i = 0; $i < $nl; $i++) {
        $temp = $array2[$i];
        $temp = trim($temp);
        $temp = strtoupper($temp);
        #verifico se il nome è già presente...
        $array[$l] = $temp;
        $l++;
        $ris = cercanome($temp);
        if ($ris == False) {
            if ($a == 1) {
                #aggiorno i nomi se ci sono nomi da aggiungere...
                scrivinomi($array);
                echo '<script type="text/javascript">alert("' . $temp . ' inserted!");</script>';
            } else {
                echo '<script type="text/javascript">alert("' . $temp . ' not found!");</script>';
            }
        } else {
            if ($a == 1) {
                echo '<script type="text/javascript">alert("' . $temp . ' exists!");</script>';
            } else {
                echo '<script type="text/javascript">alert("' . $temp . ' found!");</script>';
            }
        }
    }
}

#data ultima sessione

function datasessione() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT data FROM sessione_data";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    $data = $row['data'];
    mysql_close($db_connection);
    return $data;
}

#ritorno la data come intero

function dataprec() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT data FROM DATA_ULTIMO_LANCIO";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    $data = $row['data'];
    mysql_close($db_connection);
    $data = trim($data);
    $data = substr($data, 0, 10);
    $data = str_replace("-", "", $data);
    #conversione della stringa in intero
    $data = intval($data);
    return $data;
}

#ritorno la data come una stringa

function datastring() {
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT data FROM DATA_ULTIMO_LANCIO";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    $data = $row['data'];
    mysql_close($db_connection);
    return $data;
}

#aggiorno data_ultimo_lancio con la data di ultimo lancio

function aggiornadata() {
    $a = date("Y-m-d H:i", time());
    #definizione parametri di connessione al database
    $hostname_db = "localhost";
    $db_monte = "dmipreprints"; //nome del database
    $username_db = "root"; //l'username
    $password_db = "1234"; // password
    $db_connection = mysql_connect($hostname_db, $username_db, $password_db) or trigger_error(mysql_error(), E_USER_ERROR);
    mysql_select_db($db_monte, $db_connection);
    $sql = "SELECT data FROM DATA_ULTIMO_LANCIO";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($result);
    $sql = "DELETE FROM DATA_ULTIMO_LANCIO WHERE data='" . $row['data'] . "'";
    $query = mysql_query($sql) or die(mysql_error());
    #aggiorno la data...
    $sql = "INSERT INTO DATA_ULTIMO_LANCIO (data) VALUES ('" . $a . "') ON DUPLICATE KEY UPDATE data = VALUES(data)";
    $query = mysql_query($sql) or die(mysql_error());
    mysql_close($db_connection);
}

?>
