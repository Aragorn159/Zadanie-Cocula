Projekt je vytvorené v rámci čistého PHP. Dôvodom bolo dôkladnejšie oboznámenie sa s daným jazykom, jeho silnými stránkami, nedostatkami a obmedzeniami. V budúcnosti sa podobné zadania budú riešiť cez frameworky, ktoré uľahčia prácu s daným problémom, ako je Laravel alebo Lumen.
Projekt bol vytvorerý v PHP verzia 8.

Pokyny na spustenie:
* Pre spustenie je porebné mať nainštalovanú minimálnu verziu jazyka PHP, ako aj nástroj na generovanie requestov ako je napr. Postman (https://www.postman.com/)
* Ďalej treba stiahnuť serverové prostredie XAMPP (zdroj: https://www.apachefriends.org/)
* Následne v úložisku pre XAMPP server treba nájsť súbor php.ini (/XAMPP/php) a odkomentovať (tj. odstrániť znak ";" spred daných riadkov) ak tak nie je spravené na položkách "file_uploads=On" a "extension=gd"
* Adresár je potrebné prekopírovať do súboru /XAMPP/htdocs
* Pre spustenie servera je potrebné spustiť XAMPP Control Panel a spustiť moduly "Apache" a "MySQL"
* Pred používaním ostatných ciest je potrebné vytvoriť HTTP požiadavku na cestu (http://localhost)/gallery s requestom typu GET alebo POST za účelom vytvorenia serverovej zložky v adresári
