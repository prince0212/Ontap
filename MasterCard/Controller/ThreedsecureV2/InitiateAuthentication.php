<?php

namespace OnTap\MasterCard\Controller\ThreedsecureV2;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Payment\Gateway\Command\CommandPool;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Psr\Log\LoggerInterface;

class InitiateAuthentication extends Action
{
    const COMMAND_NAME = 'initiate_authentication';
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CommandPool
     */
    private $commandPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Check constructor.
     * @param Session $checkoutSession
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param JsonFactory $jsonFactory
     * @param CommandPool $commandPool
     * @param Context $context
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        JsonFactory $jsonFactory,
        CommandPool $commandPool,
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->jsonFactory = $jsonFactory;
        $this->commandPool = $commandPool;
        $this->logger = $logger;
    }

    /**
     * Dispatch request
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $jsonResult = $this->jsonFactory->create();
        try {
            $quote = $this->checkoutSession->getQuote();
            $payment = $quote->getPayment();
            $paymentDataObject = $this->paymentDataObjectFactory->create($payment);

            $quote->setReservedOrderId('')->reserveOrderId();

            $quote->save();

            $this->commandPool
                ->get(self::COMMAND_NAME)
                ->execute([
                    'payment' => $paymentDataObject
                ]);

            $payment->save();

            $html = $payment->getAdditionalInformation('auth_redirect_html');

            $jsonResult->setData(compact('html'));
        } catch (Exception $e) {
            $this->logger->error((string)$e);
            $jsonResult
                ->setHttpResponseCode(400)
                ->setData([
                    'message' => __('An error occurred while processing your transaction')
                ]);
        }

        return $jsonResult;
    }
}
