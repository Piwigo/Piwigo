<?php
// this file is provided as an example.
// Move it to "create_listing_file.php"
// directory if you want to modify default configuration.

// URL of main gallery
// Example : http://www.my.domain/my/directory
$conf['gallery'] = 'http://demo.piwigo.net/';

$conf['file_ext'] = array_merge($conf['file_ext'], array('flv', 'FLV'));

$conf['force_refresh_method'] =  true;

?>