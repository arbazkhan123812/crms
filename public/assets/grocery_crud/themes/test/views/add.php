<?php

$this->set_css($this->default_theme_path . '/test/css/gogi.css');
$this->set_js_lib($this->default_theme_path . '/test/js/jquery.form.js');
$this->set_js_lib($this->default_javascript_path . '/jquery_plugins/jquery.form.min.js');
$this->set_js_config($this->default_theme_path . '/test/js/gogi-add.js');

$this->set_js_lib($this->default_javascript_path . '/jquery_plugins/jquery.noty.js');
$this->set_js_lib($this->default_javascript_path . '/jquery_plugins/config/jquery.noty.config.js');
?>
<style>
	.form-control.is-invalid:focus, .was-validated .form-control:invalid:focus {
    	border-color: #dc3545 !important;
	}
</style>
<div id='report-error' class='report-div alert alert-danger'></div>
<div id='report-success' class='report-div alert alert-success'></div>
<div class="flexigrid crud-form" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
	<div class="mDiv" style="border:none !important;">
		<div class="ftitle">
			<div class='ftitle-left'>
				<?php echo $this->l('form_add'); ?> <?php echo $subject ?>
			</div>
			<div class='clear'></div>
		</div>
	</div>
	<div id='main-table-box'>
		<?php echo form_open($insert_url, 'method="post" id="crudForm"  enctype="multipart/form-data"'); ?>
		<div class='form-div row' style="border:none !important;">
			<?php
			$counter = 0;
			foreach ($fields as $field) {
				$even_odd = $counter % 2 == 0 ? 'odd' : 'even';
				$counter++;
			?>
			
				<div class="form-field-box <?php if(count($fields) == 1){ echo 'col-md-12'; } else  { echo $field->set_column; }?>" id="<?php echo $field->field_name; ?>_field_box">
					<div class='form-display-as-box' id="<?php echo $field->field_name; ?>_display_as_box">
						<?php echo $input_fields[$field->field_name]->display_as; ?><?php echo ($input_fields[$field->field_name]->required) ? "<span class='required'>*</span> " : ""; ?> :
					</div><br>
					<div class='form-input-box' id="<?php echo $field->field_name; ?>_input_box">
						<?php echo $input_fields[$field->field_name]->input ?>
						<div class="invalid-feedback"></div>
					</div>
					<div class='clear'></div>
				</div>
			<?php } ?>
			<!-- Start of hidden inputs -->
			<?php
			foreach ($hidden_fields as $hidden_field) {
				echo $hidden_field->input;
			}
			?>
			<!-- End of hidden inputs -->
			<?php if ($is_ajax) { ?><input type="hidden" name="is_ajax" value="true" /><?php } ?>
		</div>
		<div class="pDiv" style="border:none !important; float:right">
			
			<?php if (!$this->unset_back_to_list) { ?>
				<div class='form-button-box'>
					<input type='button' value='<?php echo $this->l('form_save'); ?>' id="save-and-go-back-button" class="btn btn-primary btn-large" />
				</div>
				<div class='form-button-box'>
					<input type='button' value='<?php echo $this->l('form_cancel'); ?>' class="btn btn-danger btn-large" id="cancel-button" />
				</div>
			<?php 	} ?>
			<br>
			<div class='form-button-box'>
				<div class='small-loading' id='FormLoading'><?php echo $this->l('form_insert_loading'); ?></div>
			</div>
			<div class='clear'></div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
<script>
	var validation_url = '<?php echo $validation_url ?>';
	var list_url = '<?php echo $list_url ?>';

	var message_alert_add_form = "<?php echo $this->l('alert_add_form') ?>";
	var message_insert_error = "<?php echo $this->l('insert_error') ?>";

	// $(document).ready(function () {
	// 	if ($(".textckeditor5").length > 0) {
	// 		ClassicEditor
	// 			.create(document.querySelector('.textckeditor5'))
	// 			.catch(error => {
	// 				console.error(error);
	// 			});
	// 	}
	// });
</script>