<?php


namespace Goteo\Model\User\Tests;

use Goteo\Core\DB;
use Goteo\Model\User;
use Goteo\Model\User\UserRoles;

class UserRolesTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new UserRoles(new User());

        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $ob);
        $this->assertInstanceOf('\ArrayObject', $ob);

        return $ob;
    }

    public function testGetRole() {
        $roles = UserRoles::getRolesForUser(get_test_user());
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles);
        $this->assertInstanceOf('\ArrayObject', $roles);

        return $roles;
    }

    /**
     * @depends testGetRole
     */
    public function testDefaultRoles($roles) {
        $this->assertTrue($roles->hasRole('user'));
        $this->assertTrue($roles->hasPerm('create-project'));
        $this->assertTrue($roles->hasPerm(['create-project', 'edit-project']));
        $this->assertTrue($roles->hasPerm('remove-project'));
        $this->assertFalse($roles->hasPerm('publish-any-project'));
    }

    /**
     * @depends testGetRole
     */
    public function testAddRoles($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('admin'));
        $this->assertTrue($roles->hasRole('admin'));
        $this->assertTrue($roles->hasPerm('publish-any-project'));

        try {
            // Role root cannot be added
            $roles->addRole('root');
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\RoleException', $e);
        }
        $this->assertFalse($roles->hasRole('root'));
        return $roles;
    }

    /**
     * @depends testAddRoles
     */
    public function testRemoveRoles($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->removeRole('admin'));
        $this->assertTrue($roles->hasRole('user'));
        $this->assertTrue($roles->hasPerm('create-project'));
        $this->assertFalse($roles->hasRole('admin'));
        $this->assertFalse($roles->hasPerm('publish-any-project'));
        try {
            // Role user cannot be removed
            $roles->removeRole('user');
        } catch(\Exception $e) {
            $this->assertInstanceOf('Goteo\Application\Exception\RoleException', $e);
        }
        $this->assertTrue($roles->hasRole('user'));

        return $roles;
    }

    /**
     * @depends testRemoveRoles
     */
    public function testPersistence($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('admin'));
        $this->assertTrue($roles->save($errors), print_r($errors, true));
        return $roles;
    }

    /**
     * @depends testPersistence
     */
    public function testCheckPersistence($roles) {
        // DB::cache(false);
        $new = UserRoles::getRolesForUser(get_test_user());
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $new);
        $this->assertTrue($new->hasRole('user'));
        $this->assertTrue($new->hasRole('admin'));
        $new->removeRole('admin');

        $new2 = UserRoles::getRolesForUser(get_test_user());
        $this->assertTrue($new2->hasRole('admin'));

        $this->assertTrue($new->save($errors), print_r($errors, true));
        $this->assertFalse($new->hasRole('admin'));
        $this->assertTrue($new2->hasRole('admin'));

        $new3 = UserRoles::getRolesForUser(get_test_user());
        $this->assertFalse($new3->hasRole('admin'));
    }


    /**
     * @depends testPersistence
     */
    public function testRelationalPermsTranslator($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('translator'));
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_user();
        delete_test_node();
    }

}
