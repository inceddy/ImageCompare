<?php

require 'autoload.php';

// Load the mask

$mask = Image::fromFile('demo_inputs/mask.png');

$circles = Image::fromFile('demo_inputs/circles.png');
$squares = Image::fromFile('demo_inputs/squares.png');

var_dump($circles->difference($squares));

/*
$image1 = Image::fromFile('demo_inputs/image1.png')->substract($mask, 25)->save('masked_image1', 'demo_outputs/');
$image2 = Image::fromFile('demo_inputs/image2.png')->substract($mask, 25)->save('masked_image1', 'demo_outputs/');
$image3 = Image::fromFile('demo_inputs/image2.png')->substract($mask, 25)->save('masked_image1', 'demo_outputs/');

$other1 = Image::fromFile('demo_inputs/other1.png')->substract($mask, 25)->save('masked_other1', 'demo_outputs/');
$other2 = Image::fromFile('demo_inputs/other2.png')->substract($mask, 25)->save('masked_other2', 'demo_outputs/');
$other3 = Image::fromFile('demo_inputs/other3.png')->substract($mask, 25)->save('masked_other3', 'demo_outputs/');
*/


/*

echo "Equal\n";
var_dump(
	$image1->difference($image1),
	$image1->difference($image2),
	$image1->difference($image3)
);

echo "Different to image 1\n";
var_dump(
	$image1->difference($other1),
	$image1->difference($other2),
	$image1->difference($other3)
);

echo "Different to image 2\n";
var_dump(
	$image2->difference($other1),
	$image2->difference($other2),
	$image2->difference($other3)
);

*/

/*
$image1 = Image::fromFile('demo_inputs/image1.png');
$image2 = Image::fromFile('demo_inputs/image2.png');

var_dump($image1->difference($image2));
*/