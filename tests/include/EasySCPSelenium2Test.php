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
abstract class EasySCPSelenium2Test extends PHPUnit_Extensions_Selenium2TestCase
{
	protected $config;
	protected $envconfig;

	protected function setUp(){
		$this->envconfig = parse_ini_file('config/'.getenv('CONFIG'), null, INI_SCANNER_TYPED);
		$this->config = parse_ini_file('config/common.conf', null, INI_SCANNER_TYPED);
		$this->setBrowser($this->config['browser']);
		$this->setBrowserUrl($this->envconfig['baseURL']);
		$this->prepareSession();
		PHPUnit_Extensions_Selenium2TestCase::shareSession(true);
	}

	/**
	 * Login to EasySCP
	 */
	protected function login($username, $password){
		$this->url($this->envconfig['baseURL']);

		$element = $this->byId("uname");
		$element->clear();
		$element->value($username);

		$element = $this->byId("upass");
		$element->clear();
		$element->value($password);

		$this->byName("Submit")->click();
	}

	/**
	 * Logout from EasySCP
	 */
	protected function logout(){
		$this->byLinkText("Logout")->click();
	}

	/**
	 * Sleep for a given time if debug is enabled
	 */
	protected function debugSleep(){
		if ($this->config['debug']===true){
			sleep($this->config['sleeptime']);
		}
	}
}