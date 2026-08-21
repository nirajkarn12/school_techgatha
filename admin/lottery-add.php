<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    $ticket_number = trim($_POST['ticket_number'] ?? '');
    $child_name = trim($_POST['child_name'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $ticket_price = trim($_POST['ticket_price'] ?? '');
    $prize = trim($_POST['prize'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');

    if ($ticket_number == '') {
        $valid = 0;
        $error_message .= 'Ticket number can not be empty<br>';
    }

    if ($child_name == '') {
        $valid = 0;
        $error_message .= 'Child name can not be empty<br>';
    }

    if ($ticket_price == '') {
        $ticket_price = 0;
    }

    if (!is_numeric($ticket_price) || $ticket_price < 0) {
        $valid = 0;
        $error_message .= 'Ticket price must be a valid amount<br>';
    }

    $allowed_statuses = ['Available', 'Sold', 'Winner', 'Cancelled'];

    if (!in_array($status, $allowed_statuses, true)) {
        $valid = 0;
        $error_message .= 'Invalid ticket status<br>';
    }

    // Check duplicate ticket number
    if ($valid == 1) {

        $statement = $pdo->prepare("
            SELECT ticket_id
            FROM tbl_lottery_ticket
            WHERE ticket_number = ?
        ");

        $statement->execute([$ticket_number]);

        if ($statement->fetch(PDO::FETCH_ASSOC)) {
            $valid = 0;
            $error_message .= 'This ticket number already exists<br>';
        }
    }

    if ($valid == 1) {

        $statement = $pdo->prepare("
            INSERT INTO tbl_lottery_ticket
            (
                ticket_number,
                child_name,
                class,
                contact_number,
                ticket_price,
                prize,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $statement->execute([
            $ticket_number,
            $child_name,
            $class,
            $contact_number,
            $ticket_price,
            $prize,
            $status
        ]);

        header('location: lottery.php?success=1');
        exit;
    }
}
?>

<section class="content-header">

    <div class="content-header-left">
        <h1>Add Lottery Ticket</h1>
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
                                       placeholder="e.g. LT-0001"
                                       value="<?php echo isset($_POST['ticket_number']) ? htmlspecialchars($_POST['ticket_number']) : ''; ?>">

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
                                       value="<?php echo isset($_POST['child_name']) ? htmlspecialchars($_POST['child_name']) : ''; ?>">

                            </div>

                        </div>


                        <!-- Guardian Name -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                               class
                            </label>

                            <div class="col-sm-6">

                                <input type="text"
                                       autocomplete="off"
                                       class="form-control"
                                       name="class"
                                       value="<?php echo isset($_POST['class']) ? htmlspecialchars($_POST['class']) : ''; ?>">

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
                                       value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>">

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
                                       value="<?php echo isset($_POST['ticket_price']) ? htmlspecialchars($_POST['ticket_price']) : '0'; ?>">

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
                                       placeholder="e.g. Bicycle, School Bag, Gift Hamper"
                                       value="<?php echo isset($_POST['prize']) ? htmlspecialchars($_POST['prize']) : ''; ?>">

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
                                        <?php echo (isset($_POST['status']) && $_POST['status'] == 'Available') ? 'selected' : ''; ?>>
                                        Available
                                    </option>

                                    <option value="Sold"
                                        <?php echo (isset($_POST['status']) && $_POST['status'] == 'Sold') ? 'selected' : ''; ?>>
                                        Sold
                                    </option>

                                    <option value="Winner"
                                        <?php echo (isset($_POST['status']) && $_POST['status'] == 'Winner') ? 'selected' : ''; ?>>
                                        Winner
                                    </option>

                                    <option value="Cancelled"
                                        <?php echo (isset($_POST['status']) && $_POST['status'] == 'Cancelled') ? 'selected' : ''; ?>>
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
                                    Submit
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