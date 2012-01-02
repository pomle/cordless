<?
header("Content-type: text/plain; charset=UTF-8");

require_once('../../Init.inc.php');

$Request = new Request($_GET['requestID']);

echo "Adress:\n";
echo $Request->getDeliveryAddress();
echo "\n\n";

echo 'Tel: ' . $Request->getPhone() . "\n";
echo 'Email: ' . $Request->getEmail() . "\n";
echo "\n";