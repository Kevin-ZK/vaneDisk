<?php

OC_Util::checkAdminUser();

// fill template
$tmpl = new OCP\Template('user_ldap', 'settings');

$helper = new \OCA\user_ldap\lib\Helper();
$prefixes = $helper->getServerConfigurationPrefixes();
$hosts = $helper->getServerConfigurationHosts();

$wizardHtml = '';
$toc = array();

$wControls = new OCP\Template('user_ldap', 'part.wizardcontrols');
$wControls = $wControls->fetchPage();
$sControls = new OCP\Template('user_ldap', 'part.settingcontrols');
$sControls = $sControls->fetchPage();

$l = \OC::$server->getL10N('user_ldap');

$wizTabs = array();
$wizTabs[] = array('tpl' => 'part.wizard-server',      'cap' => $l->t('Server'));
$wizTabs[] = array('tpl' => 'part.wizard-userfilter',  'cap' => $l->t('Users'));
$wizTabs[] = array('tpl' => 'part.wizard-loginfilter', 'cap' => $l->t('Login Attributes'));
$wizTabs[] = array('tpl' => 'part.wizard-groupfilter', 'cap' => $l->t('Groups'));
$wizTabsCount = count($wizTabs);
for($i = 0; $i < $wizTabsCount; $i++) {
	$tab = new OCP\Template('user_ldap', $wizTabs[$i]['tpl']);
	if($i === 0) {
		$tab->assign('serverConfigurationPrefixes', $prefixes);
		$tab->assign('serverConfigurationHosts', $hosts);
	}
	$tab->assign('wizardControls', $wControls);
	$wizardHtml .= $tab->fetchPage();
	$toc['#ldapWizard'.($i+1)] = $wizTabs[$i]['cap'];
}

$tmpl->assign('tabs', $wizardHtml);
$tmpl->assign('toc', $toc);
$tmpl->assign('settingControls', $sControls);

// assign default values
$config = new \OCA\user_ldap\lib\Configuration('', false);
$defaults = $config->getDefaults();
foreach($defaults as $key => $default) {
	$tmpl->assign($key.'_default', $default);
}

return $tmpl->fetchPage();
