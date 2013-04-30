<div class="row">
  <div class="span12">
    <ul class="breadcrumb">
      <li>
        <a href="/cpanel"><i class="icon-home">&nbsp;</i></a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel">cPanel</a> <span class="divider">/</span>
      </li>
      <li>
        <a href="/cpanel/upload">Upload</a>
      </li>
    </ul>
  </div>
</div>

<div class="row">
  <div class="span12">
  <h2>Upload file</h2>

  <div class="alert alert-success hide">
    <button type="button" class="close">&times;</button>
    <strong>:-)</strong>  File successfully uploaded.
  </div>

  <div class="status hide">
    <div class="progress progress-striped active">
      <div class="bar" style="width: 0%;">0%</div>
    </div>
    <div class="controls btn-group">
      <button id="pause" class="btn btn-inverse">Pause</button>
      <button id="resume" class="btn btn-success">Resume</button>
    </div>
  </div>

  <form id="uploadForm" method="post" enctype="multipart/form-data" action="/upload" class="form-horizontal">
    <input type="hidden" name="MAX_FILE_SIZE" value="104857600" />
    <input type="hidden" name="token" id="token" value="<?php echo $this['token']; ?>">
    <input type="hidden" name="uuid" id="uuid" value="<?php echo $this['uuid']; ?>">

    <div class="control-group">
      <label class="control-label required" for="title">Name</label>
      <div class="controls">
        <input type="text" id="name" name="name" class="span4" placeholder="name of your file" required title="name is required">
      </div>
    </div>

    <div class="control-group">
      <label class="control-label required" for="file">Select a file to upload<p><small id="fsize"></small></p></label>
      <div class="controls">
        <div class="fileupload fileupload-new" data-provides="fileupload">
          <div class="input-append">
            <div class="uneditable-input span3">
              <i class="icon-file fileupload-exists">&nbsp;</i><span class="fileupload-preview">&nbsp;</span>
            </div>
            <span class="btn btn-file">
              <span class="fileupload-new">Select file</span>
              <span class="fileupload-exists">Change</span>
              <input type="file" id="file" name="file" required title="select a file">
            </span>
            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
          </div>
        </div>
        <label for="file" class="error hide" generated="true">&nbsp;</label>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label required" for="tags">Tags</label>
      <div class="controls">
        <input type="text" id="tags" name="tags" placeholder="tag1, my_tag2, my tag3" required title="enter one or more tags comma separated">
      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        <button type="submit" class="btn btn-primary">Upload</button>&nbsp;<a href="/cpanel/upload/">Cancel</a>
      </div>
    </div>

  </form>

  </div>
</div>
