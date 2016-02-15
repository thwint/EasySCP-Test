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
class EasySCPSetupTest extends  EasySCPSelenium2Test
{
	private $testurl;

	protected function setUp()
	{
		parent::setUp();
		$this->testurl='http://'.$this->envconfig['host'].'/setup';
		$this->setBrowserUrl($this->testurl);
	}

	public function testSetup(){
		$this->prepareSession();
		$this->url($this->testurl);
		//First page settings
		$element = $this->byId("DB_HOST");
		$element->clear();
		$element->value('localhost');

		$element = $this->byId("DB_DATABASE");
		$element->clear();
		$element->value($this->envconfig['database']);

		$element = $this->byId("DB_USER");
		$element->clear();
		$element->value($this->envconfig['dbuser']);

		$element = $this->byId("DB_PASSWORD");
		$element->clear();
		$element->value($this->envconfig['dbpassword']);

		$element = $this->byId("DB_PASSWORD2");
		$element->clear();
		$element->value($this->envconfig['dbpassword']);

		if ($this->config['debug']===true){
			sleep($this->config['sleeptime']);
		}

		$this->byName("Submit")->click();

		//Second page settings
		//Basic system settings
		$element = PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($this->byId('HOST_OS'));
		$element->selectOptionByLabel($this->envconfig['hostos']);

		$element = $this->byId('HOST_FQHN');
		$element->clear();
		$element->value($this->envconfig['host']);

		$element = $this->byId('HOST_IP');
		$element->clear();
		$element->value($this->envconfig['ip']);

		//$element = $this->byId('HOST_IPv6');
		$element = $this->byId('HOST_NAME');
		$element->clear();
		$element->value($this->envconfig['host']);

		//EasySCP admin panel settings
		$element = $this->byId('PANEL_ADMIN');
		$element->clear();
		$element->value($this->config['adminuser']);

		$element = $this->byId('PANEL_PASS');
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId('PANEL_PASS2');
		$element->clear();
		$element->value($this->config['password']);

		$element = $this->byId('PANEL_MAIL');
		$element->clear();
		$element->value($this->config['adminuser'].'@'.$this->config['domain1']);

		//EasySCP other settings
		//$element = $this->byId('Secondary_DNS');
		$element = $this->byId('LocalNS_yes');
		$element->click();
		//$element = $this->byId('LocalNS_no');
		//$element = $this->byId('MYSQL_Prefix_infront');
		//$element = $this->byId('MYSQL_Prefix_behind');
		$element = $this->byId('MySQL_Prefix_none');
		$element->click();

		$element = $this->byId('Timezone');
		$element->clear();
		$element->value($this->config['timezone']);

		if ($this->config['debug']===true){
			sleep($this->config['sleeptime']);
		}

		$this->byName("Submit")->click();

		$this->byXPath("//div[@id='EasySCP_Setup']//button[.='Start Setup']")->click();

		// Wait until setup finishes
		$this->waitUntil(function () {
			$element = $this->byId('EasySCP_Setup_Finish_MSG');
			$style = $element->attribute('style');
			if (strpos($style,'none')===false){
				return true;
			}
			return null;
		}, 25000);

		$this->assertContains('successfully',$this->source());
	}
}