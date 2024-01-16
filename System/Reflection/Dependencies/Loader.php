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
 * @version 1.0.1
 */

namespace System\Reflection\Dependencies;

use System\Attributes\Partial_Content;

require_once __DIR__ . "/../../PartialsAttributes.php";

class Loader
{
      private static array $dependants = array();
      private static array $dependants_Loaded = array();
      private static int $Counter = 0;
      private static bool $php_as_partial = false;

      private static string $IndexFileName = "index.php";
      private static string $PartialsAttributesFileName = "PartialsAttributes.php";
      private static string $PhpExtension = "php";
      private static string $PhpPartialExtension = "partial_php";
      private static string $PartialFileHeading = "// --- File : ";
      private static string $PartialMainFileHeading = "// --- File : Partial Main File ---";
      private static string $ExceptionLoadPartialMessage = "Error when loading partials class file : ";

      public static function Load(string $path, int $maxTemptatives = 1, $php_as_partial = false)
      {
            Loader::$Counter = 0;
            Loader::$dependants = array();
            Loader::$dependants_Loaded = array();
            Loader::$php_as_partial = $php_as_partial;

            // Main load
            Loader::loadDependencies($path);

            for ($attempt = 0; $attempt < $maxTemptatives; $attempt++)
            {
                  if (count(Loader::$dependants) > 0)
                        Loader::newTemptative();

                  Loader::clearLoaded();
            }
      }

      public static function GetLastDependenciesCount() : int
      {
            return Loader::$Counter;
      }

      private static function loadDependencies(string $path)
      {
            Loader::$dependants = Loader::loadDependenciesFromPath($path);
      }

      private static function extractContents(string $content, string $annotation = "") : string
      {
            $indexStart = strpos(
                  $content,
                  Partial_Content) + strlen(Partial_Content);
            
            return ($annotation != "" ? $annotation . PHP_EOL : "") .
                  substr(
                        $content,
                        $indexStart,
                        strrpos($content, '}') - $indexStart);
      }

      private static function IsNotLoadable(string $fullPath)
      {
            return (str_ends_with($fullPath, '.')
                  || str_ends_with($fullPath, '..')
                  || str_ends_with($fullPath, Loader::$IndexFileName)
                  || str_ends_with($fullPath, Loader::$PartialsAttributesFileName)
                  || (str_replace("/", "\\", $fullPath) == __FILE__));
      }

      private static function partialLoader($content, $filename, &$partialMain, &$partialContents)
      {
            if (strpos($content, Partial_Attribute) > 0)
            {
                  $partialMain = $content;
            }
            else
            {
                  array_push(
                        $partialContents,
                        Loader::extractContents(
                              $content,
                              Loader::$PartialFileHeading . $filename . " ---"));
            }
      }

      private static function loadDependenciesFromPath(string $path) : array
      {
            $dependants = array();

            $partialMain = "";
            $partialContents = array();

            foreach (scandir($path) as $filename)
            {
                  $currentPath = $path . '/' . $filename;

                  if (Loader::IsNotLoadable($currentPath, $filename))
                        continue;

                  if (is_file($currentPath))
                  {
                        $ext = pathinfo($currentPath, PATHINFO_EXTENSION);

                        switch ($ext)
                        {
                              case Loader::$PhpExtension:
                                    $preload = Loader::$php_as_partial
                                          ? file_get_contents($currentPath)
                                          : "";
                                    
                                    if ((strpos($preload, Partial_Attribute) > 0)
                                          || (strpos($preload, Partial_Content) > 0))
                                    {
                                          Loader::partialLoader(
                                                file_get_contents($currentPath),
                                                $filename,
                                                $partialMain,
                                                $partialContents);
                                    }
                                    else
                                    {
                                          try {
                                                require $currentPath;
                                                Loader::$Counter++;
                                          }
                                          catch (\Error $e) {
                                                array_push($dependants, $currentPath);
                                          }
                                    }
                                    break;
                              case Loader::$PhpPartialExtension:
                                    Loader::partialLoader(
                                          file_get_contents($currentPath),
                                          $filename,
                                          $partialMain,
                                          $partialContents);
                                    break;
                        }
                  }

                  if (is_dir($currentPath))
                        $dependants = array_merge($dependants, Loader::loadDependenciesFromPath($currentPath));
            }

            if ((strlen($partialMain) > 0) && (count($partialContents) > 0))
                  Loader::CompilePartials($partialMain, $partialContents);

            return $dependants;
      }

      private static function CompilePartials(string $main, array $contents)
      {                  
            $main = str_replace(
                  Partial_Content,
                  implode(PHP_EOL, $contents) .
                  PHP_EOL . Loader::$PartialMainFileHeading,
                  $main);
                              
            try {
                  eval(str_replace(PHP_FileTag, '', $main));
                  Loader::$Counter++;
            }
            catch (\Error $e) {
                  echo new \Exception(Loader::$ExceptionLoadPartialMessage .
                        $e->getMessage());
            }
      }

      private static function newTemptative()
      {
            for($index = 0; $index < count(Loader::$dependants); $index++) {
                  try {
                        require Loader::$dependants[$index];
                        array_push(Loader::$dependants_Loaded, $index);
                  }
                  catch (\Error $e) {
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