<?
if( is_numeric($args[0]) )
	$userGroupID = $args[0];
else
	$userGroupLabel = $args[0];

$name = $args[1];

$addAnonymous = $args[2];
$preSelected = $args[3];

if( $userGroupLabel )
	$userIDs = \Manager\Dataset\UserGroup::getUserIDsFromLabel($userGroupLabel);
else
	$userIDs = \Manager\Dataset\UserGroup::getUserIDs($userGroupID);

$users = \Manager\User::loadFromDB($userIDs);

$UserSelect = new \Element\SelectBox($name ?: 'userID', $preSelected ?: null);
foreach($users as $User)
	$UserSelect->addItem($User->name, $User->userID);

asort($UserSelect->items);

if( $addAnonymous )
	$UserSelect->items = array(0 => '[' . _('Anonym') . ']') + $UserSelect->items;

echo $UserSelect;