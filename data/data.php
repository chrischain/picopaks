<?php
	/*
	*  Application: PicoBrew PicoPaks
	*  Filename: data.php
	*  $Revision: 1 $
	*  $LastChangedBy: Chris Chain $
	*  $LastChangedDate: 01/16/18 $
	*  Description: This file provides database integration for the MySQL Database
	*/

	// Include the connect.php file so we have the authentication params
	include ('connect.php');

	// Connect to the database
	// connection String
	$mysqli = new mysqli($hostname, $username, $password, $database);

	/* check connection */
	if (mysqli_connect_errno())
		{
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
		}

	 // get data and store in a json array
	$pagenum = $_GET['pagenum'];
	$pagesize = $_GET['pagesize'];
	$start = $pagenum * $pagesize;
	$query = "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks LIMIT ?,?";
	$result = $mysqli->prepare($query);
	$result->bind_param('ii', $start, $pagesize);
	$filterquery = "";
	// filter data.
	if (isset($_GET['filterscount']))
		{
		$filterscount = $_GET['filterscount'];
		if ($filterscount > 0)
			{
			$where = " WHERE (";
			$tmpdatafield = "";
			$tmpfilteroperator = "";
			$valuesPrep = "";
			$value = [];
			for ($i = 0; $i < $filterscount; $i++)
				{
				// get the filter's value.
				$filtervalue = $_GET["filtervalue" . $i];
				// get the filter's condition.
				$filtercondition = $_GET["filtercondition" . $i];
				// get the filter's column.
				$filterdatafield = $_GET["filterdatafield" . $i];
				// get the filter's operator.
				$filteroperator = $_GET["filteroperator" . $i];
				if ($tmpdatafield == "")
					{
					$tmpdatafield = $filterdatafield;
					}
				  else if ($tmpdatafield <> $filterdatafield)
					{
					$where.= ") AND (";
					}
				  else if ($tmpdatafield == $filterdatafield)
					{
					if ($tmpfilteroperator == 0)
						{
						$where.= " AND ";
						}
					  else $where.= " OR ";
					}
				// build the "WHERE" clause depending on the filter's condition, value and datafield.
				switch ($filtercondition)
					{
				case "CONTAINS":
					$condition = " LIKE ";
					$value[0][$i] = "%{$filtervalue}%";
					$values[] = & $value[0][$i];
					break;

				case "DOES_NOT_CONTAIN":
					$condition = " NOT LIKE ";
					$value[1][$i] = "%{$filtervalue}%";
					$values[] = & $value[1][$i];
					break;

				case "EQUAL":
					$condition = " = ";
					$value[2][$i] = $filtervalue;
					$values[] = & $value[2][$i];
					break;

				case "NOT_EQUAL":
					$condition = " <> ";
					$value[3][$i] = $filtervalue;
					$values[] = & $value[3][$i];
					break;

				case "GREATER_THAN":
					$condition = " > ";
					$value[4][$i] = $filtervalue;
					$values[] = & $value[4][$i];
					break;

				case "LESS_THAN":
					$condition = " < ";
					$value[5][$i] = $filtervalue;
					$values[] = & $value[5][$i];
					break;

				case "GREATER_THAN_OR_EQUAL":
					$condition = " >= ";
					$value[6][$i] = $filtervalue;
					$values[] = & $value[6][$i];
					break;

				case "LESS_THAN_OR_EQUAL":
					$condition = " <= ";
					$value[7][$i] = $filtervalue;
					$values[] = & $value[7][$i];
					break;

				case "STARTS_WITH":
					$condition = " LIKE ";
					$value[8][$i] = "{$filtervalue}%";
					$values[] = & $value[8][$i];
					break;

				case "ENDS_WITH":
					$condition = " LIKE ";
					$value[9][$i] = "%{$filtervalue}";
					$values[] = & $value[9][$i];
					break;

				case "NULL":
					$condition = " IS NULL ";
					$value[10][$i] = "%{$filtervalue}%";
					$values[] = & $value[10][$i];
					break;

				case "NOT_NULL":
					$condition = " IS NOT NULL ";
					$value[11][$i] = "%{$filtervalue}%";
					$values[] = & $value[11][$i];
					break;
					}
				$where.= " " . $filterdatafield . $condition . "? ";
				$valuesPrep = $valuesPrep . "s";
				if ($i == $filterscount - 1)
					{
					$where.= ")";
					}
				$tmpfilteroperator = $filteroperator;
				$tmpdatafield = $filterdatafield;
				}
			$filterquery.= "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks " . $where;
			// build the query.
			$valuesPrep = $valuesPrep . "ii";
			$values[] = & $start;
			$values[] = & $pagesize;
			$query = "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks " . $where . " LIMIT ?, ?";
			$result = $mysqli->prepare($query);
			call_user_func_array(array(
				$result,
				"bind_param"
			) , array_merge(array(
				$valuesPrep
			) , $values));
			}
		}
	if (isset($_GET['sortdatafield']))
		{
		$sortfield = $_GET['sortdatafield'];
		$sortorder = $_GET['sortorder'];
		if ($sortorder != '')
			{
			if ($_GET['filterscount'] == 0)
				{
				if ($sortorder == "desc")
					{
					$query = "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks ORDER BY" . " " . $sortfield . " DESC LIMIT ?, ?";
					}
				  else if ($sortorder == "asc")
					{
					$query = "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks ORDER BY" . " " . $sortfield . " ASC LIMIT ?, ?";
					}
				$result = $mysqli->prepare($query);
				$result->bind_param('ii', $start, $pagesize);
				}
			  else
				{
				if ($sortorder == "desc")
					{
					$filterquery.= " ORDER BY " . $sortfield . " DESC LIMIT ?, ?";
					}
				  else if ($sortorder == "asc")
					{
					$filterquery.= " ORDER BY " . $sortfield . " ASC LIMIT ?, ?";
					}
				// build the query.
				$query = $filterquery;
				$result = $mysqli->prepare($query);
				call_user_func_array(array(
					$result,
					"bind_param"
				) , array_merge(array(
					$valuesPrep
				) , $values));
				}
			}
		}
	$result->execute();

	/* bind result variables */
	$result->bind_result($id, $Name, $Description, $Style, $ABV, $IBU, $SRM, $Grains, $Hops, $Brewery, $Price, $URL, $Rating, $Reviews, $Updated);

	/* fetch values */
	while ($result->fetch()) {
			$beers[] = array(
				'id' => $id,
				'Name' => utf8_encode($Name),
				'Description' => utf8_encode($Description),
				'Style' => utf8_encode($Style),
				'ABV' => $ABV,
				'IBU' => $IBU,
				'SRM' => $SRM,
				'Grains' => utf8_encode($Grains),
				'Hops' => utf8_encode($Hops),
				'Brewery' => utf8_encode($Brewery),
				'Price' => utf8_encode($Price),
				'URL' => $URL,
				'Rating' => $Rating,
				'Reviews' => $Reviews,
				'Updated' => date('m/d/Y', strtotime($Updated))
			);
	}

	$result = $mysqli->prepare("SELECT FOUND_ROWS()");
	$result->execute();
	$result->bind_result($total_rows);
	$result->fetch();
	$data[] = array(
		'TotalRows' => $total_rows,
		'Rows' => $beers
	);

	echo json_encode($data);

	$result->close();

	/* close connection */
	$mysqli->close();
?>
