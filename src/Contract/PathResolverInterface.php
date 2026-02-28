<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver\Contract;

interface PathResolverInterface
{
	public function get(): string;

	public function to(string $segment): PathResolverInterface;
}