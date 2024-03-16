#!/usr/bin/env bash

UNAMEOUT="$(uname -s)"

# Verify operating system is supported...
case "${UNAMEOUT}" in
    Linux*)             MACHINE=linux;;
    Darwin*)            MACHINE=mac;;
    *)                  MACHINE="UNKNOWN"
esac

if [ "$MACHINE" == "UNKNOWN" ]; then
    echo "Unsupported operating system [$(uname -s)]. This setup script supports macOS, Linux, and Windows (WSL2)." >&2

    exit 1
fi

# Determine if stdout is a terminal...
if test -t 1; then
    # Determine if colors are supported...
    ncolors=$(tput colors)

    if test -n "$ncolors" && test "$ncolors" -ge 8; then
        BOLD="$(tput bold)"
        YELLOW="$(tput setaf 3)"
        GREEN="$(tput setaf 2)"
        RED="$(tput setaf 1)"
        NC="$(tput sgr0)"
    fi
fi

trap 'echo "${RED}* Script halted (user pressed CTRL-C?)${NC}"; exit' SIGINT SIGTERM SIGTSTP

echo "${GREEN}* Install and start Docker containers${NC}"
echo
docker compose up -d
echo

echo "${GREEN}* Setup test catalogs${NC}"
echo
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./scripts/mariadb-install-db --catalogs"
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./scripts/mariadb-install-db --catalogs=cat1"
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./scripts/mariadb-install-db --catalogs=cat2"
echo

echo "${GREEN}* Setup test databases${NC}"
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"USE CATALOG cat1; CREATE DATABASE testdb1; CREATE DATABASE testdb2;\""
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"USE CATALOG cat2; CREATE DATABASE testdb3; CREATE DATABASE testdb4;\""
echo

echo "${GREEN}* Setup test users${NC}"

docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"CREATE USER 'user1'@'%' IDENTIFIED BY 'password';\""
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"GRANT ALL PRIVILEGES ON *.* TO 'user1'@'%' IDENTIFIED BY 'password' WITH GRANT OPTION;\""
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"GRANT ALL PRIVILEGES ON *.* TO 'user1'@'localhost' IDENTIFIED BY 'password' WITH GRANT OPTION;\""

docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"CREATE USER 'user2'@'%' IDENTIFIED BY 'password';\""
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"GRANT ALL PRIVILEGES ON *.* TO 'user2'@'%' IDENTIFIED BY 'password' WITH GRANT OPTION;\""
docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"GRANT ALL PRIVILEGES ON *.* TO 'user2'@'localhost' IDENTIFIED BY 'password' WITH GRANT OPTION;\""

docker compose exec -w /usr/local/mysql mariadb-catalogs bash -c "./bin/mariadb -e \"FLUSH PRIVILEGES;\""
echo
