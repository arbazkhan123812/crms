<?php

$column_width = (int)(90 / count($columns));

if (!empty($list)) {
?>
	<div class="_bDiv table-responsive">
		<table id="flex1" class="mb-0 table table-bordered table-hover _table-striped">
			<thead>
				<tr class='_hDiv bg-gradient-gray-dark'>
					<?php if (!$unset_delete || !$unset_edit || !$unset_read || !$unset_clone || !empty($actions)) { ?>
						<th align="right" abbr="tools" axis="col1" class="" width='10%'>
							<div class="text-right">
								<?php echo $this->l('list_actions'); ?>
							</div>
						</th>
					<?php } ?>
					<?php foreach ($columns as $column) { ?>
						<th width='<?php echo $column_width ?>%'>
							<div class="text-left field-sorting <?php if (isset($order_by[0]) &&  $column->field_name == $order_by[0]) { ?><?php echo $order_by[1] ?><?php } ?>" rel='<?php echo $column->field_name ?>'>
								<?php echo $column->display_as ?>
							</div>
						</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $num_row => $row) { ?>
					<tr <?php if ($num_row % 2 == 1) { ?>class="" <?php } ?>>
						<?php if (!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)) { ?>
							<td align="right" width='10%'>
								<div class='btn-group btn-group-sm'>
									<?php if (!$unset_delete) { ?>
										<a class="btn btn-danger delete-row" data-toggle="tooltip" data-placement="top" href="<?php echo $row->delete_url ?>" title='<?php echo $this->l('list_delete') ?> <?php echo $subject ?>'>
											<i class="fa fa-trash-alt"></i>
										</a>
										<!-- <a href='<?php echo $row->delete_url ?>' title='<?php echo $this->l('list_delete') ?> <?php echo $subject ?>' class="delete-row">
											<span class='delete-icon'></span>
										</a> -->
									<?php } ?>
									<?php if (!$unset_edit) { ?>
										<a class="btn btn-warning edit_button" data-toggle="tooltip" data-placement="top" href="<?php echo $row->edit_url ?>" title='<?php echo $this->l('list_edit') ?> <?php echo $subject ?>'>
											<i class="fas fa-edit"></i>
										</a>
										<!-- <a href='<?php echo $row->edit_url ?>' title='<?php echo $this->l('list_edit') ?> <?php echo $subject ?>' class="edit_button">
											<span class='edit-icon'></span>
										</a> -->
									<?php } ?>
									<?php if (!$unset_clone) { ?>
										<a class="btn btn-primary clone_button" data-toggle="tooltip" data-placement="top" href='<?php echo $row->clone_url ?>' title='Clone <?php echo $subject ?>'>
											<i class="fas fa-copy"></i>
										</a>
										<!-- <a href='<?php echo $row->clone_url ?>' title='Clone <?php echo $subject ?>' class="clone_button"><span class='clone-icon'></span></a> -->
									<?php } ?>
									<?php if (!$unset_read) { ?>
										<a class="btn btn-dark edit_button" data-toggle="tooltip" data-placement="top" href='<?php echo $row->read_url ?>' title='<?php echo $this->l('list_view') ?> <?php echo $subject ?>'>
											<i class="fa fa-search"></i>
										</a>
										<!-- <a href='<?php echo $row->read_url ?>' title='<?php echo $this->l('list_view') ?> <?php echo $subject ?>' class="edit_button"><span class='read-icon'></span></a> -->
									<?php } ?>
									<?php
									if (!empty($row->action_urls)) {
										foreach ($row->action_urls as $action_unique_id => $action_url) {
											$action = $actions[$action_unique_id];
									?>
											<a href="<?php echo $action_url; ?>" data-toggle="tooltip" data-placement="top" class="btn btn-default" title="<?php echo $action->label ?>">
												<?php
												if (!empty($action->css_class)) { ?>
													<i class="<?php echo $action->css_class; ?>" alt="<?php echo $action->label ?>"> </i>
												<?php
												} else if (!empty($action->image_url)) {
													echo $action->image_url;
												} ?>
											</a>
									<?php }
									} ?>
									<div class='clear'></div>
								</div>
							</td>
						<?php } ?>
						<?php foreach ($columns as $column) { ?>
							<td width='<?php echo $column_width ?>%' class='<?php if (isset($order_by[0]) &&  $column->field_name == $order_by[0]) { ?>sorted<?php } ?>'>
								<div class='text-left'><?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;'; ?></div>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<script>
		$('[data-toggle="tooltip"]').tooltip();
	</script>
<?php } else { ?>
	<br />
	&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $this->l('list_no_items'); ?>
	<br />
	<br />
<?php } ?>