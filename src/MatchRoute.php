<?php

namespace Kucbel\Routing;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\SmartObject;

class MatchRoute implements IRouter
{
	use SmartObject;

	/**
	 * @var IRouter
	 */
	private $router;

	/**
	 * MatchRoute constructor.
	 *
	 * @param IRouter $router
	 */
	function __construct( IRouter $router )
	{
		$this->router = $router;
	}

	/**
	 * @param IRequest $request
	 * @return Request | null
	 */
	function match( IRequest $request ) : ?Request
	{
		return $this->router->match( $request );
	}

	/**
	 * @param Request $request
	 * @param Url $url
	 * @return string | null
	 */
	function constructUrl( Request $request, Url $url ) : ?string
	{
		return null;
	}
}
