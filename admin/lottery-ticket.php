<?php
require_once('header.php');

/*
|--------------------------------------------------------------------------
| Get Ticket ID
|--------------------------------------------------------------------------
*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ticket ID.');
}

$ticket_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| Fetch Lottery Ticket
|--------------------------------------------------------------------------
*/
$statement = $pdo->prepare("
    SELECT *
    FROM tbl_lottery_ticket
    WHERE ticket_id = ?
    LIMIT 1
");

$statement->execute([$ticket_id]);

$ticket = $statement->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Lottery ticket not found.');
}
?>

<style>
    .ticket-page {
        padding: 20px 0;
    }

    .ticket-container {
        width: 700px;
        max-width: 100%;
        margin: 20px auto;
        background: #fff;
        border: 2px solid #222;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .ticket-header {
        background: #3c8dbc;
        color: #fff;
        padding: 20px;
        text-align: center;
    }

    .ticket-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: bold;
    }

    .ticket-header p {
        margin: 5px 0 0;
        font-size: 14px;
    }

    .ticket-number-section {
        text-align: center;
        padding: 20px;
        background: #f5f5f5;
        border-bottom: 1px dashed #777;
    }

    .ticket-number-label {
        font-size: 14px;
        color: #555;
        text-transform: uppercase;
        font-weight: bold;
    }

    .ticket-number {
        font-size: 36px;
        font-weight: bold;
        color: #222;
        letter-spacing: 3px;
        margin-top: 5px;
    }

    .ticket-details {
        padding: 20px 30px;
    }

    .detail-row {
        display: flex;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }

    .detail-label {
        width: 40%;
        font-weight: bold;
        color: #555;
    }

    .detail-value {
        width: 60%;
        color: #222;
    }

    .ticket-footer {
        padding: 15px 20px;
        background: #f5f5f5;
        text-align: center;
        border-top: 1px dashed #777;
        font-size: 13px;
        color: #555;
    }

    .print-button-area {
        text-align: center;
        margin: 20px 0;
    }

    @media print {

        body * {
            visibility: hidden;
        }

        .ticket-container,
        .ticket-container * {
            visibility: visible;
        }

        .ticket-container {
            position: absolute;
            left: 50%;
            top: 20px;
            transform: translateX(-50%);
            width: 700px;
            box-shadow: none;
            margin: 0;
        }

        .print-button-area {
            display: none !important;
        }

        .main-footer,
        .main-header,
        .content-header,
        .sidebar {
            display: none !important;
        }
    }
</style>


<section class="content-header">
    <div class="content-header-left">
        <h1>Generate Lottery Ticket</h1>
    </div>

    <div class="content-header-right">
        <a href="javascript:window.print();" class="btn btn-success btn-sm">
            <i class="fa fa-print"></i> Print Ticket
        </a>

        <a href="lottery.php" class="btn btn-default btn-sm">
            Back
        </a>
    </div>
</section>


<section class="content">

    <div class="row">

        <div class="col-md-12">

            <div class="ticket-page">

                <div class="ticket-container">

                    <!-- Ticket Header -->
                    <div class="ticket-header">

                        <h1>LOTTERY TICKET</h1>

                        <p>
                            Official Lottery Entry
                        </p>

                    </div>


                    <!-- Ticket Number -->
                    <div class="ticket-number-section">

                        <div class="ticket-number-label">
                            Ticket Number
                        </div>

                        <div class="ticket-number">
                            <?php
                            echo htmlspecialchars($ticket['ticket_number']);
                            ?>
                        </div>

                    </div>


                    <!-- Ticket Details -->
                    <div class="ticket-details">

                        <div class="detail-row">

                            <div class="detail-label">
                                Child Name
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['child_name']);
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Guardian Name
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['class'] ?? '');
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Contact Number
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['contact_number'] ?? '');
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Ticket Price
                            </div>

                            <div class="detail-value">
                                <?php
                                echo number_format(
                                    (float)$ticket['ticket_price'],
                                    2
                                );
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Prize
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['prize'] ?? '');
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Status
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['status'] ?? '');
                                ?>
                            </div>

                        </div>


                        <div class="detail-row">

                            <div class="detail-label">
                                Date
                            </div>

                            <div class="detail-value">
                                <?php
                                echo htmlspecialchars($ticket['created_at'] ?? '');
                                ?>
                            </div>

                        </div>

                    </div>


                    <!-- Footer -->
                    <div class="ticket-footer">

                        <strong>
                            Please keep this ticket safe.
                        </strong>

                        <br>

                        This ticket is required for lottery verification.

                    </div>

                </div>


                <!-- Print Button -->
                <div class="print-button-area">

                    <button
                        type="button"
                        class="btn btn-success"
                        onclick="window.print();">

                        <i class="fa fa-print"></i>
                        Print Ticket

                    </button>

                    <a
                        href="lottery.php"
                        class="btn btn-default">

                        Back

                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<?php require_once('footer.php'); ?>