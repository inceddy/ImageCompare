<?php

Class Image {

	const HASH_SIZE = 8;
	const AVG_SIZE = 10;

	private $img = null;

	public function __construct($resource)
	{
		$this->img = $resource;;
	}

	private function permute(array $a1, array $a2) {
	    $perms = array();
	    for($i = 0; $i < sizeof($a1); $i++) {
	    	for($j = 0; $j < sizeof($a2); $j++) {
	    		if ($i != $j) {
	    			$perms[] = [$a1[$i], 
	    			$a2[$j]];
	    		}
	    	}
	    }

	    return $perms;
	}

	public function compare(Image $comp) {
		// Get chunk avg
		$avgComp = array();
		foreach($comp->chunk(50) as $chunk) {
			$avgComp[] = $chunk->avg();
		}

		$avgOrg = array();
		foreach($this->chunk(50) as $chunk) {
			$avgOrg[] = $chunk->avg();
		}

		// Delete white
		$white = Color::fromInt(0xFFFFFF);

		$avgComp = array_values(array_filter($avgComp, function(Color $color) use ($white){
			return !$white->compare($color, 1);
		}));

		$avgOrg = array_values(array_filter($avgOrg, function(Color $color) use ($white){
			return !$white->compare($color, 1);
		}));

		usort($avgComp, function($c1, $c2){ return $c1->toInt() - $c2->toInt();});
		usort($avgOrg,  function($c1, $c2){ return $c1->toInt() - $c2->toInt();});

		//var_dump($avgComp, $avgOrg);
		$total = min(sizeof($avgComp), sizeof($avgOrg));

		$diff = 0;
		foreach($avgComp as $index => $c) {
			if (!isset($avgOrg[$index]))
				break;

			$diff += $avgOrg[$index]->compare($c);
		}

		return ($diff / $total);
	}

	public function substract(Image $mask, $tolerance = 1)
	{
		$size = $this->size();

		if ($mask->size() != $size) {
			$mask = $mask->resize($size);
		}

		for ($x = 0; $x < $size[0]; $x++) {
			for ($y = 0; $y < $size[1]; $y++) {
				if ($this->colorat($x, $y)->compare($mask->colorat($x, $y), $tolerance))
					imagesetpixel($this->img, $x, $y, 0xFFFFFF);
			}
		}

		return $this;
	}

	public function avg($size = 10)
	{
		$target = $this->resize([self::AVG_SIZE, self::AVG_SIZE]);

		$avg   = Color::fromInt(0xFFFFFF);
		$white = Color::fromInt(0xFFFFFF);  

		for ($x = 0; $x < self::AVG_SIZE; $x++) {
			for ($y = 0; $y < self::AVG_SIZE; $y++) {
				$color = $target->colorat($x, $y);

				if (!$white->compare($color, 1)) {
					$avg->mix($color);
				}
			}
		}

		return $avg;
	}

	public function colorat($x, $y)
	{
		return Color::fromInt(imagecolorat($this->img, $x, $y));
	}

	public function chunk($chunkSize = 10)
	{
		$collection = new ImageCollection();
		$size = $this->size();

		for($x = 0; $x < $size[0]; $x += $chunkSize) {
			for($y = 0; $y < $size[1]; $y += $chunkSize) {
				switch (true) {
					case ($x + $chunkSize > $size[0] && $y + $chunkSize > $size[1]):
						$collection->push($this->slice(['x' => $x, 'y' => $y, 'height' => $size[0] - $x, 'width' => $size[1] - $y]));
						break;
					case ($x + $chunkSize > $size[0]):
						$collection->push($this->slice(['x' => $x, 'y' => $y, 'height' => $chunkSize, 'width' => $size[1] - $x]));
						break;
					case ($y + $chunkSize > $size[1]):
						$collection->push($this->slice(['x' => $x, 'y' => $y, 'height' => $size[0] - $y, 'width' => $chunkSize]));
						break;
					default:
						$collection->push($this->slice(['x' => $x, 'y' => $y, 'height' => $chunkSize, 'width' => $chunkSize]));
						break;
				}
			}
		}

		return $collection;
	}

	public function slice(array $rect)
	{
		// This does not work in all PHP versions due to a bug
		//return Image::fromResource(imagecrop($this->img, $rect));
		
		$target = imagecreatetruecolor($rect['width'], $rect['height']);
		imagecopy($target, $this->img, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height']);

		return self::fromResource($target);
	}

	public function size()
	{
		return [imagesx($this->img), imagesy($this->img)];
	}

	public function resize(array $size = array(100, 100))
	{
		$target = imagecreatetruecolor($size[0], $size[1]);
		imagecopyresized($target, $this->img, 0, 0, 0, 0, $size[0], $size[1], imagesx($this->img), imagesy($this->img));

		return Image::fromResource($target);
	}

	public function show()
	{
		header("Content-type: image/png");
		imagepng($this->img);
		die();
	}

	public function save($name = null, $path = '') {
		if ($name === null) {
			$name = $this->hash();
		}

		imagepng($this->img, $path . $name . '.png');

		return $this;
	}

	public function hash()
	{
		        // Resize the image.
        $resized = imagecreatetruecolor(self::HASH_SIZE, self::HASH_SIZE);
        imagecopyresampled($resized, $this->img, 0, 0, 0, 0, self::HASH_SIZE, self::HASH_SIZE, imagesx($this->img), imagesy($this->img));
        // Create an array of greyscale pixel values.
        $pixels = [];
        for ($y = 0; $y < self::HASH_SIZE; $y++)
        {
            for ($x = 0; $x < self::HASH_SIZE; $x++)
            {
                $rgb = imagecolorsforindex($resized, imagecolorat($resized, $x, $y));
                $pixels[] = floor(($rgb['red'] + $rgb['green'] + $rgb['blue']) / 3);
            }
        }
        // Free up memory.
        imagedestroy($resized);
        // Get the average pixel value.
        $average = floor(array_sum($pixels) / count($pixels));
        // Each hash bit is set based on whether the current pixels value is above or below the average.
        $hash = 0; $one = 1;
        foreach ($pixels as $pixel)
        {
            if ($pixel > $average) $hash |= $one;
            $one = $one << 1;
        }
        return md5($hash);
	}

	public static function fromResource($resource)
	{
		return new self($resource);
	}

	public static function fromBin($binf)
	{
		return new self(imagecreatefromstring($bin));
	}

	public static function fromFile($path)
	{
		return new self(imagecreatefromstring(file_get_contents($path)));
	}
}