<?php

// Configuration
$ftp_server = "212.18.29.164";
$ftp_user = "aln234411-at";
$ftp_pass = "tAJqhhpe2fCNwq";
$server_file = "AllnetAustria_Internal_ALL_VK.csv"; // Path on the server
$local_file = __DIR__ . "/pricelist.csv"; // Path on your machine

// 1. Establish connection
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

// 2. Login
if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    echo "Connected as $ftp_user\n";

    // 3. Set Passive Mode (Crucial for most modern firewalls/NAT)
    ftp_pasv($conn_id, true);

    // 4. Attempt to download
    if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
        echo "Successfully downloaded $server_file to $local_file\n";
    } else {
        echo "There was a problem downloading the file.\n";
    }
} else {
    echo "Couldn't connect as $ftp_user\n";
}

// 5. Close connection
ftp_close($conn_id);
