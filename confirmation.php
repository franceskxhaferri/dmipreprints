<!DOCTYPE html>
<html>
<?php
echo "
<head>
<title>DMI Preprints</title>
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js\"></script>
<script src=\"js/config.js\"></script>
<script src=\"js/skel.min.js\"></script>
<script src=\"js/skel-panels.min.js\"></script>
<noscript>
<link rel=\"stylesheet\" href=\"css/skel-noscript.css\" />
<link rel=\"stylesheet\" href=\"css/style.css\" />
<link rel=\"stylesheet\" href=\"css/style-desktop.css\" />
</noscript>
<script src=\"js/targetweb-modal-overlay.js\"></script>
<link href='css/targetweb-modal-overlay.css' rel='stylesheet' type='text/css'>
<!--[if lte IE 9]><link rel=\"stylesheet\" href=\"css/ie9.css\" /><![endif]-->
<!--[if lte IE 8]><script src=\"js/html5shiv.js\"></script><![endif]-->
<script>
</script>
<script type=\"text/javascript\" src=\"./js/allscript.js\">
</script>
</head>";
//
require_once './conf.php';
require_once './mysql/db_conn.php';
require_once './authorization/auth.php';
?>
<body>
  <div id="header-wrapper">
    <div class="container">
      <div class="row">
        <div class="12u">
          <header id="header">
            <h1><a href="./index.php" id="logo">DMI Preprints</a></h1>
            <nav id="nav">
              <a href="./index.php">Publications</a>
              <a href="./reserved.php" class="current-page-item">Reserved Area</a>
            </nav>
          </header>
        </div>
      </div>
    </div>
  </div>
  <br/><br/>
  <center>
    <div id="firstContainer"><br/><br/><br/><br/>
      <?php
      //
      if ($_GET['token'] != "" && confirm_account($_GET['token'])) {
        echo "The account has been confirmed, you can now sign in using your email address.";
      }
      require_once './graphics/loader.php';
      require_once './graphics/footer.php';
      ?>
    </center>
  </center>
</body>
</html>
