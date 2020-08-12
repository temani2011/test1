#!/usr/bin/env bash
green=$(tput setf 2)
toend=$(tput hpa $(tput cols))$(tput cub 6)

### full process ###
#docker-compose rm --all &&
#docker-compose pull &&
#docker-compose build --no-cache &&
#docker-compose up -d --force-recreate

docker-compose up -d --build