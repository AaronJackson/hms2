<?php

namespace App\Http\Controllers;

use HMS\Entites\User;
use HMS\Entities\Printers\Printer;
use HMS\Entities\Printers\PrinterJob;
use HMS\Repositories\Printers\PrinterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintingController extends Controller
{
    protected $printerRepository;

    /**
     * Create a new controller instance.
     *
     * @param LinkRepository $linkRepository
     * @param LinkFactory $linkFactory
     */
    public function __construct(PrinterRepository $printerRepository)
    {
        $this->printerRepository = $printerRepository;

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
            'user' => Auth::user()
        ]);
    }

}
