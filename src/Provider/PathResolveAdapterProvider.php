<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver\Provider;

use Generator;
use RuntimeException;
use SuperKernel\Attribute\Factory;
use SuperKernel\Attribute\Provider;
use SuperKernel\PathResolver\Adapter\ComposerAdapter;
use SuperKernel\PathResolver\Adapter\NullAdapter;
use SuperKernel\PathResolver\Adapter\StandardAdapter;
use SuperKernel\PathResolver\Contract\PathResolveAdapterInterface;

#[
	Provider(PathResolveAdapterInterface::class),
	Factory,
]
final class PathResolveAdapterProvider
{
	private static PathResolveAdapterInterface $pathResolveAdapter;

	public function __invoke(): PathResolveAdapterInterface
	{
		if (!isset(self::$pathResolveAdapter)) {
			self::$pathResolveAdapter = $this->getResolver();
		}
		return self::$pathResolveAdapter;
	}

	private function getResolver(): PathResolveAdapterInterface
	{
		foreach ($this->getResolvers() as $resolveAdapter) {
			if ($resolveAdapter->supports()) {
				return $resolveAdapter;
			}
		}

		throw new RuntimeException('No resolver suitable for the current environment was found.');
	}

	private function getResolvers(): Generator
	{
		yield new NullAdapter();
		yield new ComposerAdapter();
		yield new StandardAdapter();
	}
}