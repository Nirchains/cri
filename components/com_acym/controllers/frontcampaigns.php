<?php

namespace AcyMailing\FrontControllers;

use AcyMailing\Controllers\CampaignsController;

class FrontcampaignsController extends CampaignsController
{
    public function __construct()
    {
        if (!acym_level(2)) {
            acym_redirect(acym_rootURI(), 'ACYM_ONLY_AVAILABLE_ENTERPRISE_VERSION', 'warning');
        }

        $this->loadScripts = [
            'edit' => ['vue-applications' => ['entity_select'], 'editor-wysid'],
            'all' => ['introjs'],
        ];
        $this->authorizedFrontTasks = ['saveAsDraftCampaign', 'addQueue', 'save', 'edit', 'newEmail', 'campaigns', 'welcome', 'unsubscribe', 'countNumberOfRecipients', 'editEmail', 'saveAjax', 'confirmCampaign', 'stopScheduled'];
        $this->urlFrontMenu = 'index.php?option=com_acym&view=frontcampaigns&layout=listing';
        parent::__construct();
    }

    protected function setFrontEndParamsForTemplateChoose()
    {
        return acym_currentUserId();
    }
}

