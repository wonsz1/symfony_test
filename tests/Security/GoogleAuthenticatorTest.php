<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\GoogleAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Provider\GoogleUser as GoogleOAuthUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class GoogleAuthenticatorTest extends TestCase
{
    /** @var ClientRegistry&\PHPUnit\Framework\MockObject\MockObject */
    private $clientRegistry;
    /** @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $entityManager;
    /** @var RouterInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $router;
    private GoogleAuthenticator $authenticator;
    /** @var OAuth2Client&\PHPUnit\Framework\MockObject\MockObject */
    private $oauth2Client;
    /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $userRepository;

    protected function setUp(): void
    {
        $this->clientRegistry = $this->createMock(ClientRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->oauth2Client = $this->createMock(OAuth2Client::class);
        $this->userRepository = $this->createMock(EntityRepository::class);

        $this->authenticator = new GoogleAuthenticator(
            $this->clientRegistry,
            $this->entityManager,
            $this->router
        );

    }

    public function testAuthenticateWithExistingGoogleUser(): void
    {
        // Arrange
        $request = new Request();
        $accessToken = new AccessToken(['access_token' => 'test_token']);
        $googleUser = $this->createMock(GoogleOAuthUser::class);
        $existingUser = new User();

        $googleUser->method('getId')->willReturn('google_123');
        $googleUser->method('getEmail')->willReturn('test@example.com');

        $this->clientRegistry->expects($this->once())
            ->method('getClient')
            ->with('google')
            ->willReturn($this->oauth2Client);

        $this->oauth2Client->expects($this->once())
            ->method('fetchUserFromToken')
            ->with($accessToken)
            ->willReturn($googleUser);

        $this->oauth2Client->expects($this->once())
            ->method('getAccessToken')
            ->with([])
            ->willReturn($accessToken);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['googleId' => 'google_123'])
            ->willReturn($existingUser);

        // Act
        $passport = $this->authenticator->authenticate($request);

        // Assert
        $this->assertSame($existingUser, $passport->getUser());
    }

    public function testAuthenticateWithNewUser(): void
    {
        // Arrange
        $request = new Request();
        $accessToken = new AccessToken(['access_token' => 'test_token']);
        $googleUser = $this->createMock(GoogleOAuthUser::class);

        $googleUser->method('getId')->willReturn('google_123');
        $googleUser->method('getEmail')->willReturn('test@example.com');
        $googleUser->method('getFirstName')->willReturn('John');
        $googleUser->method('getLastName')->willReturn('Doe');

        $this->clientRegistry->expects($this->once())
            ->method('getClient')
            ->with('google')
            ->willReturn($this->oauth2Client);

        $this->oauth2Client->expects($this->once())
            ->method('fetchUserFromToken')
            ->with($accessToken)
            ->willReturn($googleUser);

        $this->oauth2Client->expects($this->once())
            ->method('getAccessToken')
            ->with([])
            ->willReturn($accessToken);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [['googleId' => 'google_123']],
                [['email' => 'test@example.com']]
            )
            ->willReturnOnConsecutiveCalls(null, null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $passport = $this->authenticator->authenticate($request);
        /** @var User $user */
        $user = $passport->getUser();

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('google_123', $user->getGoogleId());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testAuthenticateWithExistingEmailUser(): void
    {
        // Arrange
        $request = new Request();
        $accessToken = new AccessToken(['access_token' => 'test_token']);
        $googleUser = $this->createMock(GoogleOAuthUser::class);
        $existingUser = new User();
        $existingUser->setEmail('test@example.com');

        $googleUser->method('getId')->willReturn('google_123');
        $googleUser->method('getEmail')->willReturn('test@example.com');

        $this->clientRegistry->expects($this->once())
            ->method('getClient')
            ->with('google')
            ->willReturn($this->oauth2Client);

        $this->oauth2Client->expects($this->once())
            ->method('fetchUserFromToken')
            ->with($accessToken)
            ->willReturn($googleUser);

        $this->oauth2Client->expects($this->once())
            ->method('getAccessToken')
            ->with([])
            ->willReturn($accessToken);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [['googleId' => 'google_123']],
                [['email' => 'test@example.com']]
            )
            ->willReturnOnConsecutiveCalls(null, $existingUser);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $passport = $this->authenticator->authenticate($request);
        /** @var User $user */
        $user = $passport->getUser();

        // Assert
        $this->assertSame($existingUser, $user);
        $this->assertEquals('google_123', $user->getGoogleId());
    }
}
