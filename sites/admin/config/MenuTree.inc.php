<?
$tree = array (
  _('Databas') => 
  array (
    0 => 
    array (
      'caption' => _('Media'),
      'href' => '/MediaOverview.php',
      'policy' => 'AllowViewMedia',
    ),
    1 => 
    array (
      'caption' => _('Språk'),
      'href' => '/LocaleEdit.php',
      'policy' => 'AllowViewLocale',
    ),
  ),
  _('System') => 
  array (
    0 => 
    array (
      'caption' => _('Användare'),
      'href' => '/UserOverview.php',
      'policy' => 'AllowViewUser',
    ),
    1 => 
    array (
      'caption' => _('Användargrupper'),
      'href' => '/UserGroupOverview.php',
      'policy' => 'AllowViewUserGroup',
    ),
    2 => 
    array (
      'caption' => _('Diagnostik'),
      'href' => '/DiagnosticsOverview.php',
      'policy' => 'AllowViewDiagnostics',
    ),
    3 => 
    array (
      'caption' => _('Rättigheter'),
      'href' => '/PolicyEdit.php',
      'policy' => 'AllowViewPolicy',
    ),
  ),
);