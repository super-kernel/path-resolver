<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver\Adapter;

use Phar;
use SuperKernel\PathResolver\Contract\PathResolveAdapterInterface;
use function dirname;
use function extension_loaded;

final class NullAdapter implements PathResolveAdapterInterface
{
	public function supports(): bool
	{
		if (!extension_loaded('phar')) {
			return false;
		}

		return !empty(Phar::running(false));
	}

	public function resolve(): string
	{
		return dirname(Phar::running(false));
	}

	public function __toString(): string
	{
		return $this->resolve();
	}
}