<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgShopViewMyOrderDetailsDbAdapter implements IPkgShopViewMyOrderDetailsDbAdapter
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $dbConnection;

    public function __construct(\Doctrine\DBAL\Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param $userId
     * @param $orderId
     *
     * @return bool
     */
    public function hasOrder($userId, $orderId)
    {
        $query = 'select COUNT(id) AS matches FROM shop_order WHERE id = :orderId AND data_extranet_user_id = :userId';

        $row = $this->dbConnection->fetchArray($query, array('orderId' => $orderId, 'userId' => $userId));

        return intval($row[0]) >= 1;
    }

    /**
     * @param string $orderId
     *
     * @return TdbShopOrder
     */
    public function getOrder($orderId)
    {
        $order = TdbShopOrder::GetNewInstance($orderId);

        return (false !== $order->sqlData) ? $order : null;
    }
}
