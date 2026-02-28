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
	private static PathResolverInterface $pathResolver;

	public static function make(?string $segment = null): PathResolverInterface
	{
		$instance = new self()();

		if (null === $segment) {
			return $instance;
		}

		return $instance->to($segment);
	}

	public function __invoke(): PathResolverInterface
	{
		if (!isset(self::$pathResolver)) {
			self::$pathResolver = new PathResolver($this->getResolver()->resolve());
		}
		return self::$pathResolver;
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