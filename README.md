
# 🛠️ Framework PHP - Partials functions
Part of Firstruner Framework for PHP to allow use Partial function with PHP OOP objects

This project is a part of 🛠️ Firstruner Framework for PHP.
It contains a little part of the original Framework that allow you to use "partial" DotNet functionality into PHP structure project and also naturally the Framework loader.

To use, it's very simple !

## ⚗️ PHP version
Actually tested on PHP from 7.1.* to 8.3.*

## Partials versions
\
🌞 v1.0 Initial repository\
🌞 v1.1 Uses, inheritance and implementations support\
🌞 v1.2 Final and Abstract support for classes\
💫 v2.0 🎇 Features :\
 1. Interfaces, Enumerations and Trait support
 2. Conversion Enum files for PHP >= 8.1 to Abstract class files for PHP >= 7.1
 3. Fix exception on Composer Update (Tested with Symfony and Laravel project)
 4. Integrate array path for loading and ignored function

## 🧙‍♂️ Loader (main method)
Create a folder that contains all of your POO Objects (classes, interfaces, enumerations and other).

    // The only required dependence for Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies.php';
    
    // For multiple use of Loader class
    use System\Reflection\Dependencies\Loader;
    
    // Load all php POO files in "System" Folder
    Loader::Load(__DIR__ . '/System');

🔰 Load function of Loader class can take in 1st argument a single string or an array of string,\

🔰 The 4th argument is also take a single string or an array of string to ignore some path in the path scanned. Ignored paths must be a physic path like : c:\httpserver\htdocs\myproject\classes\ingoredClasses

📓 For all other method, go to "All Loader Methods" at the bottom of this documentation 👇 or consult pdf include

## 🧙‍♂️ Load with FluentLoader
This Firstruner Framework contains also a partial loader which is can use in fluent mode.

    // The only required dependence for Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies.php';
    
    // For multiple use of Loader class
    use System\Reflection\Dependencies\FluentLoader;
    
    // Load all php POO files in "System" Folder
    $fluentLoader = new FluentLoader();
    $fluentLoader->SetLogActivation(true)->Load("MyOOP_Directory/Classes")->SetLogActivation(false)->Add_Including_Path(__DIR__ . '/System')->LoadStoredPaths();

## Notes
👉 Loading note : it's recommended to load elements in this ordre :
 1. Enumerations
 2. Interfaces
 3. Classes

👉 File extension note : For standard use, partial file must have "partial_php" extension, but it's possible to use "php" extension if you specify "php_as_partial" attribute to "True" when "Load" method was called.
But use "php" are more lazy because it necessary to load the php file before determine if the file is a partial file.

## Performances
📈 For better performances, use partial_php extension for your files and DO NOT set php_as_partial argument in Load function as True.\
\
📈 It recommended if you have a project with multiple target to separate you partial classes of your projects

## IDE integration
### VS Code
⚙️ Go in File menu > Preferences > Settings.\
In "File editor" section, add "*.partial_php" use like "php" in file association item

## How use Partials on OOP object
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

## All Loader Methods
⚓ Load method :\
    ℹ️ Main OOP loading method, it can call directly.\
    ✏️ included : Specify path(s) who must be load - Can take string or string array - No default value, Required\
    ✏️ maxTemptatives : Specify the number of loading temptatives - int - default value is 1\
    ✏️ php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False\
    ✏️ ignored : Specify path(s) who must be ignored during the loading - Can take string or string array - default value is an empty array\

⚓ LoadStoredPaths method :\
    ℹ️ This method try to load OOP paths that specify with Load method or AddIncludePath\
    ✏️ maxTemptatives : Specify the number of loading temptatives - int - default value is 1\

⚓ AddIncludePath method :\
    ℹ️ This method add OOP paths for Loading. It use before call LoadStoredPaths method\
    ✏️ paths : Specify path(s) who must be load - Can take string or string array - No default value, Required\

⚓ AddIgnorePath method :\
    ℹ️ This method add OOP paths who must be ignore during Loading. It use before call LoadStoredPaths method\
    ✏️ paths : Specify path(s) who must be load - Can take string or string array - No default value, Required\

⚓ StandardPHP_LoadDependency method :\
    ℹ️ This method try to load as 'require' a specific php file path\
    ✏️ paths : Specify path who must be load - String - No default value, Required\

⚓ Clear method :\
    ℹ️ This method clear Loader parameters\

⚓ GetLastDependenciesCount method :\
    ℹ️ This method return dependencies who were well loaded\

⚓ SetLogActivation method :\
    ℹ️ This method specify if Loader use a log during loading\
    ✏️ active : Boolean - No default value, Required\

⚓ GetLog method :\
    ℹ️ This method return string array about log events\

## Possible exceptions
It's possible to have compilation exception during a composer update like this :

[![Composer-Exception.png](https://i.postimg.cc/WzsPyvS2/Composer-Exception.png)](https://postimg.cc/MM34cgh4)

To solve that, please use partial_php extension for your partial files and use the Firstruner Framework Loader for load these partial files
