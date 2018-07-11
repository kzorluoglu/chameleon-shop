<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ShopBundle\objects\TCMSListManager;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use Doctrine\DBAL\Connection;
use TCMSListManagerFullGroupTable;
use ViewRenderer;

class TCMSListManagerShopArticles extends TCMSListManagerFullGroupTable
{
    /**
     * {@inheritdoc}
     */
    protected function PostCreateTableObjectHook()
    {
        parent::PostCreateTableObjectHook();

        $filterSection = "<div class=\"form-group\">\n";

        $isActive = $this->tableObj->getCustomSearchFieldParameter('is_active');

        if (null === $isActive) {
            $isActive = '';
        }

        $filterArticleType = $this->tableObj->getCustomSearchFieldParameter('filterArticleType');

        if (null === $filterArticleType) {
            $filterArticleType = 'all';
        }

        $request = $this->getRequest();
        if (true === $request->query->has('filterArticleType')) {
            $filterArticleType = $request->query->get('filterArticleType');
        }

        $filterSection .= $this->getFilterFieldArticleType($filterArticleType);
        $filterSection .= $this->getFilterFieldActive($isActive);
        $filterSection .= "\n</div>\n";

        $this->tableObj->searchBoxContent = $filterSection;
        $this->tableObj->aHiddenFieldIgnoreList = array('filterArticleType', 'is_active');

        $customSearchFieldParams = array('filterArticleType' => $filterArticleType, 'is_active' => $isActive);
        $this->tableObj->AddCustomSearchFieldParameter($customSearchFieldParams);
    }

    /**
     * @param string $filterArticleType
     *
     * @return string
     */
    private function getFilterFieldArticleType($filterArticleType)
    {
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sInputClass', 'form-control input-sm');
        $oViewRenderer->AddSourceObject('sName', 'filterArticleType');
        $oViewRenderer->AddSourceObject('sValue', '0');
        $oViewRenderer->AddSourceObject('sLabelText', 'Artikeltyp');
        $oViewRenderer->AddSourceObject('sValue', '1');

        $aValueList = array();
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_type_all'), 'sValue' => 'all');
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_type_main'), 'sValue' => 'parents');
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_type_variants'), 'sValue' => 'variants');

        $oViewRenderer->AddSourceObject('aValueList', $aValueList);
        $oViewRenderer->AddSourceObject('sValue', $filterArticleType);

        return $oViewRenderer->Render('userInput/form/select.html.twig', null, false);
    }

    /**
     * @param string $isActive
     *
     * @return string
     */
    private function getFilterFieldActive($isActive)
    {
        $request = $this->getRequest();

        if ($request->query->has('is_active')) {
            $isActive = $request->query->get('is_active');
        }

        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sInputClass', 'form-control input-sm');
        $oViewRenderer->AddSourceObject('sName', 'is_active');
        $oViewRenderer->AddSourceObject('sLabelText', 'aktiv');
        $oViewRenderer->AddSourceObject('sValue', $isActive);

        $aValueList = array();
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_active_all'), 'sValue' => 'all');
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_active_only_active'), 'sValue' => '1');
        $aValueList[] = array('sName' => $this->getTranslation('chameleon_system_shop.list_manager_article.filter_active_only_inactive'), 'sValue' => '0');

        $oViewRenderer->AddSourceObject('aValueList', $aValueList);

        return $oViewRenderer->Render('userInput/form/select.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        $filterArticleType = '';
        if (isset($this->tableObj->_postData['filterArticleType'])) {
            if ('parents' == $this->tableObj->_postData['filterArticleType']) {
                $filterArticleType = " `shop_article`.`variant_parent_id` = '' ";
            } elseif ('variants' == $this->tableObj->_postData['filterArticleType']) {
                $filterArticleType = " `shop_article`.`variant_parent_id` != '' ";
            }
        }

        if (!empty($filterArticleType)) {
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= $filterArticleType;
        }

        $filterIsActive = '';

        if (isset($this->tableObj->_postData['is_active']) && ('0' === $this->tableObj->_postData['is_active'] || '1' === $this->tableObj->_postData['is_active'])) {
            $isActive = $this->getDBConnection()->quote($this->tableObj->_postData['is_active']);
            $filterIsActive = " `shop_article`.`active` = $isActive";
        }

        if (!empty($filterIsActive)) {
            if (!empty($query)) {
                $query .= ' AND ';
            }
            $query .= $filterIsActive;
        }

        return $query;
    }

    /**
     * @return \Symfony\Component\Translation\IdentityTranslator
     */
    protected function getTranslation($id, $data = array())
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans($id, $data, TranslationConstants::DOMAIN_BACKEND);
    }

    /**
     * @return Connection
     */
    protected function getDBConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
