<?php


namespace Goteo\Model\User\Tests;

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
    public function testRoleNames($roles) {
        $this->assertInternalType('array', $roles->getRoleNames());
        $this->assertArrayHasKey('user', $roles->getRoleNames(), print_r($roles->getRoleNames(), 1));
        $this->assertArrayHasKey('admin', $roles->getRoleNames());
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
        $errors = [];
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('admin'));
        $this->assertTrue($roles->save($errors), print_r($errors, true));
        return $roles;
    }

    /**
     * @depends testPersistence
     */
    public function testCheckPersistence($roles) {
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
        $this->assertTrue($roles->hasRole('translator'));
        $this->assertTrue($roles->hasPerm('translate-language'));
        $this->assertFalse($roles->hasPerm('translate-language', 'en'));

        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->assignUserPerm('translate-language', 'en'));
        $this->assertTrue($roles->hasPerm('translate-language', 'en'));
        return $roles;
    }

    /**
     * @depends testRelationalPermsTranslator
     */
    public function testRelationalPermsReview($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('checker'));
        $this->assertTrue($roles->hasRole('checker'));
        $this->assertTrue($roles->hasPerm('review-project'));
        $this->assertFalse($roles->hasPerm('review-project', get_test_project()->id));

        User::query('insert into review (project) values (?)', [get_test_project()->id]);
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->assignUserPerm('review-project', get_test_project()->id));
        $this->assertTrue($roles->hasPerm('review-project', get_test_project()->id));
        return $roles;
    }


    /**
     * @depends testRelationalPermsReview
     */
    public function testRelationalPermsCall($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('caller'));
        $this->assertTrue($roles->hasRole('caller'));
        $this->assertTrue($roles->hasPerm('view-call-project'));
        $this->assertFalse($roles->hasPerm('view-call-project', get_test_project()->id));

        User::query('insert into `call` (id, name, owner) values (?, ?, ?)', ['test-call', 'test call',get_test_user()->id]);
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->assignUserPerm('view-call-project', 'test-call'));
        $this->assertTrue($roles->hasPerm('view-call-project', 'test-call'));

        return $roles;
    }

    /**
     * @depends testRelationalPermsCall
     */
    public function testRelationalPermsProject($roles) {
        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->addRole('consultant'));
        $this->assertTrue($roles->hasRole('consultant'));
        $this->assertTrue($roles->hasPerm('edit-projects'));
        $this->assertTrue($roles->hasPerm('remove-projects'));
        $this->assertTrue($roles->hasPerm('publish-projects'));
        $this->assertFalse($roles->hasPerm('edit-projects', get_test_project()->id));
        $this->assertFalse($roles->hasPerm('remove-projects', get_test_project()->id));
        $this->assertFalse($roles->hasPerm('publish-projects', get_test_project()->id));

        $this->assertInstanceOf('Goteo\Model\User\UserRoles', $roles->assignUserPerm('edit-projects', get_test_project()->id));
        $this->assertTrue($roles->hasPerm('edit-projects', get_test_project()->id));
        $this->assertTrue($roles->hasPerm('remove-projects', get_test_project()->id));
        $this->assertTrue($roles->hasPerm('publish-projects', get_test_project()->id));

        return $roles;
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        User::query('delete from `call` where id = ?', 'test-call');
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }

}
