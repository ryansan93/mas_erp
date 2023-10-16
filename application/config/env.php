
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['connection'] = array(
	'default' => array(
		'driver'    => 'sqlsrv',

		// NOTE : LIVE DATABASE
		'host'      => '103.137.111.5',
		'port'		=> '1433',
		'database'  => 'mas_erp',
		'username'  => 'sa',
		'password'  => 'Mas654321',

		// NOTE : LOCAL DATABASE
		// 'host'      => 'localhost',
		// 'database'  => 'mas',
		// 'username'  => '',
		// 'password'  => '',

		// NOTE : TEST DATABASE
		// 'host'      => '192.168.100.116',
		// 'database'  => 'ekspedisi_erp',
		// 'username'  => 'it-mt',
		// 'password'  => 'musnimda',

		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	),
);
