<!DOCTYPE html>
<html>
    <head>
        <title>DMI Papers</title>
        <!--<script src="js/jquery.min.js"></script>-->
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script src="js/config.js"></script>
        <script src="js/skel.min.js"></script>
        <script src="js/skel-panels.min.js"></script>
        <noscript>
        <link rel="stylesheet" href="css/skel-noscript.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-desktop.css" />
        </noscript>
        <link rel="stylesheet" href="css/main.css" />
        <script src="js/targetweb-modal-overlay.js"></script>
        <link href='css/targetweb-modal-overlay.css' rel='stylesheet' type='text/css'>
        <!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
        <!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
        <script>
            webshims.setOptions('waitReady', false);
            webshims.setOptions('forms-ext', {types: 'date'});
            webshims.polyfill('forms forms-ext');
        </script>
        <script type="text/x-mathjax-config">
            MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});
        </script>
        <script type="text/javascript"
                src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
        </script>
        <script type="text/javascript" src="./js/allscript.js">
        </script>
        <?php
        #controllo cookie pageview
        if ($_COOKIE['pageview'] == "") {
            echo "<script>javascript:checkCookie4();</script>";
        } else {
            if ($_COOKIE['pageview'] == "0") {
                $string = "Enable";
            } else {
                $string = "Disable";
            }
        }
        #disabilita searchbar su altre pagine
        if ((isset($_GET['clos'])) && $_GET['clos'] == "1" && $_COOKIE['searchbarall'] == "1") {
            echo "<script>javascript:checkCookie7();</script>";
            echo "<meta http-equiv='refresh' content='0'; URL=./view_preprints.php>";
        }
        #controllo cookie searchbar all
        if ($_COOKIE['searchbarall'] == "0" or ! isset($_COOKIE['searchbarall'])) {
            $string3 = "Enable";
        } else {
            $string3 = "Disable";
        }
        ?>
    </head>
    <body><?php
        require_once './graphics/header_main_page.php';
        sec_session_start();
        if (isset($_SESSION['logged_type']) && $_SESSION['logged_type'] === "mod") {
            $t = "Go to arXiv panel";
            $rit = "arXiv_panel.php";
            $nav = "<header id='header'>
                                    <h1><a href='#' id='logo'>DMI Papers</a></h1>
                                    <nav id='nav'>
                                        <a href='./index.php' class='current-page-item' onclick='loading(load);'>Publications</a>
                                        <a href='./reserved.php' onclick='loading(load);'>Reserved Area</a>
                                    </nav>
                                </header>";
        } else if (isset($_SESSION['logged_type']) && $_SESSION['logged_type'] === "user") {
            $t = "Go to reserved area";
            $rit = "reserved.php";
            $nav = "<header id='header'>
                                    <h1><a href='#' id='logo'>DMI Papers</a></h1>
                                    <nav id='nav'>
                                        <a href='./index.php' class='current-page-item' onclick='loading(load);'>Publications</a>
                                        <a href='./reserved.php' onclick='loading(load);'>Reserved Area</a>
                                    </nav>
                                </header>";
        } else {
            $t = "Go to homepage";
            $rit = "main.php";
            $nav = "<header id='header'>
                                    <h1><a href='#' id='logo'>DMI Papers</a></h1>
                                    <nav id='nav'>
                                        <a href='./index.php' class='current-page-item' onclick='loading(load);'>Publications</a>
                                        <a href='./reserved.php' onclick='loading(load);'>Reserved Area</a>
                                    </nav>
                                </header>";
        }
        ?>
        <div id="header-wrapper">
            <div class="container">
                <div class="row">
                    <div class="12u">
                        <?php echo $nav; ?>
                    </div>
                </div>
            </div>
        </div><br/><br/>
    <center>
        <?php
        echo "<div id='gotop' hidden><a id='scrollToTop' title='Go top'><img style='width:25px; height:25px;' src='./images/top.gif'></a></div>";
        ?><br/>
        <div class="boxContainerSearch">
            <?php
            //fulltext search(risultati per pagina)
            $array_opt = ['5', '10', '15', '20', '25', '50'];
            foreach ($array_opt as $key) {//paginazione
                //(($value[2] == 'desc')) ? $freccia = '&#8595;' : $freccia = '&#8593;';
                if ($_GET['rp'] == $key) {
                    $checked = "selected='selected'";
                } else {
                    $checked = "";
                }
                $pageopt .= "<option value=" . $key . " " . $checked . ">" . $key . "</option>";
            }
            //dove eseguire la ricerca
            if ($_GET['st'] == "0") {
                $searchopt = "<label><input type='radio' name='st' value='1'>Currents</label><br/>"
                        . "<label><input type='radio' name='st' value='0' checked>Archived</label><br/>";
            } else {
                $searchopt = "<label><input type='radio' name='st' value='1' checked>Currents</label><br/>"
                        . "<label><input type='radio' name='st' value='0'>Archived</label><br/>";
            }
            //advanced search(checkboxs)
            $array_search = array('d' => 'Archived', 'all' => 'Full Record', 'h' => 'Author(s)', 't' => 'Title', 'a' => 'Abstract', 'e' => 'Date',
                'y' => 'Category', 'c' => 'Comments', 'j' => 'Journal Ref', 'i' => 'Identifier(ID)');
            foreach ($array_search as $key => $value) {//search on
                if ($_GET[$key] == "1") {
                    $checked = "checked";
                } else {
                    $checked = "";
                }
                if (($_GET['all'] == "1") && ($key == "h")) {
                    $disable = "disabled";
                }
                $searchcheckbox .= "<label><input type='checkbox' onChange='DisAllFields(this.id)' id='" . $key . "' name='" . $key . "' value='1' class='checkbox' " . $checked . " " . $disable . ">" . $value . "</label><br/>";
            }
            //ordine dei risultati
            $array_order = array('dated' => 'Publication Date &#8595;', 'datec' => 'Publication Date &#8593;',
                'idd' => 'Identifier(ID) &#8595;', 'idc' => 'Identifier(ID) &#8593;',
                'named' => 'Author Name &#8595;', 'namec' => 'Author Name &#8593;');
            foreach ($array_order as $key => $value) {
                if ($_GET['o'] == $key) {
                    $checked = "checked";
                } else {
                    $checked = "";
                }
                $orderradiob .= "<label><input type='radio' name='o' value='" . $key . "' " . $checked . ">" . $value . "</label><br/>";
            }
            if (isset($_GET['go']) && $_GET['go'] != "" or $_GET['fulltext'] == "yes") {//fulltext search
                $html = "<div class='adv' align='center'>
        		<h1>Fulltext Search:</h1><br/>
        		<form name='f2' action='view_preprints.php' method='GET' onsubmit='loading(load);'>
                            <input type='search' value='" . $_GET['ft'] . "' autocomplete = 'on' class='textbox' name='ft' placeholder='Insert phrase, name, keyword, etc.'>
                            <input type='submit' name='go' value='Send' class='button'/>
                            <div align='left' class='restrictionbox' style='width:100%;'><br/>
		                    Results for page: 
		                    <select name='rp'>
		                        " . $pageopt . "
		                    </select>
                            </div>
                            <div align='left' class='searchonbox' style='width:100%;'><br/>
		                    Search on: <br/>
		                    " . $searchopt . "
                            <br/>
                            </div>
                        </form></div>
                        <h1><a href='./view_preprints.php' style='color:#1976D2;'>Need Advanced Search?</a></h1>";
            } else {//advanced search
                $html = "<div class='adv' align='center'>
                <h1>Advanced Search:</h1><br/>
                <form name='f1' action='view_preprints.php' method='GET' onsubmit='loading(load);'>
                    <input type='search' value='" . $_GET['r'] . "' autocomplete = 'on' name='r' placeholder='Author name, id of publication, year of publication, etc.' required style='width:140px;' class='textbox'>
                    <input type='submit' name='s' value='Send' class='button'><br/><br/>
                    <div align='left' class='restrictionbox'>
                            Results for page:
                            <select name='rp'>
                                " . $pageopt . "
                            </select><br/><br/>
                            Years restriction:<br/>
                            From <input type='text' value='" . $_GET['year2'] . "' name='year2' style='width:35px' placeholder='First' class='textbox'>
                                to <input type='text' value='" . $_GET['year3'] . "' name='year3' style='width:35px' placeholder='Last' class='textbox'>
                        </div>
                    <div align='left' class='searchonbox'><br/>
                        Search on:<br/>
                        " . $searchcheckbox . "
                    </div>
                    <div align='left' class='orderbox'><br/>
                        Order results:<br/>
                        " . $orderradiob . "
                    </div>
                    <div style='clear:both;'>
                    </div>
                </form><br/>
                <h1><a href='./view_preprints.php?fulltext=yes' style='color:#1976D2;'>Need Fulltext Search?</a></h1>
            </div>";
            }
            echo $html;
            ?>
        </div><br/><br/>
        <div class="resultsContainer" onclick="myFunction()">
            <?php
#ricerca full text
            if (isset($_GET['go']) && $_GET['go'] != "") {
                searchfulltext();
            }
#ricerca normale
            if (isset($_GET['s']) && $_GET['s'] != "") {
                if ($_GET['f'] == "all" or $_GET['f'] == "author" or $_GET['f'] == "category"
                        or $_GET['f'] == "year" or $_GET['f'] == "id") {
                    filtropreprint();
                } else if ($_GET['all'] == "1" or $_GET['h'] == "1" or $_GET['t'] == "1" or $_GET['a'] == "1" or $_GET['e'] == "1" or $_GET['y'] == "1" or $_GET['c'] == "1" or $_GET['j'] == "1" or $_GET['i'] == "1" or $_GET['d'] == "1") {
                    searchpreprint();
                } else {
                    echo "SELECT THE FIELD WHERE RUN THE SEARCH!";
                }
            }
            require_once './graphics/loader.php';
            ?>
        </div><br/>
    </center>
</body>
</html>
