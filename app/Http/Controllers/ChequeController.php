<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Traits\ResponseTrait;

class ChequeController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $cheques = Cheque::all();
        return $this->apiSuccess('Cheques', $cheques);
    }

    public function pendingCheques()
    {
        $cheques = Cheque::where('status', 'pending')->get();
        return $this->apiSuccess('Pending Cheques', $cheques);
    }
}
