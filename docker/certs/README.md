Use nginx with https
====================

From:

https://www.humankode.com/ssl/create-a-selfsigned-certificate-for-nginx-in-5-minutes

Configure Chrome to Trust the Certificate and to Show the Site as Secure
Add the certificate to the trusted CA root store

```bash
sudo apt install libnss3-tools
certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n "localhost" -i localhost.crt
```


Certificate created with:

```
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost.key -out localhost.crt -config localhost.conf
```
