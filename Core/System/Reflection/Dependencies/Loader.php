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

class ClassAlreadyExistsException extends \Exception {}

use System\Default\_array;
use System\Default\_string;

function InitializePartialLoader(): bool
{
      $libs = array(
            __DIR__ . "/../../../../Enumerations/System/Default/_array.php",
            __DIR__ . "/../../../../Enumerations/System/Default/_string.php",
            __DIR__ . "/../../Attributes/PartialsAttributes.php",
            __DIR__ . "/../../Runtime/Version.php",
            __DIR__ . "/../../Environment/PHP.php",
            __DIR__ . "/PartialConstants.php",
            __DIR__ . "/PartialMessages.php",
            __DIR__ . "/PartialEnumerations_Element.php",
            __DIR__ . "/PartialEnumerations_ObjectType.php",
            __DIR__ . "/PartialEnumerations_DelayedMode.php",
            __DIR__ . "/PartialElements.php",
            __DIR__ . "/PartialElementsCollection.php"
      );

      foreach ($libs as $lib)
            if (!in_array(realpath($lib), get_included_files()))
                  Loader::StandardPHP_LoadDependency(realpath($lib));
      
      if (strpos(get_included_files()[0], "/") < 0)
            Loader::SetEscapeChar('\\', '/');

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
      private static array $escapesChars = [ "/", "\\"];

      private const IndexFileName = "index.php";
      private const PartialsAttributesFileName = "PartialsAttributes.php";
      private const PhpExtension = "php";
      private const PhpPartialExtension = "partial_php";
      private const PartialFileHeading = "// --- File : ";

      public static function SetEscapeChar(string $origin, string $fixed)
      {
            Loader::$escapesChars = [ $origin, $fixed ];
      }

      /**
       * Return count about last elements loaded
       */
      public static function GetLastDependenciesCount(): int
      {
            Loader::InitializeLoadingValues();
            return Loader::$Counter;
      }

      /**
       * Clear the loader
       */
      public static function Clear()
      {
            Loader::InitializeLoadingValues(true);
            Loader::$php_as_partial = false;
      }

      /**
       * Set log activation
       */
      public static function SetLogActivation(bool $active)
      {
            Loader::InitializeLoadingValues();
            Loader::$log_active = $active;
      }

      /**
       * Return log contents
       */
      public static function GetLog(): array
      {
            Loader::InitializeLoadingValues();
            return Loader::$log;
      }

      private static function IsNotLoadable(string $fullPath)
      {
            return (str_ends_with($fullPath, '.')
                  || str_ends_with($fullPath, '..')
                  || str_ends_with($fullPath, Loader::IndexFileName)
                  || str_ends_with($fullPath, Loader::PartialsAttributesFileName)
                  || (str_replace("/", "\\", $fullPath) == __FILE__)
                  || Loader::isIgnored($fullPath));
      }

      private static function isIgnored($path): bool
      {
            foreach (Loader::$ignoredPath as $ignored)
                  if (
                        str_replace("/", "\\", $path) ==
                        str_replace("/", "\\", $ignored)
                  )
                        return true;

            return false;
      }

      private static function InitializeLoadingValues(bool $force = false)
      {
            if (!isset(Loader::$ignoredPath) || $force) Loader::$ignoredPath = array();
            if (!isset(Loader::$includedPath) || $force) Loader::$includedPath = array();
            if (!isset(Loader::$log_active) || $force) Loader::$log_active = false;
            if (!isset(Loader::$log) || $force) Loader::$log = array();
            if (!isset(Loader::$dependants) || $force) Loader::$dependants = array();
            if (!isset(Loader::$dependants_Loaded) || $force) Loader::$dependants_Loaded = array();
            if (!isset(Loader::$Counter) || $force) Loader::$Counter = 0;
      }

      /**
       * Load elements
       * @included : Specify path(s) who must be load - Can take string or string array - No default value, Required
       * @maxTemptatives : Specify the number of loading temptatives - int - default value is 1
       * @php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False
       * @ignored : Specify path(s) who must be ignored during the loading - Can take string or string array - default value is an empty array
       * @loadDelayedElements : Specify if the loader load partial class that specified as delayedLoading at True - Boolean - default value is False
       * @objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All
       */
      public static function Load(
            mixed $included,
            int $maxTemptatives = 1,
            bool $php_as_partial = false,
            mixed $ignored = array(),
            int $loadDelayedElements = PartialEnumerations_DelayedMode::Without,
            int $objectType = PartialEnumerations_ObjectType::All,
            bool $clearLists = true
      ) {
            Loader::InitializeLoadingValues($clearLists);
            Loader::$php_as_partial = $php_as_partial;

            Loader::AddIgnorePath($ignored);
            Loader::AddIncludePath($included);

            Loader::LoadAllElements($maxTemptatives, $loadDelayedElements, $objectType);

            if ($loadDelayedElements == PartialEnumerations_DelayedMode::OnPost)
                  Loader::LoadDelayedElements($php_as_partial, $objectType);
      }

      /**
       * This method try to load OOP paths that specify with Load method or AddIncludePath
       * @maxTemptatives : Specify the number of loading temptatives - int - default value is 1
       * @php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False
       * @objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All
       */
      public static function LoadStoredPaths(
            int $maxTemptatives = 1,
            bool $php_as_partial = false,
            int $objectType = PartialEnumerations_ObjectType::All
      ) {
            Loader::InitializeLoadingValues();
            Loader::$php_as_partial = $php_as_partial;

            Loader::LoadAllElements($maxTemptatives, PartialEnumerations_DelayedMode::With, $objectType);
      }

      /**
       * This method try to load OOP paths that is in delayed mode only
       * @php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False
       * @objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All
       */
      public static function LoadDelayedElements(
            bool $php_as_partial = false,
            int $objectType = PartialEnumerations_ObjectType::All
      ) {
            Loader::InitializeLoadingValues();
            Loader::$php_as_partial = $php_as_partial;

            Loader::LoadAllElements(0, PartialEnumerations_DelayedMode::OnlyDelayed, $objectType);
      }

      private static function LoadAllElements(
            int $maxTemptatives = 1,
            int $loadDelayedElements = PartialEnumerations_DelayedMode::Without,
            int $objectType = PartialEnumerations_ObjectType::All
      ) {
            foreach (Loader::$includedPath as $path)
                  Loader::LoadFromPathString($path, $maxTemptatives, $loadDelayedElements, $objectType);
      }

      /**
       * Add_Ignoring_Path
       * @paths : Specify path(s) who must be load - Can take string or string array - No default value, Required
       */
      public static function AddIgnorePath(mixed $paths)
      {
            Loader::InitializeLoadingValues();

            if (gettype($paths) == _array::ClassName)
                  Loader::$ignoredPath = array_merge(Loader::$ignoredPath, $paths);
            else
                  array_push(Loader::$ignoredPath, $paths);
      }

      /**
       * Add_Including_Path
       * @paths : Specify path(s) who must be load - Can take string or string array - No default value, Required
       */
      public static function AddIncludePath(mixed $paths)
      {
            Loader::InitializeLoadingValues();

            if (gettype($paths) == _array::ClassName)
                  Loader::$includedPath = array_merge(Loader::$includedPath, $paths);
            else
                  array_push(Loader::$includedPath, $paths);
      }

      private static function LoadFromPathString(
            string $path,
            int $maxTemptatives = 1,
            int $loadDelayedElements = PartialEnumerations_DelayedMode::Without,
            int $objectType = PartialEnumerations_ObjectType::All
      ) {
            Loader::$Counter = 0;
            Loader::$dependants = array();

            // Main load
            Loader::$dependants = Loader::LoadFromPath($path, $loadDelayedElements, $objectType);

            for ($attempt = 0; $attempt < $maxTemptatives; $attempt++) {
                  if (count(Loader::$dependants) > 0)
                        Loader::StandardPHP_NewTemptative();

                  Loader::StandardPHP_ClearLoaded();
            }
      }

      private static function LoadFromFile(string $currentPath, PartialElementsCollection &$partialsCollection)
      {
            $ext = pathinfo($currentPath, PATHINFO_EXTENSION);
            $preload = _string::EmptyString;

            switch ($ext) {
                  case Loader::PhpExtension:
                        $preload = Loader::StandardPHP_TryGetContent($currentPath);
                  case Loader::PhpPartialExtension:
                        if (!Loader::PartialPHP_AddToCollection(
                              $partialsCollection,
                              strlen($preload) > 0 ? $preload : file_get_contents($currentPath),
                              $currentPath
                        ))
                              if (Loader::StandardPHP_LoadFile($currentPath))
                                    Loader::$Counter++;
                        break;
            }
      }

      private static function LoadFromPath(
            string $path,
            int $loadDelayedElements = PartialEnumerations_DelayedMode::Without,
            int $objectType = PartialEnumerations_ObjectType::All
      ): array {
            $dependants = array();

            $partialsCollection = new PartialElementsCollection();

            if (is_file($path)) {
                  Loader::LoadFromFile($path, $partialsCollection);
            } else {
                  foreach (scandir($path) as $filename) {
                        $currentPath = $path . '/' . $filename;

                        if (Loader::IsNotLoadable($currentPath, $filename))
                              continue;

                        if (is_file($currentPath)) {
                              Loader::LoadFromFile($currentPath, $partialsCollection);
                        } else if (is_dir($currentPath)) {
                              $dependants = array_merge(
                                    $dependants,
                                    Loader::LoadFromPath($currentPath, $loadDelayedElements, $objectType)
                              );
                        }
                  }
            }

            if ($partialsCollection->count() > 0)
                  Loader::LoadPartialElement($partialsCollection, $loadDelayedElements, $objectType);

            return $dependants;
      }

      private static function LoadPartialElement(
            PartialElementsCollection $partialsCollection,
            int $loadDelayedElements = PartialEnumerations_DelayedMode::Without,
            int $objectType = PartialEnumerations_ObjectType::All
      ) {
            if (Loader::$log_active) Loader::AddToLog(
                  str_replace('{0}', $partialsCollection->GetElementName(), PartialMessages::LogAddPreLoad)
            );

            if ($partialsCollection->CanBeLoad($objectType))
                  if ($partialsCollection->CompilePartials($loadDelayedElements))
                        Loader::$Counter++;

            if (Loader::$log_active) Loader::AddToLog(
                  str_replace('{0}', $partialsCollection->GetElementName(), PartialMessages::LogAddPreLoad)
            );
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
            Loader::CheckPartialMessagesKeysLoaded();

            for ($index = 0; $index < count(Loader::$dependants); $index++) {
                  try {
                        if (Loader::$log_active) Loader::AddToLog(
                              str_replace('{0}', Loader::$dependants[$index], PartialMessages::LogAddPreLoad)
                        );

                        Loader::StandardPHP_LoadDependency(Loader::$dependants[$index]);

                        if (Loader::$log_active) Loader::AddToLog(
                              str_replace('{0}', Loader::$dependants[$index], PartialMessages::LogAddPostLoad)
                        );

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

      private static function CheckPartialMessagesKeysLoaded()
      {
            if (!class_exists("System\Reflection\Dependencies\PartialMessages"))
                  require_once("PartialMessages.php");
      }

      private static function StandardPHP_LoadFile($path): bool
      {
            Loader::CheckPartialMessagesKeysLoaded();

            try {
                  if (Loader::$log_active) Loader::AddToLog(
                        str_replace('{0}', $path, PartialMessages::LogAddPreLoad)
                  );

                  Loader::StandardPHP_LoadDependency($path);

                  if (Loader::$log_active) Loader::AddToLog(
                        str_replace('{0}', $path, PartialMessages::LogAddPostLoad)
                  );

                  return true;
            } catch (\Error $e) {
                  if (Loader::$log_active) Loader::AddToLog($e->getMessage());
                  return false;
            }
      }

      private static function StandardPHP_TryGetContent($path): string
      {
            return Loader::$php_as_partial
                  ? file_get_contents($path)
                  : _string::EmptyString;
      }

      // private static function array_search_partial($arr, $keyword) {
      //       foreach($arr as $index => $string) {
      //             if (strpos($string, $keyword) !== FALSE)
      //                   return true;
      //             }

      //       return false;
      // }

      /*public static function getFullClassNameFromFile($filePath) {
            $phpCode = file_get_contents($filePath);

            // Rechercher le namespace avec une expression régulière
            $namespace = '';
            if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+)\s*;/', $phpCode, $namespaceMatches)) {
                  $namespace = $namespaceMatches[1] . '\\';
            }

            // Rechercher le nom de la classe avec une expression régulière
            if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $phpCode, $classMatches)) {
                  return $namespace . $classMatches[1];
            }

            throw new Exception("Aucune classe trouvée dans le fichier $filePath.");
      }

      public static function requireClassFile($filePath) {
            try {
                  // Obtenir le nom complet de la classe (namespace + nom de classe) à partir du fichier
                  $fullClassName = Loader::getFullClassNameFromFile($filePath);

                  // Vérifier si la classe existe déjà
                  if (class_exists($fullClassName)) {
                        throw new ClassAlreadyExistsException("La classe $fullClassName est déjà définie.");
                  }

                  // Inclure le fichier
                  require $filePath;
                  //echo "Classe $fullClassName incluse avec succès.";
            } catch (ClassAlreadyExistsException $e) {
                  // Gérer l'exception si la classe existe déjà
                  echo 'Erreur : ' . $e->getMessage();
            } catch (Exception $e) {
                  // Gérer les autres exceptions générales
                  echo 'Erreur générale : ' . $e->getMessage();
            }
      }*/

      /*// Utiliser la fonction pour inclure un fichier de classe
      requireClassFile('path/to/your/ClassFile.php');*/

      /**
       * Try to load a standard PHP File
       */
      public static function StandardPHP_LoadDependency($path, bool $OnceOnly = false): bool
      {
            Loader::InitializeLoadingValues();
            $path = realpath(str_replace('/', Loader::$escapesChars[0], $path));

            /*if (strpos($path, "_array.php") > 0)
            {
                  var_dump(get_included_files());
                  var_dump("FILE :> " . $path);
                  var_dump("CORRECTED FILE :> " . str_replace('/', '\\', $path));
                  var_dump("IN_ARRAY : " .
                        in_array(
                              str_replace('/', Loader::$escapesChars[0], $path),
                              get_included_files()
                        ) ? "Trouvé" : "Pas trouvé"
                  );
            }*/

            if (!in_array($path, get_included_files()))
            {
                  try
                  {
                        if ($OnceOnly)
                        {
                              require_once($path);
                        }
                        else
                        {
                              /*if (in_array($path, get_included_files()))
                              {
                                    var_dump(get_included_files());
                              }
                              else
                              {
                                    var_dump($path);
                              }*/

                              if (is_file($path)) require($path);
                        }
                        //Loader::requireClassFile($path);
                  } catch (\Error $err) {
                        //var_dump(get_included_files());
                        echo new \Exception(
                              "Error on loading Standard file " .
                                    $path . " - " .
                                    $err->getMessage() . " (" . $err->getLine() . ") "
                        );
                  }

                  return true;
            }

            return false;
      }

      private static function AddToLog(string $message)
      {
            array_push(Loader::$log, $message);
      }
}
