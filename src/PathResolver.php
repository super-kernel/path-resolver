<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver;

use InvalidArgumentException;
use SuperKernel\PathResolver\Contract\PathResolverInterface;
use function array_filter;
use function array_pop;
use function explode;
use function implode;
use function str_replace;
use function str_starts_with;
use const DIRECTORY_SEPARATOR;

final class PathResolver implements PathResolverInterface
{
	private readonly string $rootPath;

	private string $currentPath;

	public function __construct(string $rootPath)
	{
		if ('' === $rootPath) {
			throw new InvalidArgumentException("Root path cannot be empty.");
		}

		$this->currentPath = $this->rootPath = $rootPath;
	}

	public function to(string $segment): PathResolver
	{
		$target = $this->currentPath . DIRECTORY_SEPARATOR . $segment;

		$normalized = $this->normalize($target);
		if (!str_starts_with($normalized, $this->rootPath)) {
			throw new InvalidArgumentException(
				"Security Breach: Path segment '$segment' attempts to escape root directory.",
			);
		}

		$this->currentPath = $normalized;

		return $this;
	}

	public function get(): string
	{
		return $this->currentPath;
	}

	private function normalize(string $path): string
	{
		$path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');

		$absolutes = [];
		foreach ($parts as $part) {
			if ('.' === $part) {
				continue;
			}
			if ('..' === $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}

		$prefix = (DIRECTORY_SEPARATOR === '/') ? '/' : '';
		return $prefix . implode(DIRECTORY_SEPARATOR, $absolutes);
	}
}