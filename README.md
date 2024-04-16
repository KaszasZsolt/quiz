# SSH Tunnel létrehozása és PHP beállítások

## SSH Tunnel létrehozása

1. **Adminisztrátorként nyiss meg egy parancssort**.
2. Írd be az alábbi SSH-tunnel parancsot és nyomj Enter-t:
   ssh -L 1521:orania2.inf.u-szeged.hu:1521 h873076@linux.inf.u-szeged.hu

## oci_connect müködéséhez
https://www.youtube.com/watch?v=Y97yPK39cdQ

 1. C:\xampp2\php\ext -be belemásolni a 2 file-t
 2. XAMPP-on belül apache-nál rányomni a config-ra majd a php.ini.re, php.ini be beleírni : extension=php_ssh2.dll

## Ha elszál időközben az adatbázis vagy bármi, édemes nyomni egy terminál ujrainditást és megismételni ezt:
## SSH Tunnel létrehozása

1. **Adminisztrátorként nyiss meg egy parancssort**.
2. Írd be az alábbi SSH-tunnel parancsot és nyomj Enter-t:
   ssh -L 1521:orania2.inf.u-szeged.hu:1521 h873076@linux.inf.u-szeged.hu