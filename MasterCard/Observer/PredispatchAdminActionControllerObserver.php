<?php

namespace OnTap\MasterCard\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use OnTap\MasterCard\Model\FeedFactory;

class PredispatchAdminActionControllerObserver implements ObserverInterface
{
    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Session
     */
    protected $_backendAuthSession;

    /**
     * @param FeedFactory $feedFactory
     * @param Session $backendAuthSession
     */
    public function __construct(
        FeedFactory $feedFactory,
        Session $backendAuthSession
    ) {
        $this->feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->feedFactory->create();
            /** @var $feedModel \OnTap\MasterCard\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
