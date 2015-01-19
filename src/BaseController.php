<?php namespace Inkwell\Controller
{
	use IW;
	use Closure;
	use ArrayAccess;
	use Dotink\Flourish;

	class BaseController implements ArrayAccess
	{
		/**
		 *
		 */
		private $context = array();


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
		public function __prepare($action, $context = array())
		{
			$this->action  = $action;
			$this->context = array_merge($this->context, $context);
		}
	}
}
