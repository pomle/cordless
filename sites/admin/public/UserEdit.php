<?
use
	\Asenine\Format,
	\Asenine\Element\Input,
	\Asenine\Element\SelectBox,
	\Asenine\DB;

if( !isset($_GET['userID']) )
{
	define('ACCESS_POLICY', 'AllowCreateUser');
	require '../Init.inc.php';
	$userID = \Asenine\User\Manager::addToDB();
	header('Location: ?userID=' . $userID);
	exit();
}

define('ACCESS_POLICY', 'AllowViewUser');
require '../Init.inc.php';


if( !$_User = \Asenine\User::loadFromDB($_GET['userID']) )
	\Element\Page::error("User does not exist");


### Stow away loaded User's id for convenience
$userID = $_User->getID();

$pageTitle = _('System');
$pageSubtitle = _('Användare') . ' #' . $userID;

$UserSecurityIPList = \Element\Antiloop::getAsDomObject('UserSecurityIPs.User.Load', null, array('userID' => $userID));

require HEADER;
?>
<div class="tabs">
	<?
	$Tabs = new \Element\Tabs();
	echo $Tabs
		->addTab('properties', _('Egenskaper'), 'layers')
		->addTab('userSecurityIP', _('Åtkomst'), 'computer_key')
		->addTab('policies', _('Rättigheter'), 'key')
		->addTab('userGroups', _('Grupper'), 'group')
		->addTab('policiesResulting', _('Aktiva Rättigheter'), 'key_go');


	$IO = new \Element\IOCall('User', array('userID' => $userID));
	$SaveButton = new \Element\Button\Save();

	echo $IO->getHead();

	$size = 40;
	?>
	<div class="tab" id="properties">
		<fieldset>
			<legend><? echo \Element\Tag::legend('vcard', _('Status')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Tid skapad'), Format::timestamp($_User->timeCreated))
				->addRow(_('Tid ändrad'), Format::timestamp($_User->timeModified))
				->addRow(_('Senaste inloggning'), Format::timestamp($_User->timeLastLogin))
				->addRow(_('Senaste lösenordsbyte'), Format::timestamp($_User->timePasswordLastChange))
				;
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('application_xp_terminal', _('Inställningar')); ?></legend>
			<?
			$IdleLogout = new SelectBox('timeAutoLogout', $_User->timeAutoLogout);
			$IdleLogout->addItem(_('Av'), 0);

			foreach(array(1, 2, 3, 4, 5, 10, 15, 20, 30, 40, 50, 60) as $minutes)
				$IdleLogout->addItem($minutes, $minutes * 60);

			echo \Element\Table::inputs()
				->addRow(_('Aktiv'), Input::checkbox('isEnabled', $_User->isEnabled))
				->addRow(_('Administrator'), Input::checkbox('isAdministrator', $_User->isAdministrator))
				->addRow(_('Autologout'), $IdleLogout . ' ' . _('minuter'))
				->addRow(_('Användarnamn'), Input::text('username', $_User->username)->size($size))
				->addRow(_('Lösenord'), Input::password('newPassword')->size($size))
				;
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('vcard', _('Personuppgifter')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Namn'), Input::text('fullname', $_User->fullname)->size($size))
				->addRow(_('E-postadress'), Input::text('email', $_User->email)->size($size))
				->addRow(_('Telefon'), Input::text('phone', $_User->phone)->size($size))
				;
			?>
		</fieldset>
		<?

		$SaveButton->action = 'saveGeneral';
		echo \Element\IOControl::makeOf($IO)
			->addButton($SaveButton)
			->addButton(new \Element\Button\Delete('deleteUser'))
			;
		?>
	</div>
	<?
	echo $IO->getFoot();

	?>
	<fieldset class="tab" id="userSecurityIP">
		<legend><? echo \Element\Tag::legend('computer_key', _('Åtkomst')); ?></legend>
		<?
		echo $UserSecurityIPList;

		$SecurityIO = new \Element\IOCall('UserSecurityIP', array('userID' => $userID));

		$Control = new \Element\IOControl($SecurityIO);
		$Control
			->addButton(new \Element\Button\Clear())
			->addButton(new \Element\Button\Save())
			->addButton(new \Element\Button\Delete());

		echo $SecurityIO->getHead();
		?>
		<div class="ajaxEdit">
			<input type="hidden" name="userSecurityIPID">

			<fieldset>
				<legend><? echo \Element\Tag::legend('wrench_orange', _('Funktion')); ?></legend>
				<?
				$policyMap = \Dataset\User::getSecurityIPTypeMap();
				foreach($policyMap as &$value)
					$value = $value['caption'];

				asort($policyMap);

				echo \Element\Table::inputs()
					->addRow(_('Policy'), SelectBox::keyPair('policy', null, $policyMap))
					->addRow(_('IP-spann'), Input::text('spanStart') . ' - '. Input::text('spanEnd'))
					;
				?>
			</fieldset>

			<? echo $Control; ?>
		</div>
		<?
		echo $SecurityIO->getFoot(); ?>

	</fieldset>
	<?

	echo $IO->getHead();
	?>
	<fieldset class="tab" id="policies">
		<legend><? echo \Element\Tag::legend('key', _('Rättigheter')); ?></legend>
		<?
		$Table = \Element\Table::inputs();

		// Gets policies that current user has (or all if admin) and weather edited user has it
		$query = DB::prepareQuery("SELECT
				p.ID AS policyID,
				p.policy,
				p.description,
				(NOT upC.policyID IS NULL) AS hasPolicy
			FROM
				Asenine_Policies p
				LEFT JOIN Asenine_UserPolicies upA ON upA.policyID = p.ID AND upA.userID = %u
				LEFT JOIN Asenine_UserPolicies upC ON upC.policyID = p.ID AND upC.userID = %u
			WHERE
				upA.policyID OR %u = 1
			ORDER BY
				p.policy ASC",
			USER_ID,
			$userID,
			USER_IS_ADMIN);

		$result = DB::queryAndFetchResult($query);

		while($row = DB::assoc($result))
		{
			list($policyID, $policy, $desc, $hasPolicy) = array_values($row);
			$Table->addRow($policy, Input::checkbox('policyIDs[]', $hasPolicy, $policyID), $desc);
		}

		echo $Table;

		$SaveButton->action = 'savePolicies';
		echo \Element\IOControl::makeOf($IO)->addButton($SaveButton);
		?>
	</fieldset>
	<?
	echo $IO->getFoot();

	echo $IO->getHead();
	?>
	<fieldset class="tab" id="userGroups">
		<legend><? echo \Element\Tag::legend('group', _('Grupper')); ?></legend>
		<?
		$Table = \Element\Table::inputs();

		### Gets userGroups that current user is member of (or all if admin) and weather edited user belongs to it
		$query = DB::prepareQuery("SELECT
				ug.ID AS userGroupID,
				ug.name,
				ug.description,
				(NOT uguC.userGroupID IS NULL) AS isMember
			FROM
				Asenine_UserGroups ug
				LEFT JOIN Asenine_UserGroupUsers uguA ON uguA.userGroupID = ug.ID AND uguA.userID = %u
				LEFT JOIN Asenine_UserGroupUsers uguC ON uguC.userGroupID = ug.ID AND uguC.userID = %u
			WHERE
				uguA.userGroupID OR %u = 1
			ORDER BY
				name COLLATE || ASC",
			USER_ID,
			$userID,
			USER_IS_ADMIN);

		$result = DB::queryAndFetchResult($query);

		$allowEdit = $User->hasPolicy('AllowUserGroupEdit');

		while($row = DB::assoc($result))
		{
			list($userGroupID, $name, $desc, $hasGroup) = array_values($row);
			$Table->addRow($allowEdit ? sprintf('<a href="/UserGroupEdit.php?userGroupID=%u">%s</a>', $userGroupID, $name) : $name, Input::checkbox('userGroupIDs[]', $hasGroup, $userGroupID), $desc);
		}

		echo $Table;

		$SaveButton->action = 'saveUserGroups';
		echo \Element\IOControl::makeOf($IO)->addButton($SaveButton);
		?>
	</fieldset>
	<?
	echo $IO->getFoot();
	?>
	<fieldset class="tab" id="policiesResulting">
		<legend><? echo \Element\Tag::legend('key_go', _('Aktiva Rättigheter')); ?></legend>
		<?
		$policies = \Asenine\User\Manager::getPolicies($_User->userID);

		$Table = new \Element\Table();
		foreach($policies as $policy => $state)
			$Table->addRow($state, $policy);

		echo $Table;
		?>
	</fieldset>
</div>
<?
require FOOTER;