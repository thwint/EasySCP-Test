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
class EasySCPClientSubdomainTest extends EasySCPSelenium2Test
{
	public function setUp()
	{
		parent::setUp();
		$this->login($this->config['domain1'],$this->config['password']);
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test adding a subdomain
	 */
	public function testAddSubdomain(){
		$this->url("/client/subdomain_add.php");

		$element = $this->byId("subdomain_name");
		$element->clear();
		$element->value($this->config['sub1']);

		$this->byXPath("//input[@name='dmn_type'][@value='dmn']")->click();

		$this->byId("subdomain_mnt_pt")->click();

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User added!",$success);

		$sql_param = array(
			"domain_name" => $this->config['sub1'],
		);

		$sql_query = "SELECT * from subdomain where subdomain_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
	}

	/**
	 * Test adding a subdomain to alias
	 */
	public function testAddSubdomainAlias(){
		$this->url("/client/subdomain_add.php");

		$this->byXPath("//input[@name='dmn_type'][@value='als']")->click();

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('subdmn_id'));
		$element->selectOptionByLabel("." . $this->config['sub1']);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byName('als_id'));
		$element->selectOptionByLabel("." . $this->config['domain2']);

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User added!",$success);

		$sql_param = array(
			"domain_name" => $this->config['sub1'],
		);

		$sql_query = "SELECT * from subdomain_alias where subdomain_alias_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
	}

}