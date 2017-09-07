# ImageCompressor
Wrapper to any image compression engine

## Install via composer
```
"ytubes/image-compressor": "^1.0.0"
```

### Example usage
```
$testImage = new \SplFileInfo('/some_dir/test.jpg');
$newFilepath = '/destination/dir/test3.jpg';

$driver = 'guetzli'; // or
// $driver = \ImageCompressor\Driver\Guetzli::class; // or
// $driver = new \ImageCompressor\Driver\Guetzli('/path/to/guetzli');

$compressor = new \ImageCompressor\ImageCompressor($driver);
$compressor
    ->setOriginalFile($testImage)
    ->setDestination($newFilepath)
    ->setQuality(90)
    ->compress();
```
Or overwrite self:
```
$compressor = (new \ImageCompressor\ImageCompressor($driver));
$compressor
    ->setOriginalFile($testImage)
    ->compress();
```
