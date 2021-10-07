<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.restrictRegistration
 *
 * @copyright   Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Load language file for plugin.
$lang = JFactory::getLanguage();
$lang->load('plg_user_restrictregistration', JPATH_ADMINISTRATOR);

/**
 * An example custom user restrict plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgUserRestrictRegistration extends CMSPlugin
{
	/**
    * Load the language file on instantiation.
    *
    * @var    boolean
    * @since  __DEPLOY_VERSION__
    */
    protected $autoloadLanguage = true;

	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  __DEPLOY_VERSION__
	 */
	protected $app;

	/**
	 * Method is called before user data is stored in the database
	 *
	 * @param   array    $user   Holds the old user data.
	 * @param   boolean  $isNew  True if a new user is stored.
	 * @param   array    $data   Holds the new user data.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  InvalidArgumentException on missing required data.
	 */
	public function onUserBeforeSave($user, $isNew, $data)
	{
		$userId = ArrayHelper::getValue($user, 'id', 0, 'int');

		// User already registered, no need to check it further
		if ($userId > 0)
		{
			return true;
		}

		JLoader::import('components.com_users.models.users', JPATH_ADMINISTRATOR);
		$usersModel = BaseDatabaseModel::getInstance('Users', 'UsersModel', array('ignore_request' => true));

		$userCount = $usersModel->getTotal();

		$restrictCount = $this->params->get('userCount');

		if ($isNew && !empty($restrictCount))
		{
			if ($userCount >= $restrictCount)
			{
				throw new InvalidArgumentException(Text::_('PLG_USER_RESTRICT_REGISTRATION_MSG'));

				return false;
			}
		}

		return true;
	}
}