<?php

namespace OnTap\MasterCard\Model;

use Exception;
use OnTap\MasterCard\Api\MpgsInterface;

class Mpgs extends AbstractModel implements MpgsInterface
{
    /**
     * @const authenticationLimit
     */
    public const AUTHENTICATION_LIMIT = ['session' => ['authenticationLimit' => 25]];

    /**
     * {@inheritDoc}
     */
    public function createSession()
    {
        $payLoad = self::AUTHENTICATION_LIMIT;
        try {
            echo $this->processCurlRequest($payLoad, "/session", '');
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateSession($sessionId, $order = null, $sourceOfFunds = null)
    {
        $payLoad = [];
        if (!empty($order)) {
            $payLoad['order'] = $order;
        }

        if (!empty($sourceOfFunds)) {
            $payLoad['sourceOfFunds'] = $sourceOfFunds;
        }
        try {
            echo $this->processCurlRequest($payLoad, "/session/" . $sessionId, 'PUT');
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getToken($session, $sourceOfFunds)
    {
        $payLoad = [
            'sourceOfFunds' => $sourceOfFunds,
            'session' => $session
        ];
        try {
            echo $this->processCurlRequest($payLoad, '/token');
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveSession($sessionId)
    {
        try {
            echo $this->processCurlRequest('', '/session/' . $sessionId);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteToken($tokenId)
    {
        try {
            echo $this->processCurlRequest('', '/token/' . $tokenId, "DELETE");
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function verifySession($orderId, $transactionId, $apiOperation, $session, $order, $sourceOfFunds)
    {
        $payLoad = [
            'apiOperation' => $apiOperation,
            'order' => $order,
            'sourceOfFunds' => $sourceOfFunds,
            'session' => $session
        ];
        $endPoint = '/order/' . $orderId . "/transaction/" . $transactionId;
        try {
            echo $this->processCurlRequest($payLoad, $endPoint, 'PUT');
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function paySession($orderId, $transactionId, $apiOperation, $session, $order, $sourceOfFunds, $transaction)
    {
        $payLoad = [
            'apiOperation' => $apiOperation,
            'order' => $order,
            'sourceOfFunds' => $sourceOfFunds,
            'transaction'=> $transaction,
            'session' => $session
        ];
        $endPoint = '/order/'.$orderId."/transaction/".$transactionId;
        echo $this->processCurlRequest($payLoad, $endPoint, 'PUT');
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeSession($orderId, $transactionId, $apiOperation, $session, $authentication, $order, $sourceOfFunds, $transaction)
    {
        $payLoad = [
            'apiOperation' => $apiOperation,
            'authentication' => $authentication,
            'order' => $order,
            'sourceOfFunds' => $sourceOfFunds,
            'transaction'=> $transaction,
            'session' => $session
        ];
        $endPoint = '/order/'.$orderId."/transaction/".$transactionId;
        echo $this->processCurlRequest($payLoad, $endPoint, 'PUT');
    }
}
