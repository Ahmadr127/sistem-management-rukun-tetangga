<?php

namespace App\Http\Controllers;

use App\Http\Requests\KasWarga\Generate;
use App\Http\Requests\KasWarga\Store;
use App\Http\Requests\KasWarga\Update;
use App\Http\Services\KasWargaService;
use App\Models\KasWarga;
use App\Models\Rt;
use Illuminate\Http\Request;

class KasWargaController extends Controller
{
    public function __construct(protected KasWargaService $kasService) {}

    public function index(Request $request)
    {
        $kas = $this->kasService->getKasWarga($request->only(['search','rt_id','periode_type','periode','status','warga_id','per_page']));
        $rts = Rt::active()->orderBy('kode_rt')->get();
        $periodes = KasWarga::distinct()->orderBy('periode','desc')->pluck('periode');
        return view('kas-warga.index', compact('kas','rts','periodes'));
    }

    public function create(Request $request)
    {
        $rts = Rt::active()->orderBy('kode_rt')->get();
        $selectedRt = $request->get('rt_id') ?? auth()->user()->rt_id;
        $wargas = $selectedRt ? \App\Models\Warga::where('rt_id',$selectedRt)->where('status_warga','AKTIF')->orderBy('nama')->get() : collect();
        // for superadmin initial, if no rt selected, empty; JS will load via API
        return view('kas-warga.create', compact('rts','wargas','selectedRt'));
    }

    public function store(Store $request)
    {
        $this->kasService->createKas($request->validated());
        return redirect()->route('kas-warga.index')->with('success','Kas warga berhasil ditambahkan!');
    }

    public function show(KasWarga $kasWarga)
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && (int)$kasWarga->rt_id !== (int)$user->rt_id) abort(403);
        $kasWarga->load(['warga','rt','creator']);
        return view('kas-warga.show', compact('kasWarga'));
    }

    public function edit(KasWarga $kasWarga)
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && (int)$kasWarga->rt_id !== (int)$user->rt_id) abort(403);
        $rts = Rt::active()->orderBy('kode_rt')->get();
        $wargas = \App\Models\Warga::where('rt_id',$kasWarga->rt_id)->where('status_warga','AKTIF')->orderBy('nama')->get();
        return view('kas-warga.edit', compact('kasWarga','rts','wargas'));
    }

    public function update(Update $request, KasWarga $kasWarga)
    {
        $this->kasService->updateKas($kasWarga, $request->validated());
        return redirect()->route('kas-warga.index')->with('success','Kas warga berhasil diperbarui!');
    }

    public function destroy(KasWarga $kasWarga)
    {
        $this->kasService->deleteKas($kasWarga);
        return redirect()->route('kas-warga.index')->with('success','Kas warga berhasil dihapus!');
    }

    public function generate(Generate $request)
    {
        $created = $this->kasService->generateKas($request->validated());
        return redirect()->route('kas-warga.index')->with('success',"Generate berhasil: $created record kas dibuat.");
    }

    public function wargaByRt(Request $request)
    {
        $rtId = $request->get('rt_id');
        if (!$rtId) return response()->json([]);
        $wargas = $this->kasService->getWargaByRt($rtId);
        return response()->json($wargas);
    }
}
