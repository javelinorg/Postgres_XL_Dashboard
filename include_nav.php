    <!-- START Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img class="img-thumbnail" style="height:46px;" alt="<?php echo SITE_TITLE; ?>" src="images/logo.png"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
          <?php
            if($_SERVER['SCRIPT_NAME'] == '/index.php'){
              echo '<li class="active">';
            }else{
              echo '<li>';
            }
            echo '<a href="index.php"><i class="fa fa-home"></i> Heads Up</a></li>' . "\n";

            if($_SERVER['SCRIPT_NAME'] == '/sessions.php'){
              echo '<li class="active">';
            }else{
              echo '<li>';
            }
            echo '<a href="sessions.php"><i class="fa fa-server fa-flip-horizontal"></i> Active Sessions</a></li>' . "\n";

            if($_SERVER['SCRIPT_NAME'] == '/environment.php'){
              echo '<li class="active">';
            }else{
              echo '<li>';
            }
            echo '<a href="environment.php"><i class="fa fa-cubes"></i> Environment</a></li>' . "\n";

            if($_SERVER['SCRIPT_NAME'] == '/about.php'){
              echo '<li class="active">';
            }else{
              echo '<li>';
            }
            echo '<a href="about.php"><i class="fa fa-info-circle"></i> About This Tool</a></li>' . "\n";

          ?>

<!--
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
-->
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <!-- / END NavBar -->