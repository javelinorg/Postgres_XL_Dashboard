    <!-- START Footer -->
      <footer class="footer">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 text-center">
              <p><small><i class="fa fa-code"></i> Initial Development by R!chard Silva with the support of JLabs, a division within Javelin.<br>
              This site possible by combining the hard work of the Bootstrap, D3, jQuery, Postgres, and Postgres XL community.</small></p>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 text-center">
              <p>
                <button type="button" class="btn btn-md btn-link" onclick="rps('rock');" data-toggle="tooltip" data-placement="top" title="Rock" style="padding: 3px 3px;"><i class="fa fa-hand-rock-o"></i></button>
                <button type="button" class="btn btn-md btn-link" onclick="rps('paper');" data-toggle="tooltip" data-placement="top" title="Paper" style="padding: 3px 3px;"><i class="fa fa-hand-paper-o"></i></button>
                <button type="button" class="btn btn-md btn-link" onclick="rps('scissors');" data-toggle="tooltip" data-placement="top" title="Scissors" style="padding: 3px 3px;"><i class="fa fa-hand-scissors-o"></i></button>
              </p>
            </div>
            <div class="col-sm-6 text-center" id="rpsResults">
            </div>
          </div>
        </div>
      </footer>
      <?php
        if(DEBUG){
      ?>
        <div class="row">
          <div class="col-sm-12 panel" style="overflow-x: hidden;">
            <p><?php (isset($_GET) ? printARRAY('GET Array',$_GET) : ''); ?></p>
            <p><?php (isset($_POST) ? printARRAY('POST Array', $_POST) : ''); ?></p>
            <p><?php (session_id() ? printARRAY('SESSION Array', $_SESSION) : ''); ?></p>
            <p><?php (isset($GLOBAL) ? printARRAY('GLOBAL Array', $GLOBAL) : ''); ?></p>
            <p><?php (isset($_SERVER) ? printARRAY('SERVER Array', $_SERVER) : ''); ?></p>
          </div>
        </div>
      <?php
        }
      ?>
    <!-- / END Footer -->