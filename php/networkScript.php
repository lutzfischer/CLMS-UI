<?php
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
?>
<script>
	//<![CDATA[
		
	 "use strict";
	 	
	/*
	 * Horizontal splitter JS
	 */
	var marginBottom = 95;
	var minBottomDivHeight = 120;
	var splitterDivHeight = 20;
	var splitterDragging = false;
	var splitterDiv = document.getElementById("splitterDiv");
	var topDiv = document.getElementById("topDiv");
	var bottomDiv = document.getElementById("bottomDiv");
	var main = document;//.getElementById("main");
	splitterDiv.onmousedown = function(evt) {
		splitterDragging = true;
	}
	main.onmousemove = function(evt) {
		if (splitterDragging === true || !evt){
			var element = topDiv;
			var top = 0;
			do {
				top += element.offsetTop  || 0;
				element = element.offsetParent;
			} while(element);
			var topDivHeight;
			if (evt) topDivHeight = evt.pageY - top - (splitterDivHeight / 2);
			else topDivHeight = window.innerHeight - top - splitterDivHeight - minBottomDivHeight- marginBottom;
			if (topDivHeight < 0) topDivHeight = 0;
			var bottomDivHeight = window.innerHeight - top - topDivHeight - splitterDivHeight - marginBottom;
			if (bottomDivHeight < minBottomDivHeight){
				bottomDivHeight = minBottomDivHeight;
				topDivHeight = window.innerHeight - top - splitterDivHeight - minBottomDivHeight- marginBottom;
			}
			topDiv.setAttribute("style", "height:"+topDivHeight+"px;");
			if (document.getElementById('keyChkBx').checked == true) {
				bottomDiv.setAttribute("style", "height:"+bottomDivHeight+"px;");
			}
		};
	}
	main.onmouseup = function(evt) {
		splitterDragging = false;
	}
	main.onmousemove();
	window.onresize = function(event) {
		main.onmousemove();//event);
	};
	
	/*
	 *
	 *  Hide / show floaty panels
	 *
	 */		
	 
	showLegendPanel(false);
	showSelectionPanel(false);//publications
	showMiDataPanel(false);
	showHelpPanel(false);
	
	
	function showSelectionPanel(show) {
		var bd = d3.select('#bottomDiv');
		var splt = d3.select('#splitterDiv');
		if (show) {
			bd.style('display', 'block');
			splt.style('display', 'block');
			main.onmousemove();
		} else {
			bd.style('display', 'none');
			splt.style('display', 'none');
			var element = topDiv;
			var top = 0;
			do {
				top += element.offsetTop  || 0;
				element = element.offsetParent;
			} while(element);
			var topDivHeight = window.innerHeight - top - marginBottom;
			topDiv.setAttribute("style", "height:"+topDivHeight+"px;");
		}
		document.getElementById('selectionChkBx').checked = show;
	}
	function showLegendPanel(show) {
		var kp = d3.select('#keyPanel');
		if (show) {
			kp.style('display', 'block');
		} else {
			kp.style('display', 'none');
		}
		document.getElementById('keyChkBx').checked = show;
	}
	function showHelpPanel(show) {
		var hp = d3.select('#helpPanel');
		if (show) {
			hp.style('display', 'block');
		} else {
			hp.style('display', 'none');
		}
		document.getElementById('helpChkBx').checked = show;
	}
	function showMiDataPanel(show) {
		var miDp = d3.select('#miDataPanel');
		if (show) {
			miDp.style('display', 'block');
		} else {
			miDp.style('display', 'none');
		}
		document.getElementById('miDataChkBx').checked = show;
	}
	

	var xlv;
	//~ https://thechamplord.wordpress.com/2014/07/04/using-javascript-window-onload-event-properly/
	window.addEventListener("load", function() {

		var targetDiv = document.getElementById('topDiv');
		xlv = new xiNET(targetDiv);
		
		<?php
		include './php/loadMiData.php';
		?>
		//~ document.getElementById("miDataDiv").innerHTML = "<pre>"+JSON.stringify(data, null, "\t")+"<\pre>";
		// could also use http://bl.ocks.org/mbostock/4339083
		document.getElementById("miDataDiv").innerHTML = JSONTree.create(data);
			
			
		xlv.readMIJSON(data, false);
						
		/* Init filter bar */
		initSlider();
		//changeAnnotations();
		xlv.selfLinksShown = document.getElementById('selfLinks').checked;
		xlv.ambigShown = document.getElementById('ambig').checked;
		xlv.filter = function (match) {
			var vChar = match.validated;
			if (vChar == 'A' && document.getElementById('A').checked && (!match.score || match.score >= xlv.cutOff)) return true;
			else if (vChar == 'B' && document.getElementById('B').checked  && (!match.score || match.score >= xlv.cutOff)) return true;
			else if (vChar == 'C' && document.getElementById('C').checked && (!match.score || match.score >= xlv.cutOff)) return true;
			else if (vChar == '?' && document.getElementById('Q').checked && (!match.score || match.score >= xlv.cutOff)) return true;
			else if (match.autovalidated && document.getElementById('AUTO').checked && (!match.score || match.score >= xlv.cutOff))  return true;
			else return false;
		};

		//register callbacks
/*		xlv.linkSelectionCallbacks.push(function (selectedLinks){
			//console.log("SELECTED:", selectedLinks);
			var selectionDiv = document.getElementById("selectionDiv");
			var selectedLinkArray = selectedLinks.values();
			var selectedLinkCount = selectedLinkArray.length;
			if (selectedLinkCount === 0) {
				selectionDiv.innerHTML = "<p>No selection.</p>";
			}
			else {
				var out = ""

				var scoresTable = "<table><tr>";
				//~ scoresTable += "<th>Id</th>";
				scoresTable += "<th>Protein1</th>";
				scoresTable += "<th>PepPos1</th>";
				scoresTable += "<th>PepSeq1</th>";
				scoresTable += "<th>LinkPos1</th>";
				scoresTable += "<th>Protein2</th>";
				scoresTable += "<th>PepPos2</th>";
				scoresTable += "<th>PepSeq2</th>";
				scoresTable += "<th>LinkPos2</th>";
				scoresTable += "<th>Score</th>";
				if (xlv.autoValidatedFound === true){
					scoresTable += "<th>Auto</th>";
				}
				if (xlv.manualValidatedFound === true){
					scoresTable += "<th>Manual</th>";
				}
					scoresTable += "<th>Group</th>";
				scoresTable += "<th>Run name</th>";
				scoresTable += "<th>Scan number</th>";
				scoresTable += "</tr>";

				out +=  scoresTable;

				for (var i = 0; i < selectedLinkCount; i++) {
					var aLink = selectedLinkArray[i];
					if (aLink.residueLinks) {//its a ProteinLink
						out += proteinLinkToHTML(aLink);
					}else {//must be ResidueLink
						out += residueLinkToHTML(aLink);						
					}							
				}

				out += "</table>";

				selectionDiv.innerHTML = out;
			}
		});
*/
		xlv.legendCallbacks.push(function (linkColours, colourAssignment) {
					var coloursKeyDiv = document.getElementById('colours');			
					if (colourAssignment){
						//html legened
						/*
						var table = "<table><tr style='height:10px;'></tr><tr><td style='width:80px;margin:10px;"
									+ "background:#70BDBD;opacity:0.3;border:none;'>"
									+ "</td><td>your complex</td></tr>";
								var domain = colourAssignment.domain();
							//~ console.log("Domain:"+domain);
							var range = colourAssignment.range();
							//~ console.log("Range:"+range);
							table += "<tr style='height:10px;'></tr>";
							for (var i = 0; i < domain.length; i ++){
								//make transparent version of colour
								var temp = new RGBColor(range[i%20]);
								var trans = "rgba(" +temp.r+","+temp.g+","+temp.b+ ", 0.6)";
								table += "<tr><td style='width:75px;margin:10px;background:"
										+ trans + ";border:1px solid " 
										+ range[i%20] + ";'></td><td>"
										+ domain[i] +"</td></tr>";
								//~ console.log(i + " "+ domain[i] + " " + range[i]);
							}
						}
						table = table += "</table>";
						coloursKeyDiv.innerHTML = table;					
						*/
						//d3 svg legend
						verticalLegend = d3.svg.legend().labelFormat("none").cellPadding(5).orientation("vertical").units(xlv.annotationChoice).cellWidth(25).cellHeight(18).inputScale(colourAssignment);
						legend.selectAll("*").remove();
						legend.attr("transform", "translate(20,40)").attr("class", "legend").call(verticalLegend);
					}
		});
	});

	function changeAnnotations(){
		var annotationSelect = document.getElementById('annotationsSelect');
		xlv.setAnnotations(annotationSelect.options[annotationSelect.selectedIndex].value);
	};

	function changeLinkColours(){
		var linkColourSelect = document.getElementById('linkColourSelect');
		var selectedOption = linkColourSelect.options[linkColourSelect.selectedIndex].value;
		var rLinks = xlv.proteinLinks.values()[0].residueLinks.values();
		var rc = rLinks.length;
		alert(selectedOption);
		//~ if (selectedOption === "Search") {
			//~ for (var j = 0; j < rc; j++) {
				//~ rLinks[j].colour = null;
			//~ }				
			//~ xlv.checkLinks();
		//~ } else {				
			for (var j = 0; j < rc; j++) {
				var resLink = rLinks[j];
				var d;
				//~ if (selectedOption === "Search"){
					//~ d = distances[resLink.fromResidue][resLink.toResidue];
				//~ } else {
					d = distances[resLink.toResidue][resLink.fromResidue];
				//~ }
				if (isNan(d)){
					d = 999;
				}
				if (d <= 10) {
					resLink.colour = '#1B7837';
				}
				else if (d <= 15) {
					resLink.colour = '#5AAE61';
				}
				else if (d <= 25) {
					resLink.colour = '#FDB863';
				}
				else if (d <= 30) {
					resLink.colour = '#9970AB';
				}
				else {
					resLink.colour = '#762A83';
				}
				resLink.line.setAttribute("stroke", resLink.colour);
			}
		//~ }
	};

	/*Score slider*/
	function initSlider(){
		if (xlv.scores === null){
			d3.select('#scoreSlider').style('display', 'none');
		}
		else {
			document.getElementById('scoreLabel1').innerHTML = "Score:&nbsp;&nbsp;" + getMinScore();
			document.getElementById('scoreLabel2').innerHTML = getMaxScore();
			sliderChanged();
			d3.select('#scoreSlider').style('display', 'inline-block');
		}
	};

	var sliderDecimalPlaces = 1;
	function getMinScore(){
		if (xlv.scores){
			var powerOfTen = Math.pow(10, sliderDecimalPlaces);
			return (Math.floor(xlv.scores.min * powerOfTen) / powerOfTen)
					.toFixed(sliderDecimalPlaces);
		}
	}
	function getMaxScore(){
		if (xlv.scores){
			var powerOfTen = Math.pow(10, sliderDecimalPlaces);
			return (Math.ceil(xlv.scores.max * powerOfTen) / powerOfTen)
					.toFixed(sliderDecimalPlaces);
		}
	}
	function sliderChanged(){
		var slide = document.getElementById('slide');
		var powerOfTen = Math.pow(10, sliderDecimalPlaces);

		var cut = ((slide.value / 100)
					* (getMaxScore() - getMinScore()))
					+ (getMinScore() / 1);
		cut = cut.toFixed(sliderDecimalPlaces);
		var cutoffLabel = document.getElementById("cutoffLabel");
		cutoffLabel.innerHTML = '(' + cut + ')';
		xlv.setCutOff(cut);
	}

	//used when link clicked
	function proteinLinkToHTML(proteinLink) {
		var linkInfo = "";
		var resLinks = proteinLink.residueLinks.values();
		var resLinkCount = resLinks.length;
		for (var i = 0; i < resLinkCount; i++) {
			var resLink = resLinks[i];
			linkInfo += residueLinkToHTML(resLink);
		}
		return linkInfo;
	};

	function residueLinkToHTML(residueLink){
		var matches = residueLink.getFilteredMatches();
		var c = matches.length;
		var rows = "";
		for (var j = 0; j < c; j++) {
			var match = matches[j][0];

			var htmlTableRow = "<tr>";
			if (typeof loadSpectra == "function"){
				htmlTableRow = "<tr onclick=\"loadSpectra('"+match.id+"','"+match.pepSeq1+"',"
					+match.linkPos1+",'"+match.pepSeq2+"',"+match.linkPos2+");\">";
			}

			//~ htmlTableRow += "<td><p>" + match.id
				//~ + "</p></td>";
			htmlTableRow += "<td><p>" + match.protein1
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.pepPos1
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.pepSeq1raw
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.linkPos1
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.protein2
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.pepPos2
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.pepSeq2raw
				+ "</p></td>";
			htmlTableRow += "<td><p>" + match.linkPos2
				+ "</p></td>";

			htmlTableRow += "<td><p>" +
			((typeof match.score !== 'undefined')? match.score.toFixed(4) : 'undefined')
			+ "</p></td>";

			if (match.controller.autoValidatedFound === true){
				htmlTableRow += "<td><p>" + match.autovalidated
					+ "</p></td>";
			}

			if (match.controller.manualValidatedFound === true){
				htmlTableRow += "<td><p>" + match.validated
					+ "</p></td>";
			}
			htmlTableRow += "<td><p>" + match.group
					+ "</p></td>";
			htmlTableRow += "<td><p>" + match.runName
					+ "</p></td>";
			htmlTableRow += "<td><p>" + match.scanNumber
					+ "</p></td>";
			htmlTableRow += "</tr>";
			rows += htmlTableRow;
		}
		return rows;
	}

	//]]>								
</script>
