<?php include('include_config.php'); ?>
<?php
  if(isset($_GET['n'])){
    $strNode = $_GET['n'];
    $strNodeTable = getCoordinatorNodeTable($strNode);
  }else{
    $strInfo = 'Please select a Coordinator from the dropdown.';
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <?php include('include_head.php'); ?>

    <title><?php echo SITE_TITLE; ?></title>
    <?php include('include_css.php'); ?>
<style>

/*
.chart rect {
  fill: steelblue;
}
*/
.chart .legend {
  fill: black;
/*  text-anchor: start; */
  font-size: 10px;
}

.chart text {
  fill: black;
  font-size: 10px;
/*  text-anchor: end; */
}

.chart .label {
  fill: black;
  font-size: 11px;
  text-anchor: start;
}

.bar:hover {
  fill: steelblue;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

path.g-1 { fill: #1f77b4; }
path.g-2 { fill: #aec7e8; }
path.g-3 { fill: #ff7f0e; }
path.g-4 { fill: #ffbb78; }
path.g-5 { fill: #2ca02c; }
path.g-6 { fill: #98df8a; }
path.g-7 { fill: #d62728; }
path.g-8 { fill: #ff9896; }
path.g-9 { fill: #9467bd; }
path.g-10 { fill: #c5b0d5; }
path.g-11 { fill: #8c564b; }
path.g-12 { fill: #c49c94; }
path.g-13 { fill: #e377c2; }
path.g-14 { fill: #f7b6d2; }
path.g-15 { fill: #7f7f7f; }
path.g-16 { fill: #c7c7c7; }
path.g-17 { fill: #bcbd22; }
path.g-18 { fill: #dbdb8d; }
path.g-19 { fill: #17becf; }
path.g-20 { fill: #9edae5; }

/*svg { width: 100%; height: 100%; }*/
svg > g.label { text-anchor: middle; }
svg > g.labels g.label { -moz-pointer-events: none; -webkit-pointer-events: none; -o-pointer-events: none; pointer-events: none; }
svg > g.labels g.label rect { stroke: none; fill: #fff; fill-opacity: .5; shape-rendering: crispEdges; }
svg > g.labels g.label text { font-size: 12px; text-anchor: left; }
svg > g.labels g.label.active rect { fill-opacity: 1; }

</style>
  </head>
  <body>
    <?php include('include_nav.php'); ?>

    <!-- START page content -->
    <div class="container">
      <?php include('include_alerts.php'); ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="btn-group">
            <a class="btn btn-primary" href="#" data-toggle="dropdown"><i class="fa fa-search"></i> Choose A Coordinator</a>
            <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
            <ul class="dropdown-menu">
            <?php
              foreach($arrDBConns as $key => $value){
                if(strtolower($value['DBXLNode']) == 'coordinator'){
                  if(testPort($value['DBIP'], $value['DBPort'])){
                    echo '<li><a href="environment.php?n=' . $key . '"><i class="fa fa-cubes"></i> ' . $key . '</a></li>' . "\n";
                  }
                }
              }
            ?>
            </ul>
          </div>
          <?php
            if(isset($strNode)){
              echo '  <div class="btn-group">' . "\n";
              echo '    <a class="btn btn-primary" href="environment.php?n=' . $_GET['n'] . '"><i class="fa fa-refresh"></i> Refresh in <span id="timer">60 seconds</span></a>' . "\n";
              echo '    <a class="btn btn-primary" id="timerpause" value="play"><i class="fa fa-pause"></i> Pause</a>' . "\n";
              echo '  </div>' . "\n";
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <h1><?php echo $strNode; ?><small><?php echo $arrDBConns[$strNode]['DBIP'] ?></small></h1>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4 text-center">
          <div class="row">
            <div class="col-sm-12">
              <h4>Health</h4>
              <?php
                // check the number of connections
                $intConnections = getConnections($strNode);
                $intMaxConnections = getDBSystemParam($strNode,'max_connections');
                $strPanelColor = 'success';
                if(($intMaxConnections >= $intConnections) && ($intMaxConnections > 0)){
                  $perConnections = round((($intConnections / $intMaxConnections)*100),0);
                }else{
                  $perConnections = '???';
                  $strPanelColor = 'warning';
                }
                // display number of deadlocks
                $intDeadlocks = getDeadlocks($strNode);
                if($intDeadlocks != '0'){
                  $strPanelColor = 'warning';
                }

                // try to ping the server
                if(testPing($value['DBIP'])){
                  $strPing = 'check';
                }else{
                  $strPing = 'exclamation';
                  $strPanelColor = 'danger';
                }
                // check the listed port to see if it responds
                if(testPort($arrDBConns[$strNode]['DBIP'] , $arrDBConns[$strNode]['DBPort'])){
                  $strPort = 'check';
                }else{
                  $strPort = 'exclamation';
                  $strPanelColor = 'danger';
                }

                echo '<div class="col-xs-12 text-center">' . "\n";
                echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                echo '    </div>' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Port:<small>(' . $arrDBConns[$strNode]['DBPort'] . ')</small></div>' . "\n";
                echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                echo '    </div>' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Conns:</div>' . "\n";
                echo '      <div class="col-xs-5 text-left">' . $perConnections . '%</div>' . "\n";
                echo '    </div>' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Deadlocks:</div>' . "\n";
                echo '      <div class="col-xs-5 text-left">' . $intDeadlocks . '</div>' . "\n";
                echo '    </div>' . "\n";
                echo '  </div>' . "\n";
                echo '</div>' . "\n";
              ?>
            </div>
            <div class="col-sm-12">
              <h4>GTM Status</h4>
              <?php
                $strGTMHost = getDBSystemParam($strNode,'gtm_host');
                $strGTMPort = getDBSystemParam($strNode, 'gtm_port');
                $strPanelColor = 'success';
                // try to ping the server
                if(testPing($strGTMHost)){
                  $strPing = 'check';
                }else{
                  $strPing = 'exclamation';
                  $strPanelColor = 'danger';
                }
                // check the listed port to see if it responds
                if(testPort($strGTMHost, $strGTMPort)){
                  $strPort = 'check';
                }else{
                  $strPort = 'exclamation';
                  $strPanelColor = 'danger';
                }

                echo '<div class="col-xs-12 text-center">' . "\n";
                echo '  <div class="panel panel-default panel-' . $strPanelColor . '" style="">' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-12 text-center"><strong>' . $strGTMHost . '</strong></div>' . "\n";
                echo '    </div>' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Ping:</div>' . "\n";
                echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPing . '"></i></div>' . "\n";
                echo '    </div>' . "\n";
                echo '    <div class="row">' . "\n";
                echo '      <div class="col-xs-7 text-right">Port:<small>(' . $strGTMPort . ')</small></div>' . "\n";
                echo '      <div class="col-xs-5 text-left"><i class="fa fa-' . $strPort . '"></i></div>' . "\n";
                echo '    </div>' . "\n";
                echo '  </div>' . "\n";
                echo '</div>' . "\n";
              ?>
            </div>
          </div>
        </div>
        <div class="col-sm-4 text-center">
          <h4>Database Sizes</h4>
          <div class="chart">
            <svg class="DatabaseSpaceChart" style="width: 350; height: 350;"></svg>
          </div>
          <p><a class="btn btn-default btn-sm" href="database_space.php?dbh=<?php echo $strNode; ?>"><i class="fa fa-search"></i> Explore More</a></p>
        </div>
        <div class="col-sm-4 text-center">
          <h4>Top 10 large tables</h4>
          <div id="DatabaseTop10" class="chart"></div>
          <?php $arrDBTopTables = getTopTableSizes($strNode); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <?php
              echo $strNodeTable;
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <?php
            $arrDBSettings = array();
            $arrDBNodes = array();
            foreach($arrDBConns as $key => $value){
              if((strtolower($value['DBXLNode']) == 'datanode') || (strtolower($value['DBXLNode']) == 'coordinator')){
                if(testPort($value['DBIP'], $value['DBPort'],.05)){
                  $objResults = getDBSystemParam($key);
                  $intRows = pg_numrows($objResults);
                  $arrDBNodes[] = $key;
                  for($ri = 0; $ri < $intRows; $ri++){
                    $row = pg_fetch_array($objResults, $ri);
                    $arrDBSetting = array($key => $row['setting']);
                    $arrDBSettings[$row['name']][$key] = $row['setting'];
                  }
                }
              }
            }
            echo '<div class="table-responsive">' . "\n";
            echo '<table class="table table-bordered table-striped table-hover table-condensed">' . "\n";
            echo '<tr>' . "\n";
            echo '<th>System<br>Setting</th>' . "\n";
            foreach($arrDBNodes AS $strENode){
              echo '<th>' . $strENode . '<br>' . $arrDBConns[$strENode]['DBXLNode'] . '</th>' . "\n";
            }
            echo '</tr>' . "\n";
            foreach($arrDBSettings AS $key => $value){
              echo '<tr>' . "\n";
              echo '<td><strong>' . $key . '</strong></td>' . "\n";
              foreach($arrDBNodes AS $strENode){
                if($value[$strENode] == ''){
                  echo '<td>&nbsp;</td>' . "\n";
                }else{
                  echo '<td>' . str_replace(',', ',<br>', $value[$strENode]) . '</td>' . "\n";
                }
              }
              echo '</tr>' . "\n";
            }
            echo '</table>' . "\n";
            echo '</div>' . "\n";
          ?>
        </div>
      </div>
        <?php
          }
        ?>
    </div><!-- /.container -->

    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>

    <script>
    <?php
        // var data = {
        //   labels: [
        //     'table1', 'tabl2', 'table3', 'table4', 'table5', 'table6'
        //   ],
        //   series: [
        //     {
        //       label: 'data',
        //       values: [4, 8, 15, 16, 23, 42]
        //     },
        //     {
        //       label: 'index',
        //       values: [12, 43, 22, 11, 73, 25]
        //     },]
        // };

      foreach($arrDBTopTables as $key => $row){
        $arrDBTables = explode('.', $key);
        $strTables .= '\'' . $arrDBTables[1] . '.' . $arrDBTables[2] . '\',';
        $strData .= $row['data'] . ',';
        if($row['index'] > 0){
          $strIndex .= $row['index'] . ',';
        }else{
          $strIndex .= ',';
        }

      }

      echo 'var data = {' . "\n";
      echo '  labels: [' . "\n";
      echo rtrim($strTables,',') . '],' . "\n";
      echo '  series: [' . "\n";
      echo '     {' . "\n";
      echo '       label: \'data\',' . "\n";
      echo '       values: [' . rtrim($strData,',') . ']' . "\n";
      echo '     },' . "\n";
      echo '     {' . "\n";
      echo '       label: \'index\',' . "\n";
      echo '       values: [' . rtrim($strIndex,',') . ']' . "\n";
      echo '     },]' . "\n";
      echo ' };' . "\n";

    ?>
    var chartWidth       = 150,
        barHeight        = 10,
        groupHeight      = barHeight * data.series.length,
        gapBetweenGroups = 5,
        spaceForLabels   = 200,
        spaceForLegend   = 150;

    // Zip the series data together (first values, second values, etc.)
    var zippedData = [];
    for (var i=0; i<data.labels.length; i++) {
      for (var j=0; j<data.series.length; j++) {
        zippedData.push(data.series[j].values[i]);
      }
    }

    // Color scale
    var color = d3.scale.category20c();
    var chartHeight = barHeight * zippedData.length + gapBetweenGroups * data.labels.length;

    var x = d3.scale.linear()
        .domain([0, d3.max(zippedData)])
        .range([0, chartWidth]);

    var y = d3.scale.linear()
        .range([chartHeight + gapBetweenGroups, 0]);

    var yAxis = d3.svg.axis()
        .scale(y)
        .tickFormat('')
        .tickSize(0)
        .orient("left");

    // Specify the chart area and dimensions
    var chart = d3.select("#DatabaseTop10").append("svg")
        .attr("width", spaceForLabels + chartWidth + spaceForLegend)
        .attr("height", chartHeight);

    // Create bars
    var bar = chart.selectAll("g")
        .data(zippedData)
        .enter().append("g")
        .attr("transform", function(d, i) {
          return "translate(" + spaceForLabels + "," + (i * barHeight + gapBetweenGroups * (0.5 + Math.floor(i/data.series.length))) + ")";
        });

    // Create rectangles of the correct width
    bar.append("rect")
        .attr("fill", function(d,i) { return color(i % data.series.length); })
        .attr("class", "bar")
        .attr("width", function(d) { return x(d) || 0; })
        .attr("height", barHeight - 1);

    // Add text label in bar
    bar.append("text")
        .attr("x", function(d) { return x(d) - 0 || 0; })
        .attr("y", barHeight / 2)
        .attr("fill", "black")
        .attr("dy", ".25em")
        .text(function(d) { if(d){
                              return formatBytes(d);
                            }else{
                              return d;
                            }});

    // Draw labels
    bar.append("text")
        .attr("class", "label")
        .attr("x", function(d) { return - spaceForLabels; })
        .attr("y", groupHeight / 2)
        .attr("dy", ".25em")
        .text(function(d,i) {
          if (i % data.series.length === 0)
            return data.labels[Math.floor(i/data.series.length)];
          else
            return ""});

    chart.append("g")
          .attr("class", "y axis")
          .attr("transform", "translate(" + spaceForLabels + ", " + -gapBetweenGroups/2 + ")")
          .call(yAxis);

    // Draw legend
    var legendRectSize = barHeight,
        legendSpacing  = 4;

    var legend = chart.selectAll('.legend')
        .data(data.series)
        .enter()
        .append('g')
        .attr('transform', function (d, i) {
            var height = legendRectSize + legendSpacing;
            var offset = -gapBetweenGroups/2;
            var horz = spaceForLabels + chartWidth + 40 - legendRectSize;
            var vert = (chartHeight) - (i * height + 10);
            return 'translate(' + horz + ',' + vert + ')';
            //return 'translate(' + horz + ',' + vert + ')';
        });

    legend.append('rect')
        .attr('width', legendRectSize)
        .attr('height', legendRectSize)
        .style('fill', function (d, i) { return color(i); })
        .style('stroke', function (d, i) { return color(i); });

    legend.append('text')
        .attr('class', 'legend')
        .attr('x', legendRectSize + legendSpacing)
        .attr('y', legendRectSize)
        .text(function (d) { return d.label; });

    </script>
    <script>

    var width = 300,
        height = 300,
        radius = Math.min(width, height) / 2;

//    var color = d3.scale.ordinal()
//        .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

    var color = d3.scale.category20c();

    var arc = d3.svg.arc()
        .outerRadius(radius - 10)
        .innerRadius(radius - 90);

    var pie = d3.layout.pie()
        .sort(null)
        .value(function(d) { return d.size; });

    var svg = d3.select("#DatabaseSpaceChart").append("svg")
        .attr("width", width)
        .attr("height", height)
      .append("g")
        .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    d3.csv("database_space_csv.php?dbh=<?php echo $strNode; ?>", function(error, data) {

      data.forEach(function(d) {
        d.size = +d.size;
      });

      var g = svg.selectAll(".arc")
          .data(pie(data))
        .enter().append("g")
          .attr("class", "arc");

      g.append("path")
          .attr("d", arc)
          .style("fill", function(d) { return color(d.data.database); });

      g.append("text")
          .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
          .attr("dy", ".35em")
          .style("text-anchor", "middle")
          .text(function(d) { return d.data.database; });

    });

    </script>
    <script type="text/javascript" src="js/d3.ay-pie-chart.js"></script>
    <script type="text/javascript">
    <?php
    $arrDBSizes = getDatabaseSizes($strNode);
    $arrJSON = array();
    $intX = 1;
    foreach($arrDBSizes as $key => $value){
      $arrJSON[] = array('index' => $intX, 'name' => $key, 'value' => $value);
      $intX++;
    }
    $arrJSON = json_encode($arrJSON);
    ?>

    $(function(){
      var mydata = <?php echo $arrJSON; ?>;
      ay.pie_chart('DatabaseSpaceChart', mydata, {radius_inner: 40, percentage: true, group_data: 1});
//      ay.pie_chart('pie-b', get_random_data(10), {radius_inner: 50});
//      ay.pie_chart('pie-c', get_random_data(20), {group_data: 1});
    });
    </script>
  </body>
</html>
