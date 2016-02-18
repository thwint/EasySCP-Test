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
class EasySCPAdminManageSSLTest extends EasySCPSelenium2Test
{
	public function setUp()
	{
		parent::setUp();
		$this->login($this->config['adminuser'],$this->config['password']);
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	public function testConfigureSSL(){
		$domainName = $this->envconfig['host'];

		$cert = $this->getFileContent("config/ssl/" . $domainName .".crt");
		$key = $this->getFileContent("config/ssl/" . $domainName .".key");
		$this->url("admin/tools_config_ssl.php");

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('ssl_status'));
		$element->selectOptionByLabel("both");

		$element = $this->byId("ssl_cert");
		$element->clear();
		$element->value($cert);

		$element = $this->byId("ssl_key");
		$element->clear();
		$element->value($key);

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("SSL configuration updated!",$success);

		sleep(5);

		$sql_query = "SELECT * FROM config WHERE NAME='SSL_STATUS'";
		$rs = DB::query($sql_query,true);

		$this->assertEquals("2",$rs['value']);

		$this->assertEquals($domainName,$this->verifySSLInfo($domainName));
	}

}