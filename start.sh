#!/usr/bin/env bash
green=$(tput setf 2)
toend=$(tput hpa $(tput cols))$(tput cub 6)

docker-compose up -d --force-recreate --renew-anon-volumes --build