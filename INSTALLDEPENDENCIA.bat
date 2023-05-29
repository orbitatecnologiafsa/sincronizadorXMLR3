@echo off

cd C:\Orbita\sincronizadorXML\source_code
C:\Orbita\sincronizadorXML\php\php.exe C:\Orbita\sincronizadorXML\composer\composer.phar install %*
C:\Orbita\sincronizadorXML\php\php.exe  artisan vendor:publish --tag=laravel-assets --ansi --force
C:\Orbita\sincronizadorXML\php\php.exe  artisan storage:link
C:\Orbita\sincronizadorXML\php\php.exe -r "copy('C:\Orbita\sincronizadorXML\cacert.pem','C:\Orbita\sincronizadorXML\source_code\storage\app\cacert.pem');"
C:\Orbita\sincronizadorXML\php\php.exe  -r "copy('.env.example', '.env');"
C:\Orbita\sincronizadorXML\php\php.exe  artisan key:generate --ansi

pause
