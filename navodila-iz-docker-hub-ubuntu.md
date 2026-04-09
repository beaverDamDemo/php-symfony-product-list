# Navodila Za Zagon Iz Docker Hub Na Ubuntu

Ta postopek ponovno prenese zadnjo objavljeno sliko iz Docker Hub, postavi svezo MySQL bazo, uvozi shemo in seed podatke ter zazene aplikacijo lokalno na Ubuntu racunalniku.

Pomembno: za ta postopek potrebujes lokalni klon repozitorija, ker se SQL shema in seed podatki uvozijo iz lokalnih datotek v MySQL container.

## Predpogoji

- Docker Engine ali Docker Desktop mora biti namescen in zagnan.
- Projekt je lokalno prisoten v poljubni mapi.
- V spodnjih ukazih si najprej nastavis spremenljivko `PROJECT_ROOT` na svojo lokalno pot do repozitorija.

Primer:

```bash
PROJECT_ROOT="$HOME/dev/php-symfony-product-list"
```

## 0. Kloniraj repozitorij

Ce projekta se nimas lokalno, ga najprej kloniraj:

```bash
git clone <tvoj-github-url> "$HOME/dev/php-symfony-product-list"
```

Nato nastavi pravo lokalno pot:

```bash
PROJECT_ROOT="$HOME/dev/php-symfony-product-list"
```

Preveri, da pot res obstaja:

```bash
test -f "$PROJECT_ROOT/docker/mysql/init/001_schema.sql" && echo OK
```

## 1. Omogoci Docker brez sudo

To naredis enkrat na racunalnik:

```bash
sudo groupadd docker
```

```bash
sudo usermod -aG docker $USER
```

```bash
newgrp docker
```

Ce `groupadd` vrne, da skupina ze obstaja, je to v redu.

Po tem preveri, da Docker dela brez `sudo`.

## 2. Preveri, da Docker dela

Po potrebi najprej zazeni Docker service:

```bash
sudo systemctl start docker
```

Potem preveri:

```bash
docker version
```

```bash
docker info
```

Ce ta ukaza delata brez `sudo`, je nastavitev pravilna.

## 3. Pocisti stare containerje in omrezje

```bash
docker stop product-list-app product-list-adminer product-list-mysql
```

```bash
docker rm product-list-app product-list-adminer product-list-mysql
```

```bash
docker network rm product-list-net
```

Ce kateri ukaz vrne `No such container` ali `No such network`, to ni problem.

## 4. Po zelji pobrisi star MySQL volume

To naredi samo, ce zelis res cisto bazo brez starih podatkov:

```bash
docker volume rm product-list-mysql-data
```

## 5. Odstrani lokalni image in ponovno prenesi zadnjo verzijo

```bash
docker rmi bluestern/php-symfony-product-list:latest
```

```bash
docker pull bluestern/php-symfony-product-list:latest
```

## 6. Ustvari Docker network

```bash
docker network create product-list-net
```

## 7. Zazeni MySQL in Adminer

```bash
docker run -d --name product-list-mysql --network product-list-net \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=product_list \
  -e MYSQL_USER=app \
  -e MYSQL_PASSWORD=app \
  -v product-list-mysql-data:/var/lib/mysql \
  -p 3307:3306 \
  mysql:8.4
```

```bash
docker run -d --name product-list-adminer --network product-list-net \
  -p 8081:8080 \
  adminer:latest
```

## 8. Pocakaj, da je MySQL pripravljen

```bash
docker logs product-list-mysql
```

Ko vidis `ready for connections`, nadaljuj.

## 9. Uvozi shemo v bazo

Ce zelis uporabiti `PROJECT_ROOT`, zazeni:

```bash
cat "$PROJECT_ROOT/docker/mysql/init/001_schema.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

Ce si ze postavljen v mapo repozitorija, je ta verzija preprostejsa:

```bash
cd "$PROJECT_ROOT"
```

```bash
cat ./docker/mysql/init/001_schema.sql | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 10. Uvozi seed podatke

Ce zelis uporabiti `PROJECT_ROOT`, zazeni:

```bash
cat "$PROJECT_ROOT/docker/mysql/init/002_seed_products.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

Ce si ze postavljen v mapo repozitorija, je ta verzija preprostejsa:

```bash
cd "$PROJECT_ROOT"
```

```bash
cat ./docker/mysql/init/002_seed_products.sql | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 11. Zazeni aplikacijo iz Docker Hub

```bash
docker run -d --name product-list-app --network product-list-net \
  -p 8080:80 \
  -e DB_HOST=product-list-mysql \
  -e DB_PORT=3306 \
  -e DB_NAME=product_list \
  -e DB_USER=app \
  -e DB_PASSWORD=app \
  bluestern/php-symfony-product-list:latest
```

## 12. Odpri aplikacijo

- Aplikacija: `http://localhost:8080`
- Adminer: `http://localhost:8081`

Adminer prijava:

- System: `MySQL`
- Server: `product-list-mysql`
- Username: `app`
- Password: `app`
- Database: `product_list`

## 13. Preveri, da je navigacija pravilna

Na domaci strani klikni `IZDELKI`.

Pravilna povezava mora biti:

```text
http://localhost:8080/izdelki
```

Nepravilna povezava je:

```text
http://izdelki/
```

## 14. Ustavi vse containerje

```bash
docker stop product-list-app product-list-adminer product-list-mysql
```

```bash
docker rm product-list-app product-list-adminer product-list-mysql
```

```bash
docker network rm product-list-net
```

## Dodaten pregled

Ce zelis preveriti, kateri image dejansko tece:

```bash
docker inspect product-list-app --format "{{.Config.Image}}"
```

```bash
docker image inspect bluestern/php-symfony-product-list:latest --format "{{.Id}}"
```

Ce aplikacija po ponovnem pullu se vedno ne dela pravilno, preveri loge:

```bash
docker logs product-list-app
```

```bash
docker logs product-list-mysql
```

## Najpogostejsa napaka

Ce pri koraku 9 ali 10 dobis napako, da datoteka ne obstaja, potem `PROJECT_ROOT` ni nastavljen na pravo lokalno mapo repozitorija.

Najprej preveri:

```bash
echo "$PROJECT_ROOT"
```

```bash
test -f "$PROJECT_ROOT/docker/mysql/init/001_schema.sql" && echo OK
```

Ce ne dobis `OK`, popravi `PROJECT_ROOT` na pravo pot.

## Opombe Za Ubuntu

- Ce so porti `8080`, `8081` ali `3307` ze zasedeni, spremeni levi del pri `-p`, na primer `-p 8082:80`.
- Ce uporabljas oddaljen Ubuntu streznik brez GUI, odpri URL prek browserja na svojem racunalniku ali uporabi port forwarding.
- Ce `newgrp docker` ne zadostuje, se odjavi in ponovno prijavi v sistem.
