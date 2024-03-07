<?php

namespace Twix\Filesystem;

use RegexIterator;

final readonly class ClassFinder
{
    /**
     * Will find (recursively) all PHP class files in a directory structure
     */
    public static function findClassesInDir(string $path): array
    {
        return self::extractClassNames(self::findPhpFilesInDir($path));
    }

    public static function extractClassNames(RegexIterator|array $iterator): array
    {

        $classes = [];

        foreach ($iterator as $phpFile) {

            $fPath = $phpFile instanceof \SplFileInfo ? $phpFile->getRealPath() : $phpFile;
            $content = file_get_contents($fPath);
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

                if ($tokens[$index][0] === T_CLASS) {


                    for ($j = $index + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{' && $tokens[$index - 1][1] !== "::") {
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

    public static function findPhpFilesInDir(string $path): array
    {
        $files = [];
        self::globRecursive($path . '/*.php', $files);

        return $files;
    }

    private static function globRecursive(string $pattern, array &$files): void
    {
        foreach (glob($pattern) as $file) {
            $files[] = $file;
        }

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            self::globRecursive($dir . '/' . basename($pattern), $files);
        }
    }
}
