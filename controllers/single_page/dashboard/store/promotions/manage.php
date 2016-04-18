<?php

namespace Concrete\Package\VividStore\Controller\SinglePage\Dashboard\Store\Promotions;

use \Concrete\Core\Page\Controller\DashboardPageController;

use \Concrete\Package\VividStore\Src\VividStore\Promotion\PromotionRewardType as StorePromotionRewardType;
use \Concrete\Package\VividStore\Src\VividStore\Promotion\PromotionRuleType as StorePromotionRuleType;

class Manage extends DashboardPageController
{

    public function view($promotionID=null)
    {
        $this->requireAsset('css', 'vividStoreDashboard');
        $this->requireAsset('javascript', 'vividStoreFunctions');
        $this->set('rewardTypes',StorePromotionRewardType::getPromotionRewardTypes());
        $this->set('ruleTypes',StorePromotionRuleType::getPromotionRuleTypes());
        $js = \Concrete\Package\VividStore\Controller::returnHeaderJS();
        $this->addFooterItem($js);
    }

}
