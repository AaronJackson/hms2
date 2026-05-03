<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;

class IPPController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    public function print($jwt)
    {
        //$decoded = JWT::decode($jwt, new Key('123412341234123412341234123412341234', 'HS256'));

        $hSource = fopen('php://input', 'r');
        $body = '';

        while (!feof($hSource)) {
            $chunk = fread($hSource, 1024);
            $body .= $chunk;
        }
        fclose($hSource);

        $content = file_get_contents('php://input');

        // We need to replace the printer-uri attribute. This is made up of its length followed by the string.
        // The server uses this name to identify which printer it the job should go to.
        $len = strlen('http://172.19.0.1:8080/ipp/print/' . $jwt);
        if ($len > 255) return;
        $newLen = strlen('https://10.0.0.98/');
        $content = str_replace(chr($len) . 'http://172.19.0.1:8080/ipp/print/' . $jwt, chr($newLen) . 'https://10.0.0.98/', $content);

        $ippPayload = new \obray\ipp\transport\IPPPayload();
        $ippPayload->decode($content);
        if ($ippPayload->document) {
            $document = $ippPayload->document;
            file_put_contents('/tmp/printlog', $document);
        }

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/ipp',
                'content' => $content
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];

        $context = stream_context_create($options);
        $url = 'https://10.0.0.98/';

        $fp = fopen($url, 'r', false, $context);
        fpassthru($fp);
        fclose($fp);
    }
}
