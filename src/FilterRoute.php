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
	 * @var int
	 */
	private $allow;

	/**
	 * FilterRoute constructor.
	 *
	 * @param Router $router
	 * @param int $allow
	 */
	function __construct( Router $router, int $allow )
	{
		$this->router = $router;
		$this->allow = $allow;
	}

	/**
	 * @param IRequest $request
	 * @return array | null
	 */
	function match( IRequest $request ) : ?array
	{
		if( $this->allow & self::MATCH ) {
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
		if( $this->allow & self::BUILD ) {
			return $this->router->constructUrl( $params, $url );
		} else {
			return null;
		}
	}
}
