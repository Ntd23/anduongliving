sudo rm -f storage/framework/cache/data/d7/e6/d7e66de7fabf43550482240a6f4626db8cb8bb93
sudo chown -R duyhung:daemon storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
sudo find storage/framework/cache/data -type f -exec chmod 664 {} \;
sudo find storage/framework/cache/data -type d -exec chmod 775 {} \;
php artisan optimize:clear
