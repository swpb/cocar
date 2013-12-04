<?php
require_once './vendor/symfony/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

// You can search the include_path as a last resort.
$loader->useIncludePath(true);

// ... register namespaces and prefixes here - see below

$loader->registerNamespace('Jpgraph', __DIR__.'/vendor/jpgraph/jpgraph/lib/JpGraph/src');

$loader->register();
