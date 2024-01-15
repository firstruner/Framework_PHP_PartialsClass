
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

## Class creation
To create a class with partials files, create a folder for your class, and create all of your files inside.

### Define main partial class
#### Call attributes
Define the file that is the main class file and call Partials attributes like this :

    use System\Attributes\
    {
          Partial,
          Partial_Content
    };

#### Define the partial class
Now define the class as main partial class with using Partial attribute like this

    #[Partial]

#### Define the contents place
And specify when other partial files are include in your file, like this :

    #[Partial_Content]
          public function PrintInstanceMessage() {}

#### Full main partial class sample

    <?php
    namespace System\Printers;
    
    use System\Attributes\
    {
          Partial,
          Partial_Content
    };
    
    #[Partial]
    class ScreenPrinter
    {
          #[Partial_Content]
          public function PrintInstanceMessage()
          {
                echo "Mon Instance";
          }
    }

### Define a partial class file
In all other partial file, you just need specify that a partial content like this :

    #[Partial_Content]
    public static function PrintStaticMessage() {}

#### Full partial content class sample

    <?php
    namespace System\Printers;
    
    use System\Attributes\Partial_Content;
    
    class ScreenPrinter
    {
          #[Partial_Content]
          public static function PrintStaticMessage()
          {
                echo "Static message";
          }
    }

