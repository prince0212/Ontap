<?php

namespace OnTap\MasterCard\Model;

use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Quote\Api\BillingAddressManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use OnTap\MasterCard\Api\SessionInformationManagementInterface;

class SessionInformationManagement implements SessionInformationManagementInterface
{
    const CREATE_HOSTED_SESSION = 'create_session';

    /**
     * @var CommandPoolInterface
     */
    protected $commandPool;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var PaymentDataObjectFactory
     */
    protected $paymentDataObjectFactory;

    /**
     * @var BillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var GuestCartRepositoryInterface
     */
    private $cartRepository;

    /**
     * SessionInformationManagement constructor.
     * @param CommandPoolInterface $commandPool
     * @param CartRepositoryInterface $quoteRepository
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param BillingAddressManagementInterface $billingAddressManagement
     * @param GuestCartRepositoryInterface $cartRepository
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        CartRepositoryInterface $quoteRepository,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        BillingAddressManagementInterface $billingAddressManagement,
        GuestCartRepositoryInterface $cartRepository
    ) {
        $this->commandPool = $commandPool;
        $this->quoteRepository = $quoteRepository;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritDoc
     */
    public function createNewPaymentSession(
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $cartId = (int) $cartId;

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $quote->getPayment()->setQuote($quote);
        $quote->getPayment()->importData(
            $paymentMethod->getData()
        );

        $this->commandPool
            ->get(static::CREATE_HOSTED_SESSION)
            ->execute([
                'payment' => $this->paymentDataObjectFactory->create($quote->getPayment())
            ]);

        $this->quoteRepository->save($quote);
        $session = $quote->getPayment()->getAdditionalInformation('session');

        return [
            'id' => (string) $session['id'],
            'version' => (string) $session['version']
        ];
    }

    /**
     * @inheritDoc
     */
    public function createNewGuestPaymentSession(
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $quote = $this->cartRepository->get($cartId);

        $billingAddress->setEmail($email);
        return $this->createNewPaymentSession((string)$quote->getId(), $paymentMethod, $billingAddress);
    }
}
