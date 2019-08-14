<?php
/**
 * File Oauth.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Oauth
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Helper_Oauth extends Mage_Core_Helper_Abstract
{

	const XML_PATH_REGISTERED_USER		= 'ssmanager/ssmanager_api/magento_api_admin_user';
	const XML_PATH_REGISTERED_USER_ROLE	= 'ssmanager/ssmanager_api/magento_api_admin_user_role';
	const XML_PATH_REGISTERED_CONSUMER	= 'ssmanager/ssmanager_api/magento_api_oauth_consumer';

	protected $_adminUser;
	protected $_adminUserRole;
	protected $_consumer;

	public function hlp() {
		return Mage::helper('searchspring_manager');
	}

	public function isSupported() {
		// The oauth functionality contained within this class (and
		// module as a whole really) depends on the OAuth and Api2
		// Modules. While these would mostly only exist together,
		// it's safest to verify that both are enabled.
		return (
			$this->hlp()->isModuleEnabled('Mage_Oauth') &&
			$this->hlp()->isModuleEnabled('Mage_Api2')
		);
	}

	public function getAdminUser() {
		if (is_null($this->_adminUser)) {
			$model = Mage::getModel('admin/user')->load($this->getAdminUserId());
			if (!$model->getId()) $model = false;
			$this->_adminUser = $model;
		}

		return $this->_adminUser;
	}

	public function getAdminUserRole() {
		if (is_null($this->_adminUserRole)) {
			$this->hlp()->ensureModelExists('api2/acl_global_role');
			$model = Mage::getModel('api2/acl_global_role')->load($this->getAdminUserRoleId());
			if (!$model->getId()) $model = false;
			$this->_adminUserRole = $model;
		}

		return $this->_adminUserRole;
	}

	public function getConsumer() {
		if (is_null($this->_consumer)) {
			$this->hlp()->ensureModelExists('oauth/consumer');
			$model = Mage::getModel('oauth/consumer')->load($this->getConsumerId());
			if (!$model->getId()) $model = false;
			$this->_consumer = $model;
		}

		return $this->_consumer;
	}

	public function ensureOAuthResourcesInitialized() {

		return $this->_initializeOAuthResources();

	}

	public function getAdminUserId() {
		return Mage::getStoreConfig(self::XML_PATH_REGISTERED_USER);
	}

	public function getAdminUserRoleId() {
		return Mage::getStoreConfig(self::XML_PATH_REGISTERED_USER_ROLE);
	}

	public function getConsumerId() {
		return Mage::getStoreConfig(self::XML_PATH_REGISTERED_CONSUMER);
	}

	protected function _setAdminUser($model) {
		$this->hlp()->writeStoreConfig(self::XML_PATH_REGISTERED_USER, $model->getId());
		return $this->_adminUser = $model;
	}

	protected function _setAdminUserRole($model) {
		$this->hlp()->writeStoreConfig(self::XML_PATH_REGISTERED_USER_ROLE, $model->getId());
		return $this->_adminUserRole = $model;
	}

	protected function _setConsumer($model) {
		$this->hlp()->writeStoreConfig(self::XML_PATH_REGISTERED_CONSUMER, $model->getId());
		return $this->_consumer = $model;
	}

	protected function _initializeOAuthResources() {

		// Ensure Admin User exists
		$user = $this->_ensureAdminUser();

		// Ensure REST role exists
		$this->_ensureRole($user->getId());

		// Ensure oAuth Consumer exists
		$this->_ensureConsumer();

	}	

	protected function _ensureAdminUser() {
		if ($user = $this->getAdminUser()) return $user;
		$user = $this->_createAdminUser();
		return $this->_setAdminUser($user);
	}

	protected function _ensureRole($userId) {
		if ($role = $this->getAdminUserRole()) return $role;
		$role = $this->_createRole($userId);
		return $this->_setAdminUserRole($role);
	}

	protected function _ensureConsumer() {
		if ($consumer = $this->getConsumer()) return $consumer;
		$consumer = $this->_createConsumer();
		return $this->_setConsumer($consumer);
	}

	protected function _createAdminUser() {
		$ohlp = Mage::helper('oauth');
		$email = Mage::getConfig()->getNode('global/searchspring/api_auth/admin_user/email');

		$data = array(
			'username' => Mage::getConfig()->getNode('global/searchspring/api_auth/admin_user/username'),
			'firstname' => Mage::getConfig()->getNode('global/searchspring/api_auth/admin_user/firstname'),
			'lastname' => Mage::getConfig()->getNode('global/searchspring/api_auth/admin_user/lastname'),
			'email' => $email,
			'password' => $ohlp->generateConsumerKey(), // Something random for security
			'is_active' => '1',
		);

		// First Check for user with email, remove it
		$this->_removeAdminUserWithEmail($email);

		// Create a brand new user with data
		$user = Mage::getModel('admin/user');
		$user->setData($data);

		return $user->save();
	}

	protected function _createRole($userId) {
		$name = Mage::getConfig()->getNode('global/searchspring/api_auth/role/label');

		$this->hlp()->ensureModelExists('api2/acl_global_role');

		$role = Mage::getModel('api2/acl_global_role');
		$role->setRoleName($name);
		$role->save();

		$this->_addUserToRole($userId, $role->getId());

		// TODO -- pull resource id and privilege keys from api2 config...
		$resourceId = 'searchspring';
		$privileges = array(
			'create',
			'retrieve',
			'update',
			'delete',
		);
		$rule = Mage::getModel('api2/acl_global_rule');
		foreach($privileges as $privilege) {
			$rule->setId(null)->isObjectNew(true);
			$rule->setRoleId($role->getId())
				->setResourceId($resourceId)
				->setPrivilege($privilege)
				->save();
		}

		return $role;
	}

	protected function _addUserToRole($userId, $roleId) {
		$resourceModel = Mage::getResourceModel('api2/acl_global_role');
		$resourceModel->saveAdminToRoleRelation($userId, $roleId);
	}

	protected function _createConsumer() {
		$ohlp = Mage::helper('oauth');

		$consumerName = Mage::getConfig()->getNode('global/searchspring/api_auth/consumer/label');
		$consumerKey = $ohlp->generateConsumerKey();
		$consumerSecret = $ohlp->generateConsumerSecret();

		$consumer = Mage::getModel('oauth/consumer');
		$consumer->setName($consumerName);
		$consumer->setKey($consumerKey);
		$consumer->setSecret($consumerSecret);

		return $consumer->save();
	}

	protected function _removeAdminUserWithEmail($email) {
		$collection = Mage::getModel('admin/user')->getCollection();
		$collection->addFieldToFilter('email', $email);

		// If admin user exists, delete it
		$user = $collection->getFirstItem();
		if ($user->getId()) {
			$user->delete();
		}
	}

}
