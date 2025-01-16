<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamoDbModel;
use App\Models\User;

class UserController extends Controller
{
    protected $dynamoDbModel;

    public function __construct()
    {
        $this->dynamoDbModel = new DynamoDbModel('Users');  // DynamoDB のテーブル名
    }

    // ユーザー情報を保存するアクション
    public function saveUser(Request $request)
    {
        // $item = [
        //     'id' => ['S' => $request->input('id')],
        //     'name' => ['S' => $request->input('name')],
        //     'age' => ['N' => (string)$request->input('age')],
        //     'email' => ['S' => $request->input('email')],
        // ];

        $user = User::create([
            'id' => ['S' => $request->input('id')],
            'name' => ['S' => $request->input('name')],
            'age' => ['N' => (string)$request->input('age')],
            'email' => ['S' => $request->input('email')],
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user]);

        // $isSaved = $this->dynamoDbModel->save($item);

        // if ($isSaved) {
        //     return response()->json(['message' => 'User data saved successfully']);
        // } else {
        //     return response()->json(['message' => 'Failed to save user data'], 500);
        // }
    }

    // ユーザー情報を取得するアクション
    public function getUser($id)
    {
        $key = ['id' => ['S' => $id]];  // プライマリキーに基づいてデータを取得
        $user = $this->dynamoDbModel->getItem($key);

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
