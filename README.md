PHP 8.0.19 | Python 3.7.9 | MySQL 15.1 Distrib 10.4.24-MariaDB
```shell
sudo apt install npm
sudo npm install yarn -g
yarn install
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
Change mail configs inside .env

```php
###> symfony/google-mailer ###
# Gmail SHOULD NOT be used on production, use it in development only.
MAILER_DSN=gmail://user:pass@default
###< symfony/google-mailer ###
```

Install python3

```shell
cd nvdlib
pip3 install requests
pip3 install flask
pip3 install flask-mysqldb 
pip3 install pymysql 
pip3 install nvdlib 
```

replace __convert(product.... method by

```python
def __convert(product, CVEID):
    """Convert the JSON response to a referenceable object."""
    if product == 'cve':
        vuln = json.loads(json.dumps(CVEID), object_hook= CVE)
        vuln.getvars()
        return json.dumps(CVEID)
    else:
        cpeEntry = json.loads(json.dumps(CVEID), object_hook= CPE)
        return json.dumps(CVEID)
```

inside classes.py