<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\User;

class UserTest extends TestCase {
    private static $related_tables = array('user_api' => 'user_id',
                    'user_call' => 'user',
                    'user_donation' => 'user',
                    'user_interest' => 'user',
                    'user_lang' => 'id',
                    'user_location' => 'id',
                    'user_login' => 'user',
                    'user_personal' => 'user',
                    'user_pool' => 'user',
                    'user_prefer' => 'user',
                    'user_project' => 'user',
                    'user_review' => 'user',
                    'user_role' => 'user_id',
                    'user_translang' => 'user',
                    'user_translate' => 'user',
                    'user_vip' => 'user',
                    'user_web' => 'user',
                    'project' => 'owner',
                    'call' => 'owner',
                    // 'blog' => 'owner', => el campo type indica la tabla del owner, se deberia cambiar
                    'comment' => 'user',
                    'invest' => 'user',
                    'invest_node' => 'user_id',
                    'invest_address' => 'user',
                    'mailer_send' => 'user',
                    'message' => 'user',
                    'patron' => 'user',
                    // 'post' => 'author', => investigar esto, parece que no siempre es el usuario
                    'review_comment' => 'user',
                    'review_score' => 'user',
                    );

    private static $user = array(
            'userid' => '012-simulated-user-test-211',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test2@goteo.org'
        );

    public function testInstance() {

        $converter = new User();

        $this->assertInstanceOf('\Goteo\Model\User', $converter);

        return $converter;
    }

    public function testCount() {

        $total = User::dbCount();
        $active = User::dbCount(array('active' => 1, 'hide' => 0));
        $nolocation = $total - User::dbCount(array('location' => ''), '!=');
        $mainnode = User::dbCount(array('node' => 'goteo'));

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
        $errors = [];
        $user->save($errors, array('password'));
        $this->assertInstanceOf('\Goteo\Model\Image', $user->avatar);
        $user = User::get(self::$user['userid']);
        $this->assertInstanceOf('\Goteo\Model\User', $user);
        $this->assertInstanceOf('\Goteo\Model\Image', $user->avatar);
        return $user;
    }

    /**
     * @depends testCreateUser
     */
    public function testSuggestUserId($user) {
        $suggestions = User::suggestUserId("I hope this user does not exists");
        $this->assertInternalType('array', $suggestions);
        $this->assertGreaterThanOrEqual(1, count($suggestions));
        $this->assertEquals('ihope', $suggestions[0]);
        $suggestions = User::suggestUserId("IHopeThisUserDoesNotexists:👑");
        $this->assertEquals('ihopethisuserdoesnotexists', $suggestions[0]);

        $this->assertEquals('a-n', User::idealiza("a.ñ"));
        $this->assertEquals('a.n', User::idealiza("a.ñ", true));
        $this->assertEquals('a.ñ', User::idealiza("a.ñ", true, true));
    }

    /**
     * @depends testCreateUser
     */
    public function testDeleteUser($user) {
        $this->assertTrue($user->dbDelete());
        $this->assertFalse(User::get(self::$user['userid']));
    }

    public function testCleanUserRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, User::query("SELECT COUNT(*) FROM `$tb` WHERE `$field` NOT IN (SELECT id FROM `user`)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM `$tb` WHERE `$field` NOT IN (SELECT id FROM `user`)");
        }
    }

}
