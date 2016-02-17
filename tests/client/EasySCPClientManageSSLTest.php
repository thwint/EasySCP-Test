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
class EasySCPClientManageSSLTest extends EasySCPSelenium2Test
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

	public function testAddSSLForDomain(){
		$this->fillSSLForm($this->config['domain1']);

		$sql_param = array(
			"domain_name" => $this->config['domain1'],
		);

		$sql_query = "SELECT * from domain where domain_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
		$this->assertEquals("2",$rs['ssl_status']);

		$this->assertEquals($this->config['domain1'],$this->verifySSLInfo($this->config['domain1']));

	}

	public function testAddSSLForAlias(){
		$this->fillSSLForm($this->config['domain2']);

		$sql_param = array(
			"domain_name" => $this->config['domain2'],
		);

		$sql_query = "SELECT * from domain_aliasses where alias_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
		$this->assertEquals("2",$rs['ssl_status']);

		$this->assertEquals($this->config['domain2'],$this->verifySSLInfo($this->config['domain2']));
	}

	public function testAddSSLForSubdomain(){
		$domainName = $this->config['sub1'] . '.' . $this->config['domain1'];
		$this->fillSSLForm($domainName);

		$sql_param = array(
			"domain_name" => $this->config['sub1'],
		);

		$sql_query = "SELECT * from subdomain where subdomain_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
		$this->assertEquals("2",$rs['ssl_status']);

		$this->assertEquals($domainName,$this->verifySSLInfo($domainName));
	}

	public function testAddSSLForSubdomainAlias(){
		$domainName = $this->config['sub1'] . '.' . $this->config['domain2'];
		$this->fillSSLForm($domainName);

		$sql_param = array(
			"domain_name" => $this->config['sub1'],
		);

		$sql_query = "SELECT * from subdomain_alias where subdomain_alias_name = :domain_name";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals("ok", $rs['status']);
		$this->assertEquals("2",$rs['ssl_status']);

		$this->assertEquals($domainName,$this->verifySSLInfo($domainName));
	}

	/**
	 * Read fill in Form with SSL data
	 */
	private function fillSSLForm($domainName){
		$cert = $this->getFileContent("config/ssl/" . $domainName .".crt");
		$key = $this->getFileContent("config/ssl/" . $domainName .".key");
		$this->url("client/domain_manage_ssl.php");
		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("ssldomain"));
		$element->selectOptionByLabel($domainName);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('sslstatus'));
		$element->selectOptionByLabel("both");

		$element = $this->byId("sslcertificate");
		$element->clear();
		$element->value($cert);

		$element = $this->byId("sslkey");
		$element->clear();
		$element->value($key);

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("SSL configuration updated!",$success);

		// Sleep until apache is restarted
		sleep(5);

	}

	/**
	 * Read file from disk and return its content
	 * @param $fileName
	 * @return string
	 */
	private function getFileContent($fileName){
		$handle = fopen($fileName, "r") or die("Unable to open file!");
		$content = fread($handle,filesize($fileName));
		fclose($handle);
		return $content;
	}

	private function verifySSLInfo($domainName){
		$g = stream_context_create(
			array(
				"ssl" => array(
					"capture_peer_cert" => true,
					"allow_self_signed" => true,
					"verify_peer" => true
				)
			));
		$r = fopen("https://" . $domainName, "rb", false, $g);
		$cont = stream_context_get_params($r);
		$certinfo = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);

		return $certinfo['subject']['CN'];
	}

}