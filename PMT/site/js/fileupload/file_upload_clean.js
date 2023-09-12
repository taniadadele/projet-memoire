/*
 * jQuery File Upload Demo
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global $ */

$(function () {
  // 'use strict';

  // Initialize the jQuery File Upload widget:
  $('#fileupload').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    url: urlupload
  });

  // Enable iframe cross-domain access via redirect option:
  $('#fileupload').fileupload(
    'option',
    'redirect',
    window.location.href.replace(/\/[^/]*$/, '/cors/result.html?%s')
  );


  // Load existing files:
  $('#fileupload').addClass('fileupload-processing');
  $.ajax({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    url: $('#fileupload').fileupload('option', 'url'),
    dataType: 'json',
    context: $('#fileupload')[0]
  })
    .always(function () {
      $(this).removeClass('fileupload-processing');
    })
    .done(function (result) {
      $(this)
        .fileupload('option', 'done')
        // eslint-disable-next-line new-cap
        .call(this, $.Event('done'), { result: result });
    });






    // Function used when we move a file to reload the file list without reloading the page
    // function updateFileList() {
    //   // We empty the file list
    //   $('tbody.files').html('');
    //
    //   // We reconstruct the file list
    //   // 'use strict';
    //
    //   // Initialize the jQuery File Upload widget:
    //   $('#fileupload').fileupload({
    //       // Uncomment the following to send cross-domain cookies:
    //       //xhrFields: {withCredentials: true},
    //       url: urlupload + 'download/'+ typefiche +'/index.php?fiche='+ numfiche
    //   });
    //
    //   // Enable iframe cross-domain access via redirect option:
    //   $('#fileupload').fileupload(
    //       'option',
    //       'redirect',
    //       window.location.href.replace(
    //           /\/[^\/]*$/,
    //           '/cors/result.html?%s'
    //       )
    //   );
    //
    //   $('#fileupload').fileupload('option', {
    //     autoUpload:true,
    //   });
    //
    //   // Load existing files:
    //   $('#fileupload').addClass('fileupload-processing');
    //   $.ajax({
    //   	// Uncomment the following to send cross-domain cookies:
    //   	//xhrFields: {withCredentials: true},
    //   	url: $('#fileupload').fileupload('option', 'url'),
    //   	dataType: 'json',
    //   	context: $('#fileupload')[0]
    //   }).always(function () {
    //   	$(this).removeClass('fileupload-processing');
    //   }).done(function (result) {
    //   	$(this).fileupload('option', 'done')
    //   		.call(this, $.Event('done'), {result: result});
    //   });
    // }


});
