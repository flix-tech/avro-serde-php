#!/usr/bin/env bash

bin/wait-for-it.sh localhost:2181 -t 30 -- echo "zookeeper is up"
bin/wait-for-it.sh localhost:9092 -t 30 -- echo "kafka broker is up"
bin/wait-for-it.sh localhost:8081 -t 30 -- echo "schema registry is up"
