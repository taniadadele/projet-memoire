<?php
/***** Chargement d'un paramètre *****/
function getParam($code, $IDuser = 0)
{
	global $mysql_link;
	$val = '';
	$query  = "select _valeur from parametre ";
	$query .= "where _code = '$code' ";
	$query .= "and _IDuser = $IDuser limit 1";

	$req = mysqli_query($mysql_link, $query);
	while($data = mysqli_fetch_assoc($req))
  {
		$val = $data['_valeur'];
  }

	return $val;
}

/***** Mise à jour d'un paramètre *****/
function setParam($code, $valeur = "", $IDuser = 0)
{
	global $mysql_link;
	if($code != "")
	{
		$query  = "select _valeur from parametre ";
		$query .= "where _code = '$code' ";
		$query .= "and _IDuser = $IDuser limit 1";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

		if(mysqli_num_rows($req) > 0) // Si existe alors mise à jour
		{
			$query  = "update parametre set _valeur = '$valeur' ";
			$query .= "where _code = '$code' ";
			$query .= "and _IDuser = $IDuser ";
			$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
		}
		else // Sinon création
		{
			$query  = "insert into parametre values('$code', '$valeur', $IDuser, '') ";
			$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
		}
	}
}







/***** Chargement d'un paramètre utilisateur *****/
function getUserParam($IDuser = 0, $code)
{
	global $mysql_link;
	$val = '';
	$query  = "select _valeur from user_parametre ";
	$query .= "where _code = '$code' ";
	$query .= "and _IDuser = $IDuser limit 1";

	$req = mysqli_query($mysql_link, $query);
	while($data = mysqli_fetch_assoc($req))
  {
		$val = $data['_valeur'];
  }

	return $val;
}



/***** Mise à jour d'un paramètre utilisateur *****/
function setUserParam($IDuser = 0, $code, $valeur = '', $comm = '')
{
	global $mysql_link;
	if($code != "")
	{
		$query  = "select _valeur from user_parametre ";
		$query .= "where _code = '$code' ";
		$query .= "and _IDuser = $IDuser limit 1";
		$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));

		if(mysqli_num_rows($req) > 0) // Si existe alors mise à jour
		{
			$query  = "update user_parametre set _valeur = '".addslashes($valeur)."', _comm = '".addslashes($comm)."' ";
			$query .= "where _code = '$code' ";
			$query .= "and _IDuser = $IDuser ";
			$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
		}
		else // Sinon création
		{
			$query  = "insert into user_parametre values(NULL, '$IDuser', '".addslashes($code)."', '".addslashes($valeur)."', '".addslashes($comm)."') ";
			$req = mysqli_query($mysql_link, $query) or die('Erreur SQL !<br>'.$query.'<br>'.mysqli_error($mysql_link));
		}
	}
}
?>
