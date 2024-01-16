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
 * @version 1.1.0
 */

namespace System\Reflection\Dependencies;

require __DIR__ . "/../../Attributes/PartialsAttributes.php";
require __DIR__ . "/PartialConst.php";
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
      private const ExceptionLoadPartialMessage = "Error when loading partials class file : ";
      private const UsePartial = "use System\Attributes\Partial;";

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

      private static function loadDependenciesFromPath(string $path): array
      {
            $dependants = array();

            $partialContents = array();

            foreach (scandir($path) as $filename) {
                  $currentPath = $path . '/' . $filename;

                  if (Loader::IsNotLoadable($currentPath, $filename))
                        continue;

                  if (is_file($currentPath)) {
                        $ext = pathinfo($currentPath, PATHINFO_EXTENSION);

                        switch ($ext) {
                              case Loader::PhpExtension:
                                    $preload = Loader::$php_as_partial
                                          ? file_get_contents($currentPath)
                                          : "";

                                    if (strpos($preload, Partial_Attribute) > 0) {
                                          array_push(
                                                $partialContents,
                                                new PartialElements(
                                                      $preload,
                                                      Loader::PartialFileHeading . $filename . " ---"
                                                )
                                          );
                                    } else {
                                          try {
                                                require_once $currentPath;
                                                Loader::$Counter++;
                                          } catch (\Error $e) {
                                                array_push($dependants, $currentPath);
                                          }
                                    }
                                    break;
                              case Loader::PhpPartialExtension:
                                    array_push(
                                          $partialContents,
                                          new PartialElements(
                                                file_get_contents($currentPath),
                                                Loader::PartialFileHeading . $filename . " ---"
                                          )
                                    );
                                    break;
                        }
                  }

                  if (is_dir($currentPath))
                        $dependants = array_merge($dependants, Loader::loadDependenciesFromPath($currentPath));
            }

            if (count($partialContents) > 0)
                  Loader::CompilePartials($partialContents);

            return $dependants;
      }

      private static function CompilePartials(array $partialContents)
      {
            $Namespace = PartialConst::Tag_Namespace . $partialContents[0]->Namespace . ';' . PHP_EOL;
            $ClassName = PartialConst::Tag_Class . $partialContents[0]->ClassName . PHP_EOL;

            $Uses = "";
            $Extends = "";
            $Implements = "";
            $Contents = "";

            foreach ($partialContents as $partial) {
                  $Uses .= $partial->Tag_File . PHP_EOL . $partial->Uses . PHP_EOL;
                  $Extends .= (
                        (strlen($partial->Inherits) > 0)
                        ? (strlen($Extends) == 0
                              ?     $partial->Tag_File . PHP_EOL .
                              PartialConst::Tag_Extends . PHP_EOL .
                              $partial->Inherits
                              : "")
                        : "");
                  $Implements .=
                        (strlen($partial->Interfaces) > 0
                              ? (strlen($Implements) > 0
                                    ? ", "
                                    : PartialConst::Tag_Interfaces . PHP_EOL) .
                              $partial->Tag_File . PHP_EOL .
                              $partial->Interfaces . PHP_EOL
                              : "");
                  $Contents .= $partial->Tag_File . PHP_EOL . $partial->Content . PHP_EOL;
            }

            $Uses = str_replace(Loader::UsePartial, "", $Uses);

            Loader::AssemblyAndEvaluate(
                  $Namespace,
                  $Uses,
                  $ClassName,
                  $Extends,
                  $Implements,
                  $Contents
            );
      }

      private static function AssemblyAndEvaluate(
            $Namespace,
            $Uses,
            $ClassName,
            $Extends,
            $Implements,
            $Contents
      ) {
            $finalClass =
                  $Namespace . PHP_EOL .
                  $Uses . PHP_EOL .
                  $ClassName . " " . $Extends . " " . $Implements . PHP_EOL .
                  "{" . PHP_EOL . $Contents . PHP_EOL . "}";

            try {
                  eval($finalClass);
                  Loader::$Counter++;
            } catch (\Error $e) {
                  echo new \Exception(Loader::ExceptionLoadPartialMessage .
                        $e->getMessage());
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
