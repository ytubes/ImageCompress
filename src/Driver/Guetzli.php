<?php

/**
 * Обертка над компрессором jpg|png фотографий Guetzli
 * Подробнее: https://github.com/google/guetzli
 */

namespace ImageCompressor\Driver;

use SplFileInfo;
use ImageCompressor\CompressorInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Guetzli implements CompressorInterface
{
    protected $compressorPath;
    /** @var object SplFileInfo */
    protected $originalFile;
    protected $destination;

    protected $allowed_extensions = ['jpg', 'jpeg', 'png'];

    protected $quality;

    const MIN_QUALITY = 84;  // specific minimal quality for guetzli
    const MAX_QUALITY = 100;
    /**
     * Constructor.
     *
     * @param string $compressorPath The path to run guetzli app
     * eg. /usr/sbin/guetzli
     */
    public function __construct($compressorPath = 'guetzli')
    {
        $this->compressorPath = $compressorPath;
    }
    /**
     * Set original file info
     *
     * @return $this
     */
    public function setOriginalFile(SplFileInfo $originalFile)
    {
        $this->originalFile = $this->validateOriginalFile($originalFile);

        return $this;
    }
    /**
     * Set compressed file path
     *
     * @return $this
     */
    public function setDestination($destination)
    {
        $this->destination = $this->validateDestination($destination);

        return $this;
    }
    /**
     * Set compression image quality.
     *
     * @return $this
     */
    public function setQuality($quality = 90)
    {
        $this->quality = $this->validateQuality($quality);

        return $this;
    }

    public function compress()
    {
        $commandLine = $this->buildCommandLine();

        $process = new Process($commandLine);
        $process
            ->setTimeout(3600)
            ->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function buildCommandLine()
    {
        $args = [];

        $args[] = escapeshellarg($this->compressorPath);

        if (empty($this->destination)) {
            $this->destination = $this->originalFile->getRealPath();
        }

        if (!is_null($this->quality)) {
            $args[] = sprintf('--quality %u', $this->quality);
        }

        $args[] = escapeshellarg($this->originalFile->getRealPath());
        $args[] = escapeshellarg($this->destination);

        return escapeshellcmd(implode(' ', $args));
    }

    protected function validateOriginalFile(SplFileInfo $originalFile)
    {
        $filePath = $originalFile->getRealPath();

        if (false === $filePath || !is_file($filePath)) {
            throw new \InvalidArgumentException('No such original file');
        }

        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException('The original file is not readable');
        }

        if (!in_array(strtolower($originalFile->getExtension()), $this->allowed_extensions)) {
            throw new \InvalidArgumentException('Guetzli is not allowed extension');
        }

        return $originalFile;
    }

    protected function validateDestination($destination)
    {
        $destinationDirectory = dirname($destination);

        if (!is_dir($destinationDirectory)) {
            throw new \InvalidArgumentException('No such destination directory');
        }

        if (!is_writable($destinationDirectory)) {
            throw new \InvalidArgumentException('The destination directory is not writable');
        }

        return (string) $destination;
    }

    protected function validateQuality($quality)
    {
        if ($quality < self::MIN_QUALITY) {
            $quality = self::MIN_QUALITY;
        }

        if ($quality > self::MAX_QUALITY) {
            $quality = self::MAX_QUALITY;
        }

        return (int) $quality;
    }
}
