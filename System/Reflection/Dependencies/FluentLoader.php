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

final class FluentLoader
{
      private PartialEnumerations_ObjectType $objectTypeFilter = PartialEnumerations_ObjectType::None;

      function __construct()
      {
            require_once 'Loader.php';
      }

      /**
       * Define filter about partial element find during the load
       */
      public function SetObjectTypeFilter(int $objectType)
      {
            $this->objectTypeFilter = $objectType;
      }

      /**
       * Add_Including_Path
       * @paths : Specify path(s) who must be load - Can take string or string array - No default value, Required
       */
      public function Add_Including_Path(mixed $paths) : FluentLoader
      {
            Loader::AddIncludePath($paths);
            return $this;
      }

      /**
       * Add_Ignoring_Path
       * @paths : Specify path(s) who must be load - Can take string or string array - No default value, Required
       */
      public function Add_Ignoring_Path(mixed $paths) : FluentLoader
      {
            Loader::AddIgnorePath($paths);
            return $this;
      }

      /**
       * Clear the loader
       */
      public function Clear() : FluentLoader
      {
            Loader::Clear();
            return $this;
      }

      /**
       * Set log activation
       */
      public function SetLogActivation(bool $active) : FluentLoader
      {
            Loader::SetLogActivation($active);
            return $this;
      }

      private function getFilter(int $objectType) : int
      {
            if ($this->objectTypeFilter == PartialEnumerations_ObjectType::None)
                  return $objectType;

            if (($objectType == PartialEnumerations_ObjectType::All)
                  && ($this->objectTypeFilter != PartialEnumerations_ObjectType::None))
                  return $this->objectTypeFilter;
            
            return $objectType;
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
      public function Load(mixed $included, int $maxTemptatives = 1,
            bool $php_as_partial = false, mixed $ignored = array(),
            bool $loadDelayedElements = false,
            int $objectType = PartialEnumerations_ObjectType::All) : FluentLoader
      {
            Loader::Load($included, $maxTemptatives, $php_as_partial,
                  $ignored, $loadDelayedElements,
                  $this->getFilter($objectType));
            return $this;
      }

      /**
       * This method try to load OOP paths that specify with Load method or AddIncludePath
       * @maxTemptatives : Specify the number of loading temptatives - int - default value is 1
       * @php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False
       * @objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All
       */
      public function LoadStoredPaths(
            int $maxTemptatives = 1, bool $php_as_partial = false,
            int $objectType = PartialEnumerations_ObjectType::All) : FluentLoader
      {
            Loader::LoadStoredPaths($maxTemptatives, $php_as_partial,
                  $this->getFilter($objectType));
            return $this;
      }

      /**
       * This method try to load OOP paths that is in delayed mode only
       * @php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False
       * @objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All
       */
      public function LoadDelayedElements(bool $php_as_partial = false,
            int $objectType = PartialEnumerations_ObjectType::All) : FluentLoader
      {
            Loader::LoadDelayedElements($php_as_partial,
                  $this->getFilter($objectType));
            return $this;
      }
}