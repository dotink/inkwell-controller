<?php namespace Inkwell\Controller
{
	use IW\HTTP;
	use Closure;
	use ArrayAccess;
	use Dotink\Flourish;

	class BaseController implements ArrayAccess, NegotiatorConsumerInterface
	{
		use NegotiatorConsumer;

		/**
		 * The controller action associated with the current context.
		 *
		 * The action represents a method that will be called when the controller is invoked.
		 *
		 * @access protected
		 * @var string
		 */
		protected $action = NULL;


		/**
		 * The format decided from accept negotiation
		 *
		 * @access protected
		 * @var string
		 */
		protected $format = NULL;


		/**
		 * The current controller context.
		 *
		 * @access private
		 * @var array
		 */
		private $context = array();


		/**
		 * Get a variable from the context
		 *
		 * @return mixed The value of the property
		 */
		public function __get($property)
		{
			return $this[$property];
		}


		/**
		 *
		 */
		public function __invoke()
		{
			$action = $this->action;

			return !($action instanceof Closure)
				? $this->$action()
				: $action();
		}


		/**
		 *
		 */
		public function __prepare($action, $context = array())
		{
			$this->action  = $action;
			$this->context = array_merge($this->context, $context);
		}


		/**
		 *
		 */
		public function __set($property, $value)
		{
			return $this[$property] = $value;
		}


		/**
		 * Sets a context element via array access (NOT ALLOWED)
		 *
		 * @access public
		 * @param mixed $offset The context element offset to set
		 * @param mixed $value The value to set for the offset
		 * @return void
		 */
		public function offsetSet($offset, $value)
		{
			$this->context[$offset] = $value;
		}


		/**
		 * Checks whether or not a context element exists
		 *
		 * @access public
		 * @param mixed $offset The context element offset to check for existence
		 * @return boolean TRUE if the context exists, FALSE otherwise
		 */
		public function offsetExists($offset)
		{
			return isset($this->context[$offset]);
		}


		/**
		 * Attempts to unset a context element
		 *
		 * @access public
		 * @param mixed $offset The context element offset to unset
		 * @return void
		 */
		public function offsetUnset($offset)
		{
			if ($this->offsetExists($offset)) {
				unset($this->context[$offset]);
			}
		}


		/**
		 * Gets a context element
		 *
		 * @access public
		 * @param mixed $offset The context element offset to get
		 * @return mixed The value of the offset
		 */
		public function offsetGet($offset)
		{
			if (!$this->offsetExists($offset)) {
				throw new Flourish\ProgrammerException(
					'Provider "%s" not set on parent %s',
					$offset,
					__CLASS__
				);
			}

			return $this->context[$offset];
		}


		/**
		 *
		 */
		protected function acceptLanguages($languages)
		{
			settype($languages, 'array');

			$accept = $this->languageNegotiator->getBest(
				$this->request->headers->get('Accept-Language'),
				$languages
			);

			if (!$accept) {
				$this->response->set(NULL);
				$this->response->setStatus(HTTP\NOT_ACCEPTABLE);
				$this->router->demit();
			}

			return $accept->getValue();
		}


		/**
		 *
		 */
		protected function acceptMimeTypes($mimetypes)
		{
			settype($mimetypes, 'array');

			$accept = $this->mimeTypeNegotiator->getBest(
				$this->request->headers->get('Accept'),
				$mimetypes
			);

			if (!$accept) {
				$this->response->set(NULL);
				$this->response->setStatus(HTTP\UNSUPPORTED_MIMETYPE);
				$this->router->demit();
			}

			$mime_type    = $accept->getValue();
			$this->format = $this->mimeTypeNegotiator->getFormat($accept->getValue());

			return $mime_type;
		}


		/**
		 *
		 */
		protected function authorizeMethod($allowed_methods)
		{
			$method = $this->request->getMethod();

			settype($allowed_methods, 'array');

			if (!in_array($method, $allowed_methods)) {
				$this->response->set(NULL);
				$this->response->setStatus(HTTP\NOT_ALLOWED);
				$this->response->headers->set('Allow', implode(', ', $allowed_methods));
				$this->router->demit();
			}

			return $method;
		}
	}
}
