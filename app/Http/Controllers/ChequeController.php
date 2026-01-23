<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChequeStoreRequest;
use App\Models\Cheque;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    use ResponseTrait;
    use PaginationTrait;
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $status = $request->input('status');
        $query = Cheque::query();
        // return $status;
        if ($status) {
            $cheques = $query->where('status', $status)->get();
        }
        $cheques = $query->paginate($per_page);

        return $this->apiSuccess('Cheques list', $cheques);
    }

    public function store(ChequeStoreRequest $request)
    {
        $cheque = Cheque::create($request->validated());
        return $this->apiSuccess('Cheque created successfully', $cheque);
    }

    public function update(Request $request, Cheque $cheque)
    {
        $request->validate([
            'status' => 'required|in:pending,deposited,cleared,bounced',
        ]);
        $cheque->update([
            'status' => $request->status,
        ]);
        return $this->apiSuccess('Cheque updated successfully', $cheque);
    }

    public function destroy(Cheque $cheque)
    {
        $cheque->delete();
        return $this->apiSuccess('Cheque deleted successfully', $cheque);
    }
}
