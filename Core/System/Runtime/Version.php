<?php

/**
 * Copyright 2024-2026 Firstruner and Contributors
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
 * @copyright 2024-2026 Firstruner and Contributors
 * @license   Proprietary
 * @version 3.3.0
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
                              $this->Major = (int)$version[$i];
                              if (count($version) == 1) continue 2;
                              break;
                        case 1:
                              $this->Minor = (int)$version[$i];
                              if (count($version) == 2) continue 2;
                              break;
                        case 2:
                              $this->Patch = (string)$version[$i];
                              if (count($version) == 3) continue 2;
                              break;
                        case 3:
                              $this->Build = (string)$version[$i];
                              if (count($version) == 4) continue 2;
                              break;
                        case 4:
                              $this->Tags = explode(",", $version[$i]);
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

      public static function Compare(Version|string $v1, Version|string $v2): int
      {
            if (gettype($v1) == _string::ClassName) $v1 = new Version($v1);
            if (gettype($v2) == _string::ClassName) $v2 = new Version($v2);

            if (!($v1 instanceof Version) || !($v2 instanceof Version))
                  throw new \InvalidArgumentException("Compare attend deux Version ou deux chaînes.");

            if ($v1->Major !== $v2->Major) return ($v1->Major < $v2->Major) ? -1 : 1;
            if ($v1->Minor !== $v2->Minor) return ($v1->Minor < $v2->Minor) ? -1 : 1;

            $p1 = ($v1->Patch !== _string::EmptyString) ? $v1->Patch : "0";
            $p2 = ($v2->Patch !== _string::EmptyString) ? $v2->Patch : "0";

            if (ctype_digit($p1) && ctype_digit($p2)) {
                  $ip1 = (int)$p1;
                  $ip2 = (int)$p2;
                  if ($ip1 !== $ip2) return ($ip1 < $ip2) ? -1 : 1;
            } else {
                  if ($p1 !== $p2) return ($p1 < $p2) ? -1 : 1;
            }

            $b1 = ($v1->Build !== _string::EmptyString) ? $v1->Build : "0";
            $b2 = ($v2->Build !== _string::EmptyString) ? $v2->Build : "0";

            if (ctype_digit($b1) && ctype_digit($b2)) {
                  $ib1 = (int)$b1;
                  $ib2 = (int)$b2;
                  if ($ib1 !== $ib2) return ($ib1 < $ib2) ? -1 : 1;
            } else {
                  if ($b1 !== $b2) return ($b1 < $b2) ? -1 : 1;
            }

            return 0;
      }

      public function IsObsolete(Version|string $latestVersion): bool
      {
            if (gettype($latestVersion) == _string::ClassName) $latestVersion = new Version($latestVersion);
            return self::Compare($this, $latestVersion) < 0;
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
