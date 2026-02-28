#!/usr/bin/env php
<?php
declare(strict_types=1);

use SuperKernel\PathResolver\Provider\PathResolverProvider;

require dirname(__DIR__) . '/vendor/autoload.php';

var_dump(
	PathResolverProvider::make()->get(),
	PathResolverProvider::make('src')->get(),
);