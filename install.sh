#!/bin/bash

echo "Create file storage from fixture files"
cp install/mostUsedWords.txt.dist fileStorage/mostUsedWords.txt
cp install/posts.txt.dist fileStorage/posts.txt

echo "Loading vhost and configuring apache2"
sudo cp install/blog_test_vhost.conf /etc/apache2/sites-available/blog_test_vhost.conf
sudo sed -e "s?%BLOG_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/blog_test_vhost.conf
sudo a2ensite blog_test_vhost.conf
sudo service apache2 restart
