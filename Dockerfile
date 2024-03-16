FROM ubuntu:23.10

RUN sed -e '/^# deb-src/s/^# //' -i /etc/apt/sources.list; \
    apt update -y; \
    apt build-dep -y mariadb-server; \
    apt install -y build-essential libncurses5-dev gnutls-dev bison zlib1g-dev ccache; \
    mkdir build

ADD maria-cat.tar.gz /build/

RUN cd build; \
    cmake . -DPLUGIN_COLUMNSTORE=NO -DPLUGIN_ROCKSDB=NO -DPLUGIN_TOKUDB=NO -DPLUGIN_MROONGA=NO -DPLUGIN_GROONGA=NO -DPLUGIN_PARTITION=NO; \
    make -j8 install

RUN mkdir /datadir; \
    /usr/local/mysql/scripts/mariadb-install-db --catalogs --datadir=/datadir

ENTRYPOINT /usr/local/mysql/bin/mariadbd --datadir=/datadir --catalogs --user=root


