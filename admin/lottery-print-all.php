<?php
require_once('header.php');

/*
|--------------------------------------------------------------------------
| LOTTERY ANNOUNCEMENT DATE & TIME
|--------------------------------------------------------------------------
*/

$announceDateTime = date('d F Y, h:i A');


/*
|--------------------------------------------------------------------------
| FETCH ALL LOTTERY TICKETS
|--------------------------------------------------------------------------
*/

$statement = $pdo->prepare("
    SELECT *
    FROM tbl_lottery_ticket
    ORDER BY ticket_id ASC
");

$statement->execute();

$tickets = $statement->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| FUNCTION: GET STUDENT CLASS
|--------------------------------------------------------------------------
*/

function getStudentClass($ticket)
{
    if (isset($ticket['class_name']) && $ticket['class_name'] !== '') {
        return $ticket['class_name'];
    }

    if (isset($ticket['student_class']) && $ticket['student_class'] !== '') {
        return $ticket['student_class'];
    }

    if (isset($ticket['class']) && $ticket['class'] !== '') {
        return $ticket['class'];
    }

    if (isset($ticket['class_nm']) && $ticket['class_nm'] !== '') {
        return $ticket['class_nm'];
    }

    return 'N/A';
}

?>

<style>

/*
|--------------------------------------------------------------------------
| GENERAL
|--------------------------------------------------------------------------
*/

body {
    background: #fff !important;
}


/*
|--------------------------------------------------------------------------
| PRINT PAGE
|--------------------------------------------------------------------------
*/

.print-page {
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
}


/*
|--------------------------------------------------------------------------
| PRINT HEADER
|--------------------------------------------------------------------------
*/

.print-header {
    text-align: center;
    margin-bottom: 25px;
}

.print-header h1 {
    margin: 0;
    font-size: 30px;
    font-weight: bold;
    text-transform: uppercase;
}

.print-header h2 {
    margin: 6px 0 0;
    font-size: 22px;
    font-weight: bold;
}

.print-header p {
    margin: 6px 0 0;
    color: #555;
    font-size: 14px;
}


/*
|--------------------------------------------------------------------------
| TICKETS CONTAINER
|--------------------------------------------------------------------------
*/

.tickets-container {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
    width: 100%;
}


/*
|--------------------------------------------------------------------------
| LOTTERY TICKET
|--------------------------------------------------------------------------
|
| LANDSCAPE TICKET
|
*/

.lottery-ticket {
    width: 100%;
    height: 260px;

    border: 2px dashed #333;

    padding: 15px;

    position: relative;

    background: #fff;

    box-sizing: border-box;

    page-break-inside: avoid;
    break-inside: avoid;

    overflow: hidden;
}


/*
|--------------------------------------------------------------------------
| SCHOOL NAME
|--------------------------------------------------------------------------
*/

.school-name {
    text-align: center;

    font-size: 21px;

    font-weight: bold;

    margin-bottom: 4px;

    text-transform: uppercase;

    line-height: 1.2;
}


/*
|--------------------------------------------------------------------------
| TICKET TITLE
|--------------------------------------------------------------------------
*/

.ticket-title {
    text-align: center;

    font-size: 18px;

    font-weight: bold;

    margin-bottom: 8px;

    border-bottom: 1px solid #333;

    padding-bottom: 7px;

    text-transform: uppercase;
}


/*
|--------------------------------------------------------------------------
| BARCODE
|--------------------------------------------------------------------------
*/

.barcode-container {
    text-align: center;

    margin: 5px 0 8px;
}

.barcode {
    width: 180px;

    height: 45px;

    max-width: 100%;
}


/*
|--------------------------------------------------------------------------
| TICKET NUMBER
|--------------------------------------------------------------------------
*/

.ticket-number {
    text-align: center;

    font-size: 20px;

    font-weight: bold;

    margin: 5px 0 12px;
}


/*
|--------------------------------------------------------------------------
| TICKET DETAILS
|--------------------------------------------------------------------------
*/

.ticket-row {
    display: flex;

    margin-bottom: 7px;

    font-size: 14px;

    line-height: 1.4;
}

.ticket-label {
    width: 125px;

    font-weight: bold;

    flex-shrink: 0;
}

.ticket-value {
    flex: 1;

    word-break: break-word;
}


/*
|--------------------------------------------------------------------------
| PRINT BUTTONS
|--------------------------------------------------------------------------
*/

.print-button-container {
    text-align: center;

    margin-bottom: 25px;
}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

@media print {

    /*
    |--------------------------------------------------------------------------
    | A4 LANDSCAPE
    |--------------------------------------------------------------------------
    */

    @page {
        size: A4 landscape;
        margin: 5mm;
    }


    /*
    |--------------------------------------------------------------------------
    | HIDE ADMIN ELEMENTS
    |--------------------------------------------------------------------------
    */

    .print-button-container,
    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header,
    .navbar,
    .sidebar,
    .footer,
    .breadcrumb {
        display: none !important;
    }


    /*
    |--------------------------------------------------------------------------
    | BODY
    |--------------------------------------------------------------------------
    */

    html,
    body {
        width: 100% !important;

        margin: 0 !important;

        padding: 0 !important;

        background: #fff !important;
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN CONTENT
    |--------------------------------------------------------------------------
    */

    .wrapper,
    .content-wrapper,
    .main-content,
    .content,
    section.content {
        width: 100% !important;

        min-height: 0 !important;

        margin: 0 !important;

        padding: 0 !important;

        background: #fff !important;
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT PAGE
    |--------------------------------------------------------------------------
    */

    .print-page {
        width: 100% !important;

        margin: 0 !important;

        padding: 0 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    .print-header {
        margin-bottom: 5mm !important;
    }

    .print-header h1 {
        font-size: 24px !important;

        margin: 0 !important;
    }

    .print-header h2 {
        font-size: 18px !important;

        margin: 3px 0 0 !important;
    }

    .print-header p {
        font-size: 11px !important;

        margin: 3px 0 0 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | TICKETS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Same 2-column layout as screen.
    |
    */

    .tickets-container {
        display: grid !important;

        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;

        gap: 5mm !important;

        width: 100% !important;

        margin: 0 !important;

        padding: 0 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | LANDSCAPE TICKET
    |--------------------------------------------------------------------------
    |
    | SAME SIZE / DESIGN AS SCREEN
    |
    */

    .lottery-ticket {
        width: 100% !important;

        height: 85mm !important;

        min-height: 0 !important;

        padding: 15px !important;

        border: 2px dashed #333 !important;

        box-sizing: border-box !important;

        background: #fff !important;

        overflow: hidden !important;

        page-break-inside: avoid !important;

        break-inside: avoid !important;
    }


    /*
    |--------------------------------------------------------------------------
    | SCHOOL NAME
    |--------------------------------------------------------------------------
    */

    .school-name {
        font-size: 21px !important;

        margin-bottom: 4px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    .ticket-title {
        font-size: 18px !important;

        margin-bottom: 8px !important;

        padding-bottom: 7px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */

    .barcode-container {
        margin: 5px 0 8px !important;
    }

    .barcode {
        width: 180px !important;

        height: 45px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | TICKET NUMBER
    |--------------------------------------------------------------------------
    */

    .ticket-number {
        font-size: 20px !important;

        margin: 5px 0 12px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | DETAILS
    |--------------------------------------------------------------------------
    */

    .ticket-row {
        font-size: 14px !important;

        margin-bottom: 7px !important;

        line-height: 1.4 !important;
    }

    .ticket-label {
        width: 125px !important;
    }

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media screen and (max-width: 768px) {

    .tickets-container {
        grid-template-columns: 1fr;
    }

    .print-page {
        padding: 10px;
    }

    .lottery-ticket {
        height: 260px;
    }

    .school-name {
        font-size: 18px;
    }

    .ticket-title {
        font-size: 16px;
    }

}

</style>


<!--
|--------------------------------------------------------------------------
| BARCODE LIBRARY
|--------------------------------------------------------------------------
-->

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>


<!--
|--------------------------------------------------------------------------
| CONTENT HEADER
|--------------------------------------------------------------------------
-->

<section class="content-header">

    <div class="content-header-left">

        <h1>
            Print All Lottery Tickets
        </h1>

    </div>

</section>


<!--
|--------------------------------------------------------------------------
| CONTENT
|--------------------------------------------------------------------------
-->

<section class="content">


    <!--
    |--------------------------------------------------------------------------
    | PRINT BUTTONS
    |--------------------------------------------------------------------------
    -->

    <div class="print-button-container">

        <button
            type="button"
            onclick="window.print();"
            class="btn btn-primary">

            <i class="fa fa-print"></i>

            Print All Tickets

        </button>


        <button
            type="button"
            onclick="window.close();"
            class="btn btn-default">

            Close

        </button>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | PRINT PAGE
    |--------------------------------------------------------------------------
    -->

    <div class="print-page">


        <!--
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        -->

        <div class="print-header">

            <h1>
                Rajan Memorial International School
            </h1>

            <h2>
                Lottery Ticket
            </h2>

            <p>
                Total Tickets:
                <?php echo count($tickets); ?>
            </p>

        </div>


        <?php if (empty($tickets)): ?>


            <div class="alert alert-warning text-center">

                No lottery tickets found.

            </div>


        <?php else: ?>


            <!--
            |--------------------------------------------------------------------------
            | TICKETS
            |--------------------------------------------------------------------------
            -->

            <div class="tickets-container">


                <?php foreach ($tickets as $ticket): ?>


                    <?php

                    $ticketNumber = $ticket['ticket_number'] ?? '';

                    $studentName = $ticket['child_name'] ?? '';

                    $studentClass = getStudentClass($ticket);

                    ?>


                    <!--
                    |--------------------------------------------------------------------------
                    | SINGLE LANDSCAPE TICKET
                    |--------------------------------------------------------------------------
                    -->

                    <div class="lottery-ticket">


                        <!-- SCHOOL NAME -->

                        <div class="school-name">

                            Rajan Memorial International School

                        </div>


                        <!-- TICKET TITLE -->

                        <div class="ticket-title">

                            Lottery Ticket

                        </div>


                        <!-- BARCODE -->

                        <div class="barcode-container">

                            <svg
                                class="barcode"
                                data-ticket="<?php
                                    echo htmlspecialchars(
                                        $ticketNumber,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>">
                            </svg>

                        </div>


                        <!-- TICKET NUMBER -->

                        <div class="ticket-number">

                            Ticket Number:

                            <?php
                            echo htmlspecialchars(
                                $ticketNumber,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </div>


                        <!-- STUDENT NAME -->

                        <div class="ticket-row">

                            <div class="ticket-label">

                                Name:

                            </div>

                            <div class="ticket-value">

                                <?php
                                echo htmlspecialchars(
                                    $studentName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </div>

                        </div>


                        <!-- CLASS -->

                        <div class="ticket-row">

                            <div class="ticket-label">

                                Class:

                            </div>

                            <div class="ticket-value">

                                <?php
                                echo htmlspecialchars(
                                    $studentClass,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </div>

                        </div>


                        <!-- ANNOUNCEMENT -->

                        <div class="ticket-row">

                            <div class="ticket-label">

                                Announcement:

                            </div>

                            <div class="ticket-value">

                                <?php
                                echo htmlspecialchars(
                                    $announceDateTime,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </div>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </div>

</section>


<script>

/*
|--------------------------------------------------------------------------
| GENERATE BARCODES
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.barcode').forEach(function (barcode) {

        var ticketNumber =
            barcode.getAttribute('data-ticket');

        if (ticketNumber) {

            JsBarcode(
                barcode,
                ticketNumber,
                {
                    format: "CODE128",

                    width: 2,

                    height: 45,

                    displayValue: false,

                    margin: 0
                }
            );

        }

    });

});


/*
|--------------------------------------------------------------------------
| AUTO PRINT
|--------------------------------------------------------------------------
*/

window.addEventListener('load', function () {

    setTimeout(function () {

        window.print();

    }, 700);

});

</script>


<?php
require_once('footer.php');
?>