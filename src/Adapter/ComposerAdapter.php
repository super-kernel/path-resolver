<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver\Adapter;

use RuntimeException;
use SuperKernel\PathResolver\Contract\PathResolveAdapterInterface;
use function dirname;
use function getenv;

final class ComposerAdapter implements PathResolveAdapterInterface
{
	public function supports(): bool
	{
		return getenv('COMPOSER_BINARY') !== false;
	}

	public function resolve(): string
	{
		if (isset($GLOBALS['_composer_bin_dir'])) {
			return dirname($GLOBALS['_composer_bin_dir'], 2);
		}

		throw new RuntimeException('Composer environment detected but root path could not be inferred.');
	}

	public function __toString(): string
	{
		return $this->resolve();
	}
}