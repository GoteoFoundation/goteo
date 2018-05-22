<?php

namespace Goteo\Application\Tests;

use Goteo\Application\Role;

class RoleTest extends \PHPUnit_Framework_TestCase {

	public function testInstance() {

		$ob = new Role();

		$this->assertInstanceOf('\Goteo\Application\Role', $ob);

	}
	public function testDefaults() {
		$this->assertInternalType('array', Role::getRolePerms('non-existing-role'));
		try {
			Role::addRolePerms('non-existing-role', 'test');
		} catch (\Exception $e) {
			$this->assertInstanceOf('Goteo\Application\Exception\RoleException', $e);
		}
	}

	public function testAddRoles() {
		$roles = [
			'user1' => [
				'perms' => ['perm1', 'perm2'],
			],
			'admin1' => [
				'extends' => 'user1',
				'perms' => ['adm1', 'adm2'],
			],
		];
		Role::addRolesFromArray($roles);
		$roles = Role::getRoles();

		$this->assertInternalType('array', $roles);
		$this->assertArrayHasKey('user1', $roles);
		$this->assertArrayHasKey('admin1', $roles);

		$this->assertInternalType('array', $roles['user1']);
		$this->assertInternalType('array', $roles['admin1']);

		$this->assertContains('perm1', $roles['user1']);
		$this->assertContains('perm2', $roles['user1']);
		$this->assertContains('adm1', $roles['admin1']);
		$this->assertContains('adm2', $roles['admin1']);
	}

	public function testExtendedRoles() {

		$roles = Role::getRoles();
		$this->assertContains('perm1', $roles['admin1']);
		$this->assertContains('perm2', $roles['admin1']);

	}

	public function testRoleExists() {
		$this->assertTrue(Role::roleExists('admin1'));
		$this->assertTrue(Role::roleExists('user1'));
		$this->assertFalse(Role::roleExists('admin2'));
	}

	public function testRoleGetPerm() {
		$this->assertContains('perm1', Role::getRolePerms('user1'));
		$this->assertContains('perm2', Role::getRolePerms('user1'));
		$this->assertContains('perm1', Role::getRolePerms('admin1'));
		$this->assertContains('adm1', Role::getRolePerms('admin1'));
	}

	public function testRoleHasPerm() {
		$this->assertTrue(Role::roleHasPerm('user1', 'perm1'));
		$this->assertTrue(Role::roleHasPerm('admin1', 'perm1'));
		$this->assertTrue(Role::roleHasPerm('admin1', 'adm1'));
		$this->assertFalse(Role::roleHasPerm('user1', 'adm1'));
	}

    public function testAddPermissions() {
        $perms = [
            'perm-test1' => [
                'model' => null,
                'relational' => null
            ],
            'perm-test2' => [
                'model' => 'testmodel',
                'relational' => [
                    'table' => 'user_testmodel',
                    'user_id' => 'user_id',
                    'table_id' => 'testmodel_id'
                ]
            ]
        ];
        Role::addPermsFromArray($perms);

        $permissions = Role::getPerms();

        $this->assertInternalType('array', $permissions);
        $this->assertArrayHasKey('perm-test1', $permissions);
        $this->assertArrayHasKey('perm-test2', $permissions);
        $this->assertContains('user_testmodel', $permissions['perm-test2']['relational'], print_r($permissions['perm-test2'], 1));

    }

}
