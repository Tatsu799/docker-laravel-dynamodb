<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use App\Models\DynamoDbModel;

class DynamoDbController extends Controller
{

    protected $dynamoDb;

    public function __construct()
    {
        $this->dynamoDb = new DynamoDbClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'endpoint' => env('AWS_DYNAMODB_ENDPOINT'), // DynamoDB Local
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }

    public function saveToDynamoDB(Request $request)
    {
        $tableName = 'TestTable'; // 使用するテーブル名
        $id = uniqid(); // 例としてユニークなIDを生成

        try {
            // データをDynamoDBに保存
            $this->dynamoDb->putItem([
                'TableName' => $tableName,
                'Item' => [
                    'id' => ['S' => $id],
                    'name' => ['S' => $request->input('name')],
                    'description' => ['S' => $request->input('description')],
                ]
            ]);

            return response()->json([
                'message' => 'Item successfully inserted',
                'id' => $id,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function insertItem(Request $request)
    {
        // リクエストからデータを取得
        $id = $request->input('id');
        $name = $request->input('name');

        // DynamoDbModel
        $dynamoDbItem = new DynamoDbModel();
        $result = $dynamoDbItem->insertItem($id, $name);

        // 成功した場合、レスポンスを返す
        return response()->json([
            'message' => 'Item inserted successfully',
            'data' => $result
        ]);
    }

    // テーブルを作成するメソッド
    public function createTable()
    {
        try {
            $result = $this->dynamoDb->createTable([
                'TableName' => 'TestTable',
                'KeySchema' => [
                    [
                        'AttributeName' => 'id',
                        'KeyType' => 'HASH',  // 主キー
                    ],
                ],
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S',  // 'S'は文字列型
                    ],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits'  => 5,
                    'WriteCapacityUnits' => 5,
                ],
            ]);

            return response()->json([
                'message' => 'Table created successfully',
                'data' => $result,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function listTables()
    {
        $client = $this->getClient();

        try {
            $result = $client->listTables();
            return response()->json([
                'message' => 'List of tables retrieved successfully',
                'tables'  => $result->get('TableNames'),
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getItems()
    {
        $tableName = 'TestTable'; // 取得するテーブル名

        try {
            // DynamoDBからデータをスキャンして取得
            $result = $this->dynamoDb->scan([
                'TableName' => $tableName,
            ]);

            // 結果を返却
            return response()->json([
                'message' => 'Data retrieved successfully',
                'data' => $result['Items'], // スキャン結果のデータ
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            // エラーハンドリング
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
