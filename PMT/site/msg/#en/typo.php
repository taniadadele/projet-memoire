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
 *		projet   : messages definition
 *
 *		version  : 1.0
 *		auteur   : laporte
 *		creation : 30/03/2007
 *		modif    :
 *
 */

//---------------------------------------------------------------------------
static	$message = Array(

"the « ready to use » open source VLE 100% free",
"close window",
"
<h1>
Typographical short cuts
</h1>

<p>
To ease documents page-setting, the system proposes a certain number of \"short cuts\" intended to:<br/>
-> simplify the use by users not knowing the HTML; <br/>
-> ease the automatic treatment of page-setting.
</p>

<p>
Thus, you can naturally use HTML code in your documents, but we advise you to preferably use these short cuts, easier to memorize, and most of all allowing some automatic handling by the system.
</p>

<h2>
- > Make lists or enumerations
</h2>

<p>
To make lists, you just have to return to the line and begin the new line with an indent (\"-\") followed by one of the following characters:
</p>

-# for an automatic numbering. <br/>
-> for the graphics chip <br/>
- \ $ for the graphics chip <br/>
- § for the graphics chip <br/>
- * for the graphics chip <br/>
-. for the graphics chip <br/>
-: for the graphics chip <br/>

<p>
For example, <br/>
- # Linux is free <br/>
- # Linux is Open Source <br/>
will be posted as follows: <br/>
1. Linux is free <br/>
2. Linux is Open Source <br/>
</p>

<h2>
-> Bold and Italic
</h2>

<p>
Just indicates text in italic by placing it between simple accolades: \"... some text {in italic} in... \".
</p>

<p>
Indicate text in bold by placing it between double accolades: \"... some text {{in bold}} in... \".
</p>

<p>
Indicate text in Italic bold by placing it between triple accolades: \"... some text {{{in Italic bold}}} in... \".
</p>

<h2>
-> horizontal separation line
</h2>

<p>
It is very simple to insert an horizontal separation dash over all the text width: Just place a line containing only one succession of four, three or two indents, as follows:
</p>

----
<hr/>

---
<hr style=\"width: 75%;\"/>
--
<hr style=\"width: 50%;\"/>

<h2>
-> Tables
</h2>

<p>
To make tables, just make lines from which the \"boxes\" are separated by the symbol \"|\" (pipe, a vertical indent), lines starting and ending by vertical indents.
</p>

<p>
coded as follows:
</p>

| {{Name}} | {{First name}} | {{Age}} | <br/>
| Marso | Ben | 23 years | <br/>
| Not known | | Captain | <br/>
| Philant | Philippe | 46 years | <br/>
| Cadoc | Baby | 4 months |

<h2>
-> Title
</h2>

<p>
By default, the first line beginning your document is taken as main title. To indicate a title, just place the character @ at the beginning of line.
</p>

Thus, @my title, will become:
<h3>my title</h3>
<hr/>

<h2>
-> Paragraphs
</h2>

<p>
The paragraphs make it possible to separate distinct parts from your document and to generate a synopsis automatically. The editor manages 2 levels of paragraph which are represented in the following way:
</p>

==paragraphe level 1== <br/>
===paragraphe level 2=== <br/>
which display themselves respectively:
<h2>paragraphe level 1</h2>
<hr/>
<h3>paragraphe level 2</h3>
<hr/>

<h2>
-> hypertexts links
</h2>

<p>
You can make easily an hypertext link with the following code: \"Prométhée was developed under [licence GPL-> http://www.april.org/]. \" becomes \"Prométhée was developed under licence LPG. \".
</p>

In the same way for an email address (\"[- >infos@april.org]\")...

Or a link to another collaborative document (\" [[licence LPG]]\")...

<p>
To reveal a comment on the overflight of the mouse, just separate your comment from the address by the character |. Example: [licence GPL-> http://www.april.org/ |General Public License].
</p>

<h2>
- > Footnotes
</h2>

<p>
A footnote, usually, is announced by a number placed inside the text, number shown at the foot of the page and proposing a further information.
</p>

<p>
A footnote is indicated between hooks with brackets: \"A footnote [(*) Here is a further information.] . \" will be displayed in the form: \"A footnote [*] . \"
</p>

Nonautomatic notes

<p>
In most of the cases, the system of automatic notes indicated above is far enough. However, you can manage the notes in a nonautomatic way.
</p>

For example: \"You can use the automatic numbered notes [() By not indicating anything between the brackets.], <br/>
-> but also to force the numbering of the note [(23) By indicating the number of the note between the brackets.], <br/>
-> use notes in the form of asterisks [(*) By just placing an asterisk between the brackets.], <br/>
-> give a name (in full letters) to a note [(Rab) François Rabelais.];

<p>
Which gives:
</p>

\"You can use the automatic numbered notes [3], <br/>
but also force the numbering of the note [23], <br/>
use notes in the form of asterisks [*], <br/>
give a name (in full letters) to a note [Rab];

<hr width=\"30%\" align=\"left\"/>

<p class=\"small\">
[3] By not indicating anything between the brackets. <br/>
[23] By indicating the number of the note between the brackets. <br/>
[*] By just placing an asterisk between the brackets. <br/>
[Rab] François Rabelais.
</p>
"

);
//---------------------------------------------------------------------------
?>
