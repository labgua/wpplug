
# WPPlug

WPPlug is a minimal framework for create WordPress Plugin.
The development is inspired by the main use-cases defined by the book *Building Web Apps with WordPress* written by Brian Messenlehner and Jason Coleman, along with the basic filosofy of Symfony

In particular the is promoted the use of MVC in the frontend, via the shortcode, and in the backend, with the definitions of page.

This would be abstract the layer for developing, but not too much!
In fact, for integrate old and new methodologies of PHP apps, the MVC is based on the "FrontController" that require control files : in that files the developer is free to use flat php, a "Controller" with Twig (proposed), or other...

The Plugin is thinked (somehow) as a Component and is composed of Component. So a plugin could contains other plugin.
