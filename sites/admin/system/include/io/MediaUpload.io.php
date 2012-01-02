<?
ensurePolicies('AllowCreateMedia');

interport('productID', 'localeID');

$originalFilename = $_FILES['upload']['name'];
$uploadedFile = $_FILES['upload']['tmp_name'];

if( !$Media = \Operation\Media::importFileToLibrary($uploadedFile, $fileOriginalName) )
	throw New Exception(sprintf(_('Filen kändes inte igen: "%s"'), $fileOriginalName));

if($productID)
{
	ensurePolicies('AllowEditProduct');

	$query = DB::prepareQuery("INSERT IGNORE INTO
		ProductMedia (
			productID,
			mediaID,
			isEnabled,
			sortOrder)
		VALUES(
			%u,
			%u,
			1,
			UNIX_TIMESTAMP())",
		$productID,
		$Media->mediaID);

	if( DB::queryAndCountAffected($query) == 0 ) Message::addAlert(sprintf(_('Filen "%s" är redan uppladdad och kopplad till denna produkt.'), $originalFilename));

	if( $localeID )
	{
		// Enable for only this locale
		//$query = DB::prepareQuery("INSERT IGNORE INTO ProductMediaLocale (productID, mediaID, localeID, isEnabled) VALUES(%d, %d, %d, %d)", $productID, $mediaID, $localeID, $isEnabled = 1);

		// Enable for all locales
		$query = DB::prepareQuery("INSERT IGNORE INTO
			ProductMediaLocale (
				productID,
				mediaID,
				localeID,
				isEnabled)
			SELECT
				%u,
				%u,
				ID,
				%u
			FROM Locales",
			$productID,
			$Media->mediaID,
			$isEnabled = 1);

		DB::queryAndGetID($query);
	}
}

Message::addCall('reloadListing("#media");');

$action = 'success';