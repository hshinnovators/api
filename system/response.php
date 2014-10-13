<?php if (!defined("PROJECT")) exit("Direct script access is forbidden.");
/**
 * @category: API
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 */
final class Response
{
	/**
	 * The body to be sent.
	 * @var string
	 */
	protected $body;
	/**
	 * The headers to be sent.
	 * @var array
	 */
	protected $headers = array();
	/**
	 * The original request.
	 * @var Request|null
	 */
	private $request;
	/**
	 * The status code to be sent.
	 * @var int
	 */
	protected $status;

	/**
	 * Build a new Response, based off the incoming request.
	 *
	 * @param Request|null $request
	 */
	public function __construct(Request &$request = null)
	{
		if (!empty($request))
		{
			// This would be the part where you set loads of headers and content types based on what the request sent in.
			$this->request = $request;
		}
	}

	/**
	 * Getter & setter for the body.
	 *
	 * @param mixed|null $body
	 * @return void|string
	 */
	public function body($body = null)
	{
		if (func_num_args() > 0)
		{
			$this->body = (string) $body;
			return $this;
		}
		else
		{
			return $this->body;
		}
	}

	/**
	 * Getter and setter for individual headers.
	 *
	 * @param string $key
	 * @param string|null $value
	 * @return Response|string
	 */
	public function header($key, $value = null)
	{
		if (func_num_args() > 1)
		{
			if ($value === null)
			{
				unset($this->headers[$key]);
			}
			else
			{
				$this->headers[$key] = (string) $value;
			}
			return $this;
		}
		else
		{
			return $this->headers[$key];
		}
	}

	/**
	 * Getter and setter for all of the headers.
	 *
	 * @param array|null $headers
	 * @return Response|array
	 */
	public function headers(array $headers = array())
	{
		if (func_num_args() > 0)
		{
			$this->headers = array_merge($this->headers, $headers);
			return $this;
		}
		else
		{
			return $this->headers;
		}
	}

	/**
	 * Send the request!
	 * @return void
	 */
	public function send()
	{
		header(sprintf("HTTP/1.1 %d %s", $this->status, getHttpStatusForCode($this->status)));
		foreach($this->headers() as $header => $value)
			header("{$header}: {$value}");
		echo (string) $this->body;
	}

	/**
	 * Getter & setter for the status.
	 *
	 * @param int|null $status
	 * @return Response|int
	 */
	public function status($status = null)
	{
		if (func_num_args() > 0)
		{
			$this->status = (int) $status;
			return $this;
		}
		else
		{
			return $this->status;
		}
	}
}