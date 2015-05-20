<?php

namespace Anax\Sparkles;

/**
* Class for flashing messages in your Anax
*
*/
class CSparkles implements \Anax\DI\IInjectionAware {

	use \Anax\DI\TInjectable;

	// Properties for CSS classes
	public $error;
	public $success;
	public $notice;

	/**
	* Instanstiate custom CSS classes. Using default classes if no parameter is passed.
	*
	* @param array $options with custom CSS classes.
	*/
	public function __construct($options = array()) {
		// Set CSS classes
		$this->error = isset($options['error']) ? $options['error'] : 'error';
		$this->success = isset($options['success']) ? $options['success'] : 'success';
		$this->notice = isset($options['notice']) ? $options['notice'] : 'notice';
	}


	/**
	* Store message to session.
	*
	* @param string $message with message to store.
	* @property object $session
	* @return void
	*/
	protected function _setSession($message) {
		$messages = $this->session->get('flash', []);
		$messages['message'][] = $message;
        $this->session->set('flash', $messages);
	}


	/**
	* Get message(s) from session.
	*
	* @property object $session
	* @return string $message
	*/
	protected function _getSession() {
		$message = $this->session->get('flash', []);

		return $message;
	}


	/**
	* Removes all messages from session.
	*
	* @property object $session
	* @return void
	*/
	protected function _clean() {
		$this->session->set('flash', []);
	}


	/**
	* Edit massage with type and send it to session.
	*
	* @param string $type with the flash type.
	* @param string $message with message.
	* @return void
	*/
	public function flash($type, $message) {
		// Build HTML
		$flash = "<span class='" . $this->{$type} . "'><p>" . $message . "</p></span>";

		$this->_setSession($flash);
	}


	/**
	* Get stored messages, clean the session and output the messages.
	*
	* @return object $flash with all messages.
	*/
	public function output() {
		$flash = $this->_getSession();
		$this->_clean();

		if (isset($flash['message'])) {
			return $flash['message'];
		}
	}
}