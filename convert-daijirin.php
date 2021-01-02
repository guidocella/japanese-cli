<?php

// From Stardict's documentation:
//
// Here is a example dict.tab file:
// ============
// a	1\n2\n3
// b	4\\5\n6
// c	789
// ============
// It means: write the search word first, then a Tab character, and the definition. If the definition contains new line, just write \n, if contains \ character, just write \\.

$files = glob('term_bank*');
natsort($files);

foreach ($files as $file) {
    echo "$file\n";

    $daijirin = '';

    foreach (json_decode(file_get_contents($file)) as $row) {
        $daijirin .= "{$row[0]}\t".str_replace("\n", '\n', $row[5][0])."\n";
        if ($row[1]) {
            $daijirin .= "{$row[1]}\t".str_replace("\n", '\n', $row[5][0])."\n";
        }
    }

    file_put_contents('daijirin.tab', $daijirin, FILE_APPEND);
}
