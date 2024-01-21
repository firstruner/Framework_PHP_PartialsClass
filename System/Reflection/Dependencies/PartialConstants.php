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

abstract class PartialConstants
{
      const Tag_Namespace = "namespace ";
      const Tag_Class = "class ";
      const Tag_Enum = "enum ";
      const Tag_EnumCaseTag = "case ";
      const Tag_AbstractClass = "abstract class ";
      const Tag_AbstractClassConstTag = "const ";
      const Tag_Trait = "trait ";
      const Tag_Interface = "interface ";
      const Tag_Implements = "implements ";
      const Tag_Extends = "extends ";
      const Tag_Use = "use ";
      const Partial_Attribute = "#" . "[Partial";
      const Partial_Attribute_DelayedOption = "delayedLoading";
}
