WP Two Factor Authentication
===========================

Two factor authentication for Wordpress.

Demo
----
Check put the [demo page](http://2fwp.unserialized.dk)!


Usage
-----

1. Download the archive in wp-content/plugins and extract it.
2. Activate from the Wordpress admin panel.
3. Navigate to newly created "2 Factor Auth" menu page.
4. Click the "Show secret key" button to reveal the secret key.
5. Use QR code or the code under the image to activate the account on the phone application. 
6. Enable the 2F Authentication for the left menu.

That's all. Next time you log in, you will be required to enter the second factor.

Requirements
-----------
* Wordpress 3.8
* A smartphone
* A two factor token generator on the phone :)

Phone application
-------------

There are a variety on mobile application that can be used to generate the code

__1. iOS__
* [Authy](https://itunes.apple.com/en/app/authy/id494168017?mt=8)
* [Google Authenticator](https://itunes.apple.com/en/app/google-authenticator/id388497605?mt=8)

__2. Android__
* [Authy](https://play.google.com/store/apps/details?id=com.authy.authy)
* [Google Authenticator](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2)

__3. Windows Phone__
* [Authy](http://memecrunch.com/meme/142WS/windows-phone/image.png)
* [Authenticator](http://www.windowsphone.com/en-us/store/app/authenticator/021dd79f-0598-e011-986b-78e7d1fa76f8)

Notes
-----
The plugin is using the same secret key for all the users, which may or may not be ideal for you. If you need a solution where each user has an unique secret key, please consider one of the available Authy plugins for Wordpress.

Last Update
----
2014-03-08 15:00  
