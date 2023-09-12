//---------------------------------------------------------------------------
function popWin(url, width, height)
{
	param = "toolbar=no, menubar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=" + width + ", height=" + height
	window.open(url, '', param)
}
//---------------------------------------------------------------------------