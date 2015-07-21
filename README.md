# ImageCompare
PHP micro Lib for image comparison

## Installation
Just clone/download this repo and include the `autoload.php`.

## Concept
The idea behind this comparison is simple.

1. Eliminate the background to white
2. Isolate the remaining areas
3. Compare the mean-color of all areas and the area count in both images

## Sample

```php
// Load first image
$image1 = Image::fromFile('demo_inputs/image1.png');

// Load second image to compare
$image2 = Image::fromFile('demo_inputs/image2.png');

// If both images have an known background substract it
$mask = Image::fromFile('demo_inputs/mask.png');
$image1 = $image1->subtract($mask, 15); // use 15% tolerance
$image2 = $image2->subtract($mask, 15); // use 15% tolerance

// Compare both images
$equal = $image1->compare($image2); // Returns a boolean value whether these images are equal or not

// Or if you are interessted in how equal they are
$diff = $image1->difference($image2) // Retuns a float between 1 and 0, where 1 is equal and 0 is total difference 
```
