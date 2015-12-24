<?php
#funzione inserimento informazioni preprint

function insert_pubb($array, $uid) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
#adattamento stringhe pericolose per la query...
    $array[1] = addslashes($array[1]);
    $array[2] = addslashes($array[2]);
    $array[3] = addslashes($array[3]);
    $array[4] = addslashes($array[4]);
    $array[5] = addslashes($array[5]);
    $array[6] = addslashes($array[6]);
    $array[7] = addslashes($array[7]);
#generazione chiave
    $generato = rand();
    while (mysqli_num_rows(mysqli_query($db_connection, "SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $generato . "v1'") or die(mysql_error())) != 0) {
        $generato = rand();
    }
    $generato = $generato . "v1";
    $sql = "INSERT INTO PREPRINTS ( uid, id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) "
            . "VALUES ('" . $uid . "','" . $generato . "','" . $array[1] . "','" . date("c", time()) . "','" . $array[3] . "','" . $array[4] . "','" . $array[5] . "','" . $array[6] . "','" . $array[7] . "') ON DUPLICATE KEY UPDATE id_pubblicazione = VALUES(id_pubblicazione)";
    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
#chiusura connessione al database
    mysqli_close($db_connection);
    return $generato;
}

#funzione inserimento informazioni preprint

function insert_p($array, $uid) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
#adattamento stringhe pericolose per la query...
    $array[1] = addslashes($array[1]);
    $array[2] = addslashes($array[2]);
    $array[3] = addslashes($array[3]);
    $array[4] = addslashes($array[4]);
    $array[5] = addslashes($array[5]);
    $array[6] = addslashes($array[6]);
    $array[7] = addslashes($array[7]);
    //query
    $sql = "INSERT INTO PREPRINTS ( uid, id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) "
            . "VALUES ('" . $uid . "','" . $array[0] . "','" . $array[1] . "','" . date("c", time()) . "','" . $array[3] . "','" . $array[4] . "','" . $array[5] . "','" . $array[6] . "','" . $array[7] . "') ON DUPLICATE KEY UPDATE id_pubblicazione = VALUES(id_pubblicazione)";
    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
#chiusura connessione al database
    mysqli_close($db_connection);
}

#funzione che inserisce il pdf caricato all'interno dei database

function insertopdf($id) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    $id = str_replace("-", "/", $id);
    //query
    $sql2 = "SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $id . "'";
    $query2 = mysqli_query($db_connection, $sql2) or die(mysql_error());
    $row = mysqli_fetch_array($query2);
    if ($handle = opendir($basedir)) {
        $i = 0;
        while ((false !== ($file = readdir($handle)))) {
            if ($file != '.' && $file != '..' && $file != 'index.php') {
                $idd = substr($file, 0, -4);
                if ($row['id_pubblicazione'] == $idd) {
                    $sql = "UPDATE PREPRINTS SET Filename='" . $file . "', checked='1' WHERE id_pubblicazione='" . $id . "'";
                    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
                    $i++;
                    copy($basedir . $file, $copia . $file);
                    unlink($basedir . $file);
                }
            }
        }
#chiusura della directory...
        closedir($handle);
    }
#chiusura connessione al database
    mysqli_close($db_connection);
}

#funzione che inserisce un pdf all'interno dei database

function insertpdf($id, $type) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    //query
    $sql2 = "SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $id . "'";
    $query2 = mysqli_query($db_connection, $sql2) or die(mysql_error());
    $row = mysqli_fetch_array($query2);
    unlink($copia . $row['Filename']);
    if ($handle = opendir($basedir)) {
        $i = 0;
        while ((false !== ($file = readdir($handle)))) {
            if ($file != '.' && $file != '..' && $file != 'index.php') {
                $sql = "UPDATE PREPRINTS SET Filename= '" . $file . "', checked='1' WHERE id_pubblicazione='" . $id . "'";
                $query = mysqli_query($db_connection, $sql) or die(mysql_error());
                fclose($var);
                $i++;
                copy($basedir . $file, $copia . $file);
                unlink($basedir . $file);
            }
        }
#chiusura della directory...
        closedir($handle);
    }
#chiusura connessione al database
    mysqli_close($db_connection);
}

#funzione che visualizza lista upload

function leggiupload($uid) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    if (!isset($_GET['p'])) {
        $p = 1;
    } else {
        $p = $_GET['p'];
    }
    $risperpag = 5;
    $limit = $risperpag * $p - $risperpag;
    $sql = "SELECT * FROM PREPRINTS WHERE uid='" . $uid . "' AND checked='1'";
    $querytotale = mysqli_query($db_connection, $sql) or die(mysql_error());
    $ristot = mysqli_num_rows($querytotale);
    echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    echo "PAPERS UPLOADED: " . $ristot . "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    $npag = ceil($ristot / $risperpag);
#impostazione della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="uploaded.php?p=1&r=' . $uid . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 3) . '&r=' . $uid . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 2) . '&r=' . $uid . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 1) . '&r=' . $uid . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 1) . '&r=' . $uid . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 2) . '&r=' . $uid . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 3) . '&r=' . $uid . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="uploaded.php?p=' . $npag . '&r=' . $uid . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $sql = "SELECT * FROM PREPRINTS WHERE uid='" . $uid . "' AND checked='1' ORDER BY data_pubblicazione DESC LIMIT " . $limit . "," . $risperpag . "";
    $result = mysqli_query($db_connection, $sql) or die(mysql_error());
    $i = $limit;
#recupero info e visualizzazione
    while ($row = mysqli_fetch_array($result)) {
        $i++;
        echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:98%;'>";
        echo "<p><h1>Id of pubblication:</h1></p><div style='float:right;'><a style='color:#007897;' href=" . $copia . $row['Filename'] . " onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a>&nbsp&nbsp&nbsp<a title='Change this preprint' style='color:#007897;' href='./edit.php?id=" . $row['id_pubblicazione'] . "&r=" . $uid . "' onclick='window.open(this.href); return false'>edit</a></div><div style='margin-left:1%;'>" . $row['id_pubblicazione'] . "</div>";
#echo "<p><h1>Id of pubblication:</h1></p><div style='margin-left:1%;'>" . $row['id_pubblicazione'] . "</div>";
        echo "<p><h1>Title:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['titolo']) . "</div>";
        echo "<p><h1>Date of pubblication:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['data_pubblicazione']) . "</div>";
        echo "<p><h1>Authors:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['autori']) . "</div>";
        echo "<p><h1>Journal reference:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['referenze']) . "</div>";
        echo "<p><h1>Comments:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['commenti']) . "</div>";
        echo "<p><h1>Category:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['categoria']) . "</div>";
        echo "<p><h1>Abstract:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . stripslashes($row['abstract']) . "</div>";

        echo "</div><hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
#visualizzazione della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            echo '<a style="color:#007897; text-decoration: none;" title="First page" href="uploaded.php?p=1&r=' . $uid . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 3) . '&r=' . $uid . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 2) . '&r=' . $uid . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p - 1) . '&r=' . $uid . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 1) . '&r=' . $uid . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 2) . '&r=' . $uid . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#007897; text-decoration: none;" href="uploaded.php?p=' . ($p + 3) . '&r=' . $uid . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#007897; text-decoration: none;" title="Last page" href="uploaded.php?p=' . $npag . '&r=' . $uid . '"> &#8658 </a>';
        }
        echo "<hr style='display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0;'>";
    }
    $x = $limit + 1;
    echo "RESULTS FROM " . $x . " TO " . ($p * 5) . "<br/>";
    mysqli_close($db_connection);
}

#funzione che controlla la versione del preprint e lo archivia eventualmente

function version_preprintd($id1) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
#elaborazione dell'id...
    $lunghezza = strlen($id1);
    $id = substr($id1, 0, $lunghezza - 1);
#verifica se esistono preprints precedenti e li sposto...
    for ($i = 0; $i <= 20; $i++) {
        $sql = "SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $id . $i . "'";
        $query = mysqli_query($db_connection, $sql) or die(mysql_error());
        $row = mysqli_fetch_row($query);
        if (strcmp($id1, $row['1']) > 0) {
            #archiviazione preprints precedenti...
            $sql2 = "INSERT INTO PREPRINTS_ARCHIVIATI SELECT * FROM PREPRINTS WHERE id_pubblicazione='" . $id . $i . "' ON DUPLICATE KEY UPDATE id_pubblicazione = VALUES(id_pubblicazione)";
            $query2 = mysqli_query($db_connection, $sql2) or die(mysql_error());
            copy($copia . $row[9], $basedir4 . $row[9]);
            unlink($copia . $row[9]);
            #rimozione da preprints...
            $sql2 = "DELETE FROM PREPRINTS WHERE id_pubblicazione='" . $id . $i . "'";
            $query2 = mysqli_query($db_connection, $sql2) or die(mysql_error());
        }
    }
#chiusura connessione al database
    mysqli_close($db_connection);
}

#funzione controllo se ci sono preprint da approvare

function check_approve() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    #verifica se esistono preprints precedenti e li sposto...
    $sql = "SELECT COUNT(*) AS TOTALFOUND FROM PREPRINTS WHERE checked='0'";
    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
    $row = mysqli_fetch_array($query);
    #chiusura connessione al database
    mysqli_close($db_connection);
    if ($row['TOTALFOUND'] > 0) {
        return true;
    } else {
        return false;
    }
}

#funzione recupero informazioni degli account

function find_accounts($ord) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    #verifica se esistono preprints precedenti e li sposto...
    $sql = "SELECT * FROM ACCOUNTS ORDER BY " . $ord;
    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
    #chiusura connessione al database
    mysqli_close($db_connection);
    return $query;
}

#funzione recupero informazioni degli account

function remove_accounts($accounts) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    $k = count($accounts);
    for ($i = 0; $i < $k; $i++) {
        $sql = "DELETE FROM ACCOUNTS WHERE email='" . $accounts[$i] . "'";
        $query = mysqli_query($db_connection, $sql) or die(mysql_error());
    }
    //chiusura connessione al database
    mysqli_close($db_connection);
}

#funzione recupero informazioni account

function find_account_info($email) {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    #verifica se esistono preprints precedenti e li sposto...
    $sql = "SELECT * FROM ACCOUNTS WHERE email='" . $email . "'";
    $query = mysqli_query($db_connection, $sql) or die(mysql_error());
    $row = mysqli_fetch_array($query);
    #chiusura connessione al database
    mysqli_close($db_connection);
    return $row;
}

#invio mail agli admin quando avviene un nuovo submit(non terminata)

function sendmailadmin($uid, $idp) {
    mail("example@msn.com", "New preprint submitted by: " . $uid . " with id: " . $idp, "New preprint submitted by: " . $uid . " with id: " . $idp, "From: webmaster@{$_SERVER['SERVER_NAME']}\r\n" . "Reply-To: webmaster@{$_SERVER['SERVER_NAME']}\r\n" . "X-Mailer: PHP/" . phpversion());
}

#funzione ricerca full text

function searchfulltext() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    require_once './authorization/sec_sess.php';
    sec_session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 86400)) {
        if ($_SESSION['logged_type'] === "mod") {
            $cred = 1;
        } else {
            $cred = 0;
        }
    }
    #risultati visualizzati per pagina
    if (isset($_GET['rp']) && $_GET['rp'] != "") {
        $risperpag = $_GET['rp'];
    } else {
        $risperpag = 5;
    }
    if ($_GET['st'] == "1") {
        $query = "SELECT *, MATCH (id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) AGAINST('*" . addslashes($_GET['ft']) . "*' IN BOOLEAN MODE) AS attinenza FROM PREPRINTS WHERE MATCH (id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) AGAINST ('*" . addslashes($_GET['ft']) . "*' IN BOOLEAN MODE) ORDER BY attinenza DESC";
        $cat = "on currents";
    } else {
        $query = "SELECT *, MATCH (id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) AGAINST('*" . addslashes($_GET['ft']) . "*' IN BOOLEAN MODE) AS attinenza FROM PREPRINTS_ARCHIVIATI WHERE MATCH (id_pubblicazione, titolo, data_pubblicazione, autori, referenze, commenti, categoria, abstract) AGAINST ('*" . addslashes($_GET['ft']) . "*' IN BOOLEAN MODE) ORDER BY attinenza DESC";
        $cat = "on archived";
    }
    #recupero pagina
    if (isset($_GET['p']) && $_GET['p'] != "") {
        $p = $_GET['p'];
    } else {
        $p = 1;
    }
    #limite risultati
    $limit = $risperpag * $p - $risperpag;
    #query di ricerca
    $querytotale = mysqli_query($db_connection, $query) or die(mysql_error());
    $ristot = mysqli_num_rows($querytotale);
    if ($ristot != 0) {
        echo "Founded " . $ristot . " results:<br/><br/>";
    } else {
        echo "Founded " . $ristot . " results:<br/><br/>";
        break;
    }
    $npag = ceil($ristot / $risperpag);
    $query = $query . " LIMIT " . $limit . "," . $risperpag . "";
    $result = mysqli_query($db_connection, $query) or die(mysql_error());
    #impostazione della paginazione dei risultati
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> &#8658 </a>';
        }
    }
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="boxContainer" align="center" style="width:85%;">';
        echo "<a style='color:#1976D2; font-weight:bold;' href='" . $copia . $row['Filename'] . "' onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>" . ($row['titolo']) . "</a>";
        echo "<div>" . ($row['data_pubblicazione']) . "</div>";
        echo "<div>" . ($row['autori']) . "</div>";
        echo "</div>";
    }
    #impostazioni della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&ft=' . $_GET['ft'] . '&go=' . $_GET['go'] . '&st=' . $_GET['st'] . '"> &#8658 </a>';
        }
    }
    $x = $limit + 1;
    mysqli_close($db_connection);
}

# funzione lettura dei preprint

function searchpreprint() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    require_once './authorization/sec_sess.php';
    sec_session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 86400)) {
        if ($_SESSION['logged_type'] === "mod") {
            $cred = 1;
        } else {
            $cred = 0;
        }
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
    # controllo ricerca per anno
    if (isset($_GET['year1']) && is_numeric($_GET['year1'])) {
        if ($_GET['d'] != "1") {
            $query = " SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione <= '" . addslashes($_GET['year1'] + 1) . "' AND checked='1' UNION ";
            $cat3 = "until year " . $_GET['year1'] . ", ";
        } else {
            $query = " SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione <= '" . addslashes($_GET['year1'] + 1) . "' AND checked='1' UNION 
    		SELECT * FROM PREPRINTS_ARCHIVIATI WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione <= '" . addslashes($_GET['year1'] + 1) . "' AND checked='1' UNION ";
            $cat3 = "until year " . $_GET['year1'] . " with archived, ";
        }
    } else if (isset($_GET['year2']) && is_numeric($_GET['year2']) && isset($_GET['year3']) && is_numeric($_GET['year3'])) {
        if ($_GET['d'] != "1") {
            $query = " SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione BETWEEN '" . addslashes($_GET['year2']) . "' AND '" . addslashes($_GET['year3'] + 1) . "' AND checked='1' UNION ";
            $cat3 = "on range from " . $_GET['year2'] . " to " . $_GET['year3'] . ", ";
        } else {
            $query = " SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione BETWEEN '" . addslashes($_GET['year2']) . "' AND  '" . addslashes($_GET['year3'] + 1) . "' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND data_pubblicazione BETWEEN '" . addslashes($_GET['year2']) . "' AND '" . addslashes($_GET['year3'] + 1) . "' AND checked='1' UNION ";
            $cat3 = "on range from " . $_GET['year2'] . " to " . $_GET['year3'] . " with archived, ";
        }
    } else {
        $cat = 0;
        #verifica parametri ricerca
        if ($_GET['e'] != 1 && $_GET['i'] != 1 && $_GET['t'] != 1 && $_GET['a'] != 1 && $_GET['c'] != 1 && $_GET['j'] != 1 && $_GET['h'] != 1 && $_GET['y'] != 1 && $_GET['all'] != 1 && $_GET['d'] == 1) {
            $query = "
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
            $cat = "ALL";
        }
        if ($_GET['all'] != "1") {
            if ($_GET['d'] != "1") {
                if ($_GET['h'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "authors, ";
                }
                if ($_GET['t'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "title, ";
                }
                if ($_GET['a'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "abstract, ";
                }
                if ($_GET['y'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "category, ";
                }
                if ($_GET['c'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "comments, ";
                }
                if ($_GET['j'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "journal-ref, ";
                }
                if ($_GET['e'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "year, ";
                }
                if ($_GET['i'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "ID, ";
                }
            } else {
                if ($_GET['h'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION 
		    SELECT * FROM PREPRINTS_ARCHIVIATI WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "authors, ";
                }
                if ($_GET['t'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION 
		    SELECT * FROM PREPRINTS_ARCHIVIATI WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "title, ";
                }
                if ($_GET['a'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "abstract, ";
                }
                if ($_GET['y'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "category, ";
                }
                if ($_GET['c'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "comments, ";
                }
                if ($_GET['j'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "journal-ref, ";
                }
                if ($_GET['e'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "year, ";
                }
                if ($_GET['i'] == "1") {
                    $query = $query . "SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION SELECT * FROM PREPRINTS_ARCHIVIATI WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                    $cat++;
                    $cat3 = $cat3 . "ID, ";
                }
                $cat3 = $cat3 . "included archived, ";
            }
        } else {
            if ($_GET['d'] != "1") {
                $query = " 
	    	SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                $cat = "all records";
            } else {
                $query = " 
	    	SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION 
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
	    	SELECT * FROM PREPRINTS_ARCHIVIATI WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION ";
                $cat = "all records";
                $cat3 = $cat3 . ", included archived, ";
            }
        }
    }
    $query = substr($query, 0, -7);
    $cat3 = substr($cat3, 0, -2);
    #risultati visualizzati per pagina
    if (isset($_GET['rp']) && $_GET['rp'] != "") {
        $risperpag = $_GET['rp'];
    } else {
        $risperpag = 5;
    }
    #recupero pagina
    if (isset($_GET['p']) && $_GET['p'] != "") {
        $p = $_GET['p'];
    } else {
        $p = 1;
    }
    #limite risultati
    $limit = $risperpag * $p - $risperpag;
    #query di ricerca
    $querytotale = mysqli_query($db_connection, $query) or die(mysql_error());
    $ristot = mysqli_num_rows($querytotale);
    if ($cat != "all records") {
        echo "Found " . $ristot . " results:";
    } else {
        echo "Found " . $ristot . " results:";
    }
    $npag = ceil($ristot / $risperpag);
    $query = $query . " ORDER BY " . $order . " LIMIT " . $limit . "," . $risperpag . "";
    $result = mysqli_query($db_connection, $query) or die(mysql_error());
    #impostazione della paginazione dei risultati
    if ($ristot != 0) {
        echo "<br/><br/>";
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&w=&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> &#8658 </a>';
        }
        echo "<br/>";
    }
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="boxContainer" align="center">';
        echo "<a style='color:#1976D2; font-weight:bold;' href='" . $copia . $row['Filename'] . "' onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>" . ($row['titolo']) . "</a>";
        echo "<div>" . ($row['data_pubblicazione']) . "</div>";
        echo "<div>" . ($row['autori']) . "</div>";
        echo "</div>";
    }
    #impostazioni della navigazione per pagine

    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&t=' . $_GET['t'] . '&a=' . $_GET['a'] . '&c=' . $_GET['c'] . '&j=' . $_GET['j'] . '&d=' . $_GET['d'] . '&all=' . $_GET['all'] . '&h=' . $_GET['h'] . '&y=' . $_GET['y'] . '&e=' . $_GET['e'] . '&i=' . $_GET['i'] . '&rp=' . $_GET['rp'] . '&year1=' . $_GET['year1'] . '&year2=' . $_GET['year2'] . '&year3=' . $_GET['year3'] . '&s=' . $_GET['s'] . '"> &#8658 </a>';
        }
    }
    $x = $limit + 1;
    mysqli_close($db_connection);
}

# funzione filtro e lettura dei preprint

function filtropreprint() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    require_once './authorization/sec_sess.php';
    sec_session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 86400)) {
        if ($_SESSION['logged_type'] === "mod") {
            $cred = 1;
        } else {
            $cred = 0;
        }
    }
    #recupero pagina
    if (isset($_GET['p']) && $_GET['p'] != "") {
        $p = $_GET['p'];
    } else {
        $p = 1;
    }
    $order = "data_pubblicazione DESC";
    $orstr = "decreasing date";
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
    #risultati visualizzati per pagina
    if (isset($_GET['rp']) && $_GET['rp'] != "") {
        $risperpag = $_GET['rp'];
    } else {
        $risperpag = 5;
    }
    #limite risultati
    $limit = $risperpag * $p - $risperpag;
    #query di ricerca
    if (isset($argom)) {
        $sql = "SELECT * FROM PREPRINTS WHERE " . $argom . " LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1'";
        $querytotale = mysqli_query($db_connection, $sql) or die(mysql_error());
        $ristot = mysqli_num_rows($querytotale);
        if ($ristot != 0) {
            echo "Found " . $ristot . " results:<br/><br/>";
        } else {
            echo "Found " . $ristot . " results:<br/><br/>";
            break;
        }
        $npag = ceil($ristot / $risperpag);
        $sql = "SELECT * FROM PREPRINTS WHERE " . $argom . " LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' ORDER BY " . $order . " LIMIT " . $limit . "," . $risperpag . "";
        $result = mysqli_query($db_connection, $sql) or die(mysql_error());
    } else {
        #senza filtro
        $query = " 
    	SELECT * FROM PREPRINTS WHERE id_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE titolo LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE data_pubblicazione LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE autori LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE referenze LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE commenti LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE categoria LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1' UNION
    	SELECT * FROM PREPRINTS WHERE abstract LIKE '%" . addslashes($_GET['r']) . "%' AND checked='1'";
        $querytotale = mysqli_query($db_connection, $query) or die(mysql_error());
        $ristot = mysqli_num_rows($querytotale);
        $npag = ceil($ristot / $risperpag);
        if (isset($_GET['o']) && $_GET['o'] != "") {
            $query = $query . " ORDER BY " . $order . " LIMIT " . $limit . "," . $risperpag . "";
        } else {
            $query = $query . " ORDER BY data_pubblicazione DESC LIMIT " . $limit . "," . $risperpag . "";
        }
        if (!isset($_GET['r']) or $_GET['r'] == "") {
            echo $ristot . " ELEMENTS ON " . $npag . " PAGES";
        } else {
            echo "Found " . $ristot . " results:<br/><br/>";
        }
        $npag = ceil($ristot / $risperpag);
        $result = mysqli_query($db_connection, $query) or die(mysql_error());
    }
    #impostazione della paginazione dei risultati
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> &#8658 </a>';
        }
    }
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="boxContainer" align="center">';
        echo "<a style='color:#1976D2; font-weight:bold;' href='" . $copia . $row['Filename'] . "' onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>" . ($row['titolo']) . "</a>";
        echo "<div>" . ($row['data_pubblicazione']) . "</div>";
        echo "<div>" . ($row['autori']) . "</div>";
        echo "</div>";
    }
    #impostazioni della navigazione per pagine
    if ($ristot != 0) {
        if ($p != 1) {
            $t1 = $p - 1;
            $t2 = $p - 2;
            $t3 = $p - 3;
            echo '<a style="color:#1976D2; text-decoration: none;" title="First page" href="view_preprints.php?p=1&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> &#8656 </a>';
            if ($p >= 3 && $t3 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t3 . " " . ' </a>';
            }
            if ($p >= 2 && $t2 > 0) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t2 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p - 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t1 . " " . ' </a>';
        }
        echo " " . $p . " ";
        if ($p != $npag) {
            $t4 = $p + 1;
            $t5 = $p + 2;
            $t6 = $p + 3;
            echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 1) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t4 . " " . ' </a>';
            if ($p < $npag && $t5 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 2) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t5 . " " . ' </a>';
            }
            if ($p < $npag && $t6 <= $npag) {
                echo '<a style="color:#1976D2; text-decoration: none;" href="view_preprints.php?p=' . ($p + 3) . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> ' . " " . $t6 . " " . ' </a>';
            }
            echo '<a style="color:#1976D2; text-decoration: none;" title="Last page" href="view_preprints.php?p=' . $npag . '&r=' . $_GET['r'] . '&f=' . $_GET['f'] . '&o=' . $_GET['o'] . '&rp=' . $_GET['rp'] . '&s=' . $_GET['s'] . '"> &#8658 </a>';
        }
    }
    $x = $limit + 1;
    mysqli_close($db_connection);
}

# funzione lettura dei preprint recenti

function recentspreprints() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    require_once './authorization/sec_sess.php';
    sec_session_start();
    $query = "SELECT * FROM PREPRINTS ORDER BY data_pubblicazione DESC LIMIT 10";
    $result = mysqli_query($db_connection, $query) or die(mysql_error());
    $i = $limit;
    #recupero e visualizzazione dei campi della ricerca effettuata
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="boxContainer" align="center">';
        echo "<a style='color:#1976D2; font-weight:bold;' href='" . $copia . $row['Filename'] . "' onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>" . ($row['titolo']) . "</a>";
        echo "<div>" . ($row['data_pubblicazione']) . "</div>";
        echo "<div>" . ($row['autori']) . "</div>";
        echo "</div>";
    }
    $x = $limit + 1;
    mysqli_close($db_connection);
}

#funzione lettura dei preprint archiviati

function leggipreprintarchiviati() {
    include './conf.php';
//import connessione database
    include './mysql/db_conn.php';
    $risperpag = 5;
    #recupero pagina
    if (isset($_GET['p']) && $_GET['p'] != "") {
        $p = $_GET['p'];
    } else {
        $p = 1;
    }
    $limit = $risperpag * $p - $risperpag;
    $sql = "SELECT * FROM PREPRINTS_ARCHIVIATI";
    $querytotale = mysqli_query($db_connection, $sql) or die(mysql_error());
    $ristot = mysqli_num_rows($querytotale);
    echo "ELEMENTS ARCHIVED: " . $ristot . "<br/><br/>";
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
    if ($_GET['c'] != "Remove All") {
        $sql = "SELECT * FROM PREPRINTS_ARCHIVIATI WHERE checked='1' ORDER BY data_pubblicazione DESC LIMIT " . $limit . "," . $risperpag . "";
        $result = mysqli_query($db_connection, $sql) or die(mysql_error());
        $i = $limit;
        #recupero info e visualizzazione
        while ($row = mysqli_fetch_array($result)) {
            $i++;
            echo "<h1>" . $i . ".<br/></h1><div align='left' style='width:98%;'>";
            echo "<p><h1>Id of publication:</h1></p><div style='float:right;'><a style='color:#007897;' href='" . $basedir4 . $row['Filename'] . "' onclick='window.open(this.href);return false' title='" . $row['id_pubblicazione'] . "'>view</a></div><div style='margin-left:1%;'>" . $row['id_pubblicazione'] . "</div>";
            echo "<p><h1>Title:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['titolo']) . "</div>";
            echo "<p><h1>Date of pubblication:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['data_pubblicazione']) . "</div>";
            echo "<p><h1>Authors:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['autori']) . "</div>";
            echo "<p><h1>Journal reference:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['referenze']) . "</div>";
            echo "<p><h1>Comments:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['commenti']) . "</div>";
            echo "<p><h1>Category:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['categoria']) . "</div>";
            echo "<p><h1>Abstract:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . ($row['abstract']) . "</div>";
            echo "<p><h1>Views:</h1></p><div style='margin-left:1%; margin-right:1%;'>" . number_format(($row['counter']), 0, ',', '.') . "</div>";
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
        }
    } else {
        #controllo di preprint da rimuovere
        if ($ristot != 0) {
            cancellapreprint();
            echo '<script type="text/javascript">alert("Papers deleted from database!");</script>';
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=./archived_preprints.php?p=1">';
        } else {
            $limit = 0;
            echo '<script type="text/javascript">alert("No archived papers!");</script>';
        }
    }
    $x = $limit + 1;
    mysqli_close($db_connection);
}
?>