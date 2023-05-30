@echo off
:LOOP
C:\Orbita\sincronizadorXML\php\php.exe C:\Orbita\sincronizadorXML\source_code\artisan upload
timeout /t 3600 /nobreak
goto LOOP
