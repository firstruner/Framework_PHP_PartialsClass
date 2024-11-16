<?php

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
 * @license   Proprietary
 * @version 2.0.0
 */

namespace System\Runtime;

use System\Default\_string;

final class Version
{
      public int $Major = 0;
      public int $Minor = 0;
      public string $Patch = _string::EmptyString;
      public string $Build = _string::EmptyString;
      public array $Tags = [];

      function __construct()
      {
            if ((func_num_args() == 1) && (gettype(func_get_args()[0]) == _string::ClassName)) {
                  $this->__ctorFromStringTemplate(func_get_args()[0]);
            } else {
                  $this->__ctorFromDirectValues(
                        func_get_args()[0],
                        func_get_args()[1],
                        (func_num_args() >= 3 ? func_get_args()[2] : ""),
                        (func_num_args() >= 4 ? func_get_args()[3] : ""),
                        (func_num_args() == 5 ? func_get_args()[4] : []),
                  );
            }
      }

      private function __ctorFromStringTemplate(string $stringtemplate)
      {
            $version = explode(".", $stringtemplate);

            for ($i = 0; $i < count($version); $i++) {
                  switch ($i) {
                        case 0:
                              $this->Major = $version[$i];
                              if (count($version) == 1) continue 2;
                              break;
                        case 1:
                              $this->Minor = $version[$i];
                              if (count($version) == 2) continue 2;
                              break;
                        case 2:
                              $this->Patch = $version[$i];
                              if (count($version) == 3) continue 2;
                              break;
                        case 3:
                              $this->Build = $version[$i];
                              if (count($version) == 4) continue 2;
                              break;
                        case 4:
                              $this->Tags = $version[$i];
                              if (count($version) == 5) continue 2;
                              break;
                  }
            }
      }

      private function __ctorFromDirectValues(
            int $_major,
            int $_minor,
            string $_patch = _string::EmptyString,
            string $_build = _string::EmptyString,
            array $_tags = []
      ) {
            $this->Major = $_major;
            $this->Minor = $_minor;
            $this->Patch = $_patch;
            $this->Build = $_build;
            $this->Tags = $_tags;
      }

      function __toString()
      {
            return $this->Major .
                  '.' . $this->Minor .
                  ($this->Patch != _string::EmptyString ? '.' . $this->Patch : "") .
                  ($this->Build != _string::EmptyString ? '-' . $this->Build : "") .
                  (count($this->Tags) > 0
                        ? ' (' . implode(', ', $this->Tags) . ")"
                        : "");
      }
}
