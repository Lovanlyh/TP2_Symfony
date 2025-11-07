<?php
declare(strict_types=1);

$passphrase = getenv('JWT_PASSPHRASE') ?: 'lexik_jwt_pass';

$config = [
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'private_key_bits' => 4096,
];

$resource = openssl_pkey_new($config);
if (false === $resource) {
    fwrite(STDERR, 'Impossible de générer la clé privée : '.openssl_error_string().PHP_EOL);
    exit(1);
}

if (!openssl_pkey_export($resource, $privateKey, $passphrase)) {
    fwrite(STDERR, 'Impossible d’exporter la clé privée : '.openssl_error_string().PHP_EOL);
    exit(1);
}

$details = openssl_pkey_get_details($resource);
if (false === $details || !isset($details['key'])) {
    fwrite(STDERR, 'Impossible d’obtenir la clé publique.'.PHP_EOL);
    exit(1);
}

$publicKey = $details['key'];

$targetDir = __DIR__.'/../config/jwt';
if (!is_dir($targetDir) && !mkdir($targetDir, 0700, true) && !is_dir($targetDir)) {
    fwrite(STDERR, sprintf('Impossible de créer le dossier %s'.PHP_EOL, $targetDir));
    exit(1);
}

file_put_contents($targetDir.'/private.pem', $privateKey);
file_put_contents($targetDir.'/public.pem', $publicKey);

printf("Clés générées dans %s\n", realpath($targetDir));
