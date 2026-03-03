<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver\Provider;

use Generator;
use RuntimeException;
use SuperKernel\PathResolver\Contract\PathResolveAdapterInterface;
use SuperKernel\PathResolver\Contract\PathResolverInterface;
use SuperKernel\PathResolver\PathResolver;
use SuperKernel\PathResolver\Adapter\ComposerAdapter;
use SuperKernel\PathResolver\Adapter\StandardAdapter;

final class PathResolverProvider
{
	private static PathResolverProvider $pathResolverProvider;

	private PathResolveAdapterInterface $resolveAdapter;

	public function __construct()
	{
		$this->resolveAdapter = $this->getResolver();
	}

	public static function make(?string $segment = null): PathResolverInterface
	{
		if (!isset(self::$pathResolverProvider)) {
			self::$pathResolverProvider = new self();
		}

		$pathResolver = self::$pathResolverProvider->__invoke();

		if (null === $segment) {
			return $pathResolver;
		}

		return $pathResolver->to($segment);
	}

	public function __invoke(): PathResolverInterface
	{
		return new PathResolver($this->resolveAdapter->resolve());
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
		yield new ComposerAdapter();
		yield new ComposerAdapter();
		yield new StandardAdapter();
	}
}