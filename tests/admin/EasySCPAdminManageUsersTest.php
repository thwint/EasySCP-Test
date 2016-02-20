<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCPAdminManageUsersTest extends EasySCPSelenium2Test
{
	protected function setUp()
	{
		parent::setUp();
		$this->login($this->config['adminuser'],$this->config['password']);
	}

	protected function tearDown()
	{
		$this->logout();
		parent::tearDown();
	}

	/**
	 * Test adding an additional administrator
	 */
	public function testAddAdmin(){
		$adminName = "admin2";
		$this->url('/admin/admin_add.php');

		$element = $this->byId("username");
		$element->clear();
		$element->value($adminName);

		$element = $this->byId("pass");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("email");
		$element->clear();
		$element->value($adminName."@".$this->config['domain1']);

		$this->byName("Submit")->click();

		$this->timeouts()->implicitWait(20000);

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User added successfully",$success);

		$sql_param = array(
			"username" => $adminName,
		);
		$sql_query = "SELECT * from admin where admin_name = :username";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals($adminName,$rs['admin_name']);
		$this->assertEquals($adminName.'@'.$this->config['domain1'],$rs['email']);
	}

	/**
	 * Test adding a reseller
	 */
	public function testAddReseller(){
		$this->url('/admin/reseller_add.php');

		$element = $this->byId("username");
		$element->clear();
		$element->value($this->config['reselleruser']);

		$element = $this->byId("pass");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("email");
		$element->clear();
		$element->value($this->config['reselleruser']."@".$this->config['domain1']);

		$element = $this->byId('nreseller_max_domain_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_subdomain_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_alias_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_mail_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_ftp_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_sql_db_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_sql_user_cnt');
		$element->value(0);

		$element = $this->byId('nreseller_max_traffic');
		$element->value(0);

		$element = $this->byId('nreseller_max_disk');
		$element->value(0);

		$this->byId('support_system_yes')->click();

		$element = $this->byId('ip_1');
		if (!$element->selected()) {
			$element->click();
		}

		$this->debugSleep();

		$this->byName("Submit")->click();

		$this->timeouts()->implicitWait(20000);

		$success = $this->byClassName("success")->text();
		$this->assertEquals("Reseller added successfully",$success);

		$sql_param = array(
			"username" => $this->config['reselleruser'],
		);
		$sql_query = "SELECT * from admin where admin_name = :username";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals($this->config['reselleruser'],$rs['admin_name']);
		$this->assertEquals($this->config['reselleruser'].'@'.$this->config['domain1'],$rs['email']);
	}

	/**
	 * Delete user added in previous step
	 * @depends testAddAdmin
	 */
	public function testDeleteAdmin(){
		$adminName = "admin2";
		$this->url('/admin/manage_users.php');

		$this->byXPath("//a[contains(@onclick, 'admin2')]")->click();
		$this->acceptAlert();

		$this->timeouts()->implicitWait(20000);

		$this->debugSleep();

		$sql_param = array(
			"username" => $adminName,
		);
		$sql_query = "SELECT count(*) from admin where admin_name = :username";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);
		$this->assertEquals(0,$rs[0]);

	}
}