<?php require_once('header.php'); ?>

<?php
if (isset($_GET['success'])) {
    $success_message = 'Lottery ticket is added successfully!';
} elseif (isset($_GET['updated'])) {
    $success_message = 'Lottery ticket is updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $success_message = 'Lottery ticket is deleted successfully!';
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Lottery Tickets</h1>
    </div>

    <div class="content-header-right">
        <a href="lottery-upload.php" class="btn btn-success btn-sm">
            <i class="fa fa-upload"></i> Upload CSV
        </a>
            <a href="lottery-print-all.php"
       class="btn btn-info btn-sm"
       target="_blank">
        <i class="fa fa-print"></i> Print All Tickets
    </a>
        <a href="lottery-export.php" class="btn btn-warning btn-sm">
            <i class="fa fa-download"></i> Export CSV
        </a>
        <a href="lottery-add.php" class="btn btn-primary btn-sm">
            Add Lottery Ticket
        </a>
        <a href="spinner_ticket.php" class="btn btn-info btn-sm">
    <i class="fa fa-ticket"></i> Spinner
</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
            <div class="callout callout-danger">
                <p><?php echo $error_message; ?></p>
            </div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success">
                <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>

            <div class="box box-info">

                <div class="box-body table-responsive">

                    <table id="example1" class="table table-bordered table-hover table-striped">

                        <thead>
                            <tr>
                                <th width="30">#</th>
                                <th>Ticket Number</th>
                                <th>Child Name</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $i = 0;

                            $statement = $pdo->prepare("
                                SELECT *
                                FROM tbl_lottery_ticket
                                ORDER BY ticket_id DESC
                            ");

                            $statement->execute();

                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($result as $row) {
                                $i++;
                            ?>

                            <tr>

                                <td>
                                    <?php echo $i; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['ticket_number']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['child_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['class'] ?? ''); ?>
                                </td>

                                <td>

                                    <?php
                                    if ($row['status'] == 'Available') {
                                        $status_class = 'label-success';
                                    } elseif ($row['status'] == 'Sold') {
                                        $status_class = 'label-primary';
                                    } elseif ($row['status'] == 'Winner') {
                                        $status_class = 'label-warning';
                                    } else {
                                        $status_class = 'label-danger';
                                    }
                                    ?>

                                    <span class="label <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['created_at']); ?>
                                </td>

                                <td>

                                    <a href="lottery-edit.php?id=<?php echo (int)$row['ticket_id']; ?>"
                                       class="btn btn-primary btn-xs">
                                        Edit
                                    </a>


                                    <a href="#"
                                       class="btn btn-danger btn-xs"
                                       data-href="lottery-delete.php?id=<?php echo (int)$row['ticket_id']; ?>"
                                       data-toggle="modal"
                                       data-target="#confirm-delete">
                                        Delete
                                    </a>

                                </td>

                            </tr>

                            <?php
                            }
                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>
</section>


<div class="modal fade"
     id="confirm-delete"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-hidden="true">
                    &times;
                </button>

                <h4 class="modal-title" id="myModalLabel">
                    Delete Confirmation
                </h4>

            </div>

            <div class="modal-body">

                <p>
                    Are you sure want to delete this lottery ticket?
                </p>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <a class="btn btn-danger btn-ok">
                    Delete
                </a>

            </div>

        </div>

    </div>

</div>


<?php require_once('footer.php'); ?>