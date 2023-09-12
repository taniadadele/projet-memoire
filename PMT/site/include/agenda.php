<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2008 by Dominique Laporte(C-E-D@wanadoo.fr)

   This file is part of Prométhée.

   Prométhée is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Prométhée is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Prométhée.  If not, see <http://www.gnu.org/licenses/>.
 *-----------------------------------------------------------------------*/
?>

<?php
/*
 *		module   : agenda.php
 *		projet   : classe objet agenda
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 11/11/08
 *		modif    :
 */


//---------------------------------------------------------------------------
class diary
{
	var	$IDdata;		// ID du répertoire parent
	var	$IDgroup;		// ID du répertoire parent

	// constructeur -------------------------------------------------------
	function __construct($IDgroup = 0, $IDdata = 0)
	{
	/*
	 * fonction :	constructeur
	 * in :		$IDdata, ID de l'agenda
	 * 			$IDgroup, ID du e-groupe
	 */

		$this->IDgroup = $IDgroup;
		$this->IDdata  = ( $IDdata ) ? $IDdata : sql_getunique("agenda") ;
	}
	// création agenda ---------------------------------------------------
	function create($subject, $text = "", $public = 0)
	{
		require $_SESSION["ROOTDIR"]."/globals.php";

		require $_SESSION["ROOTDIR"]."/msg/agenda.php";
		require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

		$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/agenda.php");

		$date   = date("Y-m-d H:i:s");

		$Query  = "insert into agenda ";
		$Query .= "values('$this->IDdata', '$this->IDgroup', '0', '0', '0', '$public', '$date', '".addslashes($subject)."', '".addslashes($text)."', 'O', 'N', 'N', 'N', 'N', 'O', 'N', '".$_SESSION["lang"]."')";

		$this->IDdata = mysqli_query($mysql_link, $Query) ? mysqli_insert_id($mysql_link) : 0 ;

		return $this->IDdata;
	}
	// édition du texte --------------------------------------------------
	function createUserDiary($IDuser)
	{
		require $_SESSION["ROOTDIR"]."/globals.php";

		require $_SESSION["ROOTDIR"]."/msg/agenda.php";
		require_once $_SESSION["ROOTDIR"]."/include/TMessage.php";

		$msg  = new TMessage($_SESSION["ROOTDIR"]."/msg/".$_SESSION["lang"]."/agenda.php");

		$date   = date("Y-m-d H:i:s");
		$titre  = $msg->read($AGENDA_PERSDIARY, getUserNameByID($IDuser)) ;
		$texte  = $msg->read($AGENDA_CREATEON, date2longfmt($date)) ;

		return $this->create($titre, $texte, $IDuser);
	}
	// les persmissions --------------------------------------------------
	function perms($IDmod, $IDgrprd = 0, $IDgrpwr = 0)
	{
		require $_SESSION["ROOTDIR"]."/globals.php";

		$Query  = "update agenda ";
		$Query .= "set _IDmod = '$IDmod', _IDgrpwr = '$IDgrpwr', _IDgrprd = '$IDgrprd' ";
		$Query .= "where _IDdata = '$this->IDdata' ";
		$Query .= "limit 1";

		return mysqli_query($mysql_link, $Query);
	}
	// test existence ----------------------------------------------------
	function exist($public, $titre = "")
	{
		require $_SESSION["ROOTDIR"]."/globals.php";

		$Query  = "select _IDdata from agenda ";
		$Query .= "where _visible = 'O' ";
		$Query .= "AND _lang = '".$_SESSION["lang"]."' ";
		$Query .= "AND _IDgroup = '$this->IDgroup' ";
		$Query .= ( $titre )
			? "AND _titre = '$titre' "
			: "AND _private = '$public' " ;
		$Query .= "order by _IDdata ";
		$Query .= "limit 1";

		$result = mysqli_query($mysql_link, $Query);
		$row    = ( $result ) ? mysqli_fetch_row($result) : 0 ;

		$this->IDdata = (int) $row[0];

		return $this->IDdata;
	}
	// insertion évènement -----------------------------------------------
	function write($subject, $start, $end, $text = "", $priority = "B")
	{
		require $_SESSION["ROOTDIR"]."/globals.php";

		$date   = date("Y-m-d H:i:s");

		$Query  = "insert into agenda_items ";
		$Query .= "values('', '$this->IDdata', '0', '".$_SESSION["CnxID"]."', '".$_SESSION["CnxIP"]."', '$date', '$date', '$date', '$priority', '$start', '$end', '$subject', '$text', '0', 'N')";

		return ( $this->IDdata )
			? (mysqli_query($mysql_link, $Query) ? mysqli_insert_id($mysql_link) : 0)
			: 0 ;
	}
}
//---------------------------------------------------------------------------
?>
