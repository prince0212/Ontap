<?php

namespace OnTap\MasterCard\Api;

interface MpgsInterface
{
    /**
     * create session
     * 
     * @api
     * @return mixed
     */
    public function createSession();

    /**
     * @api
     * @param string $sessionId
     * @param mixed | null $order
     * @param mixed | null $sourceOfFunds
     * @return mixed
     */
    public function updateSession(string $sessionId, $order = null, $sourceOfFunds = null);

    /**
     * @api
     * @param mixed $session
     * @param mixed $sourceOfFunds
     * @return mixed
     */
    public function getToken($session, $sourceOfFunds);

    /**
     * @api
     * @param string $sessionId
     * @return mixed
     */
    public function retrieveSession($sessionId);

    /**
     * @api
     * @param string $tokenId
     * @return mixed
     */
    public function deleteToken($tokenId);

    /**
     * Verify Session
     *
     * @api
     * @param string $orderId
     * @param string $transactionId
     * @param string $apiOperation
     * @param mixed $session
     * @param mixed $order
     * @param mixed $sourceOfFunds
     * @return mixed
     */
    public function verifySession($orderId, $transactionId, $apiOperation, $session, $order, $sourceOfFunds);

    /**
     * Pay session
     *
     * @param string $orderId
     * @param string $transactionId
     * @param string $apiOperation
     * @param mixed $session
     * @param mixed $order
     * @param mixed $sourceOfFunds
     * @param mixed $transaction
     * @return mixed
     */
    public function paySession($orderId, $transactionId, $apiOperation, $session, $order, $sourceOfFunds, $transaction);
    /**
     *
     * @param string $orderId
     * @param string $transactionId
     * @param string $apiOperation
     * @param mixed $session
     * @param mixed $authentication
     * @param mixed $order
     * @param mixed $sourceOfFunds
     * @param mixed $transaction
     * @return mixed
     */
    public function authorizeSession($orderId, $transactionId, $apiOperation, $session, $authentication, $order, $sourceOfFunds, $transaction);
}
