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
 * Collection of image-objects.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class ImageCollection implements IteratorAggregate
{
	/**
	 * The images
	 * @var array
	 */
	
	private $images = array();


	/**
	 * Constructor
	 *
	 * @param array  (optinal) the array with images
	 *
	 */

	public function __construct(array $images = array())
	{
		$this->images = $images;
	}


	/**
	 * Add a new image to this collection.
	 *
	 * @param  Image  $image the new image
	 *
	 * @return self
	 *
	 */
	
	public function push(Image $image) {
		$this->images[] = $image;
		return $this;
	}


	/**
	 * Remove the last image of this collection and return it.
	 *
	 * @return Image  the removed image
	 * 
	 */
	
	public function pop()
	{
		return array_pop($this->images);
	}


	/**
	 * Returns the size of this collection.
	 *
	 * @return integer  the collection-size.
	 * 
	 */
	
	public function size()
	{
		return sizeof($this->images);
	}


	/**
	 * Loops over all images in this collection and calls their
	 * save-method.
	 *
	 * @param string  $prefix the preifx for the image hashs
	 * @param string  $path   the path where to store the images
	 * @param boolean $salt   wether to use a salt or not
	 *
	 * @return self
	 * 
	 */

	public function save($prefix = '', $path = '', $salt = false)
	{
		foreach($this->images as $index => $image) {
			$name = $image->hash($salt ? $index : '');
			$image->save($prefix . $name, $path);
		}

		return $this;
	}

    public function getIterator() {
        return new ArrayIterator($this->images);
    }
}