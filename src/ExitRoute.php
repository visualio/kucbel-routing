<?php

namespace Kucbel\Routing;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\InvalidArgumentException;
use Nette\SmartObject;

class ExitRoute implements IRouter
{
	use SmartObject;

	/**
	 * @var IRouter
	 */
	private $router;

	/**
	 * @var Url
	 */
	private $url;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $param;

	/**
	 * ExitRoute constructor.
	 *
	 * @param IRouter $router
	 * @param Url $url
	 * @param string $name
	 * @param string $param
	 */
	function __construct( IRouter $router, Url $url, string $name, string $param = 'exit')
	{
		if( !$name or !$param ) {
			throw new InvalidArgumentException;
		}

		$this->router = $router;
		$this->url = $url;
		$this->name = $name;
		$this->param = $param;
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
		if( $request->getParameter( $this->param ) === $this->name ) {
			$param = $request->getParameters();

			unset( $param[ $this->param ] );

			$request = clone $request;
			$request->setParameters( $param );

			return $this->router->constructUrl( $request, $this->url );
		} else {
			return null;
		}
	}
}
