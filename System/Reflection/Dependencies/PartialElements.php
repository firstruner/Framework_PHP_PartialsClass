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
      readonly string $ClassName;
      readonly string $Extends;
      readonly string $Implements;
      readonly string $Content;
      readonly string $Tag_File;

      function __construct(string $content, string $tagFile)
      {
            $this->detectClassHeaders(
                  substr(
                        $content,
                        0,
                        strpos($content, '{', strpos($content, Partial_Attribute))
                  )
            );

            $this->Tag_File = $tagFile;
            $this->Content = $this->extractContents($content);
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

      private function getClassName(string $headers): string
      {
            $classPattern = "/\bclass\s+([a-zA-Z0-9_-])+/";
            preg_match($classPattern, $headers, $class_match);

            return (count($class_match) > 0
                  ? str_replace(PartialConstants::Tag_Class, "", $class_match[0])
                  : "");
      }

      private function getInheritsNames(string $headers): string
      {
            $extendsPattern = "/\bextends\s+([\\a-zA-Z0-9_\\\\]+)/";
            preg_match($extendsPattern, $headers, $extends_match);

            return (count($extends_match) > 1
                  ? substr(
                        $extends_match[1],
                        0,
                        (strpos($extends_match[1], PartialConstants::Tag_Interfaces) > 0
                              ? strpos($extends_match[1], PartialConstants::Tag_Interfaces)
                              : strlen($extends_match[1]))
                  )
                  : "");
      }

      private function getInterfaceNames(string $headers): string
      {
            preg_match_all("/\bimplements\s+([\\a-zA-Z0-9_\\\\]+((\s)*(,)*(\s)*))/", $headers, $matches);

            return (count($matches) > 1
                  ? (count($matches[1]) > 0 ? $matches[1][0] : "")
                  : "");
      }

      private function detectClassHeaders(string $headers)
      {
            $this->Namespace = $this->getNamespace($headers);
            $this->Uses = $this->getUses($headers);
            $this->ClassName = $this->getClassName($headers);
            $this->Extends = $this->getInheritsNames($headers);
            $this->Implements = $this->getInterfaceNames($headers);
      }

      private function extractContents(string $content): string
      {
            $indexStart = strpos(
                  $content,
                  '{',
                  strpos($content, Partial_Attribute)
            ) + 1;

            return substr(
                  $content,
                  $indexStart,
                  strrpos($content, '}') - $indexStart
            );
      }
}
