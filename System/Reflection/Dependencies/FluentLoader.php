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
      function __construct()
      {
            require_once 'Loader.php';
      }

      public function Add_Including_Path(mixed $paths) : FluentLoader
      {
            Loader::AddIncludePath($paths);
            return $this;
      }

      public function Add_Ignoring_Path(mixed $paths) : FluentLoader
      {
            Loader::AddIgnorePath($paths);
            return $this;
      }

      public function Clear() : FluentLoader
      {
            Loader::Clear();
            return $this;
      }

      public function SetLogActivation(bool $active) : FluentLoader
      {
            Loader::SetLogActivation($active);
            return $this;
      }

      public function Load(mixed $included, int $maxTemptatives = 1,
            $php_as_partial = false, mixed $ignored = array()) : FluentLoader
      {
            Loader::Load($included, $maxTemptatives, $php_as_partial, $ignored);
            return $this;
      }

      public function LoadStoredPaths(int $maxTemptatives = 1, bool $php_as_partial = false) : FluentLoader
      {
            Loader::LoadStoredPaths($maxTemptatives, $php_as_partial);
            return $this;
      }
}