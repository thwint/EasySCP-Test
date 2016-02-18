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
class EasySCPResellerManageUsersTest extends EasySCPSelenium2Test
{
	public function setUp()
	{
		parent::setUp();
		$this->login($this->config['reselleruser'],$this->config['password']);
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test creating a new domain using the settings from a hosting plan
	 */
	public function testCreateDomainDefaultHostingPlan(){
		$this->url("/reseller/user_add1.php");

		// data on user_add1.php
		$element = $this->byId("dmn_name");
		$element->clear();
		$element->value($this->config['domain1']);

		//$element = $this->byId("dmn_exp_date");
		$element = $this->byId('dmn_exp_never');
		if (!$element->selected()) {
			$element->click();
		}

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('dmn_tpl'));
		$element->selectOptionByLabel($this->config['templatename']);

		$this->byId('chtpl_no')->click();

		$this->debugSleep();

		$this->byName("Submit")->click();

		// data on user_add3.php
		$element = $this->byId("password");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("useremail");
		$element->clear();
		$element->value($this->config['mailusername']."@".$this->config['domain1']);

		$this->debugSleep();

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User added!",$success);

		$element = $this->byXPath("//a[contains(@onclick, '" . $this->config['domain1'] . "') and contains(@onclick, 'change_status')]");
		$this->assertEquals("icon i_ok",$element->attribute('class'));

		$sql_param = array(
			"domain_name" => $this->config['domain1'],
		);

		$sql_query = "SELECT * from domain where domain_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);

	}

	/**
	 * Test creating a new domain with customization of existing hosting plan
	 */
	public function testCreateDomainCustomHostingPlan(){
		$this->url("/reseller/user_add1.php");

		// data on user_add1.php
		$element = $this->byId("dmn_name");
		$element->clear();
		$element->value($this->config['domain2']);

		//$element = $this->byId("dmn_exp_date");
		$element = $this->byId('dmn_exp_never');
		if (!$element->selected()) {
			$element->click();
		}

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('dmn_tpl'));
		$element->selectOptionByLabel($this->config['templatename']);

		$this->byId('chtpl_yes')->click();

		$this->debugSleep();

		$this->byName("Submit")->click();

		// data on user_add2.php
		$this->byId("php_edit_yes")->click();

		$this->debugSleep();

		$this->byName("Submit")->click();

		// data on user_add3.php
		$element = $this->byId("password");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("useremail");
		$element->clear();
		$element->value($this->config['mailusername']."@".$this->config['domain2']);

		$this->debugSleep();

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User added!",$success);

		$element = $this->byXPath("//a[contains(@onclick, '" . $this->config['domain2'] . "') and contains(@onclick, 'change_status')]");
		$this->assertEquals("icon i_ok",$element->attribute('class'));
	}

	/**
	 * Test a domain
	 * @depends testCreateDomainCustomHostingPlan
	 */
	public function testDeleteDomain(){
		$this->url("reseller/users.php");

		$this->byXPath("//a[contains(@href,'" . $this->config['domain2'] . "')]/../td/a[contains(@href,'domain_delete')]")->click();

		$this->byId("delete")->click();

		$this->byName("Submit")->click();

		$sql_param = array(
			"domain_name" => $this->config['domain2'],
		);

		$sql_query = "SELECT count(*) from domain where domain_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals(0,$rs[0]);
	}
}