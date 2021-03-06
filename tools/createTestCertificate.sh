#!/bin/sh
domain=$1

openssl req \
    -new \
    -newkey rsa:4096 \
    -days 365 \
    -nodes \
    -x509 \
    -subj "/C=DE/ST=Bern/L=Safnern/O=EasySCP/CN=${domain}" \
    -keyout ../tests/config/ssl/${domain}.key \
    -out ../tests/config/ssl/${domain}.crt
