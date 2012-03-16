<?
use
	\Asenine\Format,
	\Asenine\Element\Input,
	\Asenine\Element\TextArea,
	\Asenine\Element\SelectBox,
	\Asenine\DB;

if( !isset($_GET['userGroupID']) )
{
	define('ACCESS_POLICY', 'AllowCreateUserGroup');
	require '../Init.inc.php';
	$userGroupID = \Asenine\UserGroup\Manager::addToDB();
	header('Location: ?userGroupID=' . $userGroupID);
	exit();
}

define('ACCESS_POLICY', 'AllowViewUserGroup');
require '../Init.inc.php';


if( !$UserGroup = \Asenine\UserGroup::loadFromDB($_GET['userGroupID']) )
	echo \Element\Page::error("UserGroup does not exist");


$userGroupID = $UserGroup->userGroupID;

$pageTitle = _('System');
$pageSubtitle = _('Användargrupp') . ' #' . $userGroupID;

require HEADER;
?>
<div class="tabs">
	<?
	$Tabs = new \Element\Tabs();
	echo $Tabs
		->addTab('properties', _('Egenskaper'), 'layers')
		->addTab('policies', _('Rättigheter'), 'key')
		->addTab('users', _('Användare'), 'group');

	$IO = new \Element\IOCall('UserGroup', array('userGroupID' => $userGroupID));
	$SaveButton = new \Element\Button\Save();

	echo $IO->getHead();
	?>
	<fieldset class="tab" id="properties">
		<legend><? echo \Element\Tag::legend('vcard', _('Egenskaper')); ?></legend>
		<?
		$size = 40;
		echo \Element\Table::inputs()
			->addRow(_('Benämning'), Input::text('name', $UserGroup->name)->size($size))
			->addRow(_('Etikett'), Input::text('label', $UserGroup->label)->size($size))
			->addRow(_('Beskrivning'), TextArea::small('description', $UserGroup->description))
			->addRow(_('Kan tilldelas uppgifter'), Input::checkbox('isTaskAssignable', $UserGroup->isTaskAssignable));

		$SaveButton->action = 'saveGeneral';
		echo \Element\IOControl::makeOf($IO)
			->addButton($SaveButton)
			->addButton(new \Element\Button\Delete('deleteUserGroup'));
		?>
	</fieldset>
	<?
	echo $IO->getFoot();

echo $IO->getHead();
	?>
	<fieldset class="tab" id="policies">
		<legend><? echo \Element\Tag::legend('key', _('Rättigheter')); ?></legend>
		<?
		$Table = \Element\Table::inputs();

		### Gets policies that current user has (or all if admin) and weather edited group has it
		$query = DB::prepareQuery("SELECT
				p.ID AS policyID,
				p.policy,
				p.description,
				(NOT ugpC.policyID IS NULL) AS hasPolicy
			FROM
				Policies p
				LEFT JOIN UserPolicies upA ON upA.policyID = p.ID AND upA.userID = %u
				LEFT JOIN UserGroupPolicies ugpC ON ugpC.policyID = p.ID AND ugpC.userGroupID = %u
			WHERE
				upA.policyID OR %u = 1
			ORDER BY
				p.policy ASC",
			USER_ID,
			$userGroupID,
			USER_IS_ADMIN);

		$result = DB::queryAndFetchResult($query);

		while(list($policyID, $policy, $desc, $hasPolicy) = DB::row($result))
			$Table->addRow($policy, Input::checkbox('policyIDs[]', $hasPolicy, $policyID), $desc);

		echo $Table;

		$SaveButton->action = 'savePolicies';
		echo \Element\IOControl::makeOf($IO)->addButton($SaveButton);
		?>
	</fieldset>
	<?
	echo $IO->getFoot();

	echo $IO->getHead();
	?>
	<fieldset class="tab" id="users">
		<legend><? echo \Element\Tag::legend('group', _('Användare')); ?></legend>
		<?
		$Table = \Element\Table::inputs();

		### Gets Users that has a username (thus can login) and weather they are members of current group
		$query = DB::prepareQuery("SELECT
				u.ID AS userID,
				u.username,
				u.fullname,
				(NOT ugu.userID IS NULL) AS hasUser
			FROM
				Users u
				LEFT JOIN UserGroupUsers ugu ON ugu.userID = u.ID AND ugu.userGroupID = %u
			WHERE
				NOT u.username IS NULL
			ORDER BY
				u.username COLLATE || ASC",
			$userGroupID);

		$result = DB::queryAndFetchResult($query);

		$allowEdit = $User->hasPolicy('AllowUserEdit');

		while(list($userID, $username, $fullname, $hasUser) = DB::row($result))
			$Table->addRow($allowEdit ? sprintf('<a href="/UserEdit.php?userID=%u">%s</a>', $userID, $username) : $username, Input::checkbox('userIDs[]', $hasUser, $userID), $fullname);

		echo $Table;

		$SaveButton->action = 'saveUsers';
		echo \Element\IOControl::makeOf($IO)->addButton($SaveButton);
		?>
	</fieldset>
	<?
	echo $IO->getFoot();
	?>
</div>
<?
require FOOTER;
