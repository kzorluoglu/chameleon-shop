<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

class TPkgShopPaymentHandlerMapper_PayPalExpressBasket extends AbstractPkgShopPaymentHandlerMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        parent::GetRequirements($oRequirements);
        $oRequirements->NeedsSourceObject('sSpotName');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oPaymentMethod = TdbShopPaymentMethod::GetNewInstance();
        if ($oPaymentMethod->LoadFromFields(array('name_internal' => 'paypal-express', 'active' => '1'))) {
            if (false === \TdbShopShippingGroupList::GetShippingGroupsThatAllowPaymentWith('paypal-express')) {
                $oVisitor->SetMappedValue('sPayPalExpressLink', false);

                return;
            }

            $sSpotName = $oVisitor->GetSourceObject('sSpotName');
            $activePageService = $this->getActivePageService();
            $aURLData = array('module_fnc' => array($sSpotName => 'JumpSelectPaymentMethod'), 'sPaymentMethodNameInternal' => 'paypal-express');
            $oVisitor->SetMappedValue('sPayPalExpressLink', str_replace('&amp;', '&', $activePageService->getLinkToActivePageRelative($aURLData)));
        }
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
