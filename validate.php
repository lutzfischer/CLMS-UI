<!--
//  CLMS-UI
//  Copyright 2015 Colin Combe, Rappsilber Laboratory, Edinburgh University
//
//  This file is part of CLMS-UI.
//
//  CLMS-UI is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  CLMS-UI is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with CLMS-UI.  If not, see <http://www.gnu.org/licenses/>.
-->
<?php
session_start();
if (!$_SESSION['session_name']) {
    header("location:login.html");
    exit;
}
header('Content-type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            $sid = urldecode($_GET["sid"]);

            $pattern = '/[^0-9,\-]/';
            if (preg_match($pattern, $sid)){
                header();
                echo ("<!DOCTYPE html>\n<html><head></head><body>You're having a laugh.</body></html>");
                exit;
            }
            $pageName = "Validation";
        ?>
        <title><?php echo $pageName ?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="common platform for downstream analysis of CLMS data" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <link rel="stylesheet" href="./css/reset.css" />
        <link rel="stylesheet" href="./css/style.css" />
        <link rel="stylesheet" href="./css/tooltip.css">
        <link rel="stylesheet" href="./css/c3.css">
        <link rel="stylesheet" href="./css/minigram.css">
        <link rel="stylesheet" href="./css/ddMenuViewBB.css">
        <link rel="stylesheet" href="./css/selectionViewBB.css">
        <link rel="stylesheet" href="./css/spectrumViewWrapper.css">
        <link rel="stylesheet" href="./css/validate.css">
        <link rel="stylesheet" href="./css/filter.css">
        <link rel="stylesheet" href="./css/validationPage.css">

        <script type="text/javascript" src="./vendor/d3.js"></script>
        <script type="text/javascript" src="./vendor/colorbrewer.js"></script>

        <script type="text/javascript" src="./vendor/c3.js"></script>
        <script type="text/javascript" src="./vendor/split.js"></script>
        <script type="text/javascript" src="./vendor/svgexp.js"></script>
        <script type="text/javascript" src="./vendor/underscore.js"></script>
        <script type="text/javascript" src="./vendor/zepto.js"></script>
        <script type="text/javascript" src="./vendor/backbone.js"></script>
        <script type="text/javascript" src="./vendor/spin.js"></script>

        <script type="text/javascript" src="../CLMS-model/src/CLMS/model/SearchResultsModel.js"></script>
        <script type="text/javascript" src="../CLMS-model/src/CLMS/model/SpectrumMatch.js"></script>
        <script type="text/javascript" src="../CLMS-model/src/CLMS/model/CrossLink.js"></script>


        <!-- Backbone models/views loaded after Backbone itself, otherwise need to delay their instantiation somehow -->
        <script type="text/javascript" src="./js/Utils.js"></script>
        <script type="text/javascript" src="./js/models.js"></script>
        <script type="text/javascript" src="./js/compositeModelType.js"></script>
        <script type="text/javascript" src="./js/modelUtils.js"></script>
        <script type="text/javascript" src="./js/minigramViewBB.js"></script>
        <script type="text/javascript" src="./js/filterViewBB.js"></script>
        <script type="text/javascript" src="./js/fdr.js"></script>
        <script type="text/javascript" src="./js/ddMenuViewBB.js"></script>
        <script type="text/javascript" src="./js/tooltipViewBB.js"></script>
        <script type="text/javascript" src="./js/selectionTableViewBB.js"></script>
        <script type="text/javascript" src="./js/linkColourAssignment.js"></script>
        <script type="text/javascript" src="./js/spectrumViewWrapper.js"></script>
        <script type="text/javascript" src="./js/validate.js"></script>

        <script type="text/javascript" src="./js/networkFrame.js"></script>
        <script type="text/javascript" src="./js/downloads.js"></script>


        <!-- Spectrum view .js files -->
        <script type="text/javascript" src="../spectrum/src/model.js"></script>
        <script type="text/javascript" src="../spectrum/src/SpectrumView2.js"></script>
        <script type="text/javascript" src="../spectrum/src/FragmentationKeyView.js"></script>
        <script type="text/javascript" src="../spectrum/src/PrecursorInfoView.js"></script>        
        <script type="text/javascript" src="../spectrum/src/FragKey/KeyFragment.js"></script>
        <script type="text/javascript" src="../spectrum/src/graph/Graph.js"></script>
        <script type="text/javascript" src="../spectrum/src/graph/Peak.js"></script>
        <script type="text/javascript" src="../spectrum/src/graph/Fragment.js"></script>
        <script type="text/javascript" src="../spectrum/src/graph/IsotopeCluster.js"></script>
    </head>

    <body>
        <!-- Main -->
        <div id="main">

            <div class="page-header">
                <i class="fa fa-home" onclick="window.location = '../history/history.html';" title="Return to search history"></i>
                <span class="headerLabel">
                    <?php echo $_SESSION['session_name'] ?>
                </span>
                <p id="expDropdownPlaceholder"></p>
                <button class='btn btn-1 btn-1a' onclick=<?php echo '"window.location = \'./network.php?sid='.$sid.'\'";' ?> title="View results">Done</button>
            </div> <!-- page-header -->
			
            <div class="mainContent">
                <div id="topDiv">
                </div>
                <div id="bottomDiv"></div>
            </div>

			<div class="controls">
				<span id="filterPlaceholder"></span>
			</div>
        </div><!-- MAIN -->


        <script>
        //<![CDATA[
        
        //~ var windowLoaded = function () {
			var CLMSUI = CLMSUI || {};
			<?php
				if (isset($_SESSION['session_name'])) {
					echo "CLMSUI.loggedIn = true;";
				}
			?>
			
            var spinner = new Spinner({scale: 5}).spin (d3.select("#topDiv").node());
					
            var success = function (text) {
                spinner.stop(); // stop spinner on request returning 
				var json = JSON.parse (text);	
				CLMSUI.init.modelsEssential(json);

				var searches = CLMSUI.compositeModelInst.get("clmsModel").get("searches");
				document.title = Array.from(searches.keys()).join();
					
				CLMSUI.split = Split (["#topDiv", "#bottomDiv"], { direction: "vertical",
						sizes: [60,40], minSize: [200,10],
							onDragEnd: function () {CLMSUI.vent.trigger ("resizeSpectrumSubViews", true);
				} });	
										
				CLMSUI.init.viewsEssential({"specWrapperDiv":"#topDiv"});

                //CLMSUI.vent.trigger ("spectrumShow", true);
                
				var allCrossLinks = Array.from(
					CLMSUI.compositeModelInst.get("clmsModel").get("crossLinks").values());
				CLMSUI.compositeModelInst.set("selection", allCrossLinks);					

				var resize = function(event) {
					CLMSUI.vent.trigger ("resizeSpectrumSubViews", true);
					var alts = d3.select("#alternatives");
					var w = alts.node().parentNode.parentNode.getBoundingClientRect().width - 20;
					alts.attr("style", "width:"+w+"px;"); //dont know why d3 style() aint working
				};

				window.onresize = resize;

				resize();
			};

			var url = "./loadData.php" + window.location.search;
			
         
            d3.text (url, function (error, text) {
                if (!error) {
                    success (text);
                }
            });
        
           
        //]]>
        </script>

    </body>
</html>
