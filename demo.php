<?php

require 'autoload.php';

// Load the mask

$mask = Image::fromFile('demo_inputs/mask.png');

$image1 = Image::fromFile('demo_inputs/image1.png')->resize([100, 100])->substract($mask, 25)->save('masked_image1', 'demo_outputs/');
$image2 = Image::fromFile('demo_inputs/image2.png')->resize([100, 100])->substract($mask, 25)->save('masked_image1', 'demo_outputs/');
$image3 = Image::fromFile('demo_inputs/image3.png')->resize([100, 100])->substract($mask, 25)->save('masked_image1', 'demo_outputs/');

$other1 = Image::fromFile('demo_inputs/other1.png')->resize([100, 100])->substract($mask, 25)->save('masked_other1', 'demo_outputs/');
$other2 = Image::fromFile('demo_inputs/other2.png')->resize([100, 100])->substract($mask, 25)->save('masked_other2', 'demo_outputs/');
$other3 = Image::fromFile('demo_inputs/other3.png')->resize([100, 100])->substract($mask, 25)->save('masked_other3', 'demo_outputs/');


echo "Equal\n";
var_dump(
	$image1->compare($image1),
	$image1->compare($image2),
	$image1->compare($image3)
);

echo "All other to image 1\n";
var_dump(
	$image1->compare($other1),
	$image1->compare($other2),
	$image1->compare($other3)
);

echo "All other to image 2\n";
var_dump(
	$image2->compare($other1),
	$image2->compare($other2),
	$image2->compare($other3)
);

echo "All other to image 3\n";
var_dump(
	$image3->compare($other1),
	$image3->compare($other2),
	$image3->compare($other3)
);