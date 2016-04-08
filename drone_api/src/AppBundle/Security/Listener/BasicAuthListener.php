<?php
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserChecker;

/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 1:38 PM
 */

class BasicAuthListener implements ListenerInterface
{
    private $authenticationManager;
    private $tokenStorage;
    private $providerKey;

    private $providers;

    private $encoderFactory;

    function __construct($tokenStorage, $authenticationManager, $providerKey)
    {

        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
        $this->providerKey = $providerKey;

        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $weakEncoder = new MessageDigestPasswordEncoder('md5', true, 1);

        $encoders = array(
            'Symfony\\Component\\Security\\Core\\User\\User' => $defaultEncoder,
            'Acme\\Entity\\LegacyUser'                       => $weakEncoder,
        );

        $this->encoderFactory = new EncoderFactory($encoders);
    }


    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $username = "pietje";
        $password = "jantje";

        $unauthenticatedToken = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );

        $authenticatedToken = $this->authenticationManager->authenticate($unauthenticatedToken);

        $this->tokenStorage->setToken($authenticatedToken);


        $userProvider = new InMemoryUserProvider(
            array(
                'admin' => array(
                    'password' => 'my_impossibru_passuworderu',
                    'roles'     => array('ROLE_ADMIN'),
                ),
            )
        );

        $userChecker = new UserChecker();

        $provider = new DaoAuthenticationProvider(
            $userProvider,
            $userChecker,
            'secured_area',
            $this->encoderFactory
        );

        $provider->authenticate($unauthenticatedToken);

        $user = new User($username,$password);
    }

    public function authenticate($user){
        $encoder = $this->encoderFactory->getEncoder($user);

        $encodedPassword = $encoder->encodePassword("", $user->getSalt());

        $validPassword = $encoder->isPasswordValid(
            $user->getPassword(), // the encoded password
            "",       // the submitted password
            $user->getSalt()
        );
    }
}