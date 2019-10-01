<?php

namespace Kucbel\Routing;

use Nette\Routing\Router;
use Nette\Http\IRequest;
use Nette\Http\UrlScript;
use Nette\SmartObject;

class MethodRoute implements Router
{
	use SmartObject;

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var array
	 */
	private $actions;

	/**
	 * MethodRoute constructor.
	 *
	 * @param Router $router
	 * @param array $actions
	 */
	function __construct( Router $router, array $actions = null )
	{
		$this->router = $router;
		$this->actions = $actions ?? [
				IRequest::GET		=> 'search',
				IRequest::POST		=> 'create',
				IRequest::PATCH		=> 'update',
				IRequest::DELETE	=> 'delete',
			];
	}

	/**
	 * @param IRequest $request
	 * @return array | null
	 */
	function match( IRequest $request ) : ?array
	{
		$method = $request->getMethod();

		if( !array_key_exists( $method, $this->actions )) {
			return null;
		}

		$params = $this->router->match( $request );

		if( $params !== null ) {
			$params['action'] = $this->actions[ $method ];

			return $params;
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
		return $this->router->constructUrl( $params, $url );
	}
}
