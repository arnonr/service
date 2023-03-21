<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopicController extends Controller
{
    public function getAll(Request $request)
    {
        $items = Topic::select(
            'id as id',
            'name as name',
            'color as color',
            'is_publish as is_publish',
            'deleted_at as delete_at',
            'created_at as created_at',
            'created_by as created_by',
            'updated_at as updated_at',
            'updated_by as updated_by',
        )
        ->where('deleted_at', null);
        

        if ($request->id) {
            $items->where('id',$request->id);
        }

        if ($request->name) {
            $items->where('name','LIKE',"%".$request->name."%");
        }

        if ($request->is_publish) {
            $items->where('is_publish',$request->is_publish);
        }

        $order_by = $request->order_by ? $request->order_by : 'id';
        $order = $request->order ? $request->order : 'asc';

        $items = $items->orderBy($order_by, $order);
            
        $items = $items->get();

        return response()->json([
            'message' => 'success',
            'data' => $items,
        ], 200);
    }

    public function get($id)
    {
        // User DB
        $item = Topic::select(
            'id as id',
            'name as name',
            'color as color',
            'is_publish as is_publish',
            'deleted_at as delete_at',
            'created_at as created_at',
            'created_by as created_by',
            'updated_at as updated_at',
            'updated_by as updated_by',
        )
        ->where('id', $id)
        ->first();
        
        return response()->json([
            'message' => 'success',
            'data' => $item,
        ], 200);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name as required',
        ]);

        $item = new Topic;
        $item->name = $request->name;
        $item->color = $request->color;
        $item->is_publish = $request->is_publish;
        $item->created_by = 'arnonr';
        $item->save();

        $responseData = [
            'message' => 'success',
            'data' => $item,
        ];
        
        return response()->json($responseData, 200);
    }

    public function edit($id, Request $request)
    {
        $request->validate([
            'id as required',
            'name as required',
        ]);

        $id = $request->id;
        // $name = $request->name;

        $item = Topic::where('id', $id)->first();

        $item->name = $request->name;
        $item->color = $request->color;
        $item->is_publish = $request->is_publish;
        $item->updated_by = 'arnonr';
        $item->save();

        $responseData = [
            'message' => 'success',
            'data' => $item,
        ];
        
        return response()->json($responseData, 200);
    }

    public function delete($id)
    {
        $item = Topic::where('id', $id)->first();

        $item->deleted_at = Carbon::now();
        $item->save();

        $responseData = [
            'message' => 'success'
        ];

        return response()->json($responseData, 200);
    }
}
