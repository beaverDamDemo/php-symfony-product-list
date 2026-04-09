# Navodila Za Zagon Iz Docker Hub

Ta postopek ponovno prenese zadnjo objavljeno sliko iz Docker Hub, postavi svezo MySQL bazo, uvozi shemo in seed podatke ter zazene aplikacijo lokalno.

Pomembno: za ta postopek potrebujes lokalni klon repozitorija, ker se SQL shema in seed podatki uvozijo iz lokalnih datotek v MySQL container.

## Predpogoji

- Docker Desktop mora biti zagnan.
- Projekt je lokalno prisoten v poljubni mapi.
- V spodnjih ukazih si najprej nastavis spremenljivko `$ProjectRoot` na svojo lokalno pot do repozitorija.

## 0. Kloniraj repozitorij

Ce projekta se nimas lokalno, ga najprej kloniraj:

```powershell
git clone <tvoj-github-url> "C:\Users\MOJE_UPORABNISKO_IME\Documents\dev\php-symfony-product-list"
```

Nato nastavi pravo lokalno pot:

Primer:

```powershell
$ProjectRoot = "C:\Users\MOJE_UPORABNISKO_IME\Documents\dev\php-symfony-product-list"
```

Preveri, da pot res obstaja:

```powershell
Test-Path "$ProjectRoot\docker\mysql\init\001_schema.sql"
```

Ukaz mora vrniti `True`.

## 1. Pocisti stare containerje in omrezje

Te ukaze zazeni v PowerShellu enega po enega:

```powershell
docker stop product-list-app product-list-adminer product-list-mysql
```

```powershell
docker rm product-list-app product-list-adminer product-list-mysql
```

```powershell
docker network rm product-list-net
```

Ce kateri ukaz vrne `No such container` ali `No such network`, to ni problem.

## 2. Po zelji pobrisi star MySQL volume

To naredi samo, ce zelis res cisto bazo brez starih podatkov:

```powershell
docker volume rm product-list-mysql-data
```

## 3. Odstrani lokalni image in ponovno prenesi zadnjo verzijo

```powershell
docker rmi bluestern/php-symfony-product-list:latest
```

```powershell
docker pull bluestern/php-symfony-product-list:latest
```

## 4. Ustvari Docker network

```powershell
docker network create product-list-net
```

## 5. Zazeni MySQL in Adminer

```powershell
docker run -d --name product-list-mysql --network product-list-net -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=product_list -e MYSQL_USER=app -e MYSQL_PASSWORD=app -v product-list-mysql-data:/var/lib/mysql -p 3307:3306 mysql:8.4
```

```powershell
docker run -d --name product-list-adminer --network product-list-net -p 8081:8080 adminer:latest
```

## 6. Pocakaj, da je MySQL pripravljen

```powershell
docker logs product-list-mysql
```

Ko vidis `ready for connections`, nadaljuj.

## 7. Uvozi shemo v bazo

Ce zelis uporabiti `$ProjectRoot`, zazeni:

```powershell
Get-Content "$ProjectRoot\docker\mysql\init\001_schema.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

Ce si ze postavljen v mapo repozitorija, je ta verzija preprostejsa:

```powershell
Set-Location "$ProjectRoot"
```

```powershell
Get-Content ".\docker\mysql\init\001_schema.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 8. Uvozi seed podatke

Ce zelis uporabiti `$ProjectRoot`, zazeni:

```powershell
Get-Content "$ProjectRoot\docker\mysql\init\002_seed_products.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

Ce si ze postavljen v mapo repozitorija, je ta verzija preprostejsa:

```powershell
Set-Location "$ProjectRoot"
```

```powershell
Get-Content ".\docker\mysql\init\002_seed_products.sql" | docker exec -i product-list-mysql mysql -uapp -papp product_list
```

## 9. Zazeni aplikacijo iz Docker Hub

```powershell
docker run -d --name product-list-app --network product-list-net -p 8080:80 -e DB_HOST=product-list-mysql -e DB_PORT=3306 -e DB_NAME=product_list -e DB_USER=app -e DB_PASSWORD=app bluestern/php-symfony-product-list:latest
```

## 10. Odpri aplikacijo

- Aplikacija: `http://localhost:8080`
- Adminer: `http://localhost:8081`

Adminer prijava:

- System: `MySQL`
- Server: `product-list-mysql`
- Username: `app`
- Password: `app`
- Database: `product_list`

## 11. Preveri, da je navigacija pravilna

Na domaci strani klikni `IZDELKI`.

Pravilna povezava mora biti:

```text
http://localhost:8080/izdelki
```

Nepravilna povezava je:

```text
http://izdelki/
```

## 12. Ustavi vse containerje

```powershell
docker stop product-list-app product-list-adminer product-list-mysql
```

```powershell
docker rm product-list-app product-list-adminer product-list-mysql
```

```powershell
docker network rm product-list-net
```

## Dodaten pregled

Ce zelis preveriti, kateri image dejansko tece:

```powershell
docker inspect product-list-app --format "{{.Config.Image}}"
```

```powershell
docker image inspect bluestern/php-symfony-product-list:latest --format "{{.Id}}"
```

Ce aplikacija po ponovnem pullu se vedno ne dela pravilno, preveri loge:

```powershell
docker logs product-list-app
```

```powershell
docker logs product-list-mysql
```

## Najpogostejsa napaka

Ce pri koraku 7 ali 8 dobis napako `Cannot find path`, potem `$ProjectRoot` ni nastavljen na pravo lokalno mapo repozitorija.

Najprej preveri:

```powershell
echo $ProjectRoot
```

```powershell
Test-Path "$ProjectRoot\docker\mysql\init\001_schema.sql"
```

Ce `Test-Path` vrne `False`, popravi `$ProjectRoot` na pravo pot.
