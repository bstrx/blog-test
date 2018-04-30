## Project description
I decided not to write 100% vanilla code and implement all basic components from the ground up but also I avoided
solutions like symfony (even a slim build). So I used only a couple of vendor libs:

twig, symfony/routing symfony/http-foundation, gumlet/php-image-resize, jQuery, milligram

---
There were a lot of compromises to fasten the development. E.g. ugly fat controllers instead of using services, no DI,
configuration files, routing is not separated, no modern JS frameworks. But still there are parts to look at: all php, js,
html.twig and css files are handwritten so they are waiting for the a review : )

I think I did almost all tasks you requested. In general it required much more time than you mentioned which I spent on
learning and applying technologies like grids and docker that are not a part of my everyday work. So I kind of used this task to practice.

## Installation

The **preferred** way is docker:
```
sudo docker build -t vladimir/blog .
sudo docker run -p 89:80 -d vladimir/blog:latest
```
Then open 0.0.0.0:89

---

The **alternative**:
```
sh install.sh
```
Then install composer and run
```
composer install
```
Probably you will be asked to install some dependencies like gd lib which I didn't include to the install.sh.
In the Dockerfile you make find lib names for it. Please contact if you have any problems with installation.

### Additional info
 - Instead of the DB I keep data in .txt files
 - On submit there is a 2s timeout to imitate slow request to show tiny animated circle which indicates loading
 - I didn't use cache for templates or other elements to avoid complexity
 - I didn't know the max size of the site (which exist according to the image) so I made it 1280 px
