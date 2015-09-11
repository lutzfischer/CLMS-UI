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
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="common platform for downstream analysis of CLMS data" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<link rel="stylesheet" href="./css/reset.css" />
		<link rel="stylesheet" type="text/css" href="./css/byrei-dyndiv_0.5.css">
		<link rel="stylesheet" type="text/css" href="./css/jsontree.css">
		<link rel="stylesheet" href="./css/style.css" />
		<link rel="stylesheet" href="./css/xiNET.css">

		<script type="text/javascript" src="./vendor/signals.js"></script>
        <script type="text/javascript" src="./vendor/byrei-dyndiv_1.0rc1-src.js"></script>
        <script type="text/javascript" src="./vendor/jsontree.min.js"></script>
        <script type="text/javascript" src="./vendor/d3.js"></script>
        <script type="text/javascript" src="./vendor/colorbrewer.js"></script>
       	<script type="text/javascript" src="./vendor/FileSaver.js"></script>
        <script type="text/javascript" src="./vendor/Blob.js"></script>
        <script type="text/javascript" src="./vendor/rgbcolor.js"></script>

        <script type="text/javascript" src="../interaction-viewer/build/interactionviewer.js"></script>
    </head>
    <body>
		<div class="dynDiv_setLimit">

			<div class="dynDiv" id="keyPanel">
				<div class="dynDiv_moveParentDiv"><i class="fa fa-times-circle" onclick="showLegendPanel(false);"></i></div>
				<div class="panelInner">
<!--
					<div id="key"><img id="defaultLinkKey" src="./images/fig3_1.svg"><br><img id="logo" src="./images/logos/rappsilber-lab-small.png"></div>
-->
			
			
				<!--
					LEGEND
	-->
					<table>
					 <tr>
						<td>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/smallMol.svg"></td>
							</div>
						<td>Bioactive Entity</td>
					  </tr>				  
					 <tr>
						<td>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/proteinBlob.svg" >
							</div>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/proteinBar.svg">
							</div>						
						</td>
						<td>
							Protein<br>
							 - click or tap to toggle between circle and bar (bar shows binding sites, if known).</td>
					  </tr>
					  <tr>
						<td>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/gene.svg"></td>
							</div>
						<td>Gene</td>
					  </tr>				  
					  <tr>
						<td>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/DNA.svg"></td>
							</div>
						<td>DNA</td>
					  </tr>
					<tr>
						<td>
							<div style="float:right">
								<img src="../interaction-viewer/demo/svgForKey/RNA.svg"></td>
							</div>
						<td>RNA</td>
					  </tr>
					</table>

				
				<div id="colours"></div>
			
				</div>
				<div class="dynDiv_resizeDiv_tl"></div>
				<div class="dynDiv_resizeDiv_tr"></div>
				<div class="dynDiv_resizeDiv_bl"></div>
				<div class="dynDiv_resizeDiv_br"></div>
			</div>

			<div class="dynDiv helpPanel" id="helpPanel">
				<div class="dynDiv_moveParentDiv"><i class="fa fa-times-circle" onclick="showHelpPanel(false);"></i></div>
				<div class="panelInner">
					<?php include "./php/help.php" ?>
				</div>
				<div class="dynDiv_resizeDiv_tl"></div>
				<div class="dynDiv_resizeDiv_tr"></div>
				<div class="dynDiv_resizeDiv_bl"></div>
				<div class="dynDiv_resizeDiv_br"></div>
			</div>

			<div class="dynDiv" id="miDataPanel">
				<div class="dynDiv_moveParentDiv"><i class="fa fa-times-circle" onclick="showMiDataPanel(false);"></i></div>
				<div class="panelInner" id='miDataDiv'></div>
				<div class="dynDiv_resizeDiv_tl"></div>
				<div class="dynDiv_resizeDiv_tr"></div>
				<div class="dynDiv_resizeDiv_bl"></div>
				<div class="dynDiv_resizeDiv_br"></div>
			</div>



		</div>

		<!-- Main -->
		<div id="main">

			<div class="container">
				<h1 class="page-header noSelect">
					<i class="fa fa-home" onclick="window.location = './index.php';" title="About / Search"></i>
<!--
					http://pterkildsen.com/2014/07/13/styling-a-group-of-checkboxes-as-a-dropdown-via-css-and-javascript/
-->
<!--
					<div class="checkbox-dropdown" style="display:inline-block;" title="Select which views of data to show (includes Selection details and Legend)">
						<button class="btn btn-1 btn-1a">Views&nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
						<ul class="checkbox-dropdown-list">
							<li>
								<label style="top:60px;">
									<input type="checkbox" id="keyChkBx" title="Toggle display of legend/key"
									onclick="showKeyPanel(this.checked);" />Legend</label></li>
							<li>
								<label style="top:100px;">
									<input type="checkbox" id="selectionChkBx" title="Toggle display of selection details"
									onclick="showSelectionPanel(this.checked)"/>Selection</label></li>
							<li>
								<label style="top:140px;">
									<input type="checkbox" id="nglChkBx" title="Toggle display of 3d structure (NGL viewer)"
									disabled onclick="showNglPanel(this.checked);"/>3D</label></li>
							<li>
								<label style="top:180px;">
									<input type="checkbox" id="nglChkBx" title="Toggle display of matrix view"
									disabled onclick="showMatrixPanel(this.checked);"/>Matrix</label></li>
						</ul>
					</div>
-->

					<button class="btn btn-1 btn-1a" onclick="xlv.reset();">Reset Layout</button>
					<button class="btn btn-1 btn-1a" onclick="xlv.exportSVG();">Export SVG</button>
					<label class="btn headerChkBx">Legend
							<input id="keyChkBx" onclick="showLegendPanel(this.checked);" type="checkbox"></label>
					<label class="btn headerChkBx">Publications
							<input checked id="selectionChkBx" onclick="showSelectionPanel(this.checked)" type="checkbox"></label>
					<label id="miDataCbLabel" class="btn headerChkBx">MI Data
							<input id="miDataChkBx" onclick="showMiDataPanel(this.checked);" type="checkbox"></label>
					<label class="btn">Help
							<input id="helpChkBx" onclick="showHelpPanel(this.checked)" type="checkbox"></label>
					<i class="fa fa-github" onclick="window.location = 'github.com';" title="GitHub Issue Tracking"></i>

   	 		</div>

			<div>
				<div id="topDiv"></div>
				<div id=splitterDiv class="horizontalSplitter"><i class="fa fa-times-circle" onclick="showSelectionPanel(false);"></i></div>
				<div id="bottomDiv"><div id="selectionDiv" class="panelInner"><p>No selection.</p></div></div>
			</div>

			<div class="controls noSelect">

					<div id="scoreSlider">
						<p class="scoreLabel" id="scoreLabel1"></p>
						<input id="slide" type="range" min="0" max="100" step="1" value="0" oninput="sliderChanged()"/>
						<p class="scoreLabel" id="scoreLabel2"></p>
						<p id="cutoffLabel">(cut-off)</p>
					</div> <!-- outlined scoreSlider -->
					<label>Self-Interactions
						<input checked="checked"
							   id="selfLinks"
							   onclick="xlv.showSelfLinks(document.getElementById('selfLinks').checked);"
							   type="checkbox"
						/>
					</label>
					<label>&nbsp;&nbsp;Negative Interactions
						<input checked="checked"
							   id="ambig"
							   onclick="//xlv.showAmbig(document.getElementById('ambig').checked);"
							   type="checkbox"
						/>
					</label>
					<div style='float:right'>

						<label style="margin-left:20px;">Interactor colour:
							<select id="annotationsSelect" onChange="changeAnnotations();">
								<option selected>MI features</option>
								<option>UniprotKB</option>
								<option>SuperFamily</option>
								<option>Organism</option>
								<option>Interactor</option>
								<option>None</option>
							</select>
						</label>
						<label style="margin-left:20px;">Interaction colour:
							<select id="linkColourSelect" onChange="changeLinkColours();">
								<option selected>Detection Method</option>
								<option>Publication</option>
								<option>Interaction</option>
							</select>
						</label>
					</div>
				</div>
			</div>

		</div><!-- MAIN -->

		<?php
			include './php/networkScript.php';
		?>
</html>
