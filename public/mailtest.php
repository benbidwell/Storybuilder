<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
$stream = imap_open("{imap.one.com}INBOX.Sent", "rahul.rawat@walkwel.in", "rahulwalk");

$check = imap_check($stream);
echo "Msg Count before append: ". $check->Nmsgs . "\n";

imap_append($stream, "{imap.one.com}INBOX.Sent"
                   , "From: info@walkwel.com\r\n"
                   . "To: rahul.rawat@walkwel.com\r\n"
                   . "Subject: test\r\n"
                   . "\r\n"
                   . "this is a test message, please ignore\r\n"
                   );

$check = imap_check($stream);
echo "Msg Count after append : ". $check->Nmsgs . "\n";

imap_close($stream);
?>

