var id_en_cours = 0; var titre_en_cours = ""; var largeur_en_cours = ""; var hauteur_en_cours = "";

function closeWin(){
	 if (newWin != null){
		 if(!newWin.closed)	newWin.close();
	 }
}
function changePopup(lien){
	 if (id_en_cours == 0) {lien; return false;}
	 else popupCentrer('index.php?preaction=galerie&id_photo=' + id_en_cours + '&description=' + titre_en_cours, largeur_en_cours,  hauteur_en_cours, 1, 'toolbar=0,resizable=0,scrollbars=auto,status=0');return false;
}


var largacol=650;
function changeImage(chemin, id, desc, w, h, taille_ko, titre){
	 var image_tmp = new Image();
	 image_tmp.src = chemin;
	 if(w < 650)	{ var dimaffiche = w; }
	 else			{ var dimaffiche = largacol;}
	 window.document.getElementById('id_image').width = dimaffiche;
	 window.document.getElementById('div_titre').innerHTML = titre;
	 window.document.getElementById('div_dimension').innerHTML = w + " x " + h;
	 window.document.getElementById('div_taille').innerHTML = taille_ko;
	 window.document.getElementById('id_image').src = image_tmp.src;
	 window.document.getElementById('id_image').alt = titre;
	 id_en_cours = id;
	 titre_en_cours = titre;
	 largeur_en_cours = w;
	 hauteur_en_cours = h;
}


/* Fonction getElementsBySelector */
function getAllChildren(e) {
  // Returns all children of element. Workaround required for IE5/Windows. Ugh.
  return e.all ? e.all : e.getElementsByTagName('*');
}

document.getElementsBySelector = function(selector) {
  // Attempt to fail gracefully in lesser browsers
  if (!document.getElementsByTagName) {
    return new Array();
  }
  // Split selector in to tokens
  var tokens = selector.split(' ');
  var currentContext = new Array(document);
  for (var i = 0; i < tokens.length; i++) {
    token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
    if (token.indexOf('#') > -1) {
      // Token is an ID selector
      var bits = token.split('#');
      var tagName = bits[0];
      var id = bits[1];
      var element = document.getElementById(id);
      if (tagName && element.nodeName.toLowerCase() != tagName) {
        // tag with that ID not found, return false
        return new Array();
      }
      // Set currentContext to contain just this element
      currentContext = new Array(element);
      continue; // Skip to next token
    }
    if (token.indexOf('.') > -1) {
      // Token contains a class selector
      var bits = token.split('.');
      var tagName = bits[0];
      var className = bits[1];
      if (!tagName) {
        tagName = '*';
      }
      // Get elements matching tag, filter them for class selector
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (found[k].className && found[k].className.match(new RegExp('\\b'+className+'\\b'))) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      continue; // Skip to next token
    }
    // Code to deal with attribute selectors
    if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
      var tagName = RegExp.$1;
      var attrName = RegExp.$2;
      var attrOperator = RegExp.$3;
      var attrValue = RegExp.$4;
      if (!tagName) {
        tagName = '*';
      }
      // Grab all of the tagName elements within current context
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      var checkFunction; // This function will be used to filter the elements
      switch (attrOperator) {
        case '=': // Equality
          checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue); };
          break;
        case '~': // Match one of space seperated words 
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('\\b'+attrValue+'\\b'))); };
          break;
        case '|': // Match start with value followed by optional hyphen
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))); };
          break;
        case '^': // Match starts with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0); };
          break;
        case '$': // Match ends with value - fails with "Warning" in Opera 7
          checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
          break;
        case '*': // Match ends with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1); };
          break;
        default :
          // Just test for existence of attribute
          checkFunction = function(e) { return e.getAttribute(attrName); };
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (checkFunction(found[k])) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      // alert('Attribute Selector: '+tagName+' '+attrName+' '+attrOperator+' '+attrValue);
      continue; // Skip to next token
    }
    // If we get here, token is JUST an element (not a class or ID selector)
    tagName = token;
    var found = new Array;
    var foundCount = 0;
    for (var h = 0; h < currentContext.length; h++) {
      var elements = currentContext[h].getElementsByTagName(tagName);
      for (var j = 0; j < elements.length; j++) {
        found[foundCount++] = elements[j];
      }
    }
    currentContext = found;
  }
  return currentContext;
}

function getElementsByClassName(class_name)
{
  var all_obj,ret_obj=new Array(),j=0,teststr;

  if(document.all)all_obj=document.all;
  else if(document.getElementsByTagName && !document.all)
    all_obj=document.getElementsByTagName("*");

  for(i=0;i<all_obj.length;i++)
  {
    if(all_obj[i].className.indexOf(class_name)!=-1)
    {
      teststr=","+all_obj[i].className.split(" ").join(",")+",";
      if(teststr.indexOf(","+class_name+",")!=-1)
      {
        ret_obj[j]=all_obj[i];
        j++;
      }
    }
  }
  return ret_obj;
}


/* Augmenter / Diminuer la taille de la police */
var content;
var collec, i;
var initSize = 0;
var oldSize = '';
var pos;
var sizeInited = 0;
function setSize(size, id)
	{
	collec= getElementsByClassName(id);
	for(i=0; i<collec.length; i++)
		{
		content = collec[i];
		setSizeByObject(size, content);
		}
	}

function setSizeByObject(size, content)
	{
	oldSize = content.style.fontSize;
	if (oldSize == '') oldSize = '100%';
	pos = oldSize.indexOf('%');
	oldSize = oldSize.substring(0,pos)*1;
	size = size*1;
	if (((oldSize > 80) && (size < -1)) || ((oldSize < 200) && (size > 1))) 
		size = oldSize+size;
	else size = oldSize;
	content.style.fontSize = size + '%';
	}

function protected_mail(email)
	{
	var pattern = '!SPAM!';
	goodmail = email.replace(pattern, "@");
	window.open("mailto:"+goodmail);
	}


// Script d'ajustement de tableaux
function ajuste_image(img1, img2)
	{
	obj1 = document.getElementById('ajusteimg_ext_' + img1);
	obj2 = document.getElementById('ajusteimg_ext_' + img2);
	if (document.getElementById('ajusteimg_' + img1) != null && document.getElementById('ajusteimg_' + img2) != null)
		{
		y1 = obj1.offsetTop;
		y2 = obj2.offsetTop;
		newheight = Math.abs(y1-y2) + 1;
		if (y1>y2)
			document.getElementById('ajusteimg_' + img2).height=newheight;
		else
			document.getElementById('ajusteimg_' + img1).height=newheight;
		}
	}


// Scripts pour la galerie
var newWin = null;
function closeWin()
	{
	if (newWin != null)
		{
		if(!newWin.closed)
		newWin.close();
		}
	}

function popupCentrer(page, largeur, hauteur, close, options)
	{
	if (close == 1) closeWin();
	var top=(screen.height-hauteur)/2;
	var left=(screen.width-largeur)/2;
	  
	newWin = window.open(page,"popup","top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
	newWin.document.close();
	newWin.focus();
	}

function popupCentrerId(page, idp, largeur, hauteur, options)
	{
	var top=(screen.height-hauteur)/2;
	var left=(screen.width-largeur)/2;
	
	newWin = window.open(page, "page"+idp,"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
	newWin.document.close();
	newWin.focus();
	}
	
var ok = 0;

var Timer;
function moveLayerLeft(Sens, Pas, maxi)
	{
	if(document.getElementById)	Objet = document.getElementById("contenu");
	else				Objet = document.all["contenu"];
		
	if (Objet.style.left == "")	Objet.style.left = 0;
	
	if (maxi > 0)
		{
		if(parseInt(Objet.style.left) + (Pas*Sens) > 0)
			Objet.style.left = "0px";
		else if (parseInt(Objet.style.left) + (Pas*Sens) < (-Math.abs(maxi)))
			{
			var maxistring="-"+Math.abs(maxi)+"px";
			Objet.style.left = maxistring;
			}
		else
			Objet.style.left = (parseInt(Objet.style.left) + (Pas*Sens)) + "px";
		Timer = setTimeout("moveLayerLeft(" + Sens + ", " + Pas + ", " + maxi + ");", 30);
		}
	}

function moveLayerTop(Sens, Pas, maxi)
	{
	if(document.getElementById)	Objet = document.getElementById("contenu");
	else				Objet = document.all["contenu"];
		
	if (Objet.style.top == "")	Objet.style.top = 0;
	
	if (maxi > 0)
		{
		if(parseInt(Objet.style.top) + (Pas*Sens) > 0)
			Objet.style.top = "0px";
		else if (parseInt(Objet.style.top) + (Pas*Sens) < (-Math.abs(maxi)))
			{
			var maxistring="-"+Math.abs(maxi)+"px";
			Objet.style.top = maxistring;
			}
		else
			Objet.style.top = (parseInt(Objet.style.top) + (Pas*Sens)) + "px";
		Timer = setTimeout("moveLayerTop(" + Sens + ", " + Pas + ", " + maxi + ");", 30);
		}
	}
		
function stripslashes(ch)
	{
	return ch.replace (/(\\)([\\\'\"])/g, "$2")
	}
	
function MM_preloadImages() 
	{ //v3.0
  	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    	var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    	if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}

function MM_swapImgRestore() 
	{ //v3.0
  	var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}

function MM_findObj(n, d) 
	{ //v3.0
  	var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    	d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  	if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
	}
	
	
function MM_swapImage() 
	{ //v3.0
  	var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   	if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
	}

var _minus=new Image();
var _plus=new Image();
_minus.src="../../_images/preset/puces/minus.gif";
_plus.src="../../_images/preset/puces/plus.gif";
    
function show_hide(imageid,id) 
	{
      	var image=document.getElementById(imageid);
      	var objet=document.getElementById(id);
      	if (objet.style.display=='none') 
      		{
	        image.src=_minus.src;
	        objet.style.display='';
      		}
      	else 
      		{
        	image.src=_plus.src;
        	objet.style.display='none';
      		}
    	}
    	
function show_plus(id) 
	{
       	var objet=document.getElementById(id);
      	if (objet.style.display=='none') 
      		{
	        objet.style.display='';
		}
      	else 
      		{
        	objet.style.display='none';
      		}
    	}
    	
    	
function alerte(what)
	{
	alert(what);	
	}
	
// Scripts pour le Popup

function getCookieVal(offset)
	{
	var endstr=document.cookie.indexOf (";", offset);
	if (endstr==-1) endstr=document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
	}

function GetCookie (name) {  
		var arg = name + "=";  
	var alen = arg.length;  
	var clen = document.cookie.length;  
	var i = 0;  
	while (i < clen) {    
	var j = i + alen;    
	if (document.cookie.substring(i, j) == arg)      
	return getCookieVal (j);    
	i = document.cookie.indexOf(" ", i) + 1;    
	if (i == 0) break;   
	}  
	return null;
}

function SetCookie (name, value) {  
	var argv = SetCookie.arguments;  
	var argc = SetCookie.arguments.length;  
	var expires = (argc > 2) ? argv[2] : null;  
	var path = (argc > 3) ? argv[3] : null;  
	var domain = (argc > 4) ? argv[4] : null;  
	var secure = (argc > 5) ? argv[5] : false;  
	document.cookie = name + "=" + escape (value) + 
	((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + 
	((path == null) ? "" : ("; path=" + path)) +  
	((domain == null) ? "" : ("; domain=" + domain)) +    
	((secure == true) ? "; secure" : "");
}

function DeleteCookie (name) {  
	var exp = new Date();  
	exp.setTime (exp.getTime() - 1);  
	var cval = GetCookie (name);  
	document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}

function js_in_array(the_needle, the_haystack){
	var the_hay = the_haystack.toString();
	if(the_hay == ''){
	    return false;
	}
	var the_pattern = new RegExp(the_needle, 'g');
	var matched = the_pattern.test(the_haystack);
	return matched;
}