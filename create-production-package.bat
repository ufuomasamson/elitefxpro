@echo off
echo Creating TradeTrust Clean Deployment Package...

:: Create deployment directory
if not exist "tradetrustpoint-clean" mkdir tradetrustpoint-clean

:: Copy essential files and folders
xcopy /E /I /Y app tradetrustpoint-clean\app
xcopy /E /I /Y bootstrap tradetrustpoint-clean\bootstrap
xcopy /E /I /Y config tradetrustpoint-clean\config
xcopy /E /I /Y database tradetrustpoint-clean\database
xcopy /E /I /Y public tradetrustpoint-clean\public
xcopy /E /I /Y resources tradetrustpoint-clean\resources
xcopy /E /I /Y routes tradetrustpoint-clean\routes
xcopy /E /I /Y storage tradetrustpoint-clean\storage
xcopy /E /I /Y vendor tradetrustpoint-clean\vendor

:: Copy individual files
copy .env tradetrustpoint-clean\
copy .env.example tradetrustpoint-clean\
copy artisan tradetrustpoint-clean\
copy composer.json tradetrustpoint-clean\
copy composer.lock tradetrustpoint-clean\
copy package.json tradetrustpoint-clean\
copy package-lock.json tradetrustpoint-clean\
copy database_schema.sql tradetrustpoint-clean\
copy DEPLOYMENT_READY.md tradetrustpoint-clean\

:: Create zip file
powershell Compress-Archive -Path tradetrustpoint-clean -DestinationPath tradetrustpoint-production-ready.zip -Force

:: Clean up temp directory
rmdir /S /Q tradetrustpoint-clean

echo ✅ TradeTrust production package created: tradetrustpoint-production-ready.zip
echo Ready for cPanel deployment!
pause
