A Guice-like dependency injection framework for PHP5.

# Requirements
+ PHP 5.3+ (5.3.2 for non-public method injection)
+ Reflection

# Features
+ Constructor/field/method injection
+ Private/protected/public injection
+ Circular dependencies
+ Custom scopes
+ Custom annotations

# Missing
- Static injection
- Documentation
- Unit-testing

# Usage
    class MainModule extends \Sharbat\Inject\AbstractModule {
    
      public function configure() {
        $this->bind('MyApp')->inSingleton();
        $this->bind('HttpClient')->to('CurlHttpClient');
        $this->bind('MappingProvider')->to('SimpleMappingProvider')->inSingleton();
        $this->bind('\Third\Party\EntityManager')->toInstance(new MyEntityManager());
        
        $this->install(new DevelopmentModule());
      }
    
      /**
       * \Sharbat\Inject\@Provides(PDO)
       * \Sharbat\Inject\@InScope(\Sharbat\Inject\Singleton)
       */
      public function providePdo($dbuser, $dbpass) {
        return new PDO('mysql:dbname=myappdb;host=localhost', $dbuser, $dbpass);
      }
    
    }
    
    class DevelopmentModule extends \Sharbat\Inject\AbstractModule {
    
      public function configure() {
        ini_set('display_errors', 'On');
        ini_set('error_reporting', E_ALL|E_STRICT);
        
        $this->bindConstant('dbuser')->to('myuser');
        $this->bindConstant('dbpass')->to('mypass');
      }
    
    }
    
    $injector = \Sharbat\Inject\Sharbat::createInjector(new MainModule());
    $myApp = $injector->getInstance('MyApp');
    $myApp->run();
