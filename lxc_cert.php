<?php

// Create certificate
$dn = array(
    "countryName"            => "RU",
    "stateOrProvinceName"    => "Moscow",
    "localityName"           => "Moscow",
    "organizationName"       => "TTL",
    "organizationalUnitName" => "IT",
    "commonName"             => "127.0.0.1",
    "emailAddress"           => "info@ttl.lcl"
);

$privkeyString = 'ins3Cure';

// Generate certificate
$privkey = openssl_pkey_new();
$cert    = openssl_csr_new($dn, $privkey);
$cert    = openssl_csr_sign($cert, null, $privkey, 365);

// Generate strings
openssl_x509_export($cert, $certString);
openssl_pkey_export($privkey, $privkeyString);

// Save to file
$pemFile = __DIR__.'/client.pem';
file_put_contents($pemFile, $certString.$privkeyString);
