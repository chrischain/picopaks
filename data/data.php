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

	// connect to the database
	$mysqli = new mysqli($hostname, $username, $password, $database);

	// check connection
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	// get data and store in a json array
	$query = "SELECT SQL_CALC_FOUND_ROWS id, Name, Description, Style, ABV, IBU, SRM, Grains, Hops, Brewery, Price, URL, Rating, Reviews, Updated FROM picopaks";
	$result = $mysqli->prepare($query);
	$filterquery = "";

	$result->execute();

	// bind result variables
	$result->bind_result($id, $Name, $Description, $Style, $ABV, $IBU, $SRM, $Grains, $Hops, $Brewery, $Price, $URL, $Rating, $Reviews, $Updated);

	// fetch values
	while ($result->fetch()) {
			$beers[] = array(
				'id' => $id,
				'Name' => htmlentities($Name),
				'Description' => htmlentities($Description),
				'Style' => htmlentities($Style),
				'ABV' => $ABV,
				'IBU' => $IBU,
				'SRM' => $SRM,
				'Grains' => htmlentities($Grains),
				'Hops' => htmlentities($Hops),
				'Brewery' => htmlentities($Brewery),
				'Price' => htmlentities($Price),
				'URL' => $URL,
				'Rating' => $Rating,
				'Reviews' => $Reviews,
				'Updated' => date('m/d/Y', strtotime($Updated))
			);
	}

	// build output
	$result = $mysqli->prepare("SELECT FOUND_ROWS()");
	$result->execute();
	$result->bind_result($total_rows);
	$result->fetch();
	$data[] = array(
		'TotalRows' => $total_rows,
		'Rows' => $beers
	);

	// encode into JSON
	echo json_encode($data);

	// clean up
	$result->close();
	$mysqli->close();
?>
