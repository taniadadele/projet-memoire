<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-upload">
    <td>
      <span class="preview">
        {% if (file.thumbnailUrl) { %}
          <a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}"></a>
        {% } %}
      </span>
    </td>
    <td>
      <p class="name">{%=file.name%}</p>
      <strong class="error text-danger"></strong>
    </td>
    <td>
      <p class="size">Processing...</p>
      <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
    </td>
    <td>
      {% if (!i && !o.options.autoUpload) { %}
        <button class="btn btn-primary start" disabled>
          <i class="glyphicon glyphicon-upload"></i>
          <span>Start</span>
        </button>
      {% } %}
      {% if (!i) { %}
        <button class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span>Cancel</span>
        </button>
      {% } %}
    </td>
  </tr>
{% } %}
</script>
<!-- The template to display files available for download -->



<!-- // <td style="text-align: center; width: 100px;">
// 	<span class="preview">
// 		{% if (file.thumbnailUrl) { %}
// 			<a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}" style="height: 60px; text-align: center;"></a>
// 		{% } %}
// 	</span>
// </td> -->


<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-download" {% if (file.backgroundColor != "0"){ %} id="selectedAfterSearch" {% } %} >

  <td style="text-align: center; width: 100px;">
    <span class="preview">
      {% if (file.thumbnailUrl) { %}
        <a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}" style="height: 60px; text-align: center;"></a>
      {% } %}
    </span>
  </td>



    <td>
      <p class="name">
        {% if (file.url) { %}
          <a href="{%=file.url%}" title="{%=file.name%}">{%=file.name%}</a>
        {% } else { %}
          <span>{%=file.name%}</span>
        {% } %}
      </p>
      {% if (file.error) { %}
        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
      {% } %}
    </td>
    <td>
      <span class="size">{%=file.size%}</span>

    </td>
    <td>
      <span class="date">{%=file.dateTime%}</span>

    </td>

    <td style="text-align: center;">
      {% if (file.shared_with_user != "0") { %}
        <i class="fa fa-user">&nbsp;{%=file.shared_with_user%}</i>
      {% } %}
      {% if (file.shared_with_class != "0") { %}
        <i class="fa fa-graduation-cap">&nbsp;{%=file.shared_with_class%}</i>
      {% } %}
      {% if (file.shared_with_group != "0") { %}
        <i class="fa fa-users">&nbsp;{%=file.shared_with_group%}</i>
      {% } %}
    </td>

    {% if (file.deleteUrl != "doNotDisplay") { %}
    <td style="text-align: right;">
      {% if (file.deleteUrl) { %}




      <!-- // Menu partager -->
      <div class="tooltip">
        <div class="tooltipbutton">
          <button class="btn btn-default" id="more_options_{%=file.id%}" onclick="show_options({%=file.id%});" type="button">
            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
          </button>
        </div>



        <span class="tooltiptext {% if (file.type == "folder") { %} tooltiptext-folder {% } %} popover bs-popover-top">
          <span class=" popover-body">

              {% if (file.type != "folder") { %}
                <button class="btn btn-default" id="share_{%=file.id%}" onclick="share_file({%=file.id%});" data-shared="{%=file.shared_with%}" type="button" title="<?php echo $msg->read($USER_SHARE); ?>">
                  <i class="fa fa-share" aria-hidden="true"></i>
                </button>
                <button class="btn btn-default" id="share_mail_{%=file.id%}" onclick="share_mail_file({%=file.id%});" data-shared="{%=file.shared_with%}" type="button" title="<?php echo $msg->read($USER_SHARE_MAIL); ?>">
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </button>
                <button class="btn btn-default" id="share_link_{%=file.id%}" onclick="share_link_file({%=file.id%});" data-shared="{%=file.shared_with%}" type="button" title="<?php echo $msg->read($USER_SHARE_LINK); ?>">
                  <i class="fa fa-link" aria-hidden="true"></i>
                </button>
              {% } %}

              <button class="btn btn-default" id="move_{%=file.id%}" onclick="move_file({%=file.id%});" data-name="{%=file.name_without_extension%}" type="button" title="<?php echo $msg->read($USER_MOVE_ELEMENT); ?>">
                <i class="fas fa-arrows-alt" aria-hidden="true"></i>
              </button>

              <button class="btn btn-default" id="rename_{%=file.id%}" onclick="rename_file({%=file.id%});" data-name="{%=file.name_without_extension%}" type="button" title="<?php echo $msg->read($USER_RENAME); ?>">
                <i class="fas fa-pencil-alt" aria-hidden="true"></i>
              </button>
          </span>
        </span>
      </div>


        <span  style="display:none;"></span>

        <button style="margin-left: 10px;" id="remove_{%=file.id%}" type="button" data-remove-text="{%=file.numberOfFilesToRemove%}" onclick="removeConfirm({%=file.id%});" class="btn btn-default remove_button" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %} title="<?php echo $msg->read($USER_DELETE); ?>">
          <i class="fas fa-trash"></i>
        </button>

        <input type="checkbox" name="delete" value="1" class="toggle">
      {% } else { %}
        <button class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span><?php echo $msg->read($USER_CANCEL); ?></span>
        </button>
      {% } %}
    </td>
    {% } else { %}
    <td></td>
    {% } %}
  </tr>
{% } %}
</script>
