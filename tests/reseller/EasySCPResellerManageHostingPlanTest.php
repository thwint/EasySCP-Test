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
class EasySCPResellerManageHostingPlanTest extends EasySCPSelenium2Test
{

	/**
	 *
	 */
	public function setUp()
	{
		parent::setUp();
		$this->login($this->config['reselleruser'],$this->config['password']);
	}

	/**
	 *
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test adding a new hosting plan
	 */
	public function testAddHostingPlan(){
		$this->url("/reseller/hosting_plan_add.php");

		$element = $this->byId("hp_name");
		$element->clear();
		$element->value($this->config['templatename']);

		$element = $this->byId("hp_description");
		$element->clear();
		$element->value($this->config['templatename']);

		$element = $this->byId("hp_sub");
		$element->clear();
		$element->value("1");

		$element = $this->byId("hp_als");
		$element->clear();
		$element->value("2");

		$element = $this->byId("hp_mail");
		$element->clear();
		$element->value("3");

		$element = $this->byId("hp_ftp");
		$element->clear();
		$element->value("4");

		$element = $this->byId("hp_sql_db");
		$element->clear();
		$element->value("5");

		$element = $this->byId("hp_sql_user");
		$element->clear();
		$element->value("6");

		$element = $this->byId("hp_traff");
		$element->clear();
		$element->value("7");

		$element = $this->byId("hp_disk");
		$element->clear();
		$element->value("8");

		$this->byId("php_yes")->click();
		$this->byId("php_edit_no")->click();
		$this->byId("cgi_yes")->click();
		$this->byId("ssl_yes")->click();
		$this->byId("dns_yes")->click();
		$this->byId("backup_full")->click();
		$this->byId("countbackup_yes")->click();

		$element = $this->byId("hp_price");
		$element->clear();
		$element->value("123");

		$element = $this->byId("hp_setupfee");
		$element->clear();
		$element->value("321");

		$element = $this->byId("hp_value");
		$element->clear();
		$element->value("CHF");

		$element = $this->byId("hp_payment");
		$element->clear();
		$element->value("yearly");

		$this->byId("status_yes")->click();

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"templatename" => $this->config['templatename'],
		);
		$sql_query = "SELECT * from hosting_plans where name = :templatename";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$props = unserialize($rs['props']);

		$this->assertEquals("_yes_",$props['allow_php']);
		$this->assertEquals("_no_",$props['allow_phpe']);
		$this->assertEquals("_yes_",$props['allow_cgi']);
		$this->assertEquals("1",$props['subdomain_cnt']);
		$this->assertEquals("2",$props['alias_cnt']);
		$this->assertEquals("3",$props['mail_cnt']);
		$this->assertEquals("4",$props['ftp_cnt']);
		$this->assertEquals("5",$props['db_cnt']);
		$this->assertEquals("6",$props['sqluser_cnt']);
		$this->assertEquals("7",$props['traffic']);
		$this->assertEquals("8",$props['disk']);
		$this->assertEquals("_yes_",$props['disk_countbackup']);
		$this->assertEquals("_full_",$props['allow_backup']);
		$this->assertEquals("_yes_",$props['allow_dns']);
		$this->assertEquals("_yes_",$props['allow_ssl']);

		$this->assertEquals($this->config['templatename'],$rs['description']);
		$this->assertEquals("123",$rs['price']);
		$this->assertEquals("321",$rs['setup_fee']);
		$this->assertEquals("CHF",$rs['value']);
		$this->assertEquals("yearly",$rs['payment']);
		$this->assertEquals("1",$rs['status']);

		$success = $this->byClassName("success")->text();
		$this->assertContains("Hosting plan added!",$success);
	}

	/**
	 * Test editing an existing hosting plan
	 * @depends testAddHostingPlan
	 */
	public function testEditHostingPlan(){
		$templateName = "tmpHostingplan";
		$this->url("/reseller/hosting_plan_add.php");

		$element = $this->byId("hp_name");
		$element->clear();
		$element->value($templateName);

		$element = $this->byId("hp_description");
		$element->clear();
		$element->value($templateName);

		$element = $this->byId("hp_sub");
		$element->clear();
		$element->value("1");

		$element = $this->byId("hp_als");
		$element->clear();
		$element->value("2");

		$element = $this->byId("hp_mail");
		$element->clear();
		$element->value("3");

		$element = $this->byId("hp_ftp");
		$element->clear();
		$element->value("4");

		$element = $this->byId("hp_sql_db");
		$element->clear();
		$element->value("5");

		$element = $this->byId("hp_sql_user");
		$element->clear();
		$element->value("6");

		$element = $this->byId("hp_traff");
		$element->clear();
		$element->value("7");

		$element = $this->byId("hp_disk");
		$element->clear();
		$element->value("8");

		$this->byId("php_yes")->click();
		$this->byId("php_edit_no")->click();
		$this->byId("cgi_yes")->click();
		$this->byId("ssl_yes")->click();
		$this->byId("dns_yes")->click();
		$this->byId("backup_full")->click();
		$this->byId("countbackup_yes")->click();

		$element = $this->byId("hp_price");
		$element->clear();
		$element->value("123");

		$element = $this->byId("hp_setupfee");
		$element->clear();
		$element->value("321");

		$element = $this->byId("hp_value");
		$element->clear();
		$element->value("CHF");

		$element = $this->byId("hp_payment");
		$element->clear();
		$element->value("yearly");

		$this->byId("status_yes")->click();

		$this->debugSleep();

		$this->byName("Submit")->click();

		$sql_param = array(
			"templatename" => $templateName,
		);
		$sql_query = "SELECT * from hosting_plans where name = :templatename";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$props = unserialize($rs['props']);

		$this->assertEquals("_yes_",$props['allow_php']);
		$this->assertEquals("_no_",$props['allow_phpe']);
		$this->assertEquals("_yes_",$props['allow_cgi']);
		$this->assertEquals("1",$props['subdomain_cnt']);
		$this->assertEquals("2",$props['alias_cnt']);
		$this->assertEquals("3",$props['mail_cnt']);
		$this->assertEquals("4",$props['ftp_cnt']);
		$this->assertEquals("5",$props['db_cnt']);
		$this->assertEquals("6",$props['sqluser_cnt']);
		$this->assertEquals("7",$props['traffic']);
		$this->assertEquals("8",$props['disk']);
		$this->assertEquals("_yes_",$props['disk_countbackup']);
		$this->assertEquals("_full_",$props['allow_backup']);
		$this->assertEquals("_yes_",$props['allow_dns']);
		$this->assertEquals("_yes_",$props['allow_ssl']);
		$this->assertEquals($templateName,$rs['description']);
		$this->assertEquals("123",$rs['price']);
		$this->assertEquals("321",$rs['setup_fee']);
		$this->assertEquals("CHF",$rs['value']);
		$this->assertEquals("yearly",$rs['payment']);
		$this->assertEquals("1",$rs['status']);

		$this->url("/reseller/hosting_plan.php");

		$this->byXPath("//a[contains(text(),'" . $templateName . "')]/../../td/a[contains(@href,'hosting_plan_edit')]")->click();

		$element = $this->byId("hp_sub");
		$element->clear();
		$element->value("5");

		$element = $this->byId("hp_value");
		$element->clear();
		$element->value("EUR");

		$this->debugSleep();

		$this->byName("Submit")->click();
		$sql_query = "SELECT * from hosting_plans where name = :templatename";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$props = unserialize($rs['props']);

		$this->assertEquals("_yes_",$props['allow_php']);
		$this->assertEquals("_no_",$props['allow_phpe']);
		$this->assertEquals("_yes_",$props['allow_cgi']);
		$this->assertEquals("5",$props['subdomain_cnt']);
		$this->assertEquals("2",$props['alias_cnt']);
		$this->assertEquals("3",$props['mail_cnt']);
		$this->assertEquals("4",$props['ftp_cnt']);
		$this->assertEquals("5",$props['db_cnt']);
		$this->assertEquals("6",$props['sqluser_cnt']);
		$this->assertEquals("7",$props['traffic']);
		$this->assertEquals("8",$props['disk']);
		$this->assertEquals("_yes_",$props['disk_countbackup']);
		$this->assertEquals("_full_",$props['allow_backup']);
		$this->assertEquals("_yes_",$props['allow_dns']);
		$this->assertEquals("_yes_",$props['allow_ssl']);
		$this->assertEquals($templateName,$rs['description']);
		$this->assertEquals("123",$rs['price']);
		$this->assertEquals("321",$rs['setup_fee']);
		$this->assertEquals("EUR",$rs['value']);
		$this->assertEquals("yearly",$rs['payment']);
		$this->assertEquals("1",$rs['status']);

		$success = $this->byClassName("success")->text();
		$this->assertContains("Hosting plan updated!",$success);
	}

	/**
	 * Test deleting an existing hosting plan
	 * @depends testEditHostingPlan
	 */
	public function testDeleteHostingPlan(){
		$this->url("/reseller/hosting_plan.php");


		$this->byXPath("//a[contains(@onclick, 'tmpHostingplan') and contains(@onclick, 'action_delete')]")->click();
		$this->acceptAlert();

		$sql_param = array(
			"hostingplan" => 'tmpHostingplan',
		);
		$sql_query = "SELECT count(*) from hosting_plans where name = :hostingplan";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);

		$this->assertEquals(0,$rs[0]);

		$success = $this->byClassName("success")->text();
		$this->assertContains("Hosting plan deleted!",$success);

	}
}