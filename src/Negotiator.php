<?php namespace Inkwell\Controller
{
	trait Negotiator
	{
		/**
		 *
		 */
		private $languageNegotiator = NULL;


		/**
		 *
		 */
		private $mimeTypeNegotiator = NULL;


		/**
		 *
		 */
		public function setLanguageNegotiator($negotiator)
		{
			$this->languageNegotiator = $negotiator;
		}


		/**
		 *
		 */
		public function setMimeTypeNegotiator($negotiator)
		{
			$this->mimeTypeNegotiator = $negotiator;
		}

	}
}
