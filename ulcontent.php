<?php
date_default_timezone_set("Asia/Taipei");
// 定義 $i 變數


$html = str_get_html($htmlString); // Assuming $htmlString holds the HTML content


$data = [];
// Iterate through table rows with data
foreach ($html->find('table#ObjTable tr:not(:first-child, :last-child, :nth-child(-n+2), :nth-child(-6n))') as $row) {
    // Get header and corresponding value cells
    $headerCell = $row->find('td', 0);
    $valueCell = $row->find('td', 1);

    // Check if valid cells found
    if ($headerCell && $valueCell) {
        // Trim text contents for cleanliness
        $header = trim($headerCell->plaintext);
        $value = trim($valueCell->plaintext);

        // Add key-value pair to the data array
        $data[$header] = $value;
    }
}




$json = json_encode($data);


?>