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
        <link rel="stylesheet" type="text/css" href="css/tabelle.css">
        <link rel="stylesheet" type="text/css" href="css/controlli.css">
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
    </head>
    <body><?php
        require_once './graphics/header.php';
            $nav = "<a style='color:#ffffff; text-align: center;' href='./archived_preprints.php?c=remove' id='bottone_keyword' class='button' onclick='loading(load);'>Remove All</a>";
            $nav2 = "<header id='header'>
                                    <h1><a href='#' id='logo'>DMI Papers</a></h1>
                                    <nav id='nav'>
                                        <a href='./index.php' onclick='loading(load);'>Publications</a>
                                        <a href='./reserved.php' class='current-page-item' onclick='loading(load);'>Reserved Area</a>
                                    </nav>
                                </header>";
            $rit = "modp.php";
        ?>
        <div onclick="myFunction2()">
            <div id="header-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="12u">
                            <?php echo $nav2; ?>
                        </div>
                    </div>
                </div>
            </div><br/>
            <div id="firstContainer">
                <center>
                    <h2>ARCHIVED PAPERS</h2>
                    <br/>
                    <a style='color:#ffffff; text-align: center;' href='./modp.php' id='bottone_keyword' class='button' onclick='loading(load);'>Back</a>
                  </center><br/><br/>
                    <div style='clear:both;'></div>
                    <center>
                        <div onclick="myFunction()">
                            <?php
                            if (sessioneavviata() == True) {
                                echo "<br/><br/><center>SORRY ONE DOWNLOAD/UPDATE SESSION IS RUNNING AT THIS TIME! THE SECTION CAN'T BE USED IN THIS MOMENT!</center><br/>";
                                break;
                            } else {
                                if (isset($_GET['c'])) {
                                    #funzione gestione preprint archiviati
                                    leggipreprintarchiviati();
                                } else {
                                    #funzione gestione preprint archiviati
                                    leggipreprintarchiviati();
                                }
                            }
                            echo "<br/><br/><br/>" . $nav;
                            ?>
                        </div>
                    </center>
            </div>
            <br/>
            <br/> 
        </div>
        <?php require_once './graphics/loader.php'; ?>
    </body>
</html>
