<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\UserRole;

class UserRoleTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new UserRole();

        $this->assertInstanceOf('Goteo\Model\User\UserRole', $ob);
        $this->assertInstanceOf('\ArrayObject', $ob);

        return $ob;
    }

    public function testGetRole() {
        $roles = UserRole::getRolesForUser(get_test_user());
        $this->assertInstanceOf('Goteo\Model\User\UserRole', $roles);
        $this->assertInstanceOf('\ArrayObject', $roles);

        return $roles;
    }

    /**
     * @depends testGetRole
     */
    public function testDefaultRoles($roles) {
        $this->assertTrue($roles->hasRole('user'));
        $this->assertTrue($roles->hasPerm('create-project'));
        $this->assertTrue($roles->hasPerm('edit-project'));
        $this->assertTrue($roles->hasPerm('remove-project'));
        $this->assertFalse($roles->hasPerm('publish-any-project'));
    }

    /**
     * @depends testGetRole
     */
    public function testAddRoles($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRole', $roles->addRole('admin'));
        $this->assertTrue($roles->hasRole('admin'));
        $this->assertTrue($roles->hasPerm('publish-any-project'));
        return $roles;
    }

    /**
     * @depends testAddRoles
     */
    public function testRemoveRoles($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRole', $roles->removeRole('admin'));
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
    }

    public function testPersistence() {
        // TODO...
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_user();
        delete_test_node();
    }

}
