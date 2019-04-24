# About this framework

The ríki framework is a micro framework. At the moment I'm writing this it contains only five classes (four of them are
abstract and one is an exception). Also it has only two dependencies: `symfony/dotenv` for reading a `.env` file with
custom configuration for an instance and `tflori/dependency-injector` as a lightweight dependency injection system with
PSR-11 compatibility.

## Why should I use rìki framework?

With every library and every framework you are creating dependencies from your application to these libraries. The
smaller these libraries the easier to replace them later. But this argument is not relevant because we want you to keep
using rìki. The small amount of classes and the lean concept go also very easy on resources.
 
There is not much code to understand how this framework works and it is not executing some unnecessary code in
background that might be irrelevant for your application. In the guide you will learn how this framework works and see
that you could also write the framework itself.

Instead of proxying a lot of libraries (like monolog for logging, whoops for error handling etc..) to give them an
interface equal to other classes in this framework you get the default interface to the libraries you have chosen. This
gives new developers that are familiar with these libraries less obstacles.

This very small footprint of the framework has several benefits:

* **future proof** 
  1. You can update your libraries without conflicts
  2. Less updates of the framework
  3. No breaking changes
* easy to maintain (less code -> less errors)
* smaller applications (useful for micro services) 
