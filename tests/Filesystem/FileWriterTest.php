<?php

namespace Twix\Test\Filesystem;

use Twix\Application\AppConfig;
use Twix\Filesystem\FileWriter;
use Twix\Test\TestCase;

class FileWriterTest extends TestCase
{
    private const TEST_PATH = __DIR__ . DIRECTORY_SEPARATOR;
    private const TEST_FILENAME = 'test_file.txt';

    private FileWriter $writer;
    private string $filePath;

    public function setUp(): void
    {
        parent::setup();
        $dirPath = realpath(self::TEST_PATH);
        $this->filePath = sprintf('%s%s%s', $dirPath, DIRECTORY_SEPARATOR, self::TEST_FILENAME);
        if (! file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        file_put_contents($this->filePath, '');

        $appConfig = $this->get(AppConfig::class);
        $this->writer = (new FileWriter($appConfig))->setFileName($this->filePath);
    }

    protected function tearDown(): void
    {
        if (file_exists(self::TEST_PATH . self::TEST_FILENAME)) {
            unlink(self::TEST_PATH . self::TEST_FILENAME);
        }
    }

    public function testFileNameIsSet()
    {
        $this->assertEquals($this->writer->getFileName(), $this->filePath);
    }

    public function testWrite()
    {

        $text = 'Oh Hello!' . PHP_EOL;
        $bytesWritten = $this->writer->write($text);

        $this->assertEquals(strlen($text), $bytesWritten);

        $this->assertEquals($text, file_get_contents($this->filePath));
    }

    public function testLastBytesWrittenCount()
    {
        $text = 'Oh Hello!' . PHP_EOL;
        $this->writer->write($text);

        $this->assertEquals(strlen($text), $this->writer->getLastWriteBytes());
    }

    public function testAppend()
    {
        $initialText = "Initial content\n";
        file_put_contents(self::TEST_PATH . self::TEST_FILENAME, $initialText);

        $appendedText = "Appended content\n";
        $bytesAppended = $this->writer->append($appendedText);

        $this->assertEquals(strlen($appendedText), $bytesAppended);

        $expectedContent = $initialText . $appendedText;
        $this->assertEquals($expectedContent, file_get_contents(self::TEST_PATH . self::TEST_FILENAME));
    }
}
