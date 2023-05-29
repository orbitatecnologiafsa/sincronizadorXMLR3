@echo off

set "arquivo_origem=C:\Orbita\sincronizadorXML\init.vbs"
set "diretorio_destino=%appdata%\Microsoft\windows\start menu\programs\startup"

copy "%arquivo_origem%" "%diretorio_destino%"

pause
