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

namespace System\Default;

class _datetime
{
      public const ClassName = "datetime";

      public static function MaxValue(): \DateTime
      {
            $value = new \DateTime();
            $value->setDate(9999, 12, 31);
            $value->setTime(23, 59, 59, 999999);

            return $value;
      }

      public static function MinValue(): \DateTime
      {
            $value = new \DateTime();
            $value->setDate(1900, 1, 1);
            $value->setTime(0, 0, 0, 0);

            return $value;
      }
}
