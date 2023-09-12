/*
jQuery: desoForm plugin v1.0 - jquery.desoform.js
Copyright - 2012 - S.V.
This source code is under the GNU General Public License
contact@chez-syl.fr
*/
(function($) {
    $.fn.desoForm = function(options) {
	
		// default values
		var defaults = {
			'emptyField': 'Le champ ne doit pas être vide',
			'submit': false
		};
        
		// extend options
		var p = $.extend(defaults, options);
		
		var $el = this;
		var ok = true;
		var nbInvalid;
		
		var $inputs = $el.find('input[type="text"]');
		
		// ajout de la class result sur tous les input du form
		$inputs.each(function() {
			var $this = $(this);
			$this.after('<span class="desoform_result"></span>');				
		});
		
		// détection du type d'événement souhaité
		$el.on('submit', function(e) {
			e.preventDefault();
		
			displayErrors();
			if(p.submit) {
				p.submit($el, ok);
			}
			return false;
		});
		
		function notOk(x) {
			if(x == 1) {
				nbInvalid++;
			}

			$('#nbvalid').html(nbInvalid);
			(nbInvalid > 0) ? ok = false : ok = true;
		}
		// affichage des erreurs
		function displayErrors() {
			nbInvalid = 0;
			ok = true;
			
			$inputs.each(function() {
				var $this = $(this);
				var val = $.trim($this.val());
				
				// si on a "required" et qu'il vaut "false" alors on ne vérifie pas
				if($this.attr('data-required') == 'false' && val == '') {
					$this.removeClass('desoform_error').addClass('desoform_ok').next(span).empty();

				} else if(val == '') {
					$this.removeClass('desoform_ok').addClass('desoform_error').next(span).html(p.emptyField).fadeIn('slow');
					// focus sur la première erreur

					$('input[type="text"].desoform_error:first', $el).focus();
					notOk(1);
				} else {
						
					// si le champ a un data-regexp
					if($this.attr('data-regexp')) {
						var r = '';
						
						switch($this.attr('data-regexp')) {
							case 'date': r = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/[0-9]{4}$/; 
							break;
							case 'date-en': r = /^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/; 
							break;
							case 'email': r = /^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$/; 
							break;
							case 'cp': r = /^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3}$/; 
							break;
							default: r = new RegExp($this.data('regexp'), 'g'); 
							break;
						}
						
						var span = 'span.desoform_result';
						
						// si le champ a data-error pour un message d'erreur personnalisé
						if($this.attr('data-error')) {
							msg = $this.attr('data-error');
						}
						
						// si la regexp n'est pas bonne
						if(!r.test(val)) {
							$this.removeClass('desoform_ok').addClass('desoform_error').next(span).html(msg).fadeIn('slow');
							
							// focus sur la première erreur
							$('.desoform_error:first', $inputs).focus();
							notOk(1);
						} else {
							$this.removeClass('desoform_error').addClass('desoform_ok').next(span).empty();
						}
					}
				}
			});
		}

		return this;
    };
})(jQuery);