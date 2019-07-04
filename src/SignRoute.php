<?php

namespace Kucbel\Routing;

use Nette\Application\ForbiddenRequestException;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\InvalidArgumentException;
use Nette\SmartObject;
use Nette\Utils\Json;

class SignRoute implements IRouter
{
	use SmartObject;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $param;

	/**
	 * @var string
	 */
	private $query;

	/**
	 * SignRoute constructor.
	 *
	 * @param string $token
	 * @param string $param
	 * @param string $query
	 */
	function __construct( string $token, string $param = 'sign', string $query = 'sire')
	{
		if( !$token or !$param or !$query ) {
			throw new InvalidArgumentException;
		}

		$this->token = $token;
		$this->param = $param;
		$this->query = $query;
	}

	/**
	 * @param IRequest $request
	 * @return Request | null
	 * @throws
	 */
	function match( IRequest $request ) : ?Request
	{
		$sire = $request->getQuery( $this->query );

		if( !$sire ) {
			return null;
		}

		$sire = explode('.', $sire, 2 );
		$hash = $sire[0] ?? null;
		$base = $sire[1] ?? null;

		if( !$hash or !$base ) {
			throw new ForbiddenRequestException;
		} elseif( $hash !== $this->encode( $base )) {
			throw new ForbiddenRequestException;
		}

		$data = Json::decode( base64_decode( $base ), Json::FORCE_ARRAY );

		$flag[ Request::SECURED ] = $request->isSecured();
		$flag[ $this->param ] = true;

		$data['x']['action'] = $data['a'] ?? Presenter::DEFAULT_ACTION;

		return new Request( $data['p'], $request->getMethod(), $data['x'], $request->getPost(), $request->getFiles(), $flag );
	}

	/**
	 * @param Request $request
	 * @param Url $url
	 * @return string | null
	 * @throws
	 */
	function constructUrl( Request $request, Url $url ) : ?string
	{
		$want = $request->getParameter( $this->param );

		if( !$want ) {
			return null;
		}

		$param = $request->getParameters();

		unset(
			$param[ $this->param ],
			$param['action']
		);

		$data['p'] = $request->getPresenterName();
		$data['a'] = $request->getParameter('action');
		$data['x'] = array_filter( $param, 'is_scalar');

		$base = base64_encode( Json::encode( $data ));
		$base = rtrim( $base, '=');

		$hash = $this->encode( $base );

		$url = clone $url;
		$url->setQuery([ $this->query => "{$hash}.{$base}"]);
		$url->setPath('/');

		return $url->getAbsoluteUrl();
	}

	/**
	 * @param string $data
	 * @return string
	 */
	function encode( string $data ) : string
	{
		return substr( sha1("{$this->token}+{$data}=?"), 4, 16 );
	}
}
