<?php

namespace Smolblog\Foundation\Value\Fields;

use GuzzleHttp\Psr7\Exception\MalformedUriException;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;

/**
 * PSR-7 compatible URL field.
 */
readonly class Url extends Value implements Field, UriInterface {
	use FieldKit;

	/**
	 * Internal Guzzle URL object.
	 *
	 * @var UriInterface
	 */
	private UriInterface $internal;

	/**
	 * Create the field from a URL string.
	 *
	 * @throws InvalidValueProperties If the URL is not valid.
	 *
	 * @param string $url Valid URL.
	 */
	public function __construct(string $url) {
		try {
			$this->internal = new Uri($url);
		} catch (MalformedUriException $e) {
			throw new InvalidValueProperties($e->getMessage());
		}
	}


	/**
	 * Get the URL as as string.
	 *
	 * @return string
	 */
	public function toString(): string {
		return strval($this->internal);
	}

	/**
	 * Create a new URL from a string.
	 *
	 * This is identical to using the constructor.
	 *
	 * @param string $string URL string.
	 * @return static
	 */
	public static function fromString(string $string): static {
		return new self($string);
	}

	/**
	 * Retrieve the authority component of the URI.
	 *
	 * If no authority information is present, this method MUST return an empty
	 * string.
	 *
	 * The authority syntax of the URI is:
	 *
	 * <pre>
	 * [user-info@]host[:port]
	 * </pre>
	 *
	 * If the port component is not set or is the standard port for the current
	 * scheme, it SHOULD NOT be included.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-3.2
	 * @return string The URI authority, in "[user-info@]host[:port]" format.
	 */
	public function getAuthority() {
		return $this->internal->getAuthority();
	}

	/**
	 * Retrieve the fragment component of the URI.
	 *
	 * If no fragment is present, this method MUST return an empty string.
	 *
	 * The leading "#" character is not part of the fragment and MUST NOT be
	 * added.
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.5.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.5
	 * @return string The URI fragment.
	 */
	public function getFragment() {
		return $this->internal->getFragment();
	}

	/**
	 * Retrieve the host component of the URI.
	 *
	 * If no host is present, this method MUST return an empty string.
	 *
	 * The value returned MUST be normalized to lowercase, per RFC 3986
	 * Section 3.2.2.
	 *
	 * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
	 * @return string The URI host.
	 */
	public function getHost() {
		return $this->internal->getHost();
	}

	/**
	 * Retrieve the path component of the URI.
	 *
	 * The path can either be empty or absolute (starting with a slash) or
	 * rootless (not starting with a slash). Implementations MUST support all
	 * three syntaxes.
	 *
	 * Normally, the empty path "" and absolute path "/" are considered equal as
	 * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
	 * do this normalization because in contexts with a trimmed base path, e.g.
	 * the front controller, this difference becomes significant. It's the task
	 * of the user to handle both "" and "/".
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.3.
	 *
	 * As an example, if the value should include a slash ("/") not intended as
	 * delimiter between path segments, that value MUST be passed in encoded
	 * form (e.g., "%2F") to the instance.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.3
	 * @return string The URI path.
	 */
	public function getPath() {
		return $this->internal->getPath();
	}

	/**
	 * Retrieve the port component of the URI.
	 *
	 * If a port is present, and it is non-standard for the current scheme,
	 * this method MUST return it as an integer. If the port is the standard port
	 * used with the current scheme, this method SHOULD return null.
	 *
	 * If no port is present, and no scheme is present, this method MUST return
	 * a null value.
	 *
	 * If no port is present, but a scheme is present, this method MAY return
	 * the standard port for that scheme, but SHOULD return null.
	 *
	 * @return null|integer The URI port.
	 */
	public function getPort() {
		return $this->internal->getPort();
	}

	/**
	 * Retrieve the query string of the URI.
	 *
	 * If no query string is present, this method MUST return an empty string.
	 *
	 * The leading "?" character is not part of the query and MUST NOT be
	 * added.
	 *
	 * The value returned MUST be percent-encoded, but MUST NOT double-encode
	 * any characters. To determine what characters to encode, please refer to
	 * RFC 3986, Sections 2 and 3.4.
	 *
	 * As an example, if a value in a key/value pair of the query string should
	 * include an ampersand ("&") not intended as a delimiter between values,
	 * that value MUST be passed in encoded form (e.g., "%26") to the instance.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2
	 * @see https://tools.ietf.org/html/rfc3986#section-3.4
	 * @return string The URI query string.
	 */
	public function getQuery() {
		return $this->internal->getQuery();
	}

	/**
	 * Retrieve the scheme component of the URI.
	 *
	 * If no scheme is present, this method MUST return an empty string.
	 *
	 * The value returned MUST be normalized to lowercase, per RFC 3986
	 * Section 3.1.
	 *
	 * The trailing ":" character is not part of the scheme and MUST NOT be
	 * added.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-3.1
	 * @return string The URI scheme.
	 */
	public function getScheme() {
		return $this->internal->getScheme();
	}

	/**
	 * Retrieve the user information component of the URI.
	 *
	 * If no user information is present, this method MUST return an empty
	 * string.
	 *
	 * If a user is present in the URI, this will return that value;
	 * additionally, if the password is also present, it will be appended to the
	 * user value, with a colon (":") separating the values.
	 *
	 * The trailing "@" character is not part of the user information and MUST
	 * NOT be added.
	 *
	 * @return string The URI user information, in "username[:password]" format.
	 */
	public function getUserInfo() {
		return $this->internal->getUserInfo();
	}

	/**
	 * Return an instance with the specified URI fragment.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified URI fragment.
	 *
	 * Users can provide both encoded and decoded fragment characters.
	 * Implementations ensure the correct encoding as outlined in getFragment().
	 *
	 * An empty fragment value is equivalent to removing the fragment.
	 *
	 * @param string $fragment The fragment to use with the new instance.
	 * @return static A new instance with the specified fragment.
	 */
	public function withFragment(string $fragment): UriInterface {
		return $this->internal->withFragment($fragment);
	}

	/**
	 * Return an instance with the specified host.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified host.
	 *
	 * An empty host value is equivalent to removing the host.
	 *
	 * @param string $host The hostname to use with the new instance.
	 * @return static A new instance with the specified host.
	 * @throws \InvalidArgumentException For invalid hostnames.
	 */
	public function withHost(string $host): UriInterface {
		return $this->internal->withHost($host);
	}

	/**
	 * Return an instance with the specified path.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified path.
	 *
	 * The path can either be empty or absolute (starting with a slash) or
	 * rootless (not starting with a slash). Implementations MUST support all
	 * three syntaxes.
	 *
	 * If the path is intended to be domain-relative rather than path relative then
	 * it must begin with a slash ("/"). Paths not starting with a slash ("/")
	 * are assumed to be relative to some base path known to the application or
	 * consumer.
	 *
	 * Users can provide both encoded and decoded path characters.
	 * Implementations ensure the correct encoding as outlined in getPath().
	 *
	 * @param string $path The path to use with the new instance.
	 * @return static A new instance with the specified path.
	 * @throws \InvalidArgumentException For invalid paths.
	 */
	public function withPath(string $path): UriInterface {
		return $this->internal->withPath($path);
	}

	/**
	 * Return an instance with the specified port.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified port.
	 *
	 * Implementations MUST raise an exception for ports outside the
	 * established TCP and UDP port ranges.
	 *
	 * A null value provided for the port is equivalent to removing the port
	 * information.
	 *
	 * @param null|integer $port The port to use with the new instance; a null value
	 *         removes the port information.
	 * @return static A new instance with the specified port.
	 * @throws \InvalidArgumentException For invalid ports.
	 */
	public function withPort(int|null $port): UriInterface {
		return $this->internal->withPort($port);
	}

	/**
	 * Return an instance with the specified query string.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified query string.
	 *
	 * Users can provide both encoded and decoded query characters.
	 * Implementations ensure the correct encoding as outlined in getQuery().
	 *
	 * An empty query string value is equivalent to removing the query string.
	 *
	 * @param string $query The query string to use with the new instance.
	 * @return static A new instance with the specified query string.
	 * @throws \InvalidArgumentException For invalid query strings.
	 */
	public function withQuery(string $query): UriInterface {
		return $this->internal->withQuery($query);
	}

	/**
	 * Return an instance with the specified scheme.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified scheme.
	 *
	 * Implementations MUST support the schemes "http" and "https" case
	 * insensitively, and MAY accommodate other schemes if required.
	 *
	 * An empty scheme is equivalent to removing the scheme.
	 *
	 * @param string $scheme The scheme to use with the new instance.
	 * @return static A new instance with the specified scheme.
	 * @throws \InvalidArgumentException For invalid or unsupported schemes.
	 */
	public function withScheme(string $scheme): UriInterface {
		return $this->internal->withScheme($scheme);
	}

	/**
	 * Return an instance with the specified user information.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified user information.
	 *
	 * Password is optional, but the user information MUST include the
	 * user; an empty string for the user is equivalent to removing user
	 * information.
	 *
	 * @param string      $user     The user name to use for authority.
	 * @param null|string $password The password associated with $user.
	 * @return static A new instance with the specified user information.
	 */
	public function withUserInfo(string $user, string|null $password = null): UriInterface {
		return $this->internal->withUserInfo(user: $user, password: $password);
	}
}
