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
class EasySCPAdminSystemToolsCronTest extends EasySCPSelenium2Test
{
	private $cron1="testcron simple";
	private $cron2="testcron standard";
	private $cron3="testcron expert";
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
	 * Create a cronjob using simple schedule (e.g. @hourly)
	 */
	public function testCreateSimpleCronJob(){
		$cronName = $this->cron1;
		$this->url("/admin/cronjob_manage.php");
		$element = $this->byId("name");
		$element->value($cronName);

		$element = $this->byId("description");
		$element->value($cronName);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byName("active"));
		$element->selectOptionByValue('yes');

		$element = $this->byId("cron_cmd");
		$element->value("umask 027; /bin/date");

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('user_name'));
		$element->selectOptionByLabel('vu2000');

		$this->byId("expert_mode_simple")->click();

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('schedule'));
		$element->clearSelectedOptions();
		$element->selectOptionByLabel('Daily');

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"cronname" => $cronName,
		);
		$sql_query = "SELECT * from cronjobs where name = :cronname";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('@daily',$rs['schedule']);
		$this->assertEquals('umask 027; /bin/date',$rs['command']);
		$this->assertEquals('yes',$rs['active']);
		$this->assertEquals($cronName,$rs['description']);
		$this->assertEquals($cronName,$rs['name']);
		$this->assertEquals('vu2000',$rs['user']);
	}
	/**
	 * Create a cronjob using date and time select
	 */
	public function testCreateStandardCronJob(){
		$cronName = $this->cron2;
		$this->url("/admin/cronjob_manage.php");
		$element = $this->byId("name");
		$element->value($cronName);

		$element = $this->byId("description");
		$element->value($cronName);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byName("active"));
		$element->selectOptionByValue('no');

		$element = $this->byId("cron_cmd");
		$element->value("umask 027; /bin/date");

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('user_name'));
		$element->selectOptionByLabel('nobody');

		$this->byId("expert_mode_datetime")->click();

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("minute"));
		$element->clearSelectedOptions();
		$element->selectOptionByValue("10");
		$element->selectOptionByValue("15");

		// Maybe useful for future test cases
		// $element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("hour"));
		// $element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("day_of_month"));
		// $element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("month"));
		// $element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId("day_of_week"));

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"cronname" => $cronName,
		);
		$sql_query = "SELECT * from cronjobs where name = :cronname";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('10,15 * * * *',$rs['schedule']);
		$this->assertEquals('umask 027; /bin/date',$rs['command']);
		$this->assertEquals('no',$rs['active']);
		$this->assertEquals($cronName,$rs['description']);
		$this->assertEquals($cronName,$rs['name']);
		$this->assertEquals('nobody',$rs['user']);
	}

	/**
	 * Create a cronjob in expert mode
	 */
	public function testCreateExpertCronJob(){
		$cronName = $this->cron3;
		$this->url("/admin/cronjob_manage.php");
		$element = $this->byId("name");
		$element->value($cronName);

		$element = $this->byId("description");
		$element->value($cronName);

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byName("active"));
		$element->selectOptionByValue('yes');

		$element = $this->byId("cron_cmd");
		$element->value("umask 027; /bin/date");

		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('user_name'));
		$element->selectOptionByLabel('root');

		$this->byId("expert_mode_expert")->click();

		$element = $this->byId("minute_expert");
		$element->value("*");

		$element = $this->byId("hour_expert");
		$element->value("*/2");

		$element = $this->byId("day_of_month_expert");
		$element->value("*");

		$element = $this->byId("month_expert");
		$element->value("*");

		$element = $this->byId("day_of_week_expert");
		$element->value("*");

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"cronname" => $cronName,
		);
		$sql_query = "SELECT * from cronjobs where name = :cronname";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('* */2 * * *',$rs['schedule']);
		$this->assertEquals('umask 027; /bin/date',$rs['command']);
		$this->assertEquals('yes',$rs['active']);
		$this->assertEquals($cronName,$rs['description']);
		$this->assertEquals($cronName,$rs['name']);
		$this->assertEquals('root',$rs['user']);
	}

	/**
	 * Modify an existing cronjob
	 * @depends testCreateExpertCronJob
	 */
	public function testModifyCronJob(){
		$cronName = $this->cron3;
		$this->url("/admin/cronjob_overview.php");

		$this->byXPath("//td[contains(text(),'" . $cronName . "')]/../td/a[contains(@href,'edit_cron')]")->click();

		$element = $this->byId("minute_expert");
		$element->clear();
		$element->value("5");

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"cronname" => $cronName,
		);
		$sql_query = "SELECT * from cronjobs where name = :cronname";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('5 */2 * * *',$rs['schedule']);
	}

	/**
	 * Enable and disable a cronjob
	 * @depends testCreateExpertCronJob
	 */
	public function testEnableDisableCronJob()
	{
		$cronName = $this->cron3;
		$this->url("/admin/cronjob_overview.php");

		$this->byXPath("//a[contains(@onclick, '" . $cronName . "') and contains(@onclick, 'action_status')]")->click();
		$this->acceptAlert();

		$sql_param = array(
			"cronname" => $cronName,
		);
		$sql_query = "SELECT * from cronjobs where name = :cronname";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('no',$rs['active']);

		$this->byXPath("//a[contains(@onclick, '" . $cronName . "') and contains(@onclick, 'action_status')]")->click();
		$this->acceptAlert();

		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals('yes',$rs['active']);
	}

	/**
	 * delete cronjobs added by this test
	 * @depends testCreateSimpleCronJob
	 * @depends testCreateStandardCronJob
	 * @depends testCreateExpertCronJob
	 * @depends testModifyCronJob
	 * @depends testEnableDisableCronJob
	 */
	public function testDeleteCronJob(){
		$this->url("/admin/cronjob_overview.php");

		$this->byXPath("//a[contains(@onclick, '" . $this->cron1 . "') and contains(@onclick, 'action_delete')]")->click();
		$this->acceptAlert();

		$this->timeouts()->implicitWait(20000);
		$success = $this->byClassName("success")->text();
		$this->assertEquals("Successfully deleted cronjob!",$success);

		$this->byXPath("//a[contains(@onclick, '" . $this->cron2 . "') and contains(@onclick, 'action_delete')]")->click();
		$this->acceptAlert();

		$this->timeouts()->implicitWait(20000);
		$success = $this->byClassName("success")->text();
		$this->assertEquals("Successfully deleted cronjob!",$success);

		$this->byXPath("//a[contains(@onclick, '" . $this->cron3 . "') and contains(@onclick, 'action_delete')]")->click();
		$this->acceptAlert();

		$this->timeouts()->implicitWait(20000);
		$success = $this->byClassName("success")->text();
		$this->assertEquals("Successfully deleted cronjob!",$success);

		$sql_param = array(
			"cron1" => $this->cron1,
			"cron2" => $this->cron2,
			"cron3" => $this->cron3,
		);
		$sql_query = "SELECT count(*) from cronjobs where name in (:cron1, :cron2, :cron3)";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals(0,$rs[0]);
	}

}