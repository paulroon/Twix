<?php

namespace Twix\Test\Filesystem;

use Twix\Filesystem\FileWriter;
use Twix\Test\TestCase;

class FileWriterTest extends TestCase
{
    private const TEST_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    private const TEST_FILENAME = 'test_file.txt';

    public function setUp(): void
    {
        if (! file_exists(self::TEST_PATH)) {
            mkdir(self::TEST_PATH, 0777, true);
        }

        file_put_contents(self::TEST_PATH . self::TEST_FILENAME, '');
    }

    protected function tearDown(): void
    {
        if (file_exists(self::TEST_PATH . self::TEST_FILENAME)) {
            unlink(self::TEST_PATH . self::TEST_FILENAME);
        }
    }

    public function testWrite()
    {
        $writer = new FileWriter(self::TEST_PATH, self::TEST_FILENAME);
        $text = "Oh Hello!" . PHP_EOL;
        $bytesWritten = $writer->write($text);

        $this->assertEquals(strlen($text), $bytesWritten);

        $this->assertEquals($text, file_get_contents(self::TEST_PATH . self::TEST_FILENAME));
    }

    public function testLastBytesWrittenCount()
    {
        $writer = new FileWriter(self::TEST_PATH, self::TEST_FILENAME);
        $text = "Oh Hello!" . PHP_EOL;
        $writer->write($text);

        $this->assertEquals(strlen($text), $writer->getLastWriteBytes());
    }

    public function testAppend()
    {
        $initialText = "Initial content\n";
        file_put_contents(self::TEST_PATH . self::TEST_FILENAME, $initialText);

        $writer = new FileWriter(self::TEST_PATH, self::TEST_FILENAME);
        $appendedText = "Appended content\n";
        $bytesAppended = $writer->append($appendedText);

        $this->assertEquals(strlen($appendedText), $bytesAppended);

        $expectedContent = $initialText . $appendedText;
        $this->assertEquals($expectedContent, file_get_contents(self::TEST_PATH . self::TEST_FILENAME));
    }
}
