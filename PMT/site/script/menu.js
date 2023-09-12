//---------------------------------------------------------------------------
function $() {
	var id = arguments[0] || null;

	if ( !id )
		return null;

	var self = document.getElementById(id);
	if ( !self )
		return null;

	if ( !self._display )
		self._display = {
			current : function () {
				return self.style.display;
				},
			show : function () {
				self.style.display = '';
				},
			none : function () {
				self.style.display = 'none';
				},
			toggle : function () {
				if ( self._display.current() == 'none' )
					self._display.show();
				else
					self._display.none();
				}
			}

	return self;
}
//---------------------------------------------------------------------------