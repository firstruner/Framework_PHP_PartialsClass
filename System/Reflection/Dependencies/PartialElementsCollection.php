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
 * @version 1.2.0
 */

namespace System\Reflection\Dependencies;

use Iterator;

final class PartialElementsCollection implements Iterator
{
      private const ExceptionLoadPartialMessage = "Error when loading partials class file : ";
      private const UsePartial = "use System\Attributes\Partial;";

      private int $position = 0;
      private array $elements = array();

      public function __construct() {
            $this->position = 0;
      }

      public function rewind(): void {
            $this->position = 0;
      }

      public function current() {
            return $this->elements[$this->position];
      }

      public function key() {
            return $this->position;
      }

      public function next(): void {
            ++$this->position;
      }

      public function valid(): bool {
            return isset($this->elements[$this->position]);
      }

      public function add(PartialElements $element)
      {
            array_push($this->elements, $element);
      }

      public function count() : int
      {
            return count($this->elements);
      }

      private function extendsCompiler(int $compileType, $currentContent, $partial) : string
      {
            $incorpoElement = "";
            $prefix = "";

            switch ($compileType) {
                  case PartialEnum::_Extends:
                        $incorpoElement = $partial->Extends;
                        $prefix = (strlen($currentContent) > 0 ? ", " : PartialConst::Tag_Extends);
                        break;
                  case PartialEnum::_Implements:
                        $incorpoElement = $partial->Implements;
                        $prefix = (strlen($currentContent) > 0 ? ", " : PartialConst::Tag_Interfaces);
                        break;
            }

            if (strlen($incorpoElement) == 0) return "";

            return
                  $partial->Tag_File . PHP_EOL .
                  $prefix . PHP_EOL .
                  $incorpoElement;
      }

      public function CompilePartials() : bool
      {
            $Namespace = PartialConst::Tag_Namespace . $this->elements[0]->Namespace . ';' . PHP_EOL;
            $ClassName = PartialConst::Tag_Class . $this->elements[0]->ClassName . PHP_EOL;

            $Uses = "";
            $Extends = "";
            $Implements = "";
            $Contents = "";

            foreach ($this->elements as $partial) {
                  $Uses .= $partial->Tag_File . PHP_EOL . $partial->Uses . PHP_EOL;
                  $Extends .= $this->extendsCompiler(PartialEnum::_Extends, $Extends, $partial);
                  $Implements .= $this->extendsCompiler(PartialEnum::_Implements, $Implements, $partial);
                  $Contents .= $partial->Tag_File . PHP_EOL . $partial->Content . PHP_EOL;
            }

            $Uses = str_replace(PartialElementsCollection::UsePartial, "", $Uses);

            return $this->AssemblyAndEvaluate(
                  $Namespace,
                  $Uses,
                  $ClassName,
                  $Extends,
                  $Implements,
                  $Contents
            );
      }

      private function AssemblyAndEvaluate(
            $Namespace,
            $Uses,
            $ClassName,
            $Extends,
            $Implements,
            $Contents
      ) : bool {
            $finalClass =
                  $Namespace . PHP_EOL .
                  $Uses . PHP_EOL .
                  $ClassName . " " . $Extends . " " . $Implements . PHP_EOL .
                  "{" . PHP_EOL . $Contents . PHP_EOL . "}";

            try {
                  eval($finalClass);
                  return true;
            } catch (\Error $e) {
                  echo new \Exception(PartialElementsCollection::ExceptionLoadPartialMessage .
                        $e->getMessage());
            }
      }
}