<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Task;
use JWTAuth;
use Validator;

class TaskController extends Controller
{
    protected $user;

    public function __construct(){
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(){
        return $this->user->tasks()
            ->get()->toArray();
    }

    //TODO: Return response if task is empty
    public function show(Request $request){
        return Task::Where('user_id',$this->user->id)
            ->Where('id',$request->route('id'))->get();
//        if($task){
//            return $task;
//        }
//        else{
//            return response()->json([
//                'message' => 'There is no task with this id'
//            ]);
//        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'date' => 'required|date',
            'done' => 'required|bool'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        //TODO: Prevent task from creation if any attribute is empty
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'date' => $request->input('date'),
            'done' => $request->input('done'),
            'user_id' => $request->user()->id,
        ]);

        if($task){
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Task could not be created'
            ], 500);
        }
    }

    public function update(Request $request){
        $task = Task::findOrFail($request->route('id'));
        $task -> update($request->all());

        return $task;
    }

    public function delete(Request $request,$id)
    {
        $task = Task::findOrFail($id);
        $task -> delete();

        return response()->json('Task deleted');
    }
}
