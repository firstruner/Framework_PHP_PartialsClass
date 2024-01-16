<?php

/**
 * This file is a part of Firstruner Framework for PHP
 */

/**
 * Copyright since 2024 Firstruner and Contributors
 * Firstruner is an Registered Trademark & Property of Christophe BOULAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Freemium License
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@firstruner.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit, reproduce ou modify this file.
 * Please refer to https://firstruner.fr/ or contact Firstruner for more information.
 *
 * @author    Firstruner and Contributors <contact@firstruner.fr>
 * @copyright Since 2024 Firstruner and Contributors
 * @license   https://wikipedia.org/wiki/Freemium Freemium License
 * @version 1.2.0
 */

namespace System\Reflection\Dependencies;

require __DIR__ . "/../../Attributes/PartialsAttributes.php";
require __DIR__ . "/PartialConst.php";
require __DIR__ . "/PartialEnum.php";
require __DIR__ . "/PartialElements.php";

final class Loader
{
      private static array $dependants = array();
      private static array $dependants_Loaded = array();
      private static int $Counter = 0;
      private static bool $php_as_partial = false;

      private const IndexFileName = "index.php";
      private const PartialsAttributesFileName = "PartialsAttributes.php";
      private const PhpExtension = "php";
      private const PhpPartialExtension = "partial_php";
      private const PartialFileHeading = "// --- File : ";

      public static function Load(string $path, int $maxTemptatives = 1, $php_as_partial = false)
      {
            Loader::$Counter = 0;
            Loader::$dependants = array();
            Loader::$dependants_Loaded = array();
            Loader::$php_as_partial = $php_as_partial;

            // Main load
            Loader::loadDependencies($path);

            for ($attempt = 0; $attempt < $maxTemptatives; $attempt++) {
                  if (count(Loader::$dependants) > 0)
                        Loader::newTemptative();

                  Loader::clearLoaded();
            }
      }

      public static function GetLastDependenciesCount(): int
      {
            return Loader::$Counter;
      }

      private static function loadDependencies(string $path)
      {
            Loader::$dependants = Loader::loadDependenciesFromPath($path);
      }

      private static function IsNotLoadable(string $fullPath)
      {
            return (str_ends_with($fullPath, '.')
                  || str_ends_with($fullPath, '..')
                  || str_ends_with($fullPath, Loader::IndexFileName)
                  || str_ends_with($fullPath, Loader::PartialsAttributesFileName)
                  || (str_replace("/", "\\", $fullPath) == __FILE__));
      }

      private static function getContentIfPHPFile($path) : string
      {
            return Loader::$php_as_partial
                  ? file_get_contents($path)
                  : "";
      }

      private static function addToCollection(
            PartialElementsCollection &$collection,
            string $content, string $filename) : bool
      {
            if (strpos($content, Partial_Attribute) > 0) {
                  $collection->add(
                        new PartialElements(
                              $content,
                              Loader::PartialFileHeading . $filename . " ---"
                        )
                  );

                  return true;
            }

            return false;
      }

      private static function loadDependenciesFromPath(string $path): array
      {
            $dependants = array();

            $partialsCollection = new PartialElementsCollection();
            
            foreach (scandir($path) as $filename) {
                  $currentPath = $path . '/' . $filename;

                  if (Loader::IsNotLoadable($currentPath, $filename))
                        continue;

                  if (is_file($currentPath)) {
                        $ext = pathinfo($currentPath, PATHINFO_EXTENSION);
                        $preload = "";

                        switch ($ext) {
                              case Loader::PhpExtension:
                                    $preload = Loader::getContentIfPHPFile($currentPath);
                              case Loader::PhpPartialExtension:
                                    if (!Loader::addToCollection(
                                          $partialsCollection,
                                          strlen($preload) > 0 ? $preload : file_get_contents($currentPath),
                                          $filename
                                    ))
                                          if (Loader::standardPHPFileLoader($currentPath))
                                                Loader::$Counter++;
                                    break;
                        }
                  }
                  else if (is_dir($currentPath))
                  {
                        $dependants = array_merge(
                              $dependants,
                              Loader::loadDependenciesFromPath($currentPath));
                  }
            }

            if ($partialsCollection->count() > 0)
            {
                  if ($partialsCollection->CompilePartials())
                        Loader::$Counter++;
            }

            return $dependants;
      }

      private static function standardPHPFileLoader($path) : bool
      {
            try {
                  require_once $path;
                  return true;
            } catch (\Error $e) {
                  array_push($dependants, $path);
            }
      }

      private static function newTemptative()
      {
            for ($index = 0; $index < count(Loader::$dependants); $index++) {
                  try {
                        require Loader::$dependants[$index];
                        array_push(Loader::$dependants_Loaded, $index);
                  } catch (\Error $e) {
                  }
            }
      }

      private static function clearLoaded()
      {
            rsort(Loader::$dependants_Loaded);

            for ($index = 0; $index < count(Loader::$dependants_Loaded); $index++)
                  unset(Loader::$dependants[$index]);
      }
}
