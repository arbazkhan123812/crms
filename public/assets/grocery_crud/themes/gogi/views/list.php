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
				<?php
				$i = 0;
				?>
				<?php foreach ($list as $num_row => $row) { ?>
					<tr <?php if ($num_row % 2 == 1) { ?>class="" <?php } ?>>
						<?php if (!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)) { ?>
							<td align="right" width='10%'>
								<div class='btn-group btn-group-sm'>
									<?php if (!$unset_delete) { ?>
										<a class="btn btn-danger delete-row" data-toggle="tooltip" data-placement="top" href="<?php echo $row->delete_url ?>" title='<?php echo $this->l('list_delete') ?> <?php echo $subject ?>'>
											<i class="fa fa-trash"></i>
										</a>
									<?php } ?>
									<?php if (!$unset_edit) { ?>
										<a class="btn btn-warning edit_button" data-toggle="tooltip" data-placement="top" href="<?php echo $row->edit_url ?>" title='<?php echo $this->l('list_edit') ?> <?php echo $subject ?>'>
											<i class="fa fa-edit"></i>
										</a>
									<?php } ?>
									<?php if (!$unset_clone) { ?>
										<a class="btn btn-primary clone_button" data-toggle="tooltip" data-placement="top" href='<?php echo $row->clone_url ?>' title='Clone <?php echo $subject ?>'>
											<i class="fa fa-copy"></i>
										</a>
									<?php } ?>
									<?php if (!$unset_read) { ?>
										<a class="btn btn-dark edit_button" data-toggle="tooltip" data-placement="top" href='<?php echo $row->read_url ?>' title='<?php echo $this->l('list_view') ?> <?php echo $subject ?>'>
											<i class="fa fa-search"></i>
										</a>
									<?php } ?>
									<?php if ($this->router->class == 'Bookings' && $this->router->method == 'all') { ?>
										jkhkjh
									<?php } ?>

									<?php
									$i++;
									?>
									<button id="print_button<?php echo $i; ?>" onclick="myFunction(this)" value="jgjhghj" class="btn btn-dark edit_button print_row" data-toggle="tooltip" data-placement="top">
										<i class="fa fa-print"></i>
									</button>
									<script>
										// $(document).ready(function () {
										// 	$('.print_row').click(function (e) { 
										// 		e.preventDefault();
										// 		var buttonId = $(this).val();
										// 		// var rowId = buttonId.split('_')[2];
										// 		// var row = $('#row_' + rowId);
										// 		// console.log($(`#${buttonId}`).val());
										// 		console.log(buttonId);
										// 	});
										// });
										function myFunction(button) {
											// console.log(vl);
											var row = button.closest('tr'); // Find the closest parent 'tr' element

											var printWindow = window.open('', '_blank');
											printWindow.document.open();
											printWindow.document.write('<html><head><title>Print</title>');
											printWindow.document.write('<link type="text/css" rel="stylesheet" href="https://falaknazenhanced.softologics.com/assets/grocery_crud/themes/gogi/css/gogi.css" />');
											printWindow.document.write('<link type="text/css" rel="stylesheet" href="https://falaknazenhanced.softologics.com/assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />');
											printWindow.document.write('<link type="text/css" rel="stylesheet" href="https://falaknazenhanced.softologics.com/assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />');
											printWindow.document.write('<link rel="stylesheet" href="https://falaknazenhanced.softologics.com/assets/gogitemplate/vendors/bundle.css" type="text/css">');
											printWindow.document.write('<link rel="stylesheet" href="https://falaknazenhanced.softologics.com/assets/gogitemplate/assets/css/app.min.css" type="text/css">');
											printWindow.document.write('<style>');
											printWindow.document.write('table { border-collapse: collapse; width: 100%; }');
											printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; }');
											printWindow.document.write('th { background-color: #f2f2f2; }');
											printWindow.document.write('tr:nth-child(even) { background-color: #f9f9f9; }');
											printWindow.document.write('</style>');
											printWindow.document.write('</head><body>');

											var table = printWindow.document.createElement('table');
											var thead = printWindow.document.createElement('thead');
											var tr = printWindow.document.createElement('tr');

											tr.innerHTML = `
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_by_user_id">Booking by user id</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_date">Booking date</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_inventory_id">Booking inventory id</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_amount">Booking amount</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_client_name">Booking client name</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_client_number">Booking client number</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="userImage">UserImage</div>
												</th>
												<th width="11%">
												<div class="text-left field-sorting" rel="booking_status">Booking status</div>
												</th>
											`;

											thead.appendChild(tr);
											table.appendChild(thead);
											
											var clonedRow = row.cloneNode(true);
											clonedRow.deleteCell(0); // Remove the first cell (column) from the cloned row
											
											table.appendChild(clonedRow);

											printWindow.document.body.appendChild(table);
											printWindow.document.write('</body></html>');
											printWindow.document.close();
											printWindow.print();
											printWindow.close();
										}
									</script>
									<?php
									if (!empty($row->action_urls)) {
										foreach ($row->action_urls as $action_unique_id => $action_url) {
											$action = $actions[$action_unique_id];
									?>
											<a href="<?php echo $action_url; ?>" data-toggle="tooltip" data-placement="top" class="btn btn-outline-dark" title="<?php echo $action->label ?>">
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