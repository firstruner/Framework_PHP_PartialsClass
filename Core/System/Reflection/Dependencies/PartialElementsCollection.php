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

use Exception;
use Iterator;
use System\Environment\PHP;

final class PartialElementsCollection implements Iterator
{
      private const UsePartial = "use System\Attributes\Partial;";

      private int $position = 0;
      private array $elements = array();

      public function __construct()
      {
            $this->position = 0;
      }

      public function rewind(): void
      {
            $this->position = 0;
      }

      public function current(): mixed
      {
            return $this->elements[$this->position];
      }

      public function key(): mixed
      {
            return $this->position;
      }

      public function next(): void
      {
            ++$this->position;
      }

      public function valid(): bool
      {
            return isset($this->elements[$this->position]);
      }

      public function add(PartialElements $element)
      {
            array_push($this->elements, $element);
      }

      public function count(): int
      {
            return count($this->elements);
      }

      public function GetElementName(): string
      {
            if ($this->count() == 0) return "no elements in collection";

            return $this->elements[0]->GetCommonName();
      }

      private function extendsCompiler(int $compileType, $currentContent, $partial): string
      {
            $incorpoElement = "";
            $prefix = "";

            switch ($compileType) {
                  case PartialEnumerations_Element::_Extends:
                        $incorpoElement = $partial->Extends;
                        $prefix = (strlen($currentContent) > 0 ? ", " : PartialConstants::Tag_Extends);
                        break;
                  case PartialEnumerations_Element::_Implements:
                        $incorpoElement = $partial->Implements;
                        $prefix = (strlen($currentContent) > 0 ? ", " : PartialConstants::Tag_Implements);
                        break;
            }

            if (strlen($incorpoElement) == 0) return "";

            return
                  $partial->Tag_File . PHP_EOL .
                  $prefix . PHP_EOL .
                  $incorpoElement;
      }

      private function isAbstractClass(): bool
      {
            foreach ($this->elements as $elem)
                  if ($elem->isAbstract) return true;

            return false;
      }

      private function isFinalClass(): bool
      {
            foreach ($this->elements as $elem)
                  if ($elem->isFinal) return true;

            return false;
      }

      private function FinalAbstractRulesValids(): bool
      {
            if ($this->elements[0]->getObjectType() == PartialEnumerations_ObjectType::_Class) {
                  if ($this->isAbstractClass() && $this->isFinalClass())
                        throw new Exception(
                              PartialMessages::ExceptionOnFinalAndAbstractClass .
                                    " on " . $this->elements[0]->ElementName
                        );
            } else {
                  if ($this->isAbstractClass() || $this->isFinalClass())
                        throw new Exception(
                              PartialMessages::ExceptionOnFinalOrAbstractObject .
                                    " on " . $this->elements[0]->ElementName
                        );
            }

            return true;
      }

      private function isOldPHPVersion(): bool
      {
            $php_version = PHP::getCurrentVersion();

            return ($php_version->Major < 8)
                  || (($php_version->Major == 8)
                        && ($php_version->Minor < 1));
      }

      private function isDelayedElement(): bool
      {
            foreach ($this->elements as $elem)
                  if ($elem->DelayedLoading) return true;

            return false;
      }

      private function EnumClassHeaderAdapter(string $elementName): string
      {
            preg_replace('/\s\s+/', ' ', $elementName);

            if (
                  $this->isOldPHPVersion()
                  && ($this->elements[0]->getObjectType() == PartialEnumerations_ObjectType::_Enumeration)
            )
                  return str_replace(
                        PartialConstants::Tag_Enum,
                        PartialConstants::Tag_AbstractClass,
                        $elementName
                  );

            return $elementName;
      }

      private function EnumClassContentAdapter(string $content): string
      {
            preg_replace('/\s\s+/', ' ', $content);

            if (
                  $this->isOldPHPVersion()
                  && ($this->elements[0]->getObjectType() == PartialEnumerations_ObjectType::_Enumeration)
            )
                  return str_replace(
                        PartialConstants::Tag_EnumCaseTag,
                        PartialConstants::Tag_AbstractClassConstTag,
                        $content
                  );

            return $content;
      }

      public function CanBeLoad(int $objectType = PartialEnumerations_ObjectType::All): bool
      {
            if ($objectType == PartialEnumerations_ObjectType::All) return true;

            return ($this->elements[0]->getObjectType()) == $objectType;
      }

      public function CompilePartials(int $loadDelayedElements = PartialEnumerations_DelayedMode::Without): bool
      {
            $this->FinalAbstractRulesValids();

            if ((($loadDelayedElements == PartialEnumerations_DelayedMode::Without)
                        && $this->isDelayedElement())
                  || (($loadDelayedElements == PartialEnumerations_DelayedMode::OnPost)
                        && $this->isDelayedElement())
                  || (($loadDelayedElements == PartialEnumerations_DelayedMode::OnlyDelayed)
                        && !$this->isDelayedElement())
            )
                  return true;

            $Namespace = ($this->elements[0]->Namespace != ""
                  ? PartialConstants::Tag_Namespace . $this->elements[0]->Namespace . ';' . PHP_EOL
                  : "");

            $ElementName =
                  ($this->isFinalClass() ? "final " : "") .
                  ($this->isAbstractClass() ? "abstract " : "") .
                  $this->elements[0]->getHeaderTag() . $this->elements[0]->ElementName . PHP_EOL;

            $Uses = "";
            $Extends = "";
            $Implements = "";
            $Contents = "";

            foreach ($this->elements as $partial) {
                  $Uses .= $partial->Tag_File . PHP_EOL . $partial->Uses . PHP_EOL;
                  $Extends .= $this->extendsCompiler(PartialEnumerations_Element::_Extends, $Extends, $partial);
                  $Implements .= $this->extendsCompiler(PartialEnumerations_Element::_Implements, $Implements, $partial);
                  $Contents .= $partial->Tag_File . PHP_EOL . $partial->Content . PHP_EOL;
            }

            $Uses = str_replace(PartialElementsCollection::UsePartial, "", $Uses);

            // Managing php < 8.1
            $ElementName = $this->EnumClassHeaderAdapter($ElementName);
            $Contents = $this->EnumClassContentAdapter($Contents);

            return $this->AssemblyAndEvaluate(
                  $Namespace,
                  $Uses,
                  $ElementName,
                  $Extends,
                  $Implements,
                  $Contents,
                  $this->elements[0]->GetFullName(),
                  $this->elements[0]->getObjectType()
            );
      }

      private function AssemblyAndEvaluate(
            string $Namespace,
            string $Uses,
            string $ElementName,
            string $Extends,
            string $Implements,
            string $Contents,
            string $OOPFullName,
            string $ElementType
      ): bool {
            $finalClass =
                  $Namespace . PHP_EOL .
                  $Uses . PHP_EOL .
                  $ElementName . " " . $Extends . " " . $Implements . PHP_EOL .
                  "{" . PHP_EOL . $Contents . PHP_EOL . "}";

            // if (strpos($finalClass, 'class Guid'))
            //       var_dump($finalClass);

            try {
                  switch ($ElementType) {
                        case PartialEnumerations_ObjectType::_Class:
                              if (class_exists($OOPFullName)) return true;
                              break;
                        case PartialEnumerations_ObjectType::_Enumeration:
                              if (enum_exists($OOPFullName)) return true;
                              break;
                        case PartialEnumerations_ObjectType::_Interface:
                              if (interface_exists($OOPFullName)) return true;
                              break;
                        case PartialEnumerations_ObjectType::_Trait:
                              if (trait_exists($OOPFullName)) return true;
                              break;
                        default:
                              break;
                  }

                  eval($finalClass);
                  return true;
            } catch (\Error $err) {
                  echo new \Exception(
                        PartialMessages::ExceptionOnLoading .
                              " on " . $ElementName . " - " .
                              $err->getMessage()
                  );
            }

            return false;
      }
}
