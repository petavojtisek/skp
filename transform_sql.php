<?php

$inputFile = 'products updates.sql';
$outputFile = 'routes_updates.sql';

$in = fopen($inputFile, 'r');
$out = fopen($outputFile, 'w');

if (!$in || !$out) {
    die("Could not open files.");
}

$count = 0;
while (($line = fgets($in)) !== false) {
    // 1. Basic REPLACE and table name
    $line = str_replace('INSERT INTO ``', 'REPLACE INTO `routes`', $line);
    
    // 2. Remove `route_id`, from column list
    $line = str_replace('(`route_id`, ', '(', $line);
    
    // 3. Remove the first value (route_id) from the VALUES list
    // Pattern: VALUES (NUMBER, 
    $line = preg_replace('/VALUES \(\d+, /', 'VALUES (', $line);
    
    fwrite($out, $line);
    $count++;
}

fclose($in);
fclose($out);

echo "Processed $count lines. Saved to $outputFile\n";
