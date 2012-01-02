<?
//header("Content-type: text/plain; charset=UTF-8");
header("Content-type: text/html; charset=UTF-8");

require_once('../../Init.inc.php');

$Request = new Request($_GET['requestID']);

$DeliveryAddress = $Request->getDeliveryAddress();

$mappings = array(
	1554 => 100051, // Chokladprovning för 2
	1577 => 100187, // Zorbing för 2
	1570 => 100114, // Ölprovning för 2
	1572 => 100118, // RIB-safari för 2
	1549 => 100025, // Ballongflyg för 2
	1575 => 100144, // Folkrace för 2
	1567 => 100093, // Vinprovning för 2
	1565 => 100090, // Whiskyprovning för 2
	1558 => 100060, // Provflyg för 2
	1578 => 100214, // Äventyrsbana för 2
	1582 => 100247, // Spökmiddag för 2
	1563 => 100079, // Herrgårdsupplevelse för 2
	1583 => 100269, // Ost och vinprovning för 2
	1544 => 100010, // Spadag klassisk för 2
	1580 => 100245, // Spökvandring för 2
	1545 => 100011, // Spadag special för 2

	1547 => 100014, // Provflyg
	1568 => 100111, // Bungy jump
	1574 => 100140, // Folkrace
	1546 => 100013, // Champagne ballongflyg
	1554 => 100050, // Chokladprovning
	1579 => 100234, // Pistolskytte
	1571 => 100117, // RIB-safari
	1566 => 100092, // Vinprovning
	1569 => 100113, // Ölprovning
	1559 => 100061, // Fallskärm - tandemhopp
	1564 => 100089, // Whiskyprovning
	1543 => 100002, // Tandemskärmflyg
	1560 => 100070, // Kör rally
	1584 => 100296, // Kör Ferrari eller Lamborghini
	1576 => 100311, // Porscheägare för en dag
	1552 => 100047, // F1-paket 1
	1553 => 100048, // F1-paket 2
	1551 => 100040, // Flyg ett E.E. Lightning
	1550 => 100036, // Stridsflygsimulator
	1573 => 100135, // Specialbehandling
	1561 => 100227, // Klassisk behandling
	1562 => 100076, // Klappa varg
	1581 => 100246, // Spökmiddag
	1557 => 100056, // Makeuprådgivning
	1548 => 100019, // Provflyg en helikopter
	1556 => 100053 // Prova på dyk
);

?>
<html>
	<head>
		<style type="text/css">
			td { vertical-align: top; }
		</style>
	</head>
	<body>

		<form action="http://www.liveit.se/api/coolstuff_order.php" method="post">
			<table>
				<tr>
					<td>
						<h2>Order</h2>
						<? echo HTML::text('dt_ref', $Request->requestID); ?><br />
					</td>
					<td>
						<h2>Köpare</h2>
						<? echo HTML::text('u_namn', ''); ?><br />
						<? echo HTML::text('u_foretag', 'CoolStuff'); ?><br />
						<? echo HTML::text('u_gatuadd', 'Bjurögatan 44'); ?><br />
						<? echo HTML::text('u_postnum', '211 22'); ?><br />
						<? echo HTML::text('u_postadd', 'Malmö'); ?><br />
						<? echo HTML::text('u_landskod', '752'); ?><br />
						<? echo HTML::text('u_telefon', '040187200'); ?><br />
						<? echo HTML::text('u_email', 'info@coolstuff.se'); ?><br />
					</td>
					<td>
						<h2>Mottagare</h2>
						<? echo HTML::text('r_namn', $DeliveryAddress->name); ?><br />
						<? echo HTML::text('r_foretag', $DeliveryAddress->company); ?><br />
						<? echo HTML::text('r_gatuadd', $DeliveryAddress->street . ($DeliveryAddress->streetno ? ' ' . $DeliveryAddress->streetno : '')); ?><br />
						<? echo HTML::text('r_postnum', $DeliveryAddress->postcode); ?><br />
						<? echo HTML::text('r_postadd', $DeliveryAddress->city); ?><br />
						<? echo HTML::text('r_landskod', '752'); ?><br />
						<? echo HTML::text('r_telefon', $Request->getPhone()); ?><br />
					</td>
					<td>
						<? echo HTML::button('Skicka'); ?>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
