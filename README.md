
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
 5. Implement a fluent class for load partial OOP
 6. Implement log possibilities
 7. Implement delayed loading possibilities

## 🧙‍♂️ Loader (main method)
Create a folder that contains all of your POO Objects (classes, interfaces, enumerations and other).

    // The only required dependence for use Loader of Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies/Loader.php';
    
    // For multiple use of Loader class
    use System\Reflection\Dependencies\Loader;
    
    // Load all php POO files in "MyOOP" Folder
    Loader::Load(__DIR__ . '/MyOOP');

🔰 Load function of Loader class can take in 1st argument a single string or an array of string,\

🔰 The 4th argument is also take a single string or an array of string to ignore some path in the path scanned. Ignored paths must be a physic path like : c:\httpserver\htdocs\myproject\classes\ingoredClasses

📓 For all other method, go to "All Loader Methods" at the bottom of this documentation 👇 or consult pdf include

## 🧙‍♂️ Load with FluentLoader
This Firstruner Framework contains also a partial loader which is can use in fluent mode.

    // The only required dependence for use FluentLoader of Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies/FluentLoader.php';
    
    // For simplify usage of FluentLoader class
    use System\Reflection\Dependencies\FluentLoader;
    
    // Load all php POO files in "MyOOP_Directory/Classes" and "MyOOP" Folders
    $fluentLoader = new FluentLoader();
    $fluentLoader->SetLogActivation(true)->Load("MyOOP_Directory/Classes")->SetLogActivation(false)->Add_Including_Path(__DIR__ . '/MyOOP')->LoadStoredPaths();

## Notes
💡 Pay close attention to the loading order !

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
[![vscode-menu.png](https://i.postimg.cc/rybT6yh8/vscode-menu.png)](https://postimg.cc/14p2wShT)\
[![vscode-ext.png](https://i.postimg.cc/MTTC4gVM/vscode-ext.png)](https://postimg.cc/wtC4RfWg)\

## How use Partials on OOP object
💡 To create a php files with partials, create a folder for your OOP object, and create all of your files inside.

### 👨‍🏭 Define a OOP file as partial
#### Call attributes
To define the file as a partial file, you should reference Partials attributes like this :

    use System\Attributes\Partial;

#### Define as partial file
Now define the OOP file as partial with using Partial attribute like this :

    #[Partial]

#### Delayed loading
If you need to load OOP file later, you can specify the element with "delayedLoading" at True, like this :\

    #[Partial(true)]
    Or
    #[Partial(delayedLoading: true)]

For load delayed elements, use "LoadStoredPaths" method or specify at True "loadDelayedElements" argument on "Load" method

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

## All Loader Methods available from Loader static class or from FluentLoader class
⚓ Load method :\
>ℹ️ Main OOP loading method, it can call directly.\
>✏️ included : Specify path(s) who must be load - Can take string or string array - No default value, Required\
>✏️ maxTemptatives : Specify the number of loading temptatives - int - default value is 1\
>✏️ php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False\
>✏️ ignored : Specify path(s) who must be ignored during the loading - Can take string or string array - default value is an empty array\
>✏️ loadDelayedElements : Specify if the loader load partial class that specified as "delayedLoading" at True - Boolean - default value is Without\
>    OnPost work similar than Without but force loading after non delayed\
>✏️ loadDelayedElements : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All\

⚓ LoadStoredPaths method :\
>ℹ️ This method try to load OOP paths that specify with Load method or AddIncludePath\
>✏️ maxTemptatives : Specify the number of loading temptatives - int - default value is 1\
>✏️ php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False\
>✏️ loadDelayedElements : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All\

⚓ LoadDelayedElements method :\
>ℹ️ This method try to load OOP paths that is in delayed mode only\
>✏️ php_as_partial : Specify if partial class is in php files with php extension - Boolean - default value is False\
>✏️ loadDelayedElements : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::All\

⚓ AddIncludePath method :\
>ℹ️ This method add OOP paths for Loading. It use before call LoadStoredPaths method\
>✏️ paths : Specify path(s) who must be load - Can take string or string array - No default value, Required\

⚓ AddIgnorePath method :\
>ℹ️ This method add OOP paths who must be ignore during Loading. It use before call LoadStoredPaths method\
>✏️ paths : Specify path(s) who must be load - Can take string or string array - No default value, Required\

⚓ StandardPHP_LoadDependency method :\
>ℹ️ This method try to load as 'require' a specific php file path\
>✏️ paths : Specify path who must be load - String - No default value, Required\
>🔔 Only available from static class - work also when FluentClass is consume

⚓ SetObjectTypeFilter method :\
>ℹ️ This method defun filter loader\
>✏️ objectType : Specify object who the loader must load - Default value is PartialEnumerations_ObjectType::None, Required\
>🔔 Only available from fluent class

⚓ Clear method :\
>ℹ️ This method clear Loader parameters\

⚓ GetLastDependenciesCount method :\
>ℹ️ This method return dependencies who were well loaded\
>🔔 Only available from static class - work also when FluentClass is consume

⚓ SetLogActivation method :\
>ℹ️ This method specify if Loader use a log during loading\
>✏️ active : Boolean - No default value, Required\

⚓ GetLog method :\
>ℹ️ This method return string array about log events\
>🔔 Only available from static class - work also when FluentClass is consume

## Know exceptions
### During Composer Update
⚠️ Name is allready in use\
[![Composer-Exception.png](https://i.postimg.cc/WzsPyvS2/Composer-Exception.png)](https://postimg.cc/MM34cgh4)

➡️ To solve that, please use partial_php extension for your partial files and use the Firstruner Framework Loader for load these partial files

### PHP
⚠️ Name is allready in use\
[![Whats-App-Image-2024-01-22-at-09-38-41.jpg](https://i.postimg.cc/mg42q2DP/Whats-App-Image-2024-01-22-at-09-38-41.jpg)](https://postimg.cc/kBjmRCWC)

Solutions :\
➡️ Use Firstruner Framework Loader\
➡️ Apply partial_php extension on your partial files\
➡️ Specify php_as_partial at true on Loader calling