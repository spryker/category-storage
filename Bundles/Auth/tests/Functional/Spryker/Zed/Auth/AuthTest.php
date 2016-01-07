<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\Auth;

use Codeception\TestCase\Test;
use Generated\Zed\Ide\AutoCompletion;
use Spryker\Shared\Config;
use Spryker\Zed\Auth\AuthConfig;
use Spryker\Shared\Auth\AuthConstants;
use Spryker\Zed\Auth\Business\AuthFacade;
use Spryker\Zed\User\Business\UserFacade;
use Spryker\Zed\Auth\Business\Client\StaticToken;
use Generated\Shared\Transfer\UserTransfer;

/**
 * @group Spryker
 * @group Zed
 * @group Auth
 */
class AuthTest extends Test
{

    /**
     * @var AuthFacade
     */
    private $authFacade;

    /**
     * @var UserFacade
     */
    private $userFacade;

    /**
     * @var AutoCompletion
     */
    private $locator;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->userFacade = new UserFacade();

        $this->authFacade = new AuthFacade();
    }

    /**
     * @return string[]
     */
    private function mockUserData()
    {
        $data['firstName'] = sprintf('Test-%s', rand(100, 999));
        $data['lastName'] = sprintf('LastName-%s', rand(100, 999));
        $data['username'] = sprintf('Username-%s', rand(100, 999));
        $data['password'] = sprintf('Password-%s', rand(100, 999));

        return $data;
    }

    /**
     * @param string[] $data
     *
     * @return UserTransfer
     */
    private function mockAddUser($data)
    {
        return $this->userFacade->addUser($data['firstName'], $data['lastName'], $data['username'], $data['password']);
    }

    /**
     * @return void
     */
    public function testUserToken()
    {
        $userData = $this->mockUserData();
        $userDto = $this->mockAddUser($userData);

        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $userDto);
        $this->assertNotEquals($userData['password'], $userDto->getPassword());

        $token = $this->authFacade->getUserToken($userDto);
        $fakeToken = md5(sprintf('%s%s', $userDto->getPassword(), $userDto->getIdUser()));

        $this->assertEquals($fakeToken, $token);

        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $userDto);
        $this->assertNotEquals($userData['password'], $userDto->getPassword());

        $login = $this->authFacade->login($userDto->getUsername(), $userData['password']);
        $this->assertTrue($login);

        $isValid = $this->authFacade->isAuthenticated($token);
        $this->assertTrue($isValid);
    }

    /**
     * @return void
     */
    public function testIgnorablePath()
    {
        $ignorable = $this->authFacade->isIgnorable('auth', 'login', 'index');
        $this->assertTrue($ignorable);

        $ignorable = $this->authFacade->isIgnorable('auth', 'login', 'check');
        $this->assertTrue($ignorable);
    }

    /**
     * @return void
     */
    public function testDoLogin()
    {
        $userData = $this->mockUserData();
        $userDto = $this->mockAddUser($userData);

        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $userDto);
        $this->assertNotEquals($userData['password'], $userDto->getPassword());

        $login = $this->authFacade->login($userDto->getUsername(), $userData['password']);
        $this->assertTrue($login);
    }

    /**
     * @return void
     */
    public function testLoginNotAllowed()
    {
        $userData = $this->mockUserData();
        $userDto = $this->mockAddUser($userData);

        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $userDto);
        $this->assertNotEquals($userData['password'], $userDto->getPassword());

        $login = $this->authFacade->login($userDto->getUsername(), rand(10000, 99999));
        $this->assertEquals(false, $login);

        $login = $this->authFacade->login($userDto->getUsername(), $userDto->getPassword());
        $this->assertEquals(false, $login);

        $login = $this->authFacade->login(rand(10000, 99999), $userDto->getPassword());
        $this->assertEquals(false, $login);

        $login = $this->authFacade->login(rand(10000, 99999), $userData['password']);
        $this->assertEquals(false, $login);

        $login = $this->authFacade->login(rand(10000, 99999), rand(10000, 99999));
        $this->assertEquals(false, $login);
    }

    /**
     * @return void
     */
    public function testDoLoginWithToken()
    {
        $settings = new AuthConfig();
        $token = new StaticToken();
        $credentials = $settings->getUsersCredentials();

        foreach ($credentials as $username => $credential) {
            $token->setRawToken($credential['token']);
            $hash = $token->generate();
            $isAllowed = $this->authFacade->isAuthenticated($hash);
            $this->assertTrue($isAllowed);
        }
    }

    /**
     * @return void
     */
    public function testDenyLoginWithWrongToken()
    {
        $token = new StaticToken();

        $token->setRawToken('WRONGTOKEN');
        $hash = $token->generate();
        $isAllowed = $this->authFacade->isAuthenticated($hash);
        $this->assertTrue(!$isAllowed);
    }

    /**
     * @return void
     */
    public function testCheckDoLoginAndCurrentUserIsTheSame()
    {
        $userData = $this->mockUserData();
        $userDto = $this->mockAddUser($userData);

        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $userDto);
        $this->assertNotEquals($userData['password'], $userDto->getPassword());

        $login = $this->authFacade->login($userDto->getUsername(), $userData['password']);
        $this->assertTrue($login);

        $currentUserDto = $this->userFacade->getCurrentUser();
        $this->assertInstanceOf('\Generated\Shared\Transfer\UserTransfer', $currentUserDto);
        $this->assertEquals($userDto->getIdUser(), $currentUserDto->getIdUser());
        $this->assertEquals($userDto->getUsername(), $currentUserDto->getUsername());
        $this->assertEquals($userDto->getPassword(), $currentUserDto->getPassword());
        $this->assertEquals($userDto->getFirstName(), $currentUserDto->getFirstName());
        $this->assertEquals($userDto->getLastName(), $currentUserDto->getLastName());
    }

    /**
     * @return void
     */
    public function testIsAuthorizedWithYvesCredentialsFromConfigMustReturnTrue()
    {
        $token = new StaticToken();

        $authConfig = Config::get(AuthConstants::AUTH_DEFAULT_CREDENTIALS);
        $rawToken = $authConfig['yves_system']['token'];

        $token->setRawToken($rawToken);
        $hash = $token->generate();

        $isAllowed = $this->authFacade->isAuthenticated($hash);
        $this->assertTrue($isAllowed);
    }

}