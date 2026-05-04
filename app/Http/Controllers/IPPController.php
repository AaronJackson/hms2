<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Entities\Snackspace\TransactionState;
use HMS\Entities\Printers\Printer;
use HMS\Entities\Printers\PrinterJob;
use HMS\Repositories\UserRepository;
use HMS\Repositories\Printers\PrinterRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Helpers\IPPPrinter;

class IPPController extends Controller
{
    /**
     * @var PrinterRepository
     */
    protected $printerRepository;
    protected $userRepository;
    protected $transactionRepository;

    /**
     * Create a new controller instance.
     *
     * @param PrinterRepository $printerRepository
     */
    public function __construct(
        PrinterRepository $printerRepository,
        UserRepository $userRepository,
        TransactionRepository $transactionRepository,
    ) {
        $this->printerRepository = $printerRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function print($jwt)
    {
        $key = config('hms.printers_key', null);
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        $user = $this->userRepository->findOneById((int)$decoded->u);
        $printer = $this->printerRepository->findOneByPrinterId((int)$decoded->p);

        if (! $user || ! $printer) {
            return response('Invalid Printer URL', 404);
        }

        $helper = new IPPPrinter(file_get_contents('php://input'), $printer);

        // We need to update the 'printer-uri' attribute (at least for a CUPS target...)
        $oldIppAddress = route('ipp.user', $printer->getUserEndpoint($user));
        $newIppAddress = $printer->getIppUri();
        $helper->updatePrinterUri($oldIppAddress, $newIppAddress);

        // If it doesn't have a document, it's probably a Get-Status command or similar.
        if ($helper->hasDocument()) {
            // We're only doing PDF for now...
            if (! $helper->validatePdfJob()) {
                return response('Invalid document', 400)->header('Content-Type', 'application/ipp');
            }

            $printerJob = $helper->bodyToJob();
            if ($printerJob) {
                if ($printerJob->getCost()) {
                    $transaction = new Transaction($user, -$printerJob->getCost(), TransactionState::COMPLETE);
                    $transaction->setDescription((string)$printerJob);
                    $transaction->setType(TransactionType::PRINTING);
                    $this->transactionRepository->saveAndUpdateBalance($transaction);
                }
            }
        }
        $response = $printer->forward($helper->getBody());

        if ($helper->isGetPrinterAttributes()) {
            // I know...
            $inAttributeValues = false;
            $r = $response;
            $start = strpos($r, 'document-format-supported');
            $end = 0;
            for ($i = $start; $i < strlen($r); $i++){
                if (!$inAttributeValues && $r[$i] == chr(0x00)) {
                    $inAttributeValues = true;
                    continue;
                }

                if ($inAttributeValues) {
                    $valueLen = ord($r[$i]);
                    $i += $valueLen + 1;

                    if ($r[$i] == chr(0x49)) {
                        $i += 3; // continue to next attribute
                        continue;
                    }

                    if ($r[$i] == chr(0x41) || $r[$i] = '"') {
                        break; // end of attributes
                    }
                }
            }

            $supportedMime = 'application/pdf';
            $response = substr($r, 0, $start) . 'document-format-supported' . chr(0) . chr(strlen($supportedMime))
                      . $supportedMime . substr($response, $i);
        }

        return response($response, 200)->header('Content-Type', 'application/ipp');
    }
}
