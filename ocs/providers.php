<?php

require_once '../lib/base.php';

header('Content-type: application/xml');

$url=OCP\Util::getServerProtocol().'://'.substr(OCP\Util::getServerHost().OCP\Util::getRequestUri(), 0, -17).'ocs/v1.php/';

echo('
<providers>
<provider>
 <id>ownCloud</id>
 <location>'.$url.'</location>
 <name>ownCloud</name>
 <icon></icon>
 <termsofuse></termsofuse>
 <register></register>
 <services>
   <config ocsversion="1.7" />
   <activity ocsversion="1.7" />
   <cloud ocsversion="1.7" />
 </services>
</provider>
</providers>
');
