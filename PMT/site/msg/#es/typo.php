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
 *		modif    : 12/08/2007 11:30:58 a.m. Fernando Cormenzana
 *
 */

//---------------------------------------------------------------------------
static	$message = Array(

"El Numéricos 'llave en mano' libre y gratuito",
"cerrar esta ventana",
"
<h1>
Los atajos tipográficos
</h1>

<p>
Para facilitar la diagramación de los documentos, el sistema propone un cierto número de 'atajos' destinados a:<br/>
-> simplificar la utilización para aquellos que no conozcan HTML;<br/>
-> facilitar el procesamiento automático de la diagramación.
</p>

<p>
Así, usted puede usar naturalmente el código HTML en sus documentos, pero lo aconsejamos usar preferentemente estos atajos de teclado, más sencillos de memorizar, y que permiten además algunas manipulaciones automáticas del sistema.
</p>

<h2>
-> Fabricar listas o enumeraciones
</h2>

<p>
Se pueden fabricar listas de manera muy sencilla comenzando cada nueva línea con un guión ('-') seguido de uno de los siguientes caracteres:
</p>

-# para una numeración automática <br/>
-> para la viñeta gráfica <br/>
-\$ para la viñeta gráfica <br/>
-§ para la viñeta gráfica <br/>
-* para la viñeta gráfica <br/>
-. para la viñeta gráfica <br/>
-: para la viñeta gráfica <br/>

<p>
Por ejemplo, <br/>
-# Linux es gratuito <br/>
-# Linux es Open Source <br/>
será desplegado así : <br/>
1. Linux es gratuito <br/>
2. Linux es Open Source <br/>
</p>

<h2>
-> Negrita e itálicas
</h2>

<p>
Para indicar que un texto aparecerá en itálicas se lo debe escribir entre corchetes simples: « ...texto {en itálicas} ... ».
</p>

<p>
Para indicar un texto en negrita se lo debe escribir entre corchetes dobles: « ...texto {{en negrita}} ... ».
</p>

<p>
Para indicar un texto en negrita e itálicas se lo debe escribrir entre corchetes triples: « ...texto {{{en negrita e itálicas}}} ... ».
</p>

<h2>
-> Línea de separación horizontal
</h2>

<p>
Para una línea que ocupe todo el ancho del texto alcanza con escribir una línea conteniendo 2, 3 o 4 guiones :
</p>

----
<hr/>

---
<hr style=\"width: 75%;\" />
-- 
<hr style=\"width: 50%;\" />

<h2>
-> Cuadros
</h2>

<p>
Para crear cuadros alcanza con con crear líneas en las que los 'casilleros' están separados por el símbolo « | » (pipe, un trazo vertical). Las líneas comienzan y terminan con trazos verticales.
</p>

<p>
se codifica así :
</p>

| {{Apellido}} | {{Nombre}} | {{Edad}} | <br/>
| Marso | Ben | 23 años | <br/>
| Capitán | | no conocido | <br/>
| Philant | Philippe | 46 años | <br/>
| Cadoc | Bebe | 4 meses |

<h2>
-> Título
</h2>

<p>
Por defecto, la primera línea de su documento es considerada el título principal. Para indicar un título alcanza con ubicar el carácter '@' el principio de la línea.
</p>

Así,  @mi título se escribirá:
<h3>mi título</h3>
<hr/>

<h2>
-> Parágrafos
</h2>

<p>
Los parágrafos permiten separar las distintas partes de su documento y generar automáticamente un sumario. l editor admite 2 niveles de parágrafos que se representarán de la sigueinte manera:
</p>

==parágrafo nivel 1== <br/>
===parágrafo nivel 2=== <br/>
que se desplegarán respectivamente:
<h2>parágrafo nivel 1</h2>
<hr/>
<h3>parágrafo nivel 2</h3>
<hr/>

<h2>
-> Los vínculos hipertexto
</h2>

<p>
Para definir un vínculo se empleará el siguiente código: « Prométhée a été développé sous [licence GPL->http://www.april.org/]. » se convierte en « Prométhée a été développé sous licence GPL. ».
</p>

Lo mismo para una dirección de correo electrónico (« [->infos@april.org] »)...

O un vínculo que remite a otro documento colaborativo (« la [[licencia GPL]] »)...

<p>
Para hacer aparecer un comentario al sobrevolar el ratón, alcanza con separar el comentario de la dirección con el carácter '|'.Ejemplo : [licence GPL->http://www.april.org/|General Public License].
</p>

<h2>
-> Notas de pie de página
</h2>

<p>
Una nota de pie de página es, en general, señalada por un número ubicado dentro del texto que se repite al pie de la página para proponer un complemento de información.
</p>

<p>
Una nota de pie de páginas se indica entre paréntesis rectos usando paréntesis curvos: « Una nota[(*)He aquí un complento de información.] de pie de página. » será desplegada bajo la forma: « Una nota [*] de pie de página. »
</p>

Notas no automáticas

<p>
En la mayor parte de los casos, el sistema de notas automáticas indicado anteriormente alcanza, pero puede también administrar sus notas de manera no automática.
</p>

Por ejemplo: « Puede usar las notas numeradas automáticamente [() no indicando nada entre paréntesis curvos.], <br/>
-> pero también forzar la numeración de la nota [(23) indicando el número entre paréntesis.], <br/>
-> usar las notas bajo forma de asteriscos [(*) indicando un asterisco entre paréntesis curvos.], <br/>
-> dar un nombre (con todas las letras) a una nota [(Rab) François Rabelais.];

<p>
Lo que da :
</p>

« Puede usar las notas numeradas automáticamente [3], <br/>
 pero también forzar la numeración de la nota [23], <br/>
 usar las notas bajo forma de asteriscos [*], <br/>
 dar un nombre (con todas las letras) a una nota [Rab] ;

<hr width=\"30%\" align=\"left\" />

<p class=\"small\">
[3] No indicando nada entre paréntesis. <br/>
[23] Indicando el número de la nota entre paréntesis. <br/>
[*] Indicando un asterisco entre paréntesis. <br/>
[Rab] François Rabelais.
</p>
"

);
//---------------------------------------------------------------------------
?>