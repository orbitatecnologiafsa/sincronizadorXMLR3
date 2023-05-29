@echo off

set pname=php.exe
set pname2=cmd.exe

set pcommand="wmic process where name="%pname%" | find "%pname%" /c"
set pcommand2="wmic process where name="%pname2%" | find "%pname2%" /c"

FOR /F "tokens=*" %%i IN (' %pcommand% ') DO SET pcount=%%i

if %pcount% GTR 1 (
    echo Tem mais de um processo.
    taskkill /f /im %pname%
  
) else (
    echo Tem um ou nenhum processo.
)

FOR /F "tokens=*" %%i IN (' %pcommand2% ') DO SET pcount2=%%i

if %pcount2% GTR 1 (
    echo Tem mais de um processo.
    taskkill /f /im %pname2%
  

) else (
    echo Tem um ou nenhum processo.
)
pause

#taskkill /F /IM php.exe
#taskkill /F /IM cmd.exe

#pause 