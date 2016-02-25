<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('gallery');

$gallery = $_GET['gallery'];

$meta = \OC\Files\Filesystem::getFileInfo($gallery);
$data = array();
$data['fileid'] = $meta['fileid'];
$data['permissions'] = $meta['permissions'];

OCP\JSON::setContentTypeHeader();
echo json_encode($data);
