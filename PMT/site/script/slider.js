//	Smart Mini Tabs by Rob L Glazebrook.
//	Last edited: Feb. 10, 2006
//	This script is based on slayeroffice's focus slide navigation:		
//	http://slayeroffice.com/code/focus_slide/

var d=document;			// These four variables
var activeLI = 0;		// should not be
var currentLI = 0;		// edited unless you
var zInterval = null;	// really know your stuff

var SLIDE_STEP = 10;		// # of pixels to slide each step (higher is faster)
var RESIZE_STEP = 5;	// # of pixels to resize each step (higher is faster)

function init_slider() {
	if(!document.getElementById || window.opera)return;

	mObj = d.getElementById("navheader");
	liObj = mObj.getElementsByTagName("li");
	aObj = mObj.getElementsByTagName("a");

	for(i=0;i<liObj.length;i++) { // create mouseovers/mouseouts for the li's and the ul
		liObj[i].xid = i;
		liObj[i].onmouseover = function() { initSlide(this.xid); }
	}
	mObj.onmouseout = function() { initSlide(currentLI); }

	// create the slider object
	slideObj = mObj.appendChild(d.createElement("div"));
	slideObj.id = "slider";

	// position the slider over the current li
	for(i=0;i<liObj.length;i++) {
		if(liObj[i].className == "active") {
			activeLI = currentLI = i;
		}
	}
	x = liObj[activeLI].offsetLeft;
	y = liObj[activeLI].offsetTop-3;
	slideObj.style.top = y + "px";
	slideObj.style.left = x + "px";
	slideObj.style.width = liObj[activeLI].offsetWidth + "px";
}

function initSlide(objIndex) {
	if(objIndex == activeLI)return;
	clearInterval(zInterval);
	activeLI = objIndex;
	destX = liObj[activeLI].offsetLeft;		// the desination location
	destW = liObj[activeLI].offsetWidth;	// the destination size
	intervalMethod = function() { doSlide(destX); }
	zInterval = setInterval(intervalMethod,10);
}

function doSlide(dX) { // move the slider div
	x = slideObj.offsetLeft;
	if(x+SLIDE_STEP<dX) {
		// if the x-value is less than its destination, move it to the right
		x+=SLIDE_STEP;
		slideObj.style.left = x + "px";
		doResize(destW);
	} else if (x-SLIDE_STEP>dX) {
		// if the x-value is more than its destination, move to the left
		x-=SLIDE_STEP;
		slideObj.style.left = x + "px";
		doResize(destW);
	} else  {
		// if the div is within SLIDE_STEP pixels, move it to the proper location
		slideObj.style.left = dX + "px";
		slideObj.style.width = destW +"px";
		clearInterval(zInterval);
		zInterval = null;
	}
}

function doResize(dW) { // resize the slider div -- similar in execution to doSlide
	w = slideObj.offsetWidth;
	if (slideObj.offsetWidth!=dW) {
		if (w+RESIZE_STEP<dW) {
			w+=RESIZE_STEP;
			slideObj.style.width = w + "px";
		} else if (w-RESIZE_STEP>dW) {
			w-=RESIZE_STEP;
			slideObj.style.width = w + "px";
		} else {
			slideObj.style.width = dW + "px";
		}
	}
}

function RunSlideShow(divid,imageid,imageFiles,displaySecs)
{
	
	var imageSeparator = imageFiles.indexOf(";");
	var nextImage = imageFiles.substring(0,imageSeparator);

	changeOpac(0, imageid);
	blendimage(divid,imageid,nextImage,1000);

	var futureImages= imageFiles.substring(imageSeparator+1,imageFiles.length)+ ';' + nextImage;
	setTimeout("RunSlideShow('"+divid+"','"+imageid+"','"+futureImages+"',"+displaySecs+")",displaySecs*1000);

}

function opacity(id, opacStart, opacEnd, millisec) {
	//speed for each frame
	var speed = Math.round(millisec / 100);
	var timer = 0;

	//determine the direction for the blending, if start and end are the same nothing happens
	if(opacStart > opacEnd) {
		for(i = opacStart; i >= opacEnd; i--) {
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	} else if(opacStart < opacEnd) {
		for(i = opacStart; i <= opacEnd; i++)
			{
			setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
			timer++;
		}
	}
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
	var object = document.getElementById(id).style; 
	object.opacity = (opacity / 101);
	object.MozOpacity = (opacity / 101);
	object.KhtmlOpacity = (opacity / 100);
	object.filter = "alpha(opacity=" + opacity + ")";
}

function shiftOpacity(id, millisec) {
	//if an element is invisible, make it visible, else make it ivisible
	if(document.getElementById(id).style.opacity == 0) {
		opacity(id, 0, 100, millisec);
	} else {
		opacity(id, 100, 0, millisec);
	}
}

function blendimage(divid, imageid, imagefile, millisec) {
	var speed = Math.round(millisec / 100);
	var timer = 0;
	
	// ** NOTE: Modified by RocketTheme to be able to fade between 2 divs rather than a div and an image **
	
	//set the current image as background
	document.getElementById(divid).style.backgroundImage = document.getElementById(imageid).style.backgroundImage;
	
	//make image transparent
	changeOpac(0, imageid);
	
	//make new image
	document.getElementById(imageid).style.backgroundImage = "url(" + imagefile + ")";

	//fade in image
	for(i = 0; i <= 100; i++) {
		setTimeout("changeOpac(" + i + ",'" + imageid + "')",(timer * speed));
		timer++;
	}
}

function currentOpac(id, opacEnd, millisec) {
	//standard opacity is 100
	var currentOpac = 100;
	
	//if the element has an opacity set, get it
	if(document.getElementById(id).style.opacity < 100) {
		currentOpac = document.getElementById(id).style.opacity * 100;
	}

	//call for the function that changes the opacity
	opacity(id, currentOpac, opacEnd, millisec)
}
