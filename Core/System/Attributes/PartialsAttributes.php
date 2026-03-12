<?php

/**
 * This file is a part of Firstruner Framework for PHP
 */

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
 * @license   https://wikipedia.org/wiki/Freemium Freemium License
 * @version 3.3.0
 */

namespace System\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Partial
{
      public bool $delayedLoading = false;

      public function __construct(bool $delayedLoading = false)
      {
            $this->delayedLoading = $delayedLoading;
      }
}
