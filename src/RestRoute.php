<?php

namespace Kucbel\Routing;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\SmartObject;

class RestRoute implements IRouter
{
	use SmartObject;

	/**
	 * @var IRouter
	 */
	private $router;

	/**
	 * @var array
	 */
	private $actions = [
		IRequest::GET		=> 'search',
		IRequest::POST		=> 'create',
		IRequest::PATCH		=> 'update',
		IRequest::DELETE	=> 'delete',
	];

	/**
	 * ApeRoute constructor.
	 *
	 * @param IRouter $router
	 * @param array $actions
	 */
	function __construct( IRouter $router, array $actions = null )
	{
		$this->router = $router;

		if( $actions ) {
			$this->actions = $actions;
		}
	}

	/**
	 * @param IRequest $request
	 * @return Request | null
	 */
	function match( IRequest $request ) : ?Request
	{
		$method = $request->getMethod();

		if( $action = $this->actions[ $method ] ?? null and $forward = $this->router->match( $request )) {
			$params = $forward->getParameters();
			$params['action'] = $action;

			$forward->setParameters( $params );
			$forward->setFlag('rest');

			return $forward;
		} else {
			return null;
		}
	}

	/**
	 * @param Request $request
	 * @param Url $url
	 * @return string | null
	 */
	function constructUrl( Request $request, Url $url ) : ?string
	{
		return $this->router->constructUrl( $request, $url );
	}
}
