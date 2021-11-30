# Running the project

Clone the repository:
```
git@github.com:PedroPMS/clipr-blog.git
```

Install dependencies:
```
composer install
```

Create the JWT cert files:
```
php bin/console lexik:jwt:generate-keypair
```

Create and initialize the database:
```
php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate
```

Run the fixtures to populate th DB:
```
php bin/console doctrine:fixtures:load
```

Starts the server:
```
symfony server:start
```

# Test

For testing, after all the installation, run:

```
vendor/bin/phpunit
```

# Docs

See the docs on:

```
localhost:8000/api/doc
```

# Know Issues
- Validations on PUT routes not work as expected, seems that the validation populates the entity object before validade the request. So if a request set a not null field to null, an error is thrown. A possible workaround is set the fields on the entity to accept null `?string`, but that doesn't seem like good practice.  
- There is no route for add roles to a user. But after running the fixtures, two users are created: `admin@gmail.com` and `writer@gmail.com` with the password `secret123`. Use them if you wish or create a new user and set the roles on database. 
