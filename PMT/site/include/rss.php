<?php
/*-----------------------------------------------------------------------*
   Copyright (c) 2007 by Dominique Laporte(C-E-D@wanadoo.fr)

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


/*
 *		module   : rss.php
 *		projet   : classe objet pour flux rss
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 18/09/05
 *		modif    :  
 */


//---------------------------------------------------------------------------
class rss
{
	var	$charset;
	var	$title;
	var	$link;
	var	$desc;
	var	$lang;
	var	$admin;

	// construteur --------------------------------------------------------
	function rss($title, $link, $desc, $lang, $admin, $charset = "iso-8859-1")
	{
		$this->charset = $charset;
		$this->title   = htmlspecialchars($title);
		$this->link    = htmlspecialchars($link);
		$this->desc    = htmlspecialchars($desc);
		$this->lang    = $lang;
		$this->admin   = htmlspecialchars($admin);
	}
	// suppression des \ --------------------------------------------------
	function remove_magic_quotes($array)
	{
		/*
		 * fonction :	nettoyage des \ dans une chaîne
		 * in :		$array : tableau de valeurs
		 */

		// On n'exécute la boucle que si nécessaire
		if ( $array AND get_magic_quotes_gpc() == 1 )
			foreach($array as $key => $val) {
				// Si c'est un array, recursion de la fonction, sinon suppression des slashes
				if ( is_array($val) )
					remove_magic_quotes($array[$key]);
				else
					if ( is_string($val) )
						$array[$key] = stripslashes($val);
				}

		return $array;
	}
	// lecture du flux rss ------------------------------------------------
	function read($fichier, $objets)
	{
		// on lit tout le fichier
		if ( ($chaine = @implode("", @file($fichier))) ) {

			// on découpe la chaine obtenue en items
			$item = preg_split("/<\/?"."item".">/", $chaine);

			// pour chaque item
			for ($i=1; $i < sizeof($item) - 1; $i += 2)

				// on lit chaque objet de l'item
				foreach ($objets as $objet) {

					// on découpe la chaine pour obtenir le contenu de l'objet
					$tmp = preg_split("/<\/?".$objet.">/", $item[$i]);

					// on ajoute le contenu de l'objet au tableau resultat
					$resultat[$i-1][] = @$tmp[1];
				}

			// on retourne le tableau resultat
			return $resultat;
			}
	}
	// creation du flux rss ------------------------------------------------
	function create($result)
	{
	/*
		== Liste de tous les éléments pouvant se trouver dans la balise <channel> ==
		title 		Titre du channel
		link 			URL du site contenant le channel
		description 	Description du channel
		language 		Langue du channel
		copyright 		Info sur le copyright du channel
		managingEditor 	Mail de la personne responsable du contenu
		webMaster 		Mail du webmaster
		pubDate 		Date de publication
		lastBuildDate 	Date de la dernière publication
		category 		Catégorie à laquelle le channel appartient
		generator 		Programme utilisé pour générer le channel
		docs 			Lien vers la documentation du format utilisé dans le fichier RSS
		cloud 		Permet à un programme de s'enregistrer pour être notifié des modifications de ce channel
		ttl 			Time to live, avant le prochain rafraîchissement
		image 		Image affichée avec le channel
		rating 		note PICS
		textInput 		Ajouter une zone de saisie de texte
		skipHours 		Heures que les agrégateurs peuvent ignorer
		skipDays 		Jours que les agrégateurs peuvent ignorer

		== Liste de tous les éléments pouvant se trouver dans la balise <item> ==
		title 		Titre de l'item
		link 			URL de l'item
		description 	Description de l'item
		author 		Mail de l'auteur de l'item
		category 		Catégorie à laquelle l'item appartient
		comments 		Lien vers une page de commentaires sur l'item
		enclosure 		Objet media attaché à l'item
		guid 			Texte qui identifie de manière unique cet item
		pubDate 		Date de publication
		source 		Channel auquel l'item appartient
	*/

		// édition du début du fichier XML
		$xml  = "<?xml version=\"1.0\" encoding=\"$this->charset\" ?>";
		$xml .= "<rss version=\"2.0\">";
		$xml .= "<channel>"; 
		$xml .= ( strlen($this->title) ) ? "<title>$this->title</title>" : "" ;
		$xml .= ( strlen($this->link) ) ? "<link>$this->link</link>" : "" ;
		$xml .= ( strlen($this->desc) ) ? "<description>$this->desc</description>" : "" ;
		$xml .= ( strlen($this->lang) ) ? "<language>$this->lang</language>" : "" ;
		$xml .= ( strlen($this->admin) ) ? "<webMaster>$this->admin</webMaster>" : "" ;
		$xml .= "<ttl>3600</ttl>";

		// extraction des informations et ajout au contenu
		while ( ($tab = $this->remove_magic_quotes(mysqli_fetch_array($result))) ){   
			$title  = htmlspecialchars($tab['_title']);
			$link   = htmlspecialchars($tab['_url']);
			$text   = htmlspecialchars(str_replace("\n", "<br/>", $tab['_text']));
			$author = htmlspecialchars($tab['_author']);
			$cat    = htmlspecialchars($tab['_category']);
			$guid   = $tab['_IDitem'];
			$date   = date("D, d M Y H:i:s", strtotime($tab['_date']));

			$xml .= "<item>";
			$xml .= ( strlen($title) ) ? "<title>$title</title>" : "" ;
			$xml .= ( strlen($link) ) ? "<link>$link</link>" : "" ;
			$xml .= ( strlen($text) ) ? "<description>$text</description>" : "" ;
			$xml .= ( strlen($author) ) ? "<author>$author</author>" : "" ;
			$xml .= ( strlen($cat) ) ? "<category>$cat</category>" : "" ;
			$xml .= ( strlen($guid) ) ? "<guid>$guid</guid>" : "" ;
			$xml .= "<pubDate>$date GMT</pubDate>"; 
			$xml .= "</item>";
			}

		// édition de la fin du fichier XML
		$xml .= "</channel>";
		$xml .= "</rss>";

		echo $xml;
	}
	// --------------------------------------------------------------------
}
//---------------------------------------------------------------------------
?>