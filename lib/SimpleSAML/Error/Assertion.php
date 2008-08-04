<?php

/**
 * Class for creating exceptions from assertion failures.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Error_Assertion extends SimpleSAML_Error_Error {


	/**
	 * The assertion which failed, or NULL if only an expression was passed to the
	 * assert-function.
	 */
	private $assertion;


	/**
	 * Constructor for the assertion exception.
	 *
	 * Should only be called from the onAssertion handler.
	 *
	 * @param string|NULL $assertion  The assertion which failed, or NULL if the assert-function was
	 *                                given an expression.
	 */
	public function __construct($assertion = NULL) {
		assert('is_null($assertion) || is_string($assertion)');

		parent::__construct(array('ASSERTFAIL', '%ASSERTION%' => var_export($assertion, TRUE)));

		$this->assertion = $assertion;
	}


	/**
	 * Retrieve backtrace from where the assert-function was called.
	 *
	 * @return array  Array with a backtrace. Each element is a string which identifies a position
	 *                in the source.
	 */
	public function getAssertionBacktrace() {
		return SimpleSAML_Utilities::buildBacktrace($this, 2);
	}


	/**
	 * Retrieve the assertion which failed.
	 *
	 * @return string|NULL  The assertion which failed, or NULL if the assert-function was called with an expression.
	 */
	public function getAssertion() {
		return $this->assertion;
	}


	/**
	 * Install this assertion handler.
	 *
	 * This function will register this assertion handler. If will not enable assertions if they are
	 * disabled.
	 */
	public static function installHandler() {
		assert_options(ASSERT_WARNING,    0);
		assert_options(ASSERT_BAIL,       0);
		assert_options(ASSERT_QUIET_EVAL, 0);
		assert_options(ASSERT_CALLBACK,   array('SimpleSAML_Error_Assertion', 'onAssertion'));
	}


	/**
	 * Handle assertion.
	 *
	 * This function handles an assertion.
	 *
	 * @param string $file  The file assert was called from.
	 * @param int $line  The line assert was called from.
	 * @param mixed $message  The expression which was passed to the assert-function.
	 */
	public static function onAssertion($file, $line, $message) {

		if(!empty($message)) {
			throw new self($message);
		} else {
			throw new self();
		}
	}

}