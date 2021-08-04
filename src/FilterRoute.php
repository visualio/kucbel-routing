<?php

namespace Kucbel\Routing;

use Nette\Http\IRequest;
use Nette\Http\UrlScript;
use Nette\Routing\Router;
use Nette\SmartObject;

class FilterRoute implements Router
{
	use SmartObject;

	const
		MATCH	= 0b1,
		BUILD	= 0b10;

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var bool
	 */
	private $match;

	/**
	 * @var bool
	 */
	private $build;

	/**
	 * FilterRoute constructor.
	 *
	 * @param Router $router
	 * @param int $allow
	 */
	function __construct( Router $router, int $allow )
	{
		$this->router = $router;
		$this->match = $allow & self::MATCH ? true : false;
		$this->build = $allow & self::BUILD ? true : false;
	}

	/**
	 * @param IRequest $request
	 * @return array | null
	 */
	function match( IRequest $request ) : ?array
	{
		if( $this->match ) {
			return $this->router->match( $request );
		} else {
			return null;
		}
	}

	/**
	 * @param array $params
	 * @param UrlScript $url
	 * @return string | null
	 */
	function constructUrl( array $params, UrlScript $url ) : ?string
	{
		if( $this->build ) {
			return $this->router->constructUrl( $params, $url );
		} else {
			return null;
		}
	}
}
