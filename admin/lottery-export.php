<?php

ob_start();
session_start();

include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

$csrf = new CSRF_Protect();

$error_message = '';
$success_message = '';

/*
 * Check login
 */

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Export Filename
|--------------------------------------------------------------------------
*/

$filename = 'lottery-tickets-' . date('Y-m-d-H-i-s') . '.csv';


/*
|--------------------------------------------------------------------------
| Get Lottery Tickets
|--------------------------------------------------------------------------
*/

$statement = $pdo->prepare("
    SELECT
        ticket_number,
        child_name,
        class,
        contact_number,
        ticket_price,
        prize,
        status,
        created_at
    FROM tbl_lottery_ticket
    ORDER BY ticket_id ASC
");

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| CSV Response Headers
|--------------------------------------------------------------------------
*/

header('Content-Type: text/csv; charset=utf-8');

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header('Pragma: no-cache');
header('Expires: 0');


/*
|--------------------------------------------------------------------------
| Open CSV Output
|--------------------------------------------------------------------------
*/

$output = fopen('php://output', 'w');


/*
|--------------------------------------------------------------------------
| UTF-8 BOM
|
| Required for proper Excel support, especially for Nepali names.
|--------------------------------------------------------------------------
*/

fprintf(
    $output,
    chr(0xEF) . chr(0xBB) . chr(0xBF)
);


/*
|--------------------------------------------------------------------------
| CSV Header
|--------------------------------------------------------------------------
*/

fputcsv($output, array(
    'ticket_number',
    'child_name',
    'class',
    'contact_number',
    'ticket_price',
    'prize',
    'status',
    'created_at'
));


/*
|--------------------------------------------------------------------------
| CSV Rows
|--------------------------------------------------------------------------
*/

foreach ($result as $row) {

    fputcsv($output, array(
        $row['ticket_number'],
        $row['child_name'],
        $row['class'],
        $row['contact_number'],
        $row['ticket_price'],
        $row['prize'],
        $row['status'],
        $row['created_at']
    ));

}


fclose($output);

exit;