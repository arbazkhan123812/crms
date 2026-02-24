<?php

$column_width = (int) (90 / count($columns));
// if (!empty($list)) {
	?>
<style>
    .toolbar {
        float: left;
    }

    .dt-buttons {
        float: left;
    }

    .dt-button-collection .dropdown-menu {
        margin-top: 0;
        visibility: visible;
        opacity: 1;
        height: auto;
    }

    /* #fancybox-wrap {
        display: none !important;
    }

    #myTable_filter .select2.select2-container .select2-selection .select2-selection__placeholder {
        line-height: calc(1.3rem + 2px) !important;
        float: left;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    #myTable_filter .select2-container .select2-selection--single {
        height: calc(15px + 0.75rem + 3px) !important;
    }

    #myTable_filter .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px !important;
    }

    #myTable_filter .select2 {
        width: 200px !important;
        margin-left: 10px;
    }

    #myTable_filter .select2-container .select2-selection--single .select2-selection__rendered {
        padding-top: 1px;
        text-align : left;
    } */

    thead input {
        width: 100% !important;
    }

    div.dataTables_wrapper div.dataTables_paginate {
        margin-top: -25px !important;
    }

    @media screen and (max-width: 767px) {
        div.dataTables_wrapper div.dataTables_length, div.dataTables_wrapper div.dataTables_filter, div.dataTables_wrapper div.dataTables_info, div.dataTables_wrapper div.dataTables_paginate {
            text-align: right;
        }

        div.dt-buttons {
            float: left;
            width: unset;
            text-align: center;
            margin-bottom: 0.5em;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 0px !important;
        }
    }

    #myTable_wrapper .dataTables_scrollBody::-webkit-scrollbar {
        /* display: none; */
        height: 5px;
        border-radius: 5px;
        background-color: unset;
    }

    #myTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb {
        /* display: none; */
        height: 5px;
        border-radius: 5px;
        background: rgba(41, 49, 52, 0.5) !important;
    }

    #myTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-track {
        /* display: none; */
        height: 5px;
        border-radius: 5px;
        background-color: unset;
    }

    /* #myTable_wrapper:hover .dataTables_scrollBody::-webkit-scrollbar-thumb {
        display: block;
    } */
</style>
<div class="table-responsive" style="padding:5px;">
    <table id="myTable" class="table table-striped table-bordered">
        <thead>
            <tr >
                <?php if (!$unset_delete || !$unset_edit || !$unset_read || !$unset_clone || !empty($actions)) { ?>
                    <th align="right" abbr="tools" axis="col1" class="" width='%'>
                        <div class="text-right">
                            <?php echo $this->l('list_actions'); ?>
                        </div>
                    </th>
                <?php } ?>
                <!-- <th>No#</th> -->
                <?php foreach ($columns as $column) { ?>
                    <th width='<?php //echo $column_width ?>%'>
                        <div class="text-left field-sorting <?php if (isset($order_by[0]) && $column->field_name == $order_by[0]) { ?><?php echo $order_by[1] ?><?php } ?>"
                            rel='<?php echo $column->field_name ?>'>
                            <?php echo $column->display_as ?>
                        </div>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            
            <?php 
                $i = 1;
                foreach ($list as $num_row => $row) { 
            ?>
            <tr <?php if ($num_row % 2 == 1) { ?>class="" <?php } ?>>
                <!-- <td><?= $i; ?></td> -->
                <?php if (!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)) { ?>
                    <td align="right">
                      
                        <div class='btn-group-sm'>
                            <?php if (!$unset_delete) { ?>
                                <a class="btn btn-danger delete-row" href="<?php echo $row->delete_url ?>" data-id="<?php echo $row->$primary_key; ?>" title='<?php echo $this->l('list_delete') ?> <?php echo $subject ?>'>
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php } ?>
                            <?php if (!$unset_edit) { ?>
                                <a class="btn btn-warning edit_button edit-btn" href="<?php echo $row->edit_url ?>"  data-id="<?php echo $row->$primary_key; ?>"title='<?php echo $this->l('list_edit') ?> <?php echo $subject ?>'>
                                    <i class="fa fa-edit"></i>
                                </a>
                            <?php } ?>
                            <?php if (!$unset_clone) { ?>
                                <a class="btn btn-primary clone_button" href='<?php echo $row->clone_url ?>' data-id="<?php echo $row->$primary_key; ?>" title='Clone <?php echo $subject ?>'>
                                    <i class="fa fa-copy"></i>
                                </a>
                            <?php } ?>
                            <?php if (!$unset_read) { ?>
                                <a class="btn btn-info edit_button" href='<?php echo $row->read_url ?>' data-id="<?php echo $row->$primary_key; ?>" title='<?php echo $this->l('list_view') ?> <?php echo $subject ?>'>
                                    <i class="fa fa-eye"></i>
                                </a>
                            <?php } ?>
                            <?php
                            if (!empty($row->action_urls)) {
                                foreach ($row->action_urls as $action_unique_id => $action_url) {
                                    $action = $actions[$action_unique_id];
                            ?>
                                    <a href="<?php echo $action_url; ?>" class="btn btn-primary <?php echo str_replace(' ', '_', $action->label) ?>" data-id="<?php echo $row->$primary_key; ?>" title="<?php echo $action->label ?>">
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
                    <td
                        class='<?php if (isset($order_by[0]) && $column->field_name == $order_by[0]) { ?>sorted<?php } ?>'>
                        <div class='text-left'>
                            <?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;'; ?>
                        </div>
                    </td>
                <?php } ?>
            </tr>
            <?php 
                $i++;
                } 
            ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image View</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" alt="Image">
            </div>
        </div>
    </div>
</div>
<script>
    $('[data-toggle="tooltip"]').tooltip();
    $(document).ready(function (){
        
        // var dataTable = $('#myTable').DataTable({
            // scrollX: true,
            // dom: 'Bfrtip',
            // buttons: [
            //     'print',
            //     'excelHtml5'
            // ],
            // "order": false
        // });

        <?php if (!$unset_add) { ?>
            $(".crud-add-btn").html('<a href="<?= $add_url ?>" title="<?= $this->l('list_add'); ?> <?= $subject ?>"'+
                                        'class="btn btn-flat btn-primary">'+
                                        '<i class="fa fa-plus mr-2"></i>'+
                                        '<?= $this->l('list_add'); ?> <?= $subject ?>'+
                                    '</a>');
        
        <?php } ?>


        // var select_column_option = '<select id="columnFilter" class="select2" style="display: inline;width: 200px; margin-left: 10px;">'+
        //                                 '<option value="show_all">Show All</option>'+
        //                                 <?php
        //                                 $index = 1;
        //                                 foreach ($columns as $column) {
        //                                 ?> // Apply the customer demand condition (if).
        //                                     '<option value="<?php echo $index ?>" <?php if($column->display_as == 'Vehicle(Reg No#)' || $column->display_as == 'Registration Number' || $column->display_as == 'Vehicle id') echo 'selected="selected"'?>><?php echo $column->display_as ?></option>'+
        //                                 <?php
        //                                     $index++; 
        //                                 }
        //                                 ?>
        //                             '</select>';
            
        // $("#myTable_filter").append(select_column_option);
        
        // $('#myTable_filter').find('input').on('keyup', function () {
        //     var inputValue = $(this).val();
        //     var column = $('#columnFilter option:selected').val();

        //     if (inputValue != '' && column == 'show_all') {
        //         dataTable.search(inputValue).draw();
        //     } else if (inputValue != '' && column != '') {
        //         dataTable.column(column).search(inputValue).draw();
        //     } else if(inputValue != ''){
        //         dataTable.search(inputValue).draw();
        //     } else {
        //         dataTable.search('').columns().search('').draw();
        //     }
        // });

        // $('#columnFilter').change(function () {
        //     col = $(this).val();
        //     value = $('#myTable_filter').find('input').val();
        //     if(value != ''){
        //         dataTable.search('').columns().search('').draw();
        //         dataTable.column(col).search(value).draw();
        //         $('#myTable_filter').find('input').val(value);
        //     }
        // });

        $('#myTable thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#myTable thead');
 
        var table = $('#myTable').DataTable({
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [
                'print',
                'excelHtml5',
                'colvis'
            ],
            "order": false,
            stateSave: true,
            orderCellsTop: true,
            fixedHeader: false,
            initComplete: function () {
                var api = this.api();
    
                // For each column
                api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {
                        // Set the header cell to contain the input element
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text().trim();
                        console.log(title);
                        if (title === 'Actions') {
                            $(cell).html('<button id="clearFiltersBtn" class="btn btn-danger" style="width:100%; justify-content: center;">Clear Filter</button>');
                            $(cell).addClass('text-center');
                        } else {
                            $(cell).html('<input type="text" class=" form-control" placeholder="' + title + '" />');
        
                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                    
                            .off('keyup change')
                            .on('change', function (e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})'; //$(this).parents('th').find('select').val();
    
                                var cursorPosition = this.selectionStart;
                                // Search the column for that value
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != ''
                                            ? regexr.replace('{search}', '(((' + this.value + ')))')
                                            : '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            })
                            .on('keyup', function (e) {
                                e.stopPropagation();
    
                                $(this).trigger('change');
                                $(this)
                                    .focus()[0]
                                    .setSelectionRange(cursorPosition, cursorPosition);
                            });
                        }
                    });

                    // Clear filters and state button functionality
                    $('#clearFiltersBtn').on('click', function() {
                        $('.filters input').val('').trigger('change'); // Clear input values and trigger change event
                        api.search('').draw(); // Clear DataTable search and redraw
                        // Check if there is a saved state
                        if (table.state.loaded()) {
                            table.state.clear(); // Clear the saved state in local storage or session storage
                            window.location.reload(); // Reload the page to apply the clear state
                        }
                    });
            },
        });


        // Handle image click events
        $(document).on('click', '.image-thumbnail', function() {
            const imageUrl = $(this).data('image');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });

        // Reset the modal when it's closed
        $('#imageModal').on('hidden.bs.modal', function() {
            $('#modalImage').attr('src', '');
        });
    });
</script>
