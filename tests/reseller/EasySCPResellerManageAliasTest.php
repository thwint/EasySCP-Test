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
 * @link        http://www.easyscp.net
 * @author        EasySCP Team
 */
class EasySCPResellerManageAliasTest extends EasySCPSelenium2Test
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
	 * Test adding an alias as reseller
	 */
	public function testCreateAliasAsReseller(){
		$this->url("/reseller/alias.php");

		$this->byName("Submit")->click();

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("usraccounts"));
		$element->clearSelectedOptions();
		$element->selectedLabel($this->config['domain1']);

		$element = $this->byId("ndomain_name");
		$element->clear();
		$element->value($this->config['domain2']);

		$this->byName("Submit")->click();

		$this->timeouts()->implicitWait(20000);

		$success = $this->byClassName("success")->text();
		$this->assertEquals("Domain alias added!",$success);

		$sql_param = array(
			"domain_name" => $this->config['domain2'],
		);

		$sql_query = "SELECT * from domain_aliasses where alias_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
	}

	/**
	 * Test deleting an alias as reseller
	 */
	public function testDeleteAliasAsReseller(){
		$this->url("/reseller/alias.php");

		$this->byXPath("//a[contains(@onclick,'" . $this->config['domain2'] . "') and contains(@onclick,'alias_delete')]")->click();
		$this->acceptAlert();

		$this->timeouts()->implicitWait(20000);

		$sql_param = array(
			"domain_name" => $this->config['domain2'],
		);

		sleep(5);

		$sql_query = "SELECT count(*) from domain_aliasses where alias_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals(0,$rs[0]);
	}
}