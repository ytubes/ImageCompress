<?php
namespace ImageCompressor;

use SplFileInfo;

class ImageCompressor implements CompressorInterface
{
    private $driver;

    public function __construct($driver = 'default')
    {
        $this->driver = $this->buildCompressor($driver);
    }

    public function getCompressor()
    {
        return $this->driver;
    }

    public function setCompressor($driver)
    {
        $this->driver = $this->buildCompressor($driver);
    }

        // Добавить is_null - NullCompressor: просто копирование.
    private function buildCompressor($driver)
    {
        if ($driver instanceof CompressorInterface) {
            return $driver;
        }

        if (class_exists($driver)) {
            $driverClass = $driver;
        } elseif (is_string($driver)) {
            $driver = ucfirst(strtolower($driver));
            $driverClass = '\\ImageCompressor\\Driver\\' . $driver;

        }

        if (class_exists($driverClass) && is_subclass_of($driverClass, CompressorInterface::class)) {
            return new $driverClass;
        } else {
            throw new \InvalidArgumentException('Image compressor driver not found');
        }
    }

    public function setOriginalFile(SplFileInfo $originalFile)
    {
        return $this->driver->setOriginalFile($originalFile);
    }

    public function setDestination($destination)
    {
        return $this->driver->setDestination($destination);
    }

    public function setQuality($quality = 90)
    {
        return $this->driver->setQuality($quality);
    }

    public function compress()
    {
        return $this->driver->compress();
    }
}
