<?php

/*
 * This file is part of ImageCompare.
 *
 * (c) 2015 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */


/**
 * Representation of an image as pixel-matrix.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */


class ImagePixelMatrix {

	/**
	 * The pixel matrix of color-objects
	 * representing this image.
	 * @var array
	 */
	
	private $matrix = array();

	/**
	 * The image-object this pixel-matrix is build on.
	 * @var Image
	 */
	
	private $image = null;



	public function __construct(Image $image)
	{
		$this->image = $image;
		$this->matrix = $this->getMatrix();
	}

	private function getMatrix() 
	{
		$matrix = array();

		$size = $this->image->size();

		for ($columns = 0; $columns < $size[0]; $columns++) {
			$this->matirx[$columns] = array_fill(0, $size[1], 0);
		}

		for ($x = 0; $x < $size[0]; $x++) {
			for ($y = 0; $y < $size[1]; $y++) {
				$matrix[$x][$y] = new Pixel($x, $y, $this->image->colorat($x, $y));
			}
		}

		return $matrix;
	}

	public function getHotSpots()
	{
		$crawler  = new Crawler($this);
		$outlines = new CrawlerOutlineCollection();

		$size = $this->image->size();

		for ($x = 0; $x < $size[0]; $x++) {
			for ($y = 0; $y < $size[1]; $y++) {
			
				$pixel = $this->pixel($x, $y);

				// Skip white pixels
				if ($pixel->color()->compare(Color::white(), 5)) {
					continue;
				}
				
				// Skip crawled areas
				if ($outlines->contains($pixel)) {
					continue;
				}

				// Start crawling
				$outline = $crawler->crawl($x, $y);
				$outlines->push($outline);
			}
		}

		$hotspots = new ImageCollection();

		foreach($outlines as $outline) {
			$hotspots->push($this->image->sliceByOutline($outline));
		}

		return array($hotspots, $outlines);
	}


	/**
	 * Gets the pixel at position x, y.
	 * If the position is outside of this matrix null is returned.
	 *
	 * @param  integer $x  the x-position
	 * @param  integer $y  the y-position
	 *
	 * @return Pixel|null  the pixel or null
	 * 
	 */
	
	public function pixel($x, $y)
	{
		return isset($this->matrix[$x][$y]) ? $this->matrix[$x][$y] : null;
	}


	/**
	 * Factory method
	 *
	 * @param  Image  $image      the underlying image
	 *
	 * @return ImagePixelMatrix   the new pixel-matrix
	 * 
	 */
	
	public static function fromImage(Image $image)
	{
		return new self($image);
	}
}