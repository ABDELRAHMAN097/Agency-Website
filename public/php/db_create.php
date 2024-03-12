<?php
$statements = [
    'CREATE TABLE IF NOT EXISTS app_info( 
        record_id           INT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        access_token        VARCHAR(255) NOT NULL,
        refresh_token       VARCHAR(255) NOT NULL,
        task_id             VARCHAR(255) NOT NULL,
        created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
        last_update         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )',
];

// execute SQL statements
foreach ($statements as $statement) {
    $pdo->exec($statement);
    echo "Table Done";
}
