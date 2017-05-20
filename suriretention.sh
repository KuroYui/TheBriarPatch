#!/bin/bash
echo "clearing logs fast.log and http.log"
> /var/log/suricata/fast.log
echo "DELETE FROM EXPLOITS; VACUUM;" | sqlite3 BriarPatch.db
#> /var/log/suricata/http.log
