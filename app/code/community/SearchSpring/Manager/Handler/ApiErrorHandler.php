<?php
/**
 * File ApiErrorHandler.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Handler_ApiErrorHandler
 *
 * @author Nate Brunette <nate@b7interactive.com>
 */
class SearchSpring_Manager_Handler_ApiErrorHandler
{
	/**
	 * The amount of times we'll try to update before giving up
	 */
	const RETRY_LIMIT = 3;

	/**
	 * Email to send API failures
	 */
	const FAILURE_EMAIL = 'dev-null+alert-email@b7interactive.com';

	/**
	 * The number of times we've tried to update SearchSpring
	 *
	 * @var int
	 */
	private $attempts = 0;

	/**
	 * Check if we should continue trying based on the response code and number of attempts
	 *
	 * @param string $response
	 *
	 * @return bool
	 */
	public function shouldRetry($response)
	{
		$responseCode = Zend_Http_Response::extractCode($response);

		if (200 === $responseCode) {
			return false;
		}

		++$this->attempts;

		if ($this->attempts < self::RETRY_LIMIT) {
			return true;
		}

		$this->notifyUser();
		$this->email($response);

		return false;
	}

	/**
	 * Put a message in the flashbag that there was an issue
	 */
	private function notifyUser()
	{
		if(Mage::app()->getStore()->isAdmin()) {
			/** @var Mage_Core_Model_Session $session */
			$session = Mage::getSingleton('core/session');
			$session->addNotice('There was a problem while trying to update SearchSpring.  This issue will be investigated.');
		}
	}

	/**
	 * Send an email that something failed
	 *
	 * @param string $response
	 *
	 * @return bool
	 */
	private function email($response)
	{
		$templateVars = array('response' => $response);

		/** @var Mage_Core_Model_Email_Template $template */
		$template = Mage::getModel('core/email_template');
		$emailTemplate = $template->loadDefault('api_error');
		$emailTemplate->setData('sender_name', 'System')->setData('sender_email', 'no-reply@b7interactive.com');
		$emailTemplate->setTemplateType('html')->setTemplateSubject('SearchSpring Magento Live Indexing Error');

		$emailTemplate->send(self::FAILURE_EMAIL, 'SearchSpring', $templateVars);

		return true;
	}
}
