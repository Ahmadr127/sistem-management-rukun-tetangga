<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rt\Store;
use App\Http\Requests\Rt\Update;
use App\Http\Services\RtService;
use App\Models\Rt;
use Illuminate\Http\Request;

class RtController extends Controller
{
    public function __construct(protected RtService $rtService) {}

    public function index(Request $request)
    {
        $rts = $this->rtService->getRts($request->only(['search','is_active','per_page']));
        return view('rts.index', compact('rts'));
    }

    public function create()
    {
        return view('rts.create');
    }

    public function store(Store $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $this->rtService->createRt($data);
        return redirect()->route('rts.index')->with('success','RT berhasil ditambahkan!');
    }

    public function show(Rt $rt)
    {
        $rt->loadCount(['warga','kartuKeluarga','users']);
        $rt->load(['warga' => fn($q)=>$q->latest()->limit(5), 'users','alamatRt']);
        return view('rts.show', compact('rt'));
    }

    public function edit(Rt $rt)
    {
        return view('rts.edit', compact('rt'));
    }

    public function update(Update $request, Rt $rt)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $this->rtService->updateRt($rt, $data);
        return redirect()->route('rts.index')->with('success','RT berhasil diperbarui!');
    }

    public function destroy(Rt $rt)
    {
        try {
            $this->rtService->deleteRt($rt);
            return redirect()->route('rts.index')->with('success','RT berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('rts.index')->with('error', $e->getMessage());
        }
    }
}
