//---------------------------------------------------------------------------
/*
 * Displays an confirmation box
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 *
 * @return  boolean  whether to run the query or not
 */
function confirmLink(theLink, confirmMsg)
{
    // Confirmation is not required in the configuration file
    if ( confirmMsg == '' )
        return true;

    var is_confirmed = confirm(confirmMsg);

    if ( is_confirmed )
        theLink.href += '&js_return=1';

    return is_confirmed;
}
//---------------------------------------------------------------------------
function confirmDelCentre(theLink, confirmMsg)
{
	if(theLink.checked)
	{
		// Confirmation is not required in the configuration file
		if ( confirmMsg == '' )
			return true;

		var is_confirmed = confirm(confirmMsg);

		return is_confirmed;
	}
}
//---------------------------------------------------------------------------