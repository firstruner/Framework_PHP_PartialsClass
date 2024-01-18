
# 🛠️ Framework PHP - Partials functions
Part of Firstruner Framework for PHP to allow use Partial function with PHP OOP objects

This project is a part of 🛠️ Firstruner Framework for PHP.
It contains a little part of the original Framework that allow you to use "partial" DotNet functionality into PHP structure project and also naturally the Framework loader.

To use, it's very simple !

## ⚗️ PHP version
Actually tested on PHP from 7.1.* to 8.3.*

## Partials version

🌞 v1.0 Initial repository
🌞 v1.1 Uses, inheritance and implementations support
🌞 v1.2 Final and Abstract support for classes
💫 v2.0 🎇 Features :
 1. Interfaces, Enumerations and Trait support
 2. Conversion Enum files for PHP >= 8.1 to Abstract class files for PHP >= 7.1
 3. Fix exception on Composer Update (Tested with Symfony and Laravel project)

## 🧙‍♂️ Loader
Create a folder that contains all of your POO Objects (classes, interfaces, enumerations and other).

    // The only required dependence for Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies.php';
    
    // For multiple use of Loader class
    use System\Reflection\Dependencies\Loader;
    
    // Load all php POO files in "System" Folder
    Loader::Load(__DIR__ . '/System');

## Notes
👉 Loading note : it's recommended to load elements in this ordre :
 1. Enumerations
 2. Interfaces
 3. Classes

👉 File extension note : For standard use, partial file must have "partial_php" extension, but it's possible to use "php" extension if you specify "php_as_partial" attribute to "True" when "Load" method was called.
But use "php" are more lazy because it necessary to load the php file before determine if the file is a partial file.

## How use Partials
To create a php files with partials, create a folder for your OOP object, and create all of your files inside.

### Define a OOP file as partial
#### Call attributes
To define the file as a partial file, you should reference Partials attributes like this :

    use System\Attributes\Partial;

#### Define as partial file
Now define the OOP file as partial with using Partial attribute like this :

    #[Partial]

#### 📚 Full main partial sample

    <?php
    namespace System\Printers;
    
    use System\Attributes\Partial;
    
    #[Partial]
    class ScreenPrinter
    {
          public function PrintInstanceMessage()
          {
                echo "Mon Instance";
          }
    }

### 🔎 Some samples are present in Samples folder

### 📚 Sample 1
##### 📗 File 1

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use \Exception;
    
    #[Partial]
    class  Sample extends MainClass
    {
    }

##### 📘 File 2

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use Symfony\Component\
    {
    	HttpFoundation\Request,
    	Routing\Annotation\Route
    };
    
    #[Partial]
    class  Sample implements OwnInterface
    {
    }

### 📚 Sample 2
##### 📗 File 1

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use \Exception;
    
    #[Partial]
    class  Sample extends MainClass implements 1stInterface, 2ndInterface
    {
    }

##### 📘 File 2

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use Symfony\Component\
    {
    	HttpFoundation\Request,
    	Routing\Annotation\Route
    };
    
    #[Partial]
    class  Sample implements OwnInterface, OtherInterface
    {
    }

## Possible exceptions
It's possible to have compilation exception during a composer update like this :

[![Composer-Exception.png](https://i.postimg.cc/WzsPyvS2/Composer-Exception.png)](https://postimg.cc/MM34cgh4)

To solve that, please use partial_php extension for your partial files and use the Firstruner Framework Loader for load these partial files
