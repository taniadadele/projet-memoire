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
?>

<?php
/*
 *		module   : typo.php
 *		projet   : définition des messages
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 30/03/2007
 *		modif    : 
 *
 */

//---------------------------------------------------------------------------
static	$message = Array(

"l'ENT « clef en main » Libre et gratuit",
"fermer cette fenêtre",
"
<h1>
Les raccourcis typographiques
</h1>

<p>
Pour faciliter la mise en page des documents, le système propose un certain nombre de « raccourcis » destinés :<br/>
-> à simplifier l'utilisation par des utilisateurs ne connaissant pas le HTML ;<br/>
-> à faciliter le traitement automatique de la mise en page.
</p>

<p>
Ainsi, vous pouvez naturellement utiliser du code HTML dans vos documents, mais nous vous conseillons d'utiliser de préférence ces quelques raccourcis, plus simples à mémoriser, et permettant surtout quelques manipulations automatiques par le système.
</p>

<h2>
-> Fabriquer des listes ou des énumérations
</h2>

<p>
On peut fabriquer des listes, il suffit de revenir à la ligne et de commencer la nouvelle ligne avec un tiret (« - ») suivi d'un des caractères suivants :
</p>

-# pour une numérotation automatique. <br/>
-> pour la puce graphique <br/>
-\$ pour la puce graphique <br/>
-§ pour la puce graphique <br/>
-* pour la puce graphique <br/>
-. pour la puce graphique <br/>
-: pour la puce graphique <br/>

<p>
Par exemple, <br/>
-# Linux est gratuit <br/>
-# Linux est Open Source <br/>
sera affiché ainsi : <br/>
1. Linux est gratuit <br/>
2. Linux est Open Source <br/>
</p>

<h2>
-> Gras et italique
</h2>

<p>
On indique simplement du texte en italique en le plaçant entre des accolades simples : « ...du texte {en italique} en... ».
</p>

<p>
On indique du texte en gras en le plaçant entre des accolades doubles : « ...du texte {{en gras}} en... ».
</p>

<p>
On indique du texte en gras italique en le plaçant entre des accolades triples : « ...du texte {{{en gras italique}}} en... ».
</p>

<h2>
-> Trait de séparation horizontal
</h2>

<p>
Il est très simple d'insérer un trait de séparation horizontal sur toute la largeur du texte : il suffit de placer une ligne ne contenant qu'une succession de quatre, trois ou deux tirets, ainsi :
</p>

----
<hr/>

---
<hr style=\'width: 75%;\' />
-- 
<hr style=\'width: 50%;\' />

<h2>
-> Tableaux
</h2>

<p>
Pour réaliser des tableaux, il suffit de faire des lignes dont les « cases » sont séparées par le symbole « | » (pipe, un trait vertical), lignes commençant et se terminant par des traits verticaux.
</p>

<p>
se code ainsi :
</p>

| {{Nom}} | {{Prénom}} | {{Age}} | <br/>
| Marso | Ben | 23 ans | <br/>
| Capitaine | | non connu | <br/>
| Philant | Philippe | 46 ans | <br/>
| Cadoc | Bébé | 4 mois |

<h2>
-> Titre
</h2>

<p>
Par défaut, la première ligne qui débute votre document est prise comme titre principal. Pour indiquer un titre, il suffit de placer le caractère  @  en début de ligne.
</p>

Ainsi,  @mon titre , deviendra :
<h3>mon titre</h3>
<hr/>

<h2>
-> Paragraphes
</h2>

<p>
Les paragraphes permettent de séparer des parties distinctes de votre document et de générer automatiquement un sommaire. L'éditeur gère 2 niveaux de paragraphe que l'on représente de la façon suivante :
</p>

==paragraphe niveau 1== <br/>
===paragraphe niveau 2=== <br/>
qui s'affichent respectivement :
<h2>paragraphe niveau 1</h2>
<hr/>
<h3>paragraphe niveau 2</h3>
<hr/>

<h2>
-> Les liens hypertextes
</h2>

<p>
On fabriquera facilement un lien hypertexte avec le code suivant : « Prométhée a été développé sous [licence GPL->http://www.april.org/]. » devient « Prométhée a été développé sous licence GPL. ».
</p>

De même pour une adresse email (« [->infos@april.org] »)...

Ou un lien qui renvoie à un autre document collaboratif (« la [[licence GPL]] »)...

<p>
Pour faire apparaître un commentaire sur le survol de la souris, il suffit de séparer votre commentaire de l'adresse par le caractère |. Exemple : [licence GPL->http://www.april.org/|General Public License].
</p>

<h2>
-> Notes de bas de page
</h2>

<p>
Une note de bas de page est, habituellement, signalée par un numéro placé à l'intérieur du texte, numéro repris en bas de page et proposant un complément d'information.
</p>

<p>
Une note de bas de page est indiquée entre crochets avec des parenthèses : « Une note[(*)Voici un complément d'information.] de bas de page. » sera affiché sous la forme : « Une note [*] de bas de page. »
</p>

Des notes non automatiques

<p>
Dans la plupart des cas, le système de notes automatiques indiqué ci-dessus suffit amplement. Cependant, vous pouvez gérer les notes d'une manière non automatique.
</p>

Par exemple : « Vous pouvez utiliser les notes numérotées automatiques[() En n'indiquant rien entre parenthèses.], <br/>
-> mais aussi forcer la numérotation de la note[(23) En indiquant le numéro de la note entre parenthèses.], <br/>
-> utiliser des notes sous forme d'astérisques [(*) En plaçant simplement une astérisque entre parenthèses.], <br/>
-> donner un nom (en toutes lettres) à une note[(Rab) François Rabelais.];

<p>
Ce qui donne :
</p>

« Vous pouvez utiliser les notes numérotées automatiques [3], <br/>
 mais aussi forcer la numérotation de la note [23], <br/>
 utiliser des notes sous forme d'astérisques [*], <br/>
 donner un nom (en toutes lettres) à une note [Rab] ;

<hr width=\'30%\' align=\'left\' />

<p class=\'small\'>
[3] En n'indiquant rien entre parenthèses. <br/>
[23] En indiquant le numéro de la note entre parenthèses. <br/>
[*] En plaçant simplement une astérisque entre parenthèses. <br/>
[Rab] François Rabelais.
</p>
"

);
//---------------------------------------------------------------------------
?>