<?php require_once('header.php'); ?>

<?php

if (!isset($_REQUEST['id'])) {
    header('location: lottery.php');
    exit;
} else {

    $statement = $pdo->prepare("
        SELECT *
        FROM tbl_lottery_ticket
        WHERE ticket_id = ?
    ");

    $statement->execute(array($_REQUEST['id']));

    $total = $statement->rowCount();

    if ($total == 0) {
        header('location: lottery.php');
        exit;
    }
}


if (isset($_POST['form1'])) {

    $valid = 1;

    $ticket_number = trim($_POST['ticket_number'] ?? '');
    $child_name = trim($_POST['child_name'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $ticket_price = trim($_POST['ticket_price'] ?? '');
    $prize = trim($_POST['prize'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');


    /* Ticket Number */

    if ($ticket_number == '') {

        $valid = 0;

        $error_message .= 'Ticket number can not be empty<br>';
    }


    /* Child Name */

    if ($child_name == '') {

        $valid = 0;

        $error_message .= 'Child name can not be empty<br>';
    }


    /* Ticket Price */

    if ($ticket_price == '') {

        $ticket_price = 0;
    }

    if (!is_numeric($ticket_price) || $ticket_price < 0) {

        $valid = 0;

        $error_message .= 'Ticket price must be a valid amount<br>';
    }


    /* Status */

    $allowed_statuses = array(
        'Available',
        'Sold',
        'Winner',
        'Cancelled'
    );

    if (!in_array($status, $allowed_statuses, true)) {

        $valid = 0;

        $error_message .= 'Invalid ticket status<br>';
    }


    /* Check duplicate ticket number */

    if ($valid == 1) {

        $ticketId = (int)$_REQUEST['id'];

        $statement = $pdo->prepare("
            SELECT ticket_id
            FROM tbl_lottery_ticket
            WHERE ticket_number = ?
            AND ticket_id != ?
        ");

        $statement->execute(array(
            $ticket_number,
            $ticketId
        ));

        if ($statement->fetch(PDO::FETCH_ASSOC)) {

            $valid = 0;

            $error_message .= 'This ticket number already exists<br>';
        }
    }


    /* Update */

    if ($valid == 1) {

        $ticketId = (int)$_REQUEST['id'];

        $statement = $pdo->prepare("
            UPDATE tbl_lottery_ticket
            SET
                ticket_number = ?,
                child_name = ?,
                class = ?,
                contact_number = ?,
                ticket_price = ?,
                prize = ?,
                status = ?
            WHERE ticket_id = ?
        ");

        $statement->execute(array(
            $ticket_number,
            $child_name,
            $class,
            $contact_number,
            $ticket_price,
            $prize,
            $status,
            $ticketId
        ));

        header('location: lottery.php?updated=1');
        exit;
    }
}


/* Get current ticket data */

$statement = $pdo->prepare("
    SELECT *
    FROM tbl_lottery_ticket
    WHERE ticket_id = ?
");

$statement->execute(array($_REQUEST['id']));

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {

    $ticket_number = $row['ticket_number'];
    $child_name = $row['child_name'];
    $class = $row['class'];
    $contact_number = $row['contact_number'];
    $ticket_price = $row['ticket_price'];
    $prize = $row['prize'];
    $status = $row['status'];
}

?>

<section class="content-header">

    <div class="content-header-left">
        <h1>Edit Lottery Ticket</h1>
    </div>

    <div class="content-header-right">
        <a href="lottery.php" class="btn btn-primary btn-sm">
            View All
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


            <form class="form-horizontal"
                  action=""
                  method="post">

                <div class="box box-info">

                    <div class="box-body">


                        <!-- Ticket Number -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Ticket Number <span>*</span>
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="ticket_number"
                                       value="<?php echo htmlspecialchars($ticket_number); ?>">

                            </div>

                        </div>


                        <!-- Child Name -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Child Name <span>*</span>
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="child_name"
                                       value="<?php echo htmlspecialchars($child_name); ?>">

                            </div>

                        </div>


                        <!-- Guardian Name -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Class
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="class"
                                       value="<?php echo htmlspecialchars($class); ?>">

                            </div>

                        </div>


                        <!-- Contact Number -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Contact Number
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="contact_number"
                                       value="<?php echo htmlspecialchars($contact_number); ?>">

                            </div>

                        </div>


                        <!-- Ticket Price -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Ticket Price
                            </label>

                            <div class="col-sm-3">

                                <input type="number"
                                       autocomplete="off"
                                       class="form-control"
                                       name="ticket_price"
                                       min="0"
                                       step="0.01"
                                       value="<?php echo htmlspecialchars($ticket_price); ?>">

                            </div>

                        </div>


                        <!-- Prize -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Prize
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="prize"
                                       value="<?php echo htmlspecialchars($prize); ?>">

                            </div>

                        </div>


                        <!-- Status -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                Status
                            </label>

                            <div class="col-sm-3">

                                <select name="status"
                                        class="form-control">

                                    <option value="Available"
                                        <?php echo ($status == 'Available') ? 'selected' : ''; ?>>
                                        Available
                                    </option>

                                    <option value="Sold"
                                        <?php echo ($status == 'Sold') ? 'selected' : ''; ?>>
                                        Sold
                                    </option>

                                    <option value="Winner"
                                        <?php echo ($status == 'Winner') ? 'selected' : ''; ?>>
                                        Winner
                                    </option>

                                    <option value="Cancelled"
                                        <?php echo ($status == 'Cancelled') ? 'selected' : ''; ?>>
                                        Cancelled
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- Submit -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label"></label>

                            <div class="col-sm-6">

                                <button type="submit"
                                        class="btn btn-success pull-left"
                                        name="form1">
                                    Update
                                </button>

                            </div>

                        </div>


                    </div>

                </div>

            </form>

        </div>

    </div>

</section>


<?php require_once('footer.php'); ?>