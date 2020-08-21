<?php


namespace phpCollab;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * Class CsrfHandler
 * @package phpCollab
 */
class CsrfHandler
{
    private $csrfGenerator;
    private $csrfStorage;
    private $csrfManager;
    private $tokenName;

    /**
     * CsrfHandler constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        if (empty($session)) {
            throw new InvalidArgumentException("Session missing");
        }
        $this->csrfGenerator = new UriSafeTokenGenerator();
        $this->tokenName = $session->get('csrfToken');
        $this->csrfStorage = new SessionTokenStorage($session);
        $this->csrfManager = new CsrfTokenManager($this->csrfGenerator, $this->csrfStorage);

        $session->set('foo', 'bar');
    }

    /**
     * @param $tokenName
     */
    public function regenerate($tokenName)
    {
        if (empty($tokenName)) {
            throw new InvalidArgumentException("token name missing");
        }



    }

    /**
     * @param $formToken
     * @return bool
     */
    public function isValid($formToken)
    {
        // Take the provided token and compare it against the stored session token.
        $token = new CsrfToken($this->tokenName, $formToken);
        if (!$this->csrfManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        return true;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->csrfManager->getToken($this->tokenName)->getValue();
    }

}