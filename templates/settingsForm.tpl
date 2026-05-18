{**
 * templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2024 Simon Fraser University
 * Copyright (c) 2003-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * SWORD plugin settings
 *}

<script type="text/javascript">
	$(function() {ldelim}
		$('#swordSettingsForm').pkpHandler(
			'$.pkp.controllers.form.AjaxFormHandler'
		);

		$('#showDepositButton').click(function() {ldelim}
			const $showDepositButtonPublishedOnly = $('#showDepositButtonPublishedOnly');
			$showDepositButtonPublishedOnly.attr('disabled', !$showDepositButtonPublishedOnly.attr('disabled'));
			if ($showDepositButtonPublishedOnly.attr('disabled')) {ldelim}
				$showDepositButtonPublishedOnly.prop('checked', '');
			{rdelim}
		{rdelim});
	{rdelim});
</script>

<form class="pkp_form" id="swordSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_PAGE op="swordSettings" save=true}">
	{csrf}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="swordSettingsFormNotification"}

	{fbvFormArea id="swordSettings"}
		{fbvFormSection description="plugins.generic.sword.description" class="notice"}{/fbvFormSection}
		{fbvFormSection for="allowAuthorSpecify" list=true description="plugins.generic.sword.settings"}
			{fbvElement type="checkbox" id="allowAuthorSpecify" value="1" checked=$allowAuthorSpecify label="plugins.generic.sword.settings.allowAuthorSpecify"}
			{fbvElement type="checkbox" id="showDepositButton" value="1" checked=$showDepositButton label="plugins.generic.sword.settings.showDepositButton"}
			{fbvElement type="checkbox" id="showDepositButtonPublishedOnly" value="1" checked=$showDepositButtonPublishedOnly label="plugins.generic.sword.settings.showDepositButtonPublishedOnly" disabled=!$showDepositButton}
			
			{fbvElement type="checkbox" id="sac_identifier" value="1" checked=$sac_identifier label="plugins.generic.sword.settings.sac_identifier"}
			{fbvElement type="checkbox" id="sac_title" value="1" checked=$sac_title label="plugins.generic.sword.settings.sac_title"}
			{fbvElement type="checkbox" id="sac_abstract" value="1" checked=$sac_abstract label="plugins.generic.sword.settings.sac_abstract"}
			{fbvElement type="checkbox" id="sac_type" value="1" checked=$sac_type label="plugins.generic.sword.settings.sac_type"}
			{fbvElement type="checkbox" id="sac_custodian" value="1" checked=$sac_custodian label="plugins.generic.sword.settings.sac_custodian"}
			{fbvElement type="checkbox" id="sac_identifier" value="1" checked=$sac_identifier label="plugins.generic.sword.settings.sac_identifier"}
			{fbvElement type="checkbox" id="sac_dateavailable" value="1" checked=$sac_dateavailable label="plugins.generic.sword.settings.sac_dateavailable"}
			{fbvElement type="checkbox" id="sac_language" value="1" checked=$sac_language label="plugins.generic.sword.settings.sac_language"}
			{fbvElement type="checkbox" id="sac_publisher" value="1" checked=$sac_publisher label="plugins.generic.sword.settings.sac_publisher"}
			{fbvElement type="checkbox" id="sac_subjects" value="1" checked=$sac_subjects label="plugins.generic.sword.settings.sac_subjects"}
			{fbvElement type="checkbox" id="sac_rights" value="1" checked=$sac_rights label="plugins.generic.sword.settings.sac_rights"}
			{fbvElement type="checkbox" id="sac_copyrightholder" value="1" checked=$sac_copyrightholder label="plugins.generic.sword.settings.sac_copyrightholder"}
			{fbvElement type="checkbox" id="sac_citation" value="1" checked=$sac_citation label="plugins.generic.sword.settings.sac_citation"}
			{fbvElement type="checkbox" id="sac_statusstatement" value="1" checked=$sac_statusstatement label="plugins.generic.sword.settings.sac_statusstatement"}

		{/fbvFormSection}
	{/fbvFormArea}

	{capture assign="swordDepositPointsGridUrl"}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.sword.controllers.grid.SwordDepositPointsGridHandler" op="fetchGrid" escape=false}{/capture}
	{load_url_in_div id="swordDepositPointsGridContainer" url=$swordDepositPointsGridUrl}

	{fbvFormButtons id="swordSettingsFormSubmit" submitText="common.save" hideCancel=true}
</form>
