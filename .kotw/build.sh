#!/bin/zsh

cd /app
wp db reset --yes
wp db import .kotw/wordpress-2022-10-30-fc4197d.sql
