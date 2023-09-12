//---------------------------------------------------------------------------
function popWin(url, width, height)
{
	param = "toolbar=no, menubar=no, location=no, directories=no, status=no, scrollbars=yes, resizable=no, copyhistory=no, width=" + width + ", height=" + height
	window.open(url, '', param)
}
//---------------------------------------------------------------------------


function popupModal(url) {

	if ($('body').attr('modal_popup_number')) var modal_id = $('body').attr('modal_popup_number') + 1;
	else var modal_id = 1;
	var modal_content = '';
	modal_content = modal_content.concat('<div class="modal fade" id="popupModal_' + modal_id + '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">');
	  modal_content = modal_content.concat('<div class="modal-dialog modal-xl modal-dialog-centered">');
	    modal_content = modal_content.concat('<div class="modal-content">');
	      // modal_content = modal_content.concat('<div class="modal-header">');
	      //   modal_content = modal_content.concat('<h5 class="modal-title" id="exampleModalLabel"></h5>');
	      //   modal_content = modal_content.concat('<button type="button" class="close" data-dismiss="modal" aria-label="Close">');
	      //     modal_content = modal_content.concat('<span aria-hidden="true">&times;</span>');
	      //   modal_content = modal_content.concat('</button>');
	      // modal_content = modal_content.concat('</div>');
	      modal_content = modal_content.concat('<div class="modal-body">');
	        modal_content = modal_content.concat('<iframe style="border: 0px; width: 100%; height: calc(100vh * 0.7);" src="' + url + '"></iframe>');
	      modal_content = modal_content.concat('</div>');
	      modal_content = modal_content.concat('<div class="modal-footer">');
	        modal_content = modal_content.concat('<button type="button" class="btn btn-secondary" data-dismiss="modal" id="closePopupModal_' + modal_id + '">Fermer</button>');
	        // modal_content = modal_content.concat('<button type="button" class="btn btn-primary">Save changes</button>');
	      modal_content = modal_content.concat('</div>');
	    modal_content = modal_content.concat('</div>');
	  modal_content = modal_content.concat('</div>');
	modal_content = modal_content.concat('</div>');

	$('body').append(modal_content);
	$('#popupModal_' + modal_id).modal('show');
	$('#myModal').modal('handleUpdate')


	$('body').attr('modal_popup_number', modal_id);

}
