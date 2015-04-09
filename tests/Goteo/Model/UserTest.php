<?php


namespace Goteo\Model\Tests;

use Goteo\Model\User;

class UserTest extends \PHPUnit_Framework_TestCase {
    private static $related_tables = array('user_api' => 'user_id',
                    'user_call' => 'user',
                    'user_donation' => 'user',
                    'user_interest' => 'user',
                    'user_lang' => 'id',
                    'user_location' => 'id',
                    'user_login' => 'user',
                    'user_node' => 'user',
                    'user_personal' => 'user',
                    'user_pool' => 'user',
                    'user_prefer' => 'user',
                    'user_project' => 'user',
                    'user_review' => 'user',
                    'user_role' => 'user_id',
                    'user_translang' => 'user',
                    'user_translate' => 'user',
                    'user_vip' => 'user',
                    'user_web' => 'user'
                    );

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

    public function testCount() {

        $total = User::countTotal();
        $active = User::countTotal(array('active' => 1, 'hide' => 0));
        $nolocation = $total - User::countTotal(array('location' => ''), '!=');
        $mainnode = User::countTotal(array('node' => 'goteo'));

        $this->assertInternalType('integer', $total);
        $this->assertInternalType('integer', $active);
        $this->assertInternalType('integer', $mainnode);
        $this->assertGreaterThanOrEqual($active, $total);
        $this->assertGreaterThanOrEqual($nolocation, $total);
        $this->assertGreaterThanOrEqual($mainnode, $total);
        echo "Users: [$total] Active: [$active] no-location: [$nolocation] Node goteo: [$mainnode]\n";
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

    public function testCleanUserRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, User::query("SELECT COUNT(*) FROM $tb WHERE $field NOT IN (SELECT id FROM user)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM $tb WHERE $field NOT IN (SELECT id FROM user)");
        }
    }

}
