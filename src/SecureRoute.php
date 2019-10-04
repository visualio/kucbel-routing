<?php

namespace Kucbel\Routing;

use Nette\Application\ForbiddenRequestException;
use Nette\Http\IRequest;
use Nette\Http\UrlScript;
use Nette\InvalidArgumentException;
use Nette\Routing\Router;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class SecureRoute implements Router
{
	use SmartObject;

	/**
	 * @var string
	 */
	private $salt;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * SecureRoute constructor.
	 *
	 * @param string $salt
	 * @param string $path
	 */
	function __construct( string $salt, string $path = 'secure')
	{
		$this->salt = $salt;
		$this->path = $path;
	}

	/**
	 * @param IRequest $request
	 * @return array | null
	 * @throws
	 */
	function match( IRequest $request ) : ?array
	{
		$path = $request->getUrl()->getPath();

		if( strncmp( $path, "/{$this->path}/", strlen( $this->path ) + 2 )) {
			return null;
		}

		$path = ltrim( $path, '/');
		$path = explode('/', $path );

		if( count( $path ) !== 3 ) {
			throw new ForbiddenRequestException;
		}

		[ 1 => $hash, 2 => $base ] = $path;

		if( $hash !== $this->hash( $base )) {
			throw new ForbiddenRequestException;
		}

		return Json::decode( base64_decode( $base ), Json::FORCE_ARRAY );
	}

	/**
	 * @param array $data
	 * @param UrlScript $url
	 * @return string
	 */
	function constructUrl( array $data, UrlScript $url ) : string
	{
		$data = array_filter( $data, [ $this, 'incl']);

		try {
			$json = Json::encode( $data );
		} catch( JsonException $ex ) {
			throw new InvalidArgumentException( $ex->getMessage(), null, $ex );
		}

		$base = base64_encode( $json );
		$base = rtrim( $base, '=');

		$hash = $this->hash( $base );

		return "{$url->getBaseUrl()}{$this->path}/{$hash}/{$base}";
	}

	/**
	 * @param string $data
	 * @return string
	 */
	function hash( string $data ) : string
	{
		return md5("{$this->salt}+{$data}=?");
	}

	/**
	 * @param mixed $value
	 * @return bool
	 * @internal
	 */
	function incl( $value ) : bool
	{
		return $value !== null;
	}
}
