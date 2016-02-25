<?php

// Users
use OCP\API;

API::register('get', '/cloud/users', array('OCA\Provisioning_API\Users', 'getUsers'), 'provisioning_api', API::ADMIN_AUTH);
API::register('post', '/cloud/users', array('OCA\Provisioning_API\Users', 'addUser'), 'provisioning_api', API::ADMIN_AUTH);
API::register('get', '/cloud/users/{userid}', array('OCA\Provisioning_API\Users', 'getUser'), 'provisioning_api', API::USER_AUTH);
API::register('put', '/cloud/users/{userid}', array('OCA\Provisioning_API\Users', 'editUser'), 'provisioning_api', API::USER_AUTH);
API::register('delete', '/cloud/users/{userid}', array('OCA\Provisioning_API\Users', 'deleteUser'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('get', '/cloud/users/{userid}/groups', array('OCA\Provisioning_API\Users', 'getUsersGroups'), 'provisioning_api', API::USER_AUTH);
API::register('post', '/cloud/users/{userid}/groups', array('OCA\Provisioning_API\Users', 'addToGroup'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('delete', '/cloud/users/{userid}/groups', array('OCA\Provisioning_API\Users', 'removeFromGroup'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('post', '/cloud/users/{userid}/subadmins', array('OCA\Provisioning_API\Users', 'addSubAdmin'), 'provisioning_api', API::ADMIN_AUTH);
API::register('delete', '/cloud/users/{userid}/subadmins', array('OCA\Provisioning_API\Users', 'removeSubAdmin'), 'provisioning_api', API::ADMIN_AUTH);
API::register('get', '/cloud/users/{userid}/subadmins', array('OCA\Provisioning_API\Users', 'getUserSubAdminGroups'), 'provisioning_api', API::ADMIN_AUTH);

// Groups
API::register('get', '/cloud/groups', array('OCA\Provisioning_API\Groups', 'getGroups'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('post', '/cloud/groups', array('OCA\Provisioning_API\Groups', 'addGroup'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('get', '/cloud/groups/{groupid}', array('OCA\Provisioning_API\Groups', 'getGroup'), 'provisioning_api', API::SUBADMIN_AUTH);
API::register('delete', '/cloud/groups/{groupid}', array('OCA\Provisioning_API\Groups', 'deleteGroup'), 'provisioning_api', API::ADMIN_AUTH);
API::register('get', '/cloud/groups/{groupid}/subadmins', array('OCA\Provisioning_API\Groups', 'getSubAdminsOfGroup'), 'provisioning_api', API::ADMIN_AUTH);

// Apps
API::register('get', '/cloud/apps', array('OCA\Provisioning_API\Apps', 'getApps'), 'provisioning_api', API::ADMIN_AUTH);
API::register('get', '/cloud/apps/{appid}', array('OCA\Provisioning_API\Apps', 'getAppInfo'), 'provisioning_api', API::ADMIN_AUTH);
API::register('post', '/cloud/apps/{appid}', array('OCA\Provisioning_API\Apps', 'enable'), 'provisioning_api', API::ADMIN_AUTH);
API::register('delete', '/cloud/apps/{appid}', array('OCA\Provisioning_API\Apps', 'disable'), 'provisioning_api', API::ADMIN_AUTH);
