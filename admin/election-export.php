<?php
// Include ONLY the database/config file.
// Replace this with the correct file if your project uses another filename.
require_once('inc/config.php');

// Clear any previous output
if (ob_get_length()) {
    ob_end_clean();
}

// Force download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="election_candidates.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM for Excel (fixes Nepali text)
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Main Heading
fputcsv($output, [
    'Election Post',
    'Rank',
    'Candidate Name',
    'Votes'
]);

$statement = $pdo->prepare("
    SELECT *,
           DENSE_RANK() OVER (
               PARTITION BY election_post
               ORDER BY vote_count DESC
           ) AS rank_position
    FROM tbl_elections
    WHERE is_active = 1
    ORDER BY election_post ASC,
             rank_position ASC,
             candidate_name ASC
");

$statement->execute();

$data = $statement->fetchAll(PDO::FETCH_ASSOC);

$currentPost = '';

foreach ($data as $row) {

    if ($currentPost != $row['election_post']) {

        $currentPost = $row['election_post'];

        fputcsv($output, []);
        fputcsv($output, [$currentPost]);

        fputcsv($output, [
            '',
            'Rank',
            'Candidate',
            'Votes',
        ]);
    }

fputcsv($output, [
    '',
    $row['rank_position'],
    $row['candidate_name'],
    $row['vote_count']
]);
}

fclose($output);
exit;