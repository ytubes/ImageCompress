<?php
namespace ImageCompressor;

use SplFileInfo;

interface CompressorInterface
{
    public function setOriginalFile(SplFileInfo $originalFile);

    public function setDestination($destination);

    public function setQuality($quality = 90);

    public function compress();
}
