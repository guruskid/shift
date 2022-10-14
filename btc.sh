#!/usr/bin/expect -f

spawn tatum-kms daemon --apiKey eb8d3c59-80ea-4758-b397-e166db8a9cf6_100 --chain=BTC

expect "Enter*:"
send  "Dantownms4$\r"
set timeout -1

expect eof
