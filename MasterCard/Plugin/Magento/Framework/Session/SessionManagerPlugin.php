<?php

namespace OnTap\MasterCard\Plugin\Magento\Framework\Session;

use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\SessionStartChecker;
use OnTap\MasterCard\Gateway\Request\ThreeDSecure\CheckDataBuilder;

class SessionManagerPlugin
{
    /**
     * @var SessionStartChecker
     */
    private $sessionStartChecker;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param SessionStartChecker $sessionStartChecker
     * @param AppState $appState
     * @param RequestInterface $request
     */
    public function __construct(
        SessionStartChecker $sessionStartChecker,
        AppState $appState,
        RequestInterface $request
    ) {
        $this->sessionStartChecker = $sessionStartChecker;
        $this->appState = $appState;
        $this->request = $request;
    }

    /**
     * @param SessionManager $subject
     */
    public function beforeStart(SessionManager $subject): void
    {
        if (!$this->isValidRequest()) {
            return;
        }

        $sid = $this->request->getParam(CheckDataBuilder::RESPONSE_SID_PARAMETER);
        if (!$sid) {
            return;
        }

        if ($subject->getSessionId() !== $sid) {
            $subject->setSessionId($sid);
        }
    }

    /**
     * @return bool
     */
    private function isValidRequest(): bool
    {
        if (!$this->sessionStartChecker->check()) {
            return false;
        }

        try {
            if ($this->appState->getAreaCode() !== AppArea::AREA_FRONTEND) {
                return false;
            }
        } catch (LocalizedException $e) {
            return false;
        }

        return true;
    }
}
