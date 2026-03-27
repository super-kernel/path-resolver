<?php
declare(strict_types=1);

namespace SuperKernel\PathResolver;

use InvalidArgumentException;
use SuperKernel\Attribute\Provider;
use SuperKernel\Contract\PathResolverInterface;
use SuperKernel\PathResolver\Contract\PathResolveAdapterInterface;
use function array_filter;
use function array_pop;
use function explode;
use function implode;
use function rtrim;
use function str_replace;
use function str_starts_with;
use const DIRECTORY_SEPARATOR;

#[Provider(PathResolverInterface::class)]
final readonly class PathResolver implements PathResolverInterface
{
	private string $currentPath;

	public function __construct(private PathResolveAdapterInterface $pathResolveAdapter, ?string $currentPath = null)
	{
		$this->currentPath = $currentPath ?? $this->pathResolveAdapter->resolve();
	}

	public function to(string $path): PathResolverInterface
	{
		$root = $this->pathResolveAdapter->resolve();

		$target = $this->currentPath . DIRECTORY_SEPARATOR . $path;
		$normalized = $this->normalize($target);

		$safeRoot = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$safeNormalized = rtrim($normalized, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if (!str_starts_with($safeNormalized, $safeRoot)) {
			throw new InvalidArgumentException(
				"Security Breach: Path segment '$path' attempts to escape root directory: $root",
			);
		}

		return new self($this->pathResolveAdapter, $normalized);
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

		$result = implode(DIRECTORY_SEPARATOR, $absolutes);

		if (DIRECTORY_SEPARATOR === '/' && str_starts_with($path, '/')) {
			return '/' . $result;
		}

		return $result;
	}
}