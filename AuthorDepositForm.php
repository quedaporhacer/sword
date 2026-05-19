<?php

/**
* @file AuthorDepositForm.php
*
* Copyright (c) 2003-2024 Simon Fraser University
* Copyright (c) 2003-2024 John Willinsky
* Distributed under the GNU GPL v3. For full terms see the file LICENSE.
*
* @class AuthorDepositForm
* @brief Form to perform an author's SWORD deposit(s)
*/

namespace APP\plugins\generic\sword;

use PKP\form\Form;

use APP\plugins\generic\sword\classes\DepositPoint;
use APP\plugins\generic\sword\classes\DepositPointsHelper;
use APP\plugins\generic\sword\classes\PKPSwordDeposit;
use APP\submission\Submission;
use APP\template\TemplateManager;
use PKP\context\Context;
use PKP\db\DAORegistry;
use GuzzleHttp\Client;

class AuthorDepositForm extends Form {
	/** @var Context $_context */
	protected $_context = null;

	/** @var SwordPlugin $_plugin */
	protected $_plugin = null;

	/** @var Submission $_submission */
	protected $_submission = null;


	public function __construct(SwordPlugin $plugin, Context $context, Submission $submission) {
		$this->_plugin = $plugin;
		$this->_context = $context;
		$this->_submission = $submission;
		parent::__construct($plugin->getTemplateResource('authorDepositForm.tpl'));
	}

	/**
	 * Get reference to the sword plugin
	 * @return SwordPlugin
	 */
	public function getSwordPlugin() {
		return $this->_plugin;
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	public function readInputData() {
		$this->readUserVars([
			'authorDepositUrl',
			'authorDepositUsername',
			'authorDepositPassword',
			'depositPoint',
		]);
	}

	/**
	 * @copydoc Form::display()
	 */
	public function display($request = null, $template = null) {
		$templateMgr = TemplateManager::getManager($request);
		$depositPoints = $this->_getDepositableDepositPoints($this->_context);
		$templateMgr->assign([
			'depositPoints' 	=> $depositPoints,
			'submission'		=> $this->_submission,
			'allowAuthorSpecify' 	=> $this->getSwordPlugin()->getSetting($this->_context->getId(), 'allowAuthorSpecify'),
			'pluginJavaScriptURL' 	=> $this->_plugin->getJsUrl($request),
		]);
		parent::display($request, $template);
	}

	/**
	 * Save form.
	 * @param $request PKPRequest
	 * @return array Set of SWORDAPPEntry responses
	 */
	public function execute(...$functionArgs) {
		parent::execute(...$functionArgs);
		$request = $functionArgs[0];


		$deposit = new PKPSwordDeposit($this->_submission);
		$deposit->setMetadata($request, $this->getSwordPlugin());

		$deposit->addEditorial();
		$deposit->createPackage();

		$responses = [];

		$allowAuthorSpecify = $this->getSwordPlugin()->getSetting($this->_context->getId(), 'allowAuthorSpecify');
		$authorDepositUrl = $this->getData('authorDepositUrl');
		if (($allowAuthorSpecify) && ($authorDepositUrl != '')) {
			$responses[$this->getData('authorDepositUrl')] = $deposit->deposit(
				$this->getData('authorDepositUrl'),
				$this->getData('authorDepositUsername'),
				$this->getData('authorDepositPassword')
			);
			$deposit->cleanup();
		}

		$url = '';
		$depositPoints = $this->getData('depositPoint');
		$depositableDepositPoints = $this->_getDepositableDepositPoints($this->_context);
		foreach ($depositableDepositPoints as $key => $depositPoint) {
			if (!isset($depositPoints[$key]['enabled']))
				continue;
			if ($depositPoint['type'] == SWORD_DEPOSIT_TYPE_OPTIONAL_SELECTION) {
				$url = $depositPoints[$key]['depositPoint'];
			} else { // SWORD_DEPOSIT_TYPE_OPTIONAL_FIXED
				$url = $depositPoint['url'];
			}
			$responses[$depositPoint['url']] = $deposit->deposit(
				$url,
				$depositPoint['username'] ?: $depositPoints[$key]['username'],
				$depositPoint['password'] ?: $depositPoints[$key]['password'],
				$depositPoint['apikey']
			);
			$deposit->cleanup();
		}
		return $responses;
	}

	/**
	 * Build a list of collections available for deposit points of type SWORD_DEPOSIT_TYPE_OPTIONAL_SELECTION
	 * @param $context Context
	 * @return array
	 */
	protected function _getDepositableDepositPoints($context) {
		$list = [];
		/** @var DepositPointDAO $depositPointDao */
		$depositPointDao = DAORegistry::getDAO('DepositPointDAO');
		$depositPoints = $depositPointDao->getByContextId($context->getId());
		foreach ($depositPoints as $depositPoint) {
			if (!in_array($depositPoint->getType(), [SWORD_DEPOSIT_TYPE_OPTIONAL_SELECTION, SWORD_DEPOSIT_TYPE_OPTIONAL_FIXED]))
				continue;

			$list[$depositPoint->getId()] = [
				'name' => $depositPoint->getLocalizedName(),
				'description' => $depositPoint->getLocalizedDescription(),
				'url' => $depositPoint->getSwordUrl(),
				'type' => $depositPoint->getType(),
				'username' => $depositPoint->getSwordUsername(),
				'password' => $depositPoint->getSwordPassword(),
				'apikey' => $depositPoint->getSwordApikey(),
			];
			if ($depositPoint->getType() == SWORD_DEPOSIT_TYPE_OPTIONAL_SELECTION) {
				$collections = DepositPointsHelper::loadCollectionsFromServer(
					$depositPoint->getSwordUrl(),
					$depositPoint->getSwordUsername(),
					$depositPoint->getSwordPassword(),
					$depositPoint->getSwordApikey()
				);

				$enrichedCollections = [];
               // Dentro de tu método, reemplaza la línea del error por:
				$httpClient = new \GuzzleHttp\Client([
					'allow_redirects' => true, // Crucial para que siga el 302 del endpoint /pid/find
					'timeout'         => 5.0,  // Evita que OJS se quede colgado si DSpace no responde rápido
				]);
                // Definir la URL base de la API REST de tu DSpace 9 (ej: de una config o hardcodeada para probar)
                $baseUrlDSpaceRest = "https://staging.sedici.unlp.edu.ar/server/api";

				foreach ($collections as $url => $title) {
                    // 1. Extraer el handle de la URL de SWORD (ej: de '.../swordv2/collection/10915/49140' sacás '10915/49140')
                    $handle = $this->_extractHandleFromSwordUrl($url); 
                    
                    $communityName = 'Sin Comunidad Asociada'; // Valor por defecto

                    if ($handle) {
                        try {
                            // 2. Traducir Handle -> UUID usando el endpoint PID de DSpace 9
                            $pidUrl = $baseUrlDSpaceRest . "/pid/find?id=" . urlencode($handle);
                            $pidResponse = $httpClient->get($pidUrl);

                            if ($pidResponse->getStatusCode() === 200) {
                                $pidData = json_decode($pidResponse->getBody(), true);
                                $collectionUuid = $pidData['uuid'] ?? null;

                                // 3. Si encontramos el UUID, le pedimos la Comunidad Madre a la API REST
                                if ($collectionUuid) {
                                    $communityUrl = $baseUrlDSpaceRest . "/core/collections/" . $collectionUuid . "/parentCommunity";
                                    $commResponse = $httpClient->get($communityUrl);

                                    if ($commResponse->getStatusCode() === 200) {
                                        $commData = json_decode($commResponse->getBody(), true);
                                        // DSpace 9 expone el nombre directamente en la propiedad 'name'
                                        $communityName = $commData['name'] ?? 'Comunidad sin título';
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Si cae el DSpace o la API cambia, logueamos pero NO rompemos la pantalla de OJS
                            error_log("Error en integración REST DSpace 9 para el handle {$handle}: " . $e->getMessage());
                        }
                    }

                    // 4. Enriquecemos el nodo de la colección inyectando el nombre de la comunidad
                    $enrichedCollections[$url] = "[" . $communityName . "] " . $title;
                }

				$list[$depositPoint->getId()]['depositPoints'] = $enrichedCollections;
			}



		}
		return $list;
	}

	private function _extractHandleFromSwordUrl($url) {
		// Busca patrones tipo '123456789/123' al final de la URL de SWORD
		if (preg_match('/collection\/([^\/]+\/[^\/]+)$/', $url, $matches)) {
			return $matches[1];
		}
		return null;
	}
}
