<?php

namespace App\Http\Controllers;

use HMS\Entites\User;
use HMS\Entities\Printers\Printer;
use HMS\Entities\Printers\PrinterJob;
use HMS\Repositories\Printers\PrinterRepository;
use HMS\Repositories\Printers\PrinterJobRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintingController extends Controller
{
    protected $printerRepository;

    protected $printerJobRepository;

    /**
     * Create a new controller instance.
     *
     * @param LinkRepository $linkRepository
     * @param LinkFactory $linkFactory
     */
    public function __construct(PrinterRepository $printerRepository, PrinterJobRepository $printerJobRepository)
    {
        $this->printerRepository = $printerRepository;
        $this->printerJobRepository = $printerJobRepository;

        $this->middleware('can:printers.print')->only(['index']);
        $this->middleware('can:printers.edit')->only(['edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('printing.index')->with([
            'printers' => $this->printerRepository->paginateAll(),
            'printerJobs' => $this->printerJobRepository->paginateByUser(Auth::user()),
            'user' => Auth::user()
        ]);
    }

}
