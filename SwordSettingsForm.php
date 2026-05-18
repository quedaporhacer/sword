<?php

/**
 * @file SwordSettingsForm.php
 *
 * Copyright (c) 2003-2024 Simon Fraser University
 * Copyright (c) 2003-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class SwordSettingsForm
 * @brief Form for SWORD plugin settings
 */

namespace APP\plugins\generic\sword;

use PKP\form\Form;
use PKP\context\Context;

use APP\template\TemplateManager;

use APP\plugins\generic\sword\SwordPlugin;


class SwordSettingsForm extends Form {
	/** @var $_context Context */
	protected $_context = null;

	/** @var $_plugin SwordPlugin */
	protected $_plugin = null;
	
	/**
	 * Constructor
	 * @param $plugin SwordPlugin
	 * @param $context Context
	 */
	public function __construct(SwordPlugin $plugin, Context $context) {
		$this->_plugin = $plugin;
		$this->_context = $context;
		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
	}

	/**
	 * Initialize plugin settings form
	 *
	 * @return void
	 */
	public function initData() {
		$this->setData('allowAuthorSpecify', $this->_plugin->getSetting($this->_context->getId(), 'allowAuthorSpecify'));
		$this->setData('showDepositButton', $this->_plugin->getSetting($this->_context->getId(), 'showDepositButton'));
		$this->setData('showDepositButtonPublishedOnly', $this->_plugin->getSetting($this->_context->getId(), 'showDepositButtonPublishedOnly'));
		$this->setData('sac_identifier', $this->_plugin->getSetting($this->_context->getId(), 'sac_identifier'));
		$this->setData('sac_title', $this->_plugin->getSetting($this->_context->getId(), 'sac_title'));
		$this->setData('sac_abstract', $this->_plugin->getSetting($this->_context->getId(), 'sac_abstract'));
		$this->setData('sac_creators', $this->_plugin->getSetting($this->_context->getId(), 'sac_creators'));
		$this->setData('sac_type', $this->_plugin->getSetting($this->_context->getId(), 'sac_type'));
		$this->setData('sac_custodian', $this->_plugin->getSetting($this->_context->getId(), 'sac_custodian'));
		$this->setData('sac_dateavailable', $this->_plugin->getSetting($this->_context->getId(), 'sac_dateavailable'));
		$this->setData('sac_language', $this->_plugin->getSetting($this->_context->getId(), 'sac_language'));
		$this->setData('sac_publisher', $this->_plugin->getSetting($this->_context->getId(), 'sac_publisher'));
		$this->setData('sac_subjects', $this->_plugin->getSetting($this->_context->getId(), 'sac_subjects'));
		$this->setData('sac_rights', $this->_plugin->getSetting($this->_context->getId(), 'sac_rights'));
		$this->setData('sac_copyrightholder', $this->_plugin->getSetting($this->_context->getId(), 'sac_copyrightholder'));
		$this->setData('sac_citation', $this->_plugin->getSetting($this->_context->getId(), 'sac_citation'));
		$this->setData('sac_statusstatement', $this->_plugin->getSetting($this->_context->getId(), 'sac_statusstatement'));
	}

	/**
	 * Assign form data to user-submitted data
	 *
	 * @return void
	 */
	public function readInputData() {
		$this->readUserVars(['allowAuthorSpecify', 'showDepositButton', 'showDepositButtonPublishedOnly','sac_identifier','sac_title','sac_abstract','sac_creators','sac_type','sac_custodian','sac_dateavailable','sac_language','sac_publisher','sac_subjects','sac_rights','sac_copyrightholder','sac_citation','sac_statusstatement']);
	}

	/**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginJavaScriptURL', $this->_plugin->getJsUrl($request));
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save form.
	 */
	public function execute(...$functionArgs) {
		$allowAuthorSpecify = intval($this->getData('allowAuthorSpecify'));
		$this->_plugin->updateSetting($this->_context->getId(), 'allowAuthorSpecify', $allowAuthorSpecify);

		$showDepositButton = intval($this->getData('showDepositButton'));
		$this->_plugin->updateSetting($this->_context->getId(), 'showDepositButton', $showDepositButton);

		$showDepositButtonPublishedOnly = intval($this->getData('showDepositButtonPublishedOnly'));
		$this->_plugin->updateSetting($this->_context->getId(), 'showDepositButtonPublishedOnly', $showDepositButtonPublishedOnly);

		$sac_identifier = intval($this->getData('sac_identifier'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_identifier', $sac_identifier);

		$sac_title = intval($this->getData('sac_title'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_title', $sac_title);	

		$sac_abstract = intval($this->getData('sac_abstract'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_abstract', $sac_abstract);

		$sac_creators = intval($this->getData('sac_creators'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_creators', $sac_creators);

		$sac_type = intval($this->getData('sac_type'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_type', $sac_type);

		$sac_custodian = intval($this->getData('sac_custodian'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_custodian', $sac_custodian);

		$sac_dateavailable = intval($this->getData('sac_dateavailable'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_dateavailable', $sac_dateavailable);

		$sac_language = intval($this->getData('sac_language'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_language', $sac_language);

		$sac_publisher = intval($this->getData('sac_publisher'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_publisher', $sac_publisher);

		$sac_subjects = intval($this->getData('sac_subjects'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_subjects', $sac_subjects);

		$sac_rights = intval($this->getData('sac_rights'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_rights', $sac_rights);

		$sac_copyrightholder = intval($this->getData('sac_copyrightholder'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_copyrightholder', $sac_copyrightholder);

		$sac_citation = intval($this->getData('sac_citation'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_citation', $sac_citation);

		$sac_statusstatement = intval($this->getData('sac_statusstatement'));
		$this->_plugin->updateSetting($this->_context->getId(), 'sac_statusstatement', $sac_statusstatement);

		parent::execute(...$functionArgs);
	}
}
