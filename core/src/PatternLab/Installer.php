<?php

/*!
 * Installer Class
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * References the InstallerUtil class that is included in pattern-lab/core
 *
 */

namespace PatternLab;

use \Composer\Script\Event;
use \Composer\Installer\PackageEvent;
use \PatternLab\InstallerUtil;

class Installer {
	
	protected static $installerInfo = array("suggestedStarterKits" => array(), "patternLabPackages" => array());
	
	/**
	 * Get the package info from each patternlab-* package's composer.json
	 * @param  {String}     the type of event fired during the composer install
	 * @param  {Object}     a script event object from composer
	 */
	public static function getPackageInfo($type, $event) {
		
		$package      = ($type == "install") ? $event->getOperation()->getPackage()->getType() : $event->getOperation()->getTargetPackage()->getType();
		$packageType  = $package->type();
		$packageExtra = $package->getExtra();
		$packageInfo  = array();
		
		// make sure we're only evaluating pattern lab packages
		if (strpos($packageType,"patternlab-") !== false) {
			
			$packageInfo["name"]     = $package->getName();
			$packageInfo["type"]     = $packageType;
			$packageInfo["pathBase"] = $package->getTargetDir();
			//$packageInfo["pathBase"] = $event->getComposer()->getInstallationManager()->getInstallPath($package);
			$packageInfo["pathDist"] = $packageInfo["pathBase"].DIRECTORY_SEPARATOR."dist".DIRECTORY_SEPARATOR;
			$packageInfo["extra"]    = (isset($packageExtra["patternlab"])) ? $packageExtra["patternlab"] : array();
			
			self::$installerInfo["packages"][] = $packageInfo;
			
		}
		
	}
	
	/**
	 * Get the suggested starter kits from the root package composer.json
	 * @param  {Object}     a script event object from composer
	 */
	public static function getSuggestedStarterKits(Event $event) {
		
		$extra = $event->getComposer()->getPackage()->getExtra();
		if (isset($extra["patternlab"]) && isset($extra["patternlab"]["starterKitSuggestions"]) && is_array($extra["patternlab"]["starterKitSuggestions"])) {
			self::$installerInfo["suggestedStarterKits"] = $extra["patternlab"]["starterKitSuggestions"];
		}
		
	}
	
	/**
	 * Run the centralized postInstallCmd
	 * @param  {Object}     a script event object from composer
	 */
	public static function postInstallCmd(Event $event) {
		
		InstallerUtil::postInstallCmd(self::$installerInfo, $event);
		
	}
	
	/**
	 * Run the centralized postUpdateCmd
	 * @param  {Object}     a script event object from composer
	 */
	public static function postUpdateCmd(Event $event) {
		
		InstallerUtil::postUpdateCmd(self::$installerInfo, $event);
		
	}
	
	/**
	 * Clean-up when a package is removed
	 * @param  {Object}     a script event object from composer
	 */
	public static function postPackageInstall(PackageEvent $event) {
		
		self::getPackageInfo("install", $event);
		
	}
	
	/**
	 * Clean-up when a package is removed
	 * @param  {Object}     a script event object from composer
	 */
	public static function postPackageUpdate(PackageEvent $event) {
		
		self::getPackageInfo("update", $event);
		
	}
	
	/**
	 * Clean-up when a package is removed
	 * @param  {Object}     a script event object from composer
	 */
	public static function prePackageUninstall(PackageEvent $event) {
		
		// this isn't finished
		InstallerUtil::prePackageUninstall($event);
		
	}
	
}
