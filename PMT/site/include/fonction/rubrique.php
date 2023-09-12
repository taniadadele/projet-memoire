<?php
/***** Chargement des rubriques d'une table *****/
function displayRubriqueTable($table, $ID = 0, $lang = "FR")
{
	global $mysql_link;
	$val = Array();
	$query  = "SELECT r._IDrubrique 'IDrubrique', r._type 'type', r._libelle 'librub', v._valeur 'libval', ";
	$query .= " v._code 'libcode', r._attr 'rubattr', d._valeur 'selected', v._valeur 'default' ";
	$query .= "FROM rubrique r ";
	$query .= "LEFT JOIN rubrique_valeur v ON r._IDrubrique = v._IDrubrique AND v._lang = '$lang' ";
	$query .= "LEFT JOIN rubrique_data d ON r._IDrubrique = d._IDrubrique AND r._lang = '$lang' AND (d._IDdata = $ID OR d._IDdata IS NULL) ";
	$query .= "WHERE r._table = '$table' ";
	$query .= "AND r._lang = '$lang' ";
	$query .= "ORDER BY r._ordre ";
	$req = mysqli_query($mysql_link, $query);
	// return $query;

	while($data = mysqli_fetch_assoc($req))
    {
		switch ($data['type']) {
			case "chaine":
				$val[$data["IDrubrique"]]["type"] = $data["type"];
				$val[$data["IDrubrique"]]["rubrique"] = $data["librub"];
				$val[$data["IDrubrique"]]["valeur"][] = $data["selected"];
				if($data["rubattr"] != "")
				{
					$attr = new SimpleXMLElement($data["rubattr"]);
					$val[$data["IDrubrique"]]["default"] = $attr->defaut;
				}
				break;
			case "liste":
				$val[$data["IDrubrique"]]["type"] = $data["type"];
				$val[$data["IDrubrique"]]["rubrique"] = $data["librub"];
				$val[$data["IDrubrique"]]["valeur"][$data["libcode"]] = $data["libval"];
				$val[$data["IDrubrique"]]["selected"] = $data["selected"];
				if($data["rubattr"] != "")
				{
					$attr = new SimpleXMLElement($data["rubattr"]);
					$val[$data["IDrubrique"]]["default"] = $attr->defaut;
				}
				break;
		}
    }

	return $val;
}


/***** Chargement d'UNE rubrique d'une table *****/
function displayOneRubriqueTable($table, $IDrubrique, $ID = 0, $lang = "FR")
{
	global $mysql_link;
	$val = Array();
	$query  = "SELECT r._IDrubrique 'IDrubrique', r._type 'type', r._libelle 'librub', v._valeur 'libval', ";
	$query .= " v._code 'libcode', r._attr 'rubattr', d._valeur 'selected', v._valeur 'default' ";
	$query .= "FROM rubrique r ";
	$query .= "LEFT JOIN rubrique_valeur v ON r._IDrubrique = v._IDrubrique AND v._lang = '$lang' ";
	$query .= "LEFT JOIN rubrique_data d ON r._IDrubrique = d._IDrubrique AND r._lang = '$lang' AND (d._IDdata = $ID OR d._IDdata IS NULL) ";
	$query .= "WHERE r._table = '$table' ";
	$query .= "AND r._IDrubrique = '$IDrubrique' ";
	$query .= "AND r._lang = '$lang' ";
	$query .= "ORDER BY r._ordre ";
	$req = mysqli_query($mysql_link, $query);
	// return $query;

	while($data = mysqli_fetch_assoc($req))
    {
		switch ($data['type']) {
			case "chaine":
				$val[$data["IDrubrique"]]["type"] = $data["type"];
				$val[$data["IDrubrique"]]["rubrique"] = $data["librub"];
				$val[$data["IDrubrique"]]["valeur"][] = $data["selected"];
				if($data["rubattr"] != "")
				{
					$attr = new SimpleXMLElement($data["rubattr"]);
					$val[$data["IDrubrique"]]["default"] = $attr->defaut;
				}
				break;
			case "liste":
				$val[$data["IDrubrique"]]["type"] = $data["type"];
				$val[$data["IDrubrique"]]["rubrique"] = $data["librub"];
				$val[$data["IDrubrique"]]["valeur"][$data["libcode"]] = $data["libval"];
				$val[$data["IDrubrique"]]["selected"] = $data["selected"];
				if($data["rubattr"] != "")
				{
					$attr = new SimpleXMLElement($data["rubattr"]);
					$val[$data["IDrubrique"]]["default"] = $attr->defaut;
				}
				break;
		}
    }

	return $val;
}

/***** Chargement d'une rubrique d'une table *****/
function getRubriqueValeur($table, $IDrubrique, $ID = 0, $lang = "FR")
{
	global $mysql_link;
	$query  = "SELECT r._IDrubrique 'IDrubrique', r._type 'type', r._libelle 'librub', v._valeur 'libval', ";
	$query .= " v._code 'libcode', r._attr 'rubattr', d._valeur 'selected', v._valeur 'default' ";
	$query .= "FROM rubrique r ";
	$query .= "LEFT JOIN rubrique_valeur v ON r._IDrubrique = v._IDrubrique AND v._lang = '$lang' ";
	$query .= "LEFT JOIN rubrique_data d ON r._IDrubrique = d._IDrubrique AND r._lang = '$lang' ";
	$query .= "WHERE (d._IDdata = $ID OR d._IDdata IS NULL) ";
	$query .= "AND r._table = '$table' ";
	$query .= "AND d._IDdata = '$ID' ";
	$query .= "AND d._IDrubrique = '$IDrubrique' ";

	$req = mysqli_query($mysql_link, $query);

	while($data = mysqli_fetch_assoc($req))
    {
		switch ($data['type']) {
			case "chaine":
				$val = $data["selected"];
				break;
			case "liste":
				if($data["selected"] == $data["libcode"])
				{
					$val = $data["libval"];
				}
				break;
		}
    }

	return @$val;
}

/***** Mise a jour de la valeur d'une rubrique *****/
function setRubriqueVal($table = "user", $IDrubrique, $valeur, $ID = 0, $lang = "FR")
{
	global $mysql_link;
	$query  = "SELECT * ";
	$query .= "FROM rubrique r ";
	$query .= "LEFT JOIN rubrique_valeur v ON r._IDrubrique = v._IDrubrique AND v._lang = '$lang' ";
	$query .= "LEFT JOIN rubrique_data d ON r._IDrubrique = d._IDrubrique AND r._lang = '$lang' AND (d._IDdata = $ID OR d._IDdata IS NULL) ";
	$query .= "WHERE r._table = '$table' ";
	$query .= "AND d._IDrubrique = $IDrubrique ";
	$query .= "ORDER BY r._IDrubrique, v._code ";
	$req = mysqli_query($mysql_link, $query);

	if(mysqli_num_rows($req) > 0) // Si existe alors mise Ã  jour
	{
		while($data = mysqli_fetch_assoc($req))
		{
			$query  = "UPDATE rubrique_data SET _valeur = '$valeur' ";
			$query .= "WHERE _IDrubrique = $IDrubrique ";
			$query .= "AND _IDdata = $ID ";
			$req2 = mysqli_query($mysql_link, $query);
		}
	}
	else // Sinon crÃ©ation
	{
		$query  = "INSERT INTO rubrique_data VALUES('$IDrubrique', '$ID', '$valeur') ";
		$req2 = mysqli_query($mysql_link, $query);
	}
}
?>
