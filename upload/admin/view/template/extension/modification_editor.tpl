<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
<a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a> /
<?php } ?>
</div>

<div class="box">
<div class="heading">
    <h1><img src="view/image/product.png" alt="<?php echo $heading_title; ?>" /> <?php echo $heading_title; ?></h1>
	<div style="text-align:right; margin-top:5px;" >
	<a class="button" href="<?php echo $this->url->link('extension/modification_editor', 'token=' . $this->session->data['token'], 'SSL'); ?>">New OcMod</a>
	&nbsp;<a class="button" href="<?php echo $refresh; ?>" class="btn btn-info">Refresh Page</a>
	&nbsp;<a class="button" href="<?php echo $return; ?>" title="Return" class="btn btn-info">Return</a>
	&nbsp;<a class="button" id="button-save" title="Save OcMod" data-loading-text="saving OcMod">Save OcMod</a>
	</div>
</div>
    <div class="content">
	<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $name; ?></h3>

  <div class="panel-body clearfix">
        <form action="" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
          <div class="form-group required">
            <div class="col-sm-12">
              <label class="control-label" for="input-text"><?php echo $entry_xml_code; ?></label><p>
              <pre id="code" style="width:99% !important; height:480px; position:relative; margin:0 1%; font-size:1.1em;"><?php echo htmlentities($xml); ?></pre>
			  <input type="hidden" name="modification_id" value="<?php echo $modification_id; ?>" /></p>
            </div>
          </div>  
        </form>
	  </div>    
	 </div>
   </div>
 </div>
<script type="text/javascript"><!--
	$('#warning').hide();
	$('#success').hide();

	var path = "view/javascript/ace";
	var editorconfig = ace.require("ace/config");
	editorconfig.set("workerPath", path);
	var xml_editor = ace.edit("code");
	xml_editor.setTheme("ace/theme/cobalt");
	xml_editor.getSession().setMode("ace/mode/xml");

	$('#button-clear-data').on('click', function() {
		$('#warning').hide();
		$('#success').hide();
		$.ajax({
			url: 'index.php?route=extension/modification_editor/clearCacheData&token=<?php echo $token; ?>',
			dataType: 'json',
			cache: false,	
			beforeSend: function() {
				$('#button-clear-data').button('loading');
			},
			complete: function() {
				$('#button-clear-data').button('reset');
			},
			success: function(json) {
				$('#success').html(json['success']);
				$('#success').show();
			},			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#button-clear-image').on('click', function() {
		$('#warning').hide();
		$('#success').hide();
		$.ajax({
			url: 'index.php?route=extension/modification_editor/clearCacheImage&token=<?php echo $token; ?>',
			dataType: 'json',
			cache: false,	
			beforeSend: function() {
				$('#button-clear-image').button('loading');
			},
			complete: function() {
				$('#button-clear-image').button('reset');
			},
			success: function(json) {
				$('#success').html(json['success']);
				$('#success').show();
			},			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$('#button-save').on('click', function() {
		$('#warning').hide();
		$('#success').hide();
		var id = $('input[name="modification_id"]').val();
		var xml_code = xml_editor.getValue();
		$.ajax({
			url: 'index.php?route=extension/modification_editor/save&token=<?php echo $token; ?>',
			type: 'post',		
			dataType: 'json',
			data: { modification_id: id, xml: xml_code },
			cache: false,	
			beforeSend: function() {
				$('#button-save').button('loading');
			},
			complete: function() {
				$('#button-save').button('reset');
			},
			success: function(json) {
				if (json['error']) {
					$('#warning').html(json['error']).show();
				} else {
					$('#success').html(json['success']);
					$('#success').show();
					if (id == 0) { location.href = 'index.php?route=extension/modification&token=<?php echo $token; ?>'; }
				}
			},			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
//--></script> 
<?php echo $footer; ?>