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
            preg_match('/document-format-supported\x{0}([\x{0}-\x{FF}]([a-zA-Z0-9\/\-\.]*)(\x{49}\x{0}*|\x{33}))*/', $response, $output);
            if (sizeof($output) > 0) {
                $supportedMime = 'application/pdf';
                $newSupported = 'document-format-supported' . chr(0) . chr(strlen($supportedMime)) . $supportedMime . chr(0x33);
                $response = str_replace($output[0], $newSupported, $response);
            }
        }

        return response($response, 200)->header('Content-Type', 'application/ipp');
    }
}
