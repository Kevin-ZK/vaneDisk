<?php

\OC_Util::checkAdminUser();

\OCP\Util::addStyle('templateeditor', 'settings-admin');
\OCP\Util::addScript('templateeditor', 'settings-admin');

$themes = \OCA\TemplateEditor\MailTemplate::getEditableThemes();
$editableTemplates = \OCA\TemplateEditor\MailTemplate::getEditableTemplates();

$tmpl = new \OCP\Template('templateeditor', 'settings-admin');
$tmpl->assign('themes', $themes);
$tmpl->assign('editableTemplates', $editableTemplates);

return $tmpl->fetchPage();
