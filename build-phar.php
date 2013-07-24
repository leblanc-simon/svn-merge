<?php

if(!Phar::canWrite()) {
    echo 'Unable to write phar, phar.readonly must be set to zero in your php.ini otherwise use: $ php -dphar.readonly=0 build-phar.php <command> ...';
    exit(1);
}

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$phar_filename = __DIR__.'/svn-merge.phar';
if (file_exists($phar_filename) === true) {
    unlink($phar_filename);
}
$phar_name = pathinfo($phar_filename, PATHINFO_BASENAME);

$phar = new Phar($phar_filename, Phar::CURRENT_AS_FILEINFO | Phar::KEY_AS_FILENAME, 'svn-merge.phar');

if ($argc === 2) {
    $shebang = $argv[1];
} else {
    $shebang = '#!/usr/bin/env php';
}

$phar->setStub($shebang."\n".'<?php Phar::mapPhar(); include \'phar://'.$phar_name.'/bin/svn-merge\'; __HALT_COMPILER();');

$finder = new \Symfony\Component\Finder\Finder();

$finder
    ->files()
    ->name('*.php')
    ->name('svn-merge')
    ->in(__DIR__.'/bin')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/vendor')
    ->exclude('.git')
    ->exclude('phpunit')
    ->exclude('vendor/bin')
    ->exclude('Tests');

foreach ($finder as $file) {
    $phar[str_replace(__DIR__.DIRECTORY_SEPARATOR, '', $file)] = file_get_contents($file->getRealpath());
}

chmod($phar_filename, 0755);

echo "$phar_filename is created\n";
exit(0);
