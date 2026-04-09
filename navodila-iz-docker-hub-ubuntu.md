# Navodila Za Zagon Iz Docker Hub Na Ubuntu

Ta postopek ponovno prenese zadnjo objavljeno sliko iz Docker Hub, postavi svezo MySQL bazo, uvozi shemo in seed podatke ter zazene aplikacijo lokalno na Ubuntu racunalniku.

## Predpogoji

- Docker Engine ali Docker Desktop mora biti namescen in zagnan.
- Projekt je lokalno prisoten v poljubni mapi.
- V spodnjih ukazih si najprej nastavis spremenljivko `PROJECT_ROOT` na svojo lokalno pot do repozitorija.
- Ce tvoj uporabnik nima pravic za Docker, dodaj `sudo` pred vsak `docker` ukaz.

Primer:

```bash
PROJECT_ROOT="$HOME/dev/php-symfony-product-list"
```

## 1. Preveri, da Docker dela

```bash
docker version
docker info
```

Ce Docker ni zagnan, ga najprej zazeni.

## 2. Pocisti stare containerje in omrezje

```bash
docker stop product-list-app product-list-adminer product-list-mysql
docker rm product-list-app product-list-adminer product-list-mysql
docker network rm product-list-net
```

Ce kateri ukaz vrne `No such container` ali `No such network`, to ni problem.

## 3. Po zelji pobrisi star MySQL volume

To naredi samo, ce zelis res cisto bazo brez starih podatkov:

```bash
docker volume rm product-list-mysql-data
```

## 4. Odstrani lokalni image in ponovno prenesi zadnjo verzijo

```bash
docker rmi bluestern/php-symfony-product-list:latest
docker pull bluestern/php-symfony-product-list:latest
```

## 5. Ustvari Docker network

```bash
docker network create product-list-net
```

## 6. Zazeni MySQL in Adminer

```bash
docker run -d --name product-list-mysql --network product-list-net \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=product_list \
  -e MYSQL_USER=app \
  -e MYSQL_PASSWORD=app \
  -v product-list-mysql-data:/var/lib/mysql \
  -p 3307:3306 \
  mysql:8.4

docker run -d --name product-list-adminer --network product-list-net \
  -p 8081:8080 \
  adminer:latest
```

## 7. Pocakaj, da je MySQL pripravljen

```bash
docker logs product-list-mysql
```

Ko vidis `ready for connections`, nadaljuj.

## 8. Uvozi shemo v bazo

```bash
cat "$PROJECT_ROOT/docker/mysql/init/001_schema.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 9. Uvozi seed podatke

```bash
cat "$PROJECT_ROOT/docker/mysql/init/002_seed_products.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 10. Zazeni aplikacijo iz Docker Hub

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

## 11. Odpri aplikacijo

- Aplikacija: `http://localhost:8080`
- Adminer: `http://localhost:8081`

Adminer prijava:

- System: `MySQL`
- Server: `product-list-mysql`
- Username: `app`
- Password: `app`
- Database: `product_list`

## 12. Preveri, da je navigacija pravilna

Na domaci strani klikni `IZDELKI`.

Pravilna povezava mora biti:

```text
http://localhost:8080/izdelki
```

Nepravilna povezava je:

```text
http://izdelki/
```

## 13. Ustavi vse containerje

```bash
docker stop product-list-app product-list-adminer product-list-mysql
docker rm product-list-app product-list-adminer product-list-mysql
docker network rm product-list-net
```

## Dodaten pregled

Ce zelis preveriti, kateri image dejansko tece:

```bash
docker inspect product-list-app --format "{{.Config.Image}}"
docker image inspect bluestern/php-symfony-product-list:latest --format "{{.Id}}"
```

Ce aplikacija po ponovnem pullu se vedno ne dela pravilno, preveri loge:

```bash
docker logs product-list-app
docker logs product-list-mysql
```

## Opombe Za Ubuntu

- Ce so porti `8080`, `8081` ali `3307` ze zasedeni, spremeni levi del pri `-p`, na primer `-p 8082:80`.
- Ce uporabljas oddaljen Ubuntu streznik brez GUI, odpri URL prek browserja na svojem racunalniku ali uporabi port forwarding.
- Ce zelis Docker brez `sudo`, dodaj uporabnika v skupino `docker` in se ponovno prijavi.
