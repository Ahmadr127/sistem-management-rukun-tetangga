<?php
namespace App\Http\Controllers;
use App\Http\Requests\AlamatRt\Store;
use App\Http\Requests\AlamatRt\Update;
use App\Http\Services\AlamatRtService;
use App\Models\AlamatRt;
use App\Models\Rt;
use Illuminate\Http\Request;
class AlamatRtController extends Controller
{
    public function __construct(protected AlamatRtService $service) {}
    public function index(Request $request) {
        $alamats = $this->service->getAlamat($request->only(['search','per_page']));
        $rts = Rt::orderBy('kode_rt')->get();
        // also show RT without alamat for quick action
        $rtWithoutAlamat = Rt::whereDoesntHave('alamatRt')->get();
        return view('alamat-rt.index', compact('alamats','rts','rtWithoutAlamat'));
    }
    public function create() {
        $rts = Rt::whereDoesntHave('alamatRt')->orderBy('kode_rt')->get();
        // if all RT have alamat, show all for superadmin to choose (but unique constraint will block)
        if ($rts->isEmpty()) $rts = Rt::orderBy('kode_rt')->get();
        return view('alamat-rt.create', compact('rts'));
    }
    public function store(Store $request) {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $this->service->createAlamat($data);
        return redirect()->route('alamat-rt.index')->with('success','Alamat RT berhasil ditambahkan!');
    }
    public function edit(AlamatRt $alamatRt) {
        $rts = Rt::orderBy('kode_rt')->get();
        return view('alamat-rt.edit', compact('alamatRt','rts'));
    }
    public function update(Update $request, AlamatRt $alamatRt) {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $this->service->updateAlamat($alamatRt, $data);
        return redirect()->route('alamat-rt.index')->with('success','Alamat RT berhasil diperbarui!');
    }
    public function destroy(AlamatRt $alamatRt) {
        $this->service->deleteAlamat($alamatRt);
        return redirect()->route('alamat-rt.index')->with('success','Alamat RT berhasil dihapus!');
    }
    // API
    public function apiShow(Rt $rt) {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && (int)$user->rt_id !== (int)$rt->id) {
            return response()->json(['message'=>'Forbidden: bukan RT Anda'], 403);
        }
        $alamat = $rt->alamatRt;
        if (!$alamat) return response()->json(['message'=>'Alamat RT belum dikonfigurasi','rt_id'=>$rt->id,'rt'=>$rt->kode_rt], 404);
        return response()->json($alamat->toApiArray());
    }
}
