# Framework PHP - Partials functions
Part of Firstruner Framework for PHP to allow use Partial function with PHP class

This project is a part of Firstruner Framework for PHP.
It contains a little part of the original Framework that allow you to use "partial" DotNet functionality into PHP structure project and also naturally the Framework loader.

To use, it's very simple !

## Loader
Create a folder that contains all of your POO Objects (classes, interfaces, enumerations and other).

    // The only required dependence for Firstruner Framework
    require __DIR__ . '/System/Reflection/Dependencies.php';
    
    // For multiple use of Loader class
    use System\Reflection\Dependencies\Loader;
    
    // Load all php POO files in "System" Folder
    Loader::Load(__DIR__ . '/System');

Note : For standard use, partial file must have "partial_php" extension, but it's possible to use "php" extension if you specify "php_as_partial" attribute to "True" when "Load" method was called.
But use "php" are more lazy because it necessary to load the php file before determine if the file is a partial file.

## Class creation
To create a class with partials files, create a folder for your class, and create all of your files inside.

### Define a class as partial
#### Call attributes
Define the file that is the main class file and call Partials attributes like this :

    use System\Attributes\Partial;

#### Define the partial class
Now define the class as main partial class with using Partial attribute like this

    #[Partial]

#### Full main partial class sample

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

## Uses, inheritance and implementations
Since the 1.1.* version, you can specify independently inheritance and implementation by file

### Sample 1
##### File 1

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use \Exception;
    
    #[Partial]
    class  Sample extends MainClass
    {
    }

##### File 

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

### Sample 2
##### File 1

    namespace  System\Sample;
    
    use System\Attributes\Partial;
    use \Exception;
    
    #[Partial]
    class  Sample extends MainClass implements 1stInterface, 2ndInterface
    {
    }

##### File 

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

