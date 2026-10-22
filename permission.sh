echo 'Changing File Permission'; 
echo 'find . -type f -exec chmod 664 {} \;';
find . -type f -exec chmod 664 {} \;
echo 'find . -type d -exec chmod 775 {} \;';
find . -type d -exec chmod 775 {} \;
echo 'find var pub/static pub/media app/etc -type f -exec chmod g+w {} \;';
find var pub/static pub/media app/etc -type f -exec chmod g+w {} \;
echo 'find var pub/static pub/media app/etc -type d -exec chmod g+ws {} \;';
find var pub/static pub/media app/etc -type d -exec chmod g+ws {} \;
echo 'u+x bin/magento';
chmod u+x bin/magento
echo 'Completed';