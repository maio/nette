<?php

/**
 * Test: Nette\Caching\Storages\MemcachedStorage files dependency test.
 *
 * @author     David Grudl
 * @package    Nette\Caching
 * @subpackage UnitTests
 */

use Nette\Caching\Storages\MemcachedStorage,
	Nette\Caching\Cache;



require __DIR__ . '/../bootstrap.php';



if (!MemcachedStorage::isAvailable()) {
	TestHelpers::skip('Requires PHP extension Memcache.');
}



$key = 'nette';
$value = 'rulez';

$cache = new Cache(new MemcachedStorage('localhost'));


$dependentFile = TEMP_DIR . '/spec.file';
@unlink($dependentFile);

// Writing cache...
$cache->save($key, $value, array(
	Cache::FILES => array(
		__FILE__,
		$dependentFile,
	),
));

Assert::true( isset($cache[$key]), 'Is cached?' );


// Modifing dependent file
file_put_contents($dependentFile, 'a');

Assert::false( isset($cache[$key]), 'Is cached?' );


// Writing cache...
$cache->save($key, $value, array(
	Cache::FILES => $dependentFile,
));

Assert::true( isset($cache[$key]), 'Is cached?' );


// Modifing dependent file
sleep(2);
file_put_contents($dependentFile, 'b');
clearstatcache();

Assert::false( isset($cache[$key]), 'Is cached?' );
