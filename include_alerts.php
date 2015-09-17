      <?php
        if(isset($strInfo)){
          echo '<div class="alert alert-info alert-dismissable">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo $strInfo;
          echo '</div>';
        }
        if(isset($strSuccess)){
          echo '<div class="alert alert-success alert-dismissable">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo $strSuccess;
          echo '</div>';
        }
        if(isset($strWarning)){
          echo '<div class="alert alert-warning alert-dismissable">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo $strWarning;
          echo '</div>';
        }
        if(isset($strError)){
          echo '<div class="alert alert-danger alert-dismissable">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo $strError;
          echo '</div>';
        }
      ?>
