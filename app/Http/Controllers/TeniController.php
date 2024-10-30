<?php

namespace App\Http\Controllers;

use App\Models\Teni;
use Illuminate\Http\Request;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TeniController extends Controller
{
    public function index()
    {
        $tenis = Teni::select('tenis.*', 'marcas.marca as marca')
            ->join('marcas', 'marcas.id', '=', 'tenis.marca_id')
            ->paginate(10);
        return response()->json($tenis);
    }

    public function store(Request $request)
    {
        $rules = [
            'color' => 'required|string|max:50',              
            'marca_id' => 'required|exists:marcas,id',      
            'costo' => 'required|numeric|min:0|max:999.99', 
            'talla' => 'required|string|max:10',           
            'categoria' => 'required|string|max:100',      
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }
    
        // Subir la imagen y cambiar el nombre
        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            $imageName = now()->format('Ymd_His') . '.' . $image->getClientOriginalExtension();
            $image->storeAs('imagenes_tenis', $imageName, 'public');
        }
    
        // Guardar el teni con la ruta de la imagen
        $teni = new Teni($request->all());
        $teni->imagen = 'imagenes_tenis/' . $imageName;
        $teni->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Teni creado exitosamente',
            'data' => $teni
        ], 200);
    }
    

    public function show($id)
    {
        try {
            $teni = Teni::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $teni
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'El teni no existe.'
            ], 400);
        }
    }

    public function update(Request $request, Teni $teni)
    {
        // Validar los campos de la solicitud
        $rules = [
            'color' => 'required|string|max:50',
            'marca_id' => 'required|exists:marcas,id',
            'costo' => 'required|numeric|min:0|max:999.99',
            'talla' => 'required|string|max:10',
            'categoria' => 'required|string|max:100',
            'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048' // Imagen opcional
        ];
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }
    
        // Si se sube una nueva imagen, manejar la eliminación de la anterior y subir la nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($teni->imagen && Storage::exists('public/' . $teni->imagen)) {
                Storage::delete('public/' . $teni->imagen);
            }
    
            // Subir la nueva imagen
            $image = $request->file('imagen');
            $imageName = now()->format('Ymd_His') . '.' . $image->getClientOriginalExtension();
            $image->storeAs('imagenes_tenis', $imageName, 'public');
    
            // Asignar la nueva ruta de la imagen al modelo
            $teni->imagen = 'imagenes_tenis/' . $imageName;
        }
    
        // Actualizar todos los campos del modelo, incluyendo la ruta de la imagen si ha cambiado
        $teni->color = $request->input('color');
        $teni->marca_id = $request->input('marca_id');
        $teni->costo = $request->input('costo');
        $teni->talla = $request->input('talla');
        $teni->categoria = $request->input('categoria');
        $teni->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Teni actualizado exitosamente',
            'data' => $teni
        ], 200);
    }
    

    public function destroy(Teni $teni)
    {
        // Eliminar la imagen asociada
        if (Storage::exists('public/' . $teni->imagen)) {
            Storage::delete('public/' . $teni->imagen);
        }

        // Eliminar el teni
        $teni->delete();

        return response()->json([
            'status' => true,
            'message' => 'Teni eliminado exitosamente'
        ], 200);
    }

    public function TenisByMarca()
    {
        $tenis = Teni::select(DB::raw('count(tenis.id) as count, marcas.marca'))
            ->rightJoin('marcas', 'marcas.id', '=', 'tenis.marca_id')
            ->groupBy('marcas.marca')
            ->get();
        return response()->json($tenis);
    }

    public function all()
    {
        $tenis = Teni::select('tenis.*', 'marcas.marca as marca')
            ->join('marcas', 'marcas.id', '=', 'tenis.marca_id')
            ->get();
        return response()->json($tenis);
    }
}
