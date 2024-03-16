<?php

$content = <<<'EOD'
DELIMITER $$
IF @auth_root_socket is not null THEN
IF not exists(select 1 from information_schema.plugins where plugin_name='unix_socket') THEN
   INSTALL SONAME 'auth_socket'; END IF; END IF$$
DELIMITER ;
DELIMITER $$
IF @auth_root_socket is not null THEN
IF not exists(select 1 from information_schema.plugins where plugin_name='unix_socket') THEN
   INSTALL SONAME 'auth_socket'; END IF; END IF$$
DELIMITER ;

EOD;

$matches = [];
preg_match_all('/DELIMITER\s+\$\$(.*?)\$\$.*?DELIMITER ;/s', $content, $matches);


var_dump($matches);