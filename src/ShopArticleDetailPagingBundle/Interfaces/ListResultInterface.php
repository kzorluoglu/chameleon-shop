<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ShopArticleDetailPagingBundle\Interfaces;

interface ListResultInterface
{
    /**
     * @return string
     */
    public function getNextPageUrl();

    /**
     * @return string
     */
    public function getPreviousPageUrl();

    /**
     * @return ListItemInterface[]
     */
    public function getItemList();

    /**
     * @param $url
     */
    public function setNextPageUrl($url);

    /**
     * @param $url
     */
    public function setPreviousPageUrl($url);

    /**
     * @param ListItemInterface[] $items (key = id)
     */
    public function setItemList(array $items);
}
