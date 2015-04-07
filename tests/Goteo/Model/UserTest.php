<?php


namespace Goteo\Model\Tests;

use Goteo\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase {

    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );

    public function testInstance() {

        $converter = new User();

        $this->assertInstanceOf('\Goteo\Model\User', $converter);

        return $converter;
    }
    public function testCreateUser() {
        // TODO: more tests...
        $user = new User(self::$user);
        $user->save($errors, array('password'));
        $user = User::get(self::$user['userid']);
        $this->assertInstanceOf('\Goteo\Model\User', $user);
        return $user;
    }
    /**
     * @depends testCreateUser
     */
    public function testDeleteUser($user) {
        $this->assertTrue($user->delete());
        $this->assertFalse(User::get(self::$user['userid']));
    }
}
