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
<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Index";
		include("./php/head.php");
		?>
	</head>

	<body>

		<div class="container">
			<h1 class="page-header">

			<span style="text-transform: uppercase;margin-right:10px;font-size:0.9em;font-weight:bold;">&nbsp;</span>
<!--					<button class="btn btn-1 btn-1a" onclick="window.location = '../util/logout.php';">
						Log Out
					</button>
				<div style='float:right'>
					<button class="btn btn-1 btn-1a" onclick="aggregate();">
						Aggregate
					</button>

				</div>
-->

			</h1>
			<div class="tableContainer">
				<table id='t1'>
					<tbody>
						<?php
						include('../intactConnectionString.php');
						//open connection
						$dbconn = pg_connect($connectionString)
							or die('Could not connect: ' . pg_last_error());
						$result = pg_prepare($dbconn, "my_query",
"select filename, count(uniprotkb) as pcount from uniprotkb_filename group by filename order by count(uniprotkb) ASC");
						// Execute the prepared query
						$result = pg_execute($dbconn, "my_query", []);
						while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
							$url = $line['filename'];//.'-'.$line['random_id'];
							echo "<td><a href='./network.php?files=" . urlencode($url) . "'>" . $line['filename'] . "</a>" . "</td>";
							echo "<td>" .$line['pcount'] . "</td>";
							//~ echo  "<td class='centre'><input type='checkbox' class='aggregateCheckbox' value='". $url . "'></td>";
							echo "</tr>\n";
						}
						?>
					</tbody>
				</table>
			</div><!-- tableContainer -->
		</div> <!-- CONTAINER -->

        <script>
			//<![CDATA[

			var opt1 = {
				colTypes: ["alpha","none", "alpha", "alpha", "clearCheckboxes"],
				pager: {
				rowsCount: 30
				}
			}
			new DynamicTable("t1", opt1);

            function aggregate(){
				var inputs = document.getElementsByClassName('aggregateCheckbox');
                var values = new Array();
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].checked) {
                        values.push(inputs[i].value);
                    }
                }
                if (values.length === 0) alert ("Cannot aggregate: no selection - use checkboxes in right most table column.");
                else {
                    window.open("./network.php?sid="+values.join(','), "_self");
                }
            }

            function clearAggregationCheckboxes(){
				var inputs = document.getElementsByClassName('aggregateCheckbox');
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type === 'checkbox') {
                        inputs[i].checked = false;
                    }
                }
			}

            //]]>
        </script>

	</body>
</html>
