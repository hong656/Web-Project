<?php echo "Server is working"; ?><?php
header('Content-Type: application/json');
echo json_encode(['status' => 'working', 'directory' => __DIR__]);