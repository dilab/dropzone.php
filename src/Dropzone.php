<?php

namespace Dilab;

use League\Flysystem\Adapter\Local;
use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;

class Dropzone
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * @var  Filesystem
     */
    private $filesystem;

    private $name;

    private $metaOption = [
        'dzuuid' => 'dzuuid',
        'dzchunkindex' => 'dzchunkindex',
        'dztotalfilesize' => 'dztotalfilesize',
        'dzchunksize' => 'dzchunksize',
        'dztotalchunkcount' => 'dztotalchunkcount',
        'dzchunkbyteoffset' => 'dzchunkbyteoffset'
    ];

    private $tmpDir = 'tmp';

    private $uploadDir = 'upload';

    /**
     * Dropzone constructor.
     * @param Filesystem $filesystem
     */
    public function __construct($root)
    {
        $this->name = time();

        $adapter = new Local($root);

        $this->filesystem = new Filesystem($adapter);
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $meta
     * @return boolean
     */
    private function isAlreadyUploaded($meta)
    {
        return $this->filesystem->has($this->tmpFilePath($meta));
    }

    private function uploadToTmp($stream, $meta)
    {
        try {

            $this->filesystem->writeStream($this->tmpFilePath($meta), $stream);

        } catch (FileExistsException $exception) {

        }
    }

    /**
     * @param $meta
     * @return boolean
     */
    private function isUploadCompleted($meta)
    {
        return
            count($this->filesystem->listContents($this->tmpDirPath($meta))) ==
            $meta[$this->metaOption['dztotalchunkcount']];
    }

    private function assemble($meta)
    {
        foreach ($this->filesystem->listContents($this->tmpDirPath($meta), true) as $object) {

            $stream = $this->filesystem->readStream($object['path']);

            try {

                $this->filesystem->writeStream($this->uploadFilePath($meta), $stream);

            } catch (FileExistsException $exception) {

            } finally {

                fclose($stream);

            }

        }

    }

    public function upload($stream, $meta)
    {
        if ($this->isAlreadyUploaded($meta)) {
            return $this;
        }

        $this->uploadToTmp($stream, $meta);

        if (!$this->isUploadCompleted($meta)) {
            return $this;
        }

        $this->assemble($meta);

        $this->removeTmpDir($meta);

        return $this;
    }

    private function removeTmpDir($meta)
    {
        $this->filesystem->deleteDir($this->tmpDirPath($meta));
    }

    private function tmpFilePath($meta)
    {
        return $this->tmpDirPath($meta) . self::DS .
            $meta[$this->metaOption['dzchunkindex']];
    }

    private function tmpDirPath($meta)
    {
        return $this->tmpDir . self::DS .
            $meta[$this->metaOption['dzuuid']];
    }

    private function uploadFilePath($meta)
    {
        return $this->uploadDir . self::DS .
            $meta[$this->metaOption['dzuuid']] . self::DS .
            $this->name;
    }


}