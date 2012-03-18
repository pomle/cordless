<?
$tree = array (
  _('Database') => 
  array (
    0 => 
    array (
      'caption' => _('Media'),
      'href' => '/MediaOverview.php',
      'policy' => 'AllowViewMedia',
    ),
  ),
  _('System') => 
  array (
    0 => 
    array (
      'caption' => _('Diagnostics'),
      'href' => '/DiagnosticsOverview.php',
      'policy' => 'AllowViewDiagnostics',
    ),
    1 => 
    array (
      'caption' => _('Policies'),
      'href' => '/PolicyEdit.php',
      'policy' => 'AllowViewPolicy',
    ),
    2 => 
    array (
      'caption' => _('User Groups'),
      'href' => '/UserGroupOverview.php',
      'policy' => 'AllowViewUserGroup',
    ),
    3 => 
    array (
      'caption' => _('Users'),
      'href' => '/UserOverview.php',
      'policy' => 'AllowViewUser',
    ),
  ),
);