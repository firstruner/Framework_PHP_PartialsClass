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
 * @version 2.0.0
 */

namespace System\Reflection\Dependencies;

function InitializePartialLoader() : bool
{
      $libs = array(
            __DIR__ . "/../../Attributes/PartialsAttributes.php",
            __DIR__ . "/../../Environment/PHP.php",
            __DIR__ . "/PartialConstants.php",
            __DIR__ . "/PartialEnumerations_Element.php",
            __DIR__ . "/PartialEnumerations_ObjectType.php",
            __DIR__ . "/PartialElements.php",
            __DIR__ . "/PartialElementsCollection.php"
      );

      foreach($libs as $lib)
            if (!in_array($lib, get_included_files()))
                  Loader::StandardPHP_LoadDependency($lib);

      return true;
}

InitializePartialLoader();

final class Loader
{
      private static array $dependants = array();
      private static array $dependants_Loaded = array();
      private static int $Counter = 0;
      private static bool $php_as_partial = false;
      private static array $ignoredPath = array();
      private static array $includedPath = array();
      private static bool $log_active = false;
      private static array $log = array();

      private const IndexFileName = "index.php";
      private const PartialsAttributesFileName = "PartialsAttributes.php";
      private const PhpExtension = "php";
      private const PhpPartialExtension = "partial_php";
      private const PartialFileHeading = "// --- File : ";

      public static function GetLastDependenciesCount(): int
      {
            return Loader::$Counter;
      }

      public static function Clear()
      {
            Loader::InitializeLoadingValues();
            Loader::$php_as_partial = false;
      }

      public static function SetLogActivation(bool $active)
      {
            Loader::$log_active = $active;
      }

      public static function GetLog() : array
      {
            return Loader::$log;
      }

      private static function IsNotLoadable(string $fullPath)
      {
            return (str_ends_with($fullPath, '.')
                  || str_ends_with($fullPath, '..')
                  || str_ends_with($fullPath, Loader::IndexFileName)
                  || str_ends_with($fullPath, Loader::PartialsAttributesFileName)
                  || (str_replace("/", "\\", $fullPath) == __FILE__)
                  || in_array($fullPath, Loader::$ignoredPath, true));
      }

      private static function InitializeLoadingValues()
      {
            Loader::$ignoredPath = array();
            Loader::$includedPath = array();
            Loader::$log_active = false;
            Loader::$log = array();
            Loader::$dependants = array();
            Loader::$dependants_Loaded = array();
            Loader::$Counter = 0;
      }

      public static function Load(mixed $included, int $maxTemptatives = 1,
            $php_as_partial = false, mixed $ignored = array())
      {
            Loader::InitializeLoadingValues();

            Loader::$php_as_partial = $php_as_partial;
            Loader::AddIgnorePath($ignored);
            Loader::AddIncludePath($included);

            Loader::LoadStoredPaths($maxTemptatives);
      }

      public static function LoadStoredPaths(int $maxTemptatives = 1)
      {
            foreach (Loader::$includedPath as $path)
                  Loader::LoadFromPathString($path, $maxTemptatives);
      }

      public static function AddIgnorePath(mixed $paths)
      {
            array_push(Loader::$ignoredPath, $paths);
      }

      public static function AddIncludePath(mixed $paths)
      {
            array_push(Loader::$includedPath, $paths);
      }

      private static function LoadFromPathString(string $path, int $maxTemptatives = 1)
      {
            Loader::$Counter = 0;
            Loader::$dependants = array();

            // Main load
            Loader::$dependants = Loader::LoadFromPath($path);

            for ($attempt = 0; $attempt < $maxTemptatives; $attempt++) {
                  if (count(Loader::$dependants) > 0)
                        Loader::StandardPHP_NewTemptative();

                  Loader::StandardPHP_ClearLoaded();
            }
      }

      private static function LoadFromPath(string $path): array
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
                                    $preload = Loader::StandardPHP_TryGetContent($currentPath);
                              case Loader::PhpPartialExtension:
                                    if (!Loader::PartialPHP_AddToCollection(
                                          $partialsCollection,
                                          strlen($preload) > 0 ? $preload : file_get_contents($currentPath),
                                          $filename
                                    ))
                                          if (Loader::StandardPHP_LoadFile($currentPath))
                                                Loader::$Counter++;
                                    break;
                        }
                  } else if (is_dir($currentPath)) {
                        $dependants = array_merge(
                              $dependants,
                              Loader::LoadFromPath($currentPath)
                        );
                  }
            }

            if ($partialsCollection->count() > 0)
                  Loader::LoadPartialElement($partialsCollection);

            return $dependants;
      }

      private static function LoadPartialElement(PartialElementsCollection $partialsCollection)
      {
            if (Loader::$log_active) Loader::AddToLog(
                  str_replace('{0}', $partialsCollection->GetElementName(), PartialMessages::LogAddPreLoad));

            if ($partialsCollection->CompilePartials())
                  Loader::$Counter++;

            if (Loader::$log_active) Loader::AddToLog(
                  str_replace('{0}', $partialsCollection->GetElementName(), PartialMessages::LogAddPreLoad));
      }

      private static function PartialPHP_AddToCollection(
            PartialElementsCollection &$collection,
            string $content,
            string $filename
      ): bool {
            if (strpos($content, PartialConstants::Partial_Attribute) > 0) {
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

      private static function StandardPHP_NewTemptative()
      {
            for ($index = 0; $index < count(Loader::$dependants); $index++) {
                  try {
                        if (Loader::$log_active) Loader::AddToLog(
                              str_replace('{0}', Loader::$dependants[$index], PartialMessages::LogAddPreLoad));

                        Loader::StandardPHP_LoadDependency(Loader::$dependants[$index]);

                        if (Loader::$log_active) Loader::AddToLog(
                              str_replace('{0}', Loader::$dependants[$index], PartialMessages::LogAddPostLoad));

                        array_push(Loader::$dependants_Loaded, $index);
                  } catch (\Error $e) {
                        if (Loader::$log_active) Loader::AddToLog($e->getMessage());
                  }
            }
      }

      private static function StandardPHP_ClearLoaded()
      {
            rsort(Loader::$dependants_Loaded);

            for ($index = 0; $index < count(Loader::$dependants_Loaded); $index++)
                  unset(Loader::$dependants[$index]);
      }

      private static function StandardPHP_LoadFile($path): bool
      {
            try {
                  if (Loader::$log_active) Loader::AddToLog(
                        str_replace('{0}', $path, PartialMessages::LogAddPreLoad));

                  Loader::StandardPHP_LoadDependency($path);

                  if (Loader::$log_active) Loader::AddToLog(
                        str_replace('{0}', $path, PartialMessages::LogAddPostLoad));

                  return true;
            } catch (\Error $e) {
                  array_push($dependants, $path);
            }
      }

      private static function StandardPHP_TryGetContent($path): string
      {
            return Loader::$php_as_partial
                  ? file_get_contents($path)
                  : "";
      }

      public static function StandardPHP_LoadDependency($path) : bool
      {
            if (!in_array(
                  str_replace('/', '\\', $path),
                  get_included_files()
            ))
            {
                  require $path;
                  return true;
            }

            return false;
      }

      private static function AddToLog(string $message)
      {
            array_push(Loader::$log, $message);
      }
}
