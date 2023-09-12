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
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-download">
    <td>
      <span class="preview">
        {% if (file.thumbnailUrl) { %}
          <a href="{%=file.url%}" title="{%=file.name%}"><img src="{%=file.thumbnailUrl%}" style="max-height: 70px;"></a>
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
      <span class="size">{%=o.formatFileSize(file.size)%}</span>
    </td>
    <td style="text-align: right;">
      {% if (file.deleteUrl) { %}
        <button class="btn btn-default delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
          <i class="fas fa-trash"></i>
        </button>
        <input type="checkbox" name="delete" value="1" class="toggle">
      {% } else { %}
        <!-- <button class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span>Annuler</span>
        </button> -->
      {% } %}
    </td>
  </tr>
{% } %}
</script>
