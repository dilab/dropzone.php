<?php

namespace Dilab\Test;


use Dilab\Dropzone;
use League\Flysystem\Filesystem;
use Prophecy\Argument;

class DropzoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dropzone
     */
    public $dropzone;

    public function setUp()
    {
        parent::setUp();
        $this->rmdir(__DIR__ . '/upload');
        $this->rmdir(__DIR__ . '/tmp');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->rmdir(__DIR__ . '/upload');
        $this->rmdir(__DIR__ . '/tmp');
    }

    function rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object))
                        $this->rmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

    public function testUpload()
    {
        // Arrange

        $this->dropzone = new Dropzone(__DIR__);

        $stream = fopen(__DIR__ . '/files/mock.png.0001', 'r+');

        $metaA = [
            'dzuuid' => 'c5960cd7-0c8a-498f-8b88-75ef269f9176',
            'dzchunkindex' => '0',
            'dztotalfilesize' => '123',
            'dzchunksize' => '102400',
            'dztotalchunkcount' => '3',
            'dzchunkbyteoffset' => '123'
        ];

        $metaB = [
            'dzuuid' => 'c5960cd7-0c8a-498f-8b88-75ef269f9176',
            'dzchunkindex' => '1',
            'dztotalfilesize' => '123',
            'dzchunksize' => '102400',
            'dztotalchunkcount' => '3',
            'dzchunkbyteoffset' => '123'
        ];

        $metaC = [
            'dzuuid' => 'c5960cd7-0c8a-498f-8b88-75ef269f9176',
            'dzchunkindex' => '2',
            'dztotalfilesize' => '123',
            'dzchunksize' => '102400',
            'dztotalchunkcount' => '3',
            'dzchunkbyteoffset' => '123'
        ];

        $metaD = [
            'dzuuid' => '123',
            'dzchunkindex' => '2',
            'dztotalfilesize' => '123',
            'dzchunksize' => '102400',
            'dztotalchunkcount' => '3',
            'dzchunkbyteoffset' => '123'
        ];

        // Act
        $this->dropzone->upload($stream, $metaA)->name('result.png');

        $this->dropzone->upload($stream, $metaB)->name('result.png');

        $this->dropzone->upload($stream, $metaC)->name('result.png');

        $this->dropzone->upload($stream, $metaD)->name('result.png');

        // Assert

        $resultPath = __DIR__ . DIRECTORY_SEPARATOR .
            'upload' . DIRECTORY_SEPARATOR .
            'c5960cd7-0c8a-498f-8b88-75ef269f9176' . DIRECTORY_SEPARATOR .
            'result.png';

        $this->assertFileExists($resultPath);
    }

}
