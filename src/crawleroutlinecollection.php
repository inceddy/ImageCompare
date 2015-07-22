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
 * Collection of crawler-outline-objects.
 *
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 * 
 */

class CrawlerOutlineCollection implements IteratorAggregate, ArrayAccess
{
	/**
	 * The outline-objects
	 * @var array
	 */
	
	private $outlines = array();


	/**
	 * Constructor
	 *
	 * @param array  (optinal) the array with outline-objects
	 *
	 */

	public function __construct(array $outlines = array())
	{
		$this->outlines = $outlines;
	}

	public function size()
	{
		return sizeof($this->outlines);
	}


	/**
	 * Add a new outline to this collection.
	 *
	 * @param  CrawlerOutline $outline  the new outline-object
	 *
	 * @return self
	 *
	 */
	
	public function push(CrawlerOutline $outline) {
		$this->outlines[] = $outline;
		return $this;
	}


	/**
	 * Remove the last outline-object of this collection and return it.
	 *
	 * @return CrawlerOutline  the removed outline-object
	 * 
	 */
	
	public function pop()
	{
		return array_pop($this->outlines);
	}


	/**
	 * Check whether the giben point/pixel is in or outside 
	 * of the boundaries in this collection.
	 *
	 * @param Point   $point  the point/pixel to check.
	 *
	 * @return boolean        whether the point/pixel is in- or oudsite.
	 * 
	 */

	public function contains(Point $point)
	{
		foreach($this->outlines as $outline) {
			if ($outline->contains($point) >= 0) {
				return true;
			}
		}

		return false;
	}

	public function offsetExists($offset)
	{
		return isset($this->outlines[$offset]);
	}
	
	public function offsetGet($offset)
	{
		return $this->outlines[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new Exception('Readonly');
	}

	public function offsetUnset($offset)
	{
		throw new Exception('Readonly');
	}

    public function getIterator() {
        return new ArrayIterator($this->outlines);
    }
}