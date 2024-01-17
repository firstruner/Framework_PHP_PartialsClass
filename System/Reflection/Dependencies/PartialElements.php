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

final class PartialElements
{
      readonly string $Namespace;
      readonly string $Uses;
      readonly string $ElementName;
      readonly string $Extends;
      readonly string $Implements;
      readonly string $Content;
      readonly string $Tag_File;

      public bool $isAbstract = false;
      public bool $isFinal = false;

      private int $objectType;
      private string $header;

      private const class_Pattern = "/\bclass\s+([a-zA-Z0-9_-])+/";
      private const interface_Pattern = "/\binterface\s+([a-zA-Z0-9_-])+/";
      private const trait_Pattern = "/\btrait\s+([a-zA-Z0-9_-])+/";
      private const enum_Pattern = "/\benum\s+([a-zA-Z0-9_-])+/";
      private const empty_Pattern = "/\b([a-zA-Z0-9_-])+/";

      function __construct(string $content, string $tagFile)
      {
            $this->Tag_File = $tagFile;
            $this->objectType = PartialEnumerations_ObjectType::_Other;
            $this->Content = $this->extractContents($content);

            $this->detectClassHeaders(
                  substr(
                        $content,
                        0,
                        strpos($content, '{', strpos($content, PartialConstants::Partial_Attribute))
                  )
            );
      }

      public function getObjectType() : int
      {
            return $this->objectType;
      }

      private function getNamespace(string $headers): string
      {
            $namespaceStart = strpos($headers, PartialConstants::Tag_Namespace)
                  + strlen(PartialConstants::Tag_Namespace);

            return substr(
                  $headers,
                  $namespaceStart,
                  strpos($headers, ';', $namespaceStart) - $namespaceStart
            );
      }

      private function getUses(string $headers): string
      {
            preg_match_all("/\buse\s+([\\a-zA-Z0-9_{}\\\\]+)\s*;/", $headers, $matches); //, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $match) {
                  if (strlen($match) > 0)
                        return $match;
            }

            return "";
      }

      private function setElementType()
      {
            if (preg_match(
                  $this::class_Pattern,
                  $this->header) > 0)
                  $this->objectType = PartialEnumerations_ObjectType::_Class;

            if (preg_match(
                  $this::interface_Pattern,
                  $this->header) > 0)
                  $this->objectType = PartialEnumerations_ObjectType::_Interface;

            if (preg_match(
                  $this::trait_Pattern,
                  $this->header) > 0)
                  $this->objectType = PartialEnumerations_ObjectType::_Trait;

            if (preg_match(
                  $this::enum_Pattern,
                  $this->header) > 0)
                  $this->objectType = PartialEnumerations_ObjectType::_Enumeration;
      }

      private function getElementPattern() : string
      {
            switch ($this->objectType)
            {
                  case PartialEnumerations_ObjectType::_Class;
                        return $this::class_Pattern;
                  case PartialEnumerations_ObjectType::_Interface;
                        return $this::interface_Pattern;
                  case PartialEnumerations_ObjectType::_Trait;
                        return $this::trait_Pattern;
                  case PartialEnumerations_ObjectType::_Enumeration;
                        return $this::enum_Pattern;
                  default:
                        return $this::empty_Pattern;                  
            }
      }

      public function getHeaderTag() : string
      {
            switch ($this->objectType)
            {
                  case PartialEnumerations_ObjectType::_Class;
                        return PartialConstants::Tag_Class;
                  case PartialEnumerations_ObjectType::_Interface;
                        return PartialConstants::Tag_Implements;
                  case PartialEnumerations_ObjectType::_Trait;
                        return PartialConstants::Tag_Trait;
                  case PartialEnumerations_ObjectType::_Enumeration;
                        return PartialConstants::Tag_Enum;
                  default:
                        return "";                  
            }
      }

      private function getElementName(string $headers) : string
      {
            preg_match(
                  $this->getElementPattern(),
                  $this->header,
                  $class_match);

            if (count($class_match) > 0)
            {
                  $this->isAbstract |= (strpos($headers, 'abstract ' . $class_match[0]) > 0);
                  $this->isFinal |= (strpos($headers, 'final ' . $class_match[0]) > 0);
                  
                  return str_replace($this->getHeaderTag(), "", $class_match[0]);
            }

            return "";
      }

      private function getInheritsNames(string $headers): string
      {
            $extendsPattern = "/\bextends\s+([\\a-zA-Z0-9_\\\\]+)/";
            preg_match($extendsPattern, $headers, $extends_match);

            return (count($extends_match) > 1
                  ? substr(
                        $extends_match[1],
                        0,
                        (strpos($extends_match[1], PartialConstants::Tag_Implements) > 0
                              ? strpos($extends_match[1], PartialConstants::Tag_Implements)
                              : strlen($extends_match[1]))
                  )
                  : "");
      }

      private function getImplementsNames(string $headers): string
      {
            preg_match_all("/\bimplements\s+([\\a-zA-Z0-9_\\\\]+((\s)*(,)*(\s)*))/", $headers, $matches);

            return (count($matches) > 1
                  ? (count($matches[1]) > 0 ? $matches[1][0] : "")
                  : "");
      }

      private function detectClassHeaders(string $headers)
      {
            $this->header = substr($headers,
                  strpos($headers, PartialConstants::Partial_Attribute));
            
            $this->setElementType();

            $this->Namespace = $this->getNamespace($headers);
            $this->Uses = $this->getUses($headers);
            $this->ElementName = $this->getElementName($headers);
            $this->Extends = $this->getInheritsNames($headers);
            $this->Implements = $this->getImplementsNames($headers);
      }

      private function extractContents(string $content): string
      {
            $indexStart = strpos(
                  $content,
                  '{',
                  strpos($content, PartialConstants::Partial_Attribute)
            ) + 1;

            return substr(
                  $content,
                  $indexStart,
                  strrpos($content, '}') - $indexStart
            );
      }
}
