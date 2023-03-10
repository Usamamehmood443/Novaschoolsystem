<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>backend/dist/css/zoom_addon.css">
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('live_class'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('live_class'); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('live_class'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class') . " " . $this->lang->line('title'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('date'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('class'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('class') . ' ' . $this->lang->line('host'); ?>
                                        </th>
                                        <th><?php echo $this->lang->line('status'); ?>
                                        </th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($conferences)) {
    ?>

                                        <?php
} else {
    foreach ($conferences as $conference_key => $conference_value) {

        $return_response = json_decode($conference_value->return_response);
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $conference_value->title; ?></a>

                                                    <div class="fee_detail_popover displaynone">
                                                        <?php
if ($conference_value->description == "") {
            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
            ?>
                                                            <p class="text text-info"><?php echo $conference_value->description; ?></p>
                                                            <?php
}
        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo $this->customlib->dateyyyymmddToDateTimeformat($conference_value->date); ?></td>
                                                <td class="mailbox-name">
                                                    <?php echo $conference_value->class . " (" . $conference_value->section . ")"; ?>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php
$name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
        echo $name . " (" . $conference_value->for_create_role_name . " : " . $conference_value->for_create_employee_id . ")";
        ?>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php
if ($conference_value->status == 0) {
            ?>
                                                        <span class="label label-warning font-w-normal">
                                                            <?php
echo $this->lang->line('awaited');
            ?>
                                                        </span>
                                                        <?php
} elseif ($conference_value->status == 1) {
            ?>
                                                        <span class="label label-default font-w-normal">
                                                            <?php
echo $this->lang->line('cancelled');
            ?>
                                                        </span>
                                                        <?php
} else {
            ?>
                                                        <span class="label label-success font-w-normal">
                                                            <?php
echo $this->lang->line('finished');
            ?>
                                                        </span>
                                                        <?php
}
        ?>
                                                </td>
                                                <td class="mailbox-date">
                                                    <?php
if ($conference_value->status == 0) {
            ?>

                        <a data-placement="left" href="#" class="btn btn-xs label-success p0" data-toggle="modal" data-target="#modal-chkstatus" data-id="<?php echo $conference_value->id; ?>">
                                                      <span class="label" ><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join') ?></span>
                                                        </a>

                                                        <?php

        }

        ?>
                                                </td>
                                            </tr>
                                            <?php
}
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="modal-chkstatus" data-backdrop="static">
    <div class="modal-dialog">
        <form id="form-chkstatus" action="<?php echo site_url('admin/conference/chkstatus'); ?>" method="POST">
            <div class="modal-content">
                <div class="">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <!-- <h4 class="modal-title"> Zoom Details</h4> -->
                </div>
                <div class="modal-body zoom_details">

                </div>

            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        "use strict";
        $(document).on('click', 'a.join-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).attr('href');
            $.ajax({
                url: "<?php echo site_url("user/conference/add_history") ?>",
                type: "POST",
                data: {"id": id},
                dataType: 'json',
                beforeSend: function () {
                }, success: function (res)
                {
                    if (res.status == 0) {
                    } else if (res.status == 1) {
                        window.open(url, '_blank');
                    }
                },
                error: function (xhr) {
                    alert("Error occured.please try again");
                },
                complete: function () {
                }
            });
        });

             $('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);

              var id=$(e.relatedTarget).data('id');


            $.ajax({
                type: "POST",
                url: '<?php echo site_url("user/conference/getlivestatus") ?>',
                data: {'id':id},
                dataType: "JSON",
                beforeSend: function () {
                $('.zoom_details').html("");
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {
                   $('.zoom_details').html(data.page);
                    $modalDiv.removeClass('modal_loading');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function (data) {
                    $modalDiv.removeClass('modal_loading');
                }
            });
        })

    })(jQuery);
</script>