<?php include('include_config.php'); ?>
<?php
  if(isset($_GET['dbh'])){
    $strNode = $_GET['dbh'];
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

    circle,
    path {
      cursor: pointer;
    }

    circle {
      fill: none;
      pointer-events: all;
    }

    #tooltip { background-color: white;
            padding: 3px 5px;
/*            border: 1px solid black; */
            text-align: center;}
    </style>
  </head>
  <body>
    <?php include('include_nav.php'); ?>

    <!-- START page content -->
    <div class="container">
      <?php include('include_alerts.php'); ?>
      <div class="row">
        <div class="col-sm-12 text-center">
          <h1>Database Space Explorer</h1>
          <p class="lead">Click a color to zoom in. Click the center to zoom out.</p>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div id="DatabaseSpaceInfo"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div id="DatabaseSpaceChart"></div>
        </div>
      </div>
    </div><!-- /.container -->

    <?php include('include_footer.php'); ?>

    <!-- / END Page Content -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('include_javascript.php'); ?>
    <script>
      $(document).ready(function () {
        var margin = {top: 350, right: 480, bottom: 350, left: 480},
            radius = Math.min(margin.top, margin.right, margin.bottom, margin.left) - 10;

        function filter_min_arc_size_text(d, i) {return (d.dx*d.depth*radius/3)>14};

        var hue = d3.scale.category20c();

        var luminance = d3.scale.sqrt()
            .domain([0, 1e6])
            .clamp(true)
            .range([90, 20]);

        var svg = d3.select("#DatabaseSpaceChart").append("svg")
            .attr("width", margin.left + margin.right)
            .attr("height", margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var partition = d3.layout.partition()
            .sort(function(a, b) { return d3.ascending(a.name, b.name); })
            .size([2 * Math.PI, radius]);

        var arc = d3.svg.arc()
            .startAngle(function(d) { return d.x; })
            .endAngle(function(d) { return d.x + d.dx - .01 / (d.depth + .5); })
            .innerRadius(function(d) { return radius / 3 * d.depth; })
            .outerRadius(function(d) { return radius / 3 * (d.depth + 1) - 1; });

        //Tooltip description
        var tooltip = d3.select("#DatabaseSpaceInfo")
            .append("div")
            .attr("id", "tooltip")
//            .style("position", "absolute")
            .style("z-index", "10")
            .style("opacity", 0);

        function format_number(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


        function format_description(d) {
          var description = d.description;
              return  '<b>' + d.name + '</b></br>'+ d.description + '<br> (' + formatBytes(d.value,1024) + ')';
        }

        function computeTextRotation(d) {
          var angle=(d.x +d.dx/2)*180/Math.PI - 90
          return angle;
        }

        function mouseOverArc(d) {
          d3.select(this).attr("stroke","black")
          tooltip.html(format_description(d));
          return tooltip.transition()
          .duration(50)
          .style("opacity", 0.9);
        }

        function mouseOutArc(){
          d3.select(this).attr("stroke","")
          return tooltip.style("opacity", 0);
        }

        function mouseMoveArc (d) {
          return tooltip
          .style("top", (d3.event.pageY-5)+"px")
          .style("left", (d3.event.pageX+5)+"px");
        }

        var root_ = null;
        d3.json("database_space_json.php?dbh=<?php echo $strNode; ?>", function(error, root) {
    //    d3.json("database_space_data_orig.json", function(error, root) {
          if (error) return console.warn(error);
          // Compute the initial layout on the entire tree to sum sizes.
          // Also compute the full name and fill color for each node,
          // and stash the children so they can be restored as we descend.

          partition
            .value(function(d) { return d.size; })
            .nodes(root)
            .forEach(function(d){
              d._children = d.children;
              d.sum = d.value;
              d.key = key(d);
              d.fill = fill(d);
            });

          // Now redefine the value function to use the previously-computed sum.
          partition
            .children(function(d, depth) { return depth < 2 ? d._children : null; })
            .value(function(d) { return d.sum; });

          var center = svg.append("circle")
            .attr("r", radius / 3)
            .on("click", zoomOut);

          center.append("title")
            .text("zoom out");

          var partitioned_data=partition.nodes(root).slice(1)

          var path = svg.selectAll("path")
            .data(partitioned_data)
            .enter().append("path")
            .attr("d", arc)
            .style("fill", function(d) { return d.fill; })
            .each(function(d) { this._current = updateArc(d); })
            .on("click", zoomIn)
            .on("mouseover", mouseOverArc)
            .on("mousemove", mouseMoveArc)
            .on("mouseout", mouseOutArc);


          var texts = svg.selectAll("text")
            .data(partitioned_data)
            .enter().append("text")
            .filter(filter_min_arc_size_text)
            .attr("transform", function(d) { return "rotate(" + computeTextRotation(d) + ")"; })
            .attr("x", function(d) { return radius / 3 * d.depth; })
            .attr("dx", "6") // margin
            .attr("dy", ".35em") // vertical-align
            .text(function(d,i) {return d.name})
            //.style("fill", function(d) { return brightness(d3.rgb(colour(d.fill))) < 125 ? "#eee" : "#000";})
            .style("fill", function(d) { return "#BDBDBD";})


          function zoomIn(p) {
            if (p.depth > 1) p = p.parent;
            if (!p.children) return;
            zoom(p, p);
          }

          function zoomOut(p) {
            if (!p.parent) return;
            zoom(p.parent, p);
          }

          // Zoom to the specified new root.
          function zoom(root, p) {
            if (document.documentElement.__transition__) return;

            // Rescale outside angles to match the new layout.
            var enterArc,
                exitArc,
                outsideAngle = d3.scale.linear().domain([0, 2 * Math.PI]);

            function insideArc(d) {
              return p.key > d.key
                  ? {depth: d.depth - 1, x: 0, dx: 0} : p.key < d.key
                  ? {depth: d.depth - 1, x: 2 * Math.PI, dx: 0}
                  : {depth: 0, x: 0, dx: 2 * Math.PI};
            }

            function outsideArc(d) {
              return {depth: d.depth + 1, x: outsideAngle(d.x), dx: outsideAngle(d.x + d.dx) - outsideAngle(d.x)};
            }

            center.datum(root);

            // When zooming in, arcs enter from the outside and exit to the inside.
            // Entering outside arcs start from the old layout.
            if (root === p) enterArc = outsideArc, exitArc = insideArc, outsideAngle.range([p.x, p.x + p.dx]);

            var new_data=partition.nodes(root).slice(1)

            path = path.data(new_data, function(d) { return d.key; });

            // When zooming out, arcs enter from the inside and exit to the outside.
            // Exiting outside arcs transition to the new layout.
            if (root !== p) enterArc = insideArc, exitArc = outsideArc, outsideAngle.range([p.x, p.x + p.dx]);

            d3.transition().duration(d3.event.altKey ? 7500 : 750).each(function() {
              path.exit().transition()
                .style("fill-opacity", function(d) { return d.depth === 1 + (root === p) ? 1 : 0; })
                .attrTween("d", function(d) { return arcTween.call(this, exitArc(d)); })
                .remove();

              path.enter().append("path")
                .style("fill-opacity", function(d) { return d.depth === 2 - (root === p) ? 1 : 0; })
                .style("fill", function(d) { return d.fill; })
                .on("click", zoomIn)
                .on("mouseover", mouseOverArc)
                .on("mousemove", mouseMoveArc)
                .on("mouseout", mouseOutArc)
                .each(function(d) { this._current = enterArc(d); });

              path.transition()
                .style("fill-opacity", 1)
                .attrTween("d", function(d) { return arcTween.call(this, updateArc(d)); });
            });

            texts = texts.data(new_data, function(d) { return d.key; })

            texts.exit()
              .remove()
            texts.enter()
              .append("text")
            texts.style("opacity", 0)
              .attr("transform", function(d) { return "rotate(" + computeTextRotation(d) + ")"; })
              .attr("x", function(d) { return radius / 3 * d.depth; })
              .attr("dx", "6") // margin
              .attr("dy", ".35em") // vertical-align
              .filter(filter_min_arc_size_text)
              .text(function(d,i) {return d.name})
              .transition().delay(750).style("opacity", 1)
              .style("fill", function(d) { return "#BDBDBD";})
          }
        });

        function key(d) {
          var k = [], p = d;
          while (p.depth) k.push(p.name), p = p.parent;
          return k.reverse().join(".");
        }

        function fill(d) {
          var p = d;
          while (p.depth > 1) p = p.parent;
          var c = d3.lab(hue(p.name));
          c.l = luminance(d.sum);
          return c;
        }

        function arcTween(b) {
          var i = d3.interpolate(this._current, b);
          this._current = i(0);
          return function(t) {
            return arc(i(t));
          };
        }

        function updateArc(d) {
          return {depth: d.depth, x: d.x, dx: d.dx};
        }

        // http://www.w3.org/WAI/ER/WD-AERT/#color-contrast
        function brightness(rgb) {
          return rgb.r * .299 + rgb.g * .587 + rgb.b * .114;
        }
        function colour(d) {
          if (d.children) {
            // There is a maximum of two children!
            var colours = d.children.map(colour),
                a = d3.hsl(colours[0]),
                b = d3.hsl(colours[1]);
            // L*a*b* might be better here...
            return d3.hsl((a.h + b.h) / 2, a.s * 1.2, a.l / 1.2);
          }
          return d.colour || "#fff";
        }
        d3.select(self.frameElement).style("height", margin.top + margin.bottom + "px");
      });
    </script>

  </body>
</html>
