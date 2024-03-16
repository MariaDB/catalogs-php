# Package outline

The following document describes what parts of the MariaDB catalogs are to be implemented.


- Catalogs
    - USE CATALOG catalog_name; (does this make sense for the project?)
    - CREATE CATALOG
    - DROP CATALOG
    - ALTER CATALOG
    - SHOW CATALOGS (and also information_schema.catalogs)
    - SHOW CREATE CATALOG catalog_name;
    - SELECT CATALOG(); 
    - [Shutdown](https://mariadb.com/kb/en/shutdown/)




- permissions


# Questions

Can we use [PDO](https://www.php.net/manual/en/book.pdo.php) or [myslqi](https://www.php.net/manual/en/class.mysqli.php)?

- Shell is required for installation of a cat only, the rest is doable with SQLQuerys

    ```bash
    mariadb-install-db --catalogs=["list"]
    ```

- Proper error handling if `mysql` bash is restricted.


# Useage

1. A user wants to create a new website and needs a DB in a seperate container.
2. In a hosting pannel a user creates a new website, the pannel creates a DB in a new container.

3. A webhoster has an existing client, who wants a new website, This client get's a new DB in their existing catalog.
4. A webhoster has an existing client that has terminated it's contract. The webhoster deletes the whole catalog.


