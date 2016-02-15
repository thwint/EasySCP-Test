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
class EasySCPAdminGeneralInformationTest extends EasySCPSelenium2Test
{
	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test changing the password of the admin user
	 */
	public function testPasswordChange(){
		$this->login($this->config['adminuser'],$this->config['password']);
		$this->byLinkText("Change password")->click();

		$element = $this->byId("curr_pass");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass");
		$element->clear();
		$element->value($this->config['wrongpassword']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['wrongpassword']);

		$this->debugSleep();

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User password updated successfully!",$success);

		// set password back to original
		$this->byLinkText("Change password")->click();

		$element = $this->byId("curr_pass");
		$element->clear();
		$element->value($this->config['wrongpassword']);

		$element = $this->byId("pass");
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId("pass_rep");
		$element->clear();
		$element->value($this->config['password']);

		$this->debugSleep();

		$this->byName("Submit")->click();

		$success = $this->byClassName("success")->text();
		$this->assertEquals("User password updated successfully!",$success);


		$this->logout();
	}

	/**
	 * Initially no personal data is filled. This test will fill all personal data and check directly in the database
	 */
	public function testChangePersonalData(){
		$this->login($this->config['adminuser'],$this->config['password']);
		$this->byLinkText("Change personal data")->click();

		$firstName = "First name";
		$lastName = "Last name";
		$gender = "M";
		$firm = "EasySCP";
		$street1 = "EasySCP street 1";
		$street2 = "EasySCP street 2";
		$zip = "zip";
		$city = "City";
		$state = "State/Province";
		$country = "Country";
		$phone = "+411234567";
		$fax = "+411234568";

		$element = $this->byId("fname");
		$element->clear();
		$element->value($firstName);

		$element = $this->byId("lname");
		$element->clear();
		$element->value($lastName);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('gender'));
		$element->selectOptionByValue($gender);

		$element = $this->byId("firm");
		$element->clear();
		$element->value($firm);

		$element = $this->byId("street1");
		$element->clear();
		$element->value($street1);

		$element = $this->byId("street2");
		$element->clear();
		$element->value($street2);

		$element = $this->byId("zip");
		$element->clear();
		$element->value($zip);

		$element = $this->byId("city");
		$element->clear();
		$element->value($city);

		$element = $this->byId("state");
		$element->clear();
		$element->value($state);

		$element = $this->byId("country");
		$element->clear();
		$element->value($country);

		$element = $this->byId("phone");
		$element->clear();
		$element->value($phone);

		$element = $this->byId("fax");
		$element->clear();
		$element->value($fax);

		$this->byName("Submit")->click();

		$sql_param = array(
			"username" => $this->config['adminuser'],
		);
		$sql_query = "SELECT * from admin where admin_name = :username";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals($firstName,$rs['fname']);
		$this->assertEquals($lastName,$rs['lname']);
		$this->assertEquals($gender,$rs['gender']);
		$this->assertEquals($street1,$rs['street1']);
		$this->assertEquals($street2,$rs['street2']);
		$this->assertEquals($zip,$rs['zip']);
		$this->assertEquals($city,$rs['city']);
		$this->assertEquals($state,$rs['state']);
		$this->assertEquals($country,$rs['country']);
		$this->assertEquals($phone,$rs['phone']);
		$this->assertEquals($fax,$rs['fax']);
		$this->logout();
	}
}