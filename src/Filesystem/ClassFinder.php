<?php

namespace Twix\Filesystem;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final readonly class ClassFinder
{
    public static function List(string $path): array
    {
        $directoryIterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directoryIterator);
        $phpFiles = new RegexIterator($iterator, '/\.php$/');
        $classes = [];

        foreach ($phpFiles as $phpFile) {
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';

            for ($index = 0; $index < count($tokens); $index++) {
                if ($tokens[$index][0] === T_NAMESPACE) {
                    $index += 2; // Skip namespace and whitespace
                    while ($tokens[$index] !== ';') {
                        $namespace .= is_array($tokens[$index]) ? $tokens[$index][1] : $tokens[$index];
                        $index++;
                    }
                }

                if ($tokens[$index][0] === T_CLASS || $tokens[$index][0] === T_INTERFACE || $tokens[$index][0] === T_TRAIT) {
                    for ($j = $index + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{') {
                            $className = $tokens[$index + 2][1];
                            $classes[] = ltrim($namespace . '\\' . $className, '\\');
                            break;
                        }
                    }
                }
            }
        }

        return $classes;
    }
}