<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use App\Models\DynamoDbModel;

class DynamoDbController extends Controller
{

    protected $dynamoDb;
    private $tableName = 'TestTable';

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
        // $id = $request->input('id');
        // $name = $request->input('name');

        // // DynamoDbModel
        // $dynamoDbItem = new DynamoDbModel();
        // $result = $dynamoDbItem->insertItem($id, $name);

        // // 成功した場合、レスポンスを返す
        // return response()->json([
        //     'message' => 'Item inserted successfully',
        //     'data' => $result
        // ]);
        try {
            $result = $this->dynamoDb->putItem([
                'TableName' => 'TestTable',
                'Item' => [
                    'store_id' => ['S' => 'store123'], // パーティションキー
                    'order_id' => ['S' => 'order456'], // ソートキー
                    'remark' => ['S' => 'This is a sample remark.'], // 任意のフィールド
                    'created_at' => ['S' => now()->toIso8601String()],
                ],
            ]);

            return response()->json(['message' => 'Data inserted successfully.', 'details' => $result], 200);
        } catch (DynamoDbException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // テーブルを作成するメソッド
    public function createTable()
    {
        try {
            $result = $this->dynamoDb->createTable([
                'TableName' => 'TestTable',
                'KeySchema' => [
                    ['AttributeName' => 'store_id', 'KeyType' => 'HASH'], // パーティションキー
                    ['AttributeName' => 'order_id', 'KeyType' => 'RANGE'], // ソートキー（オプション）
                ],
                'AttributeDefinitions' => [
                    ['AttributeName' => 'store_id', 'AttributeType' => 'S'], // 文字列型
                    ['AttributeName' => 'order_id', 'AttributeType' => 'S'], // 文字列型
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 5,
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

    public function updateRemark(Request $request)
    {
        $request->validate([
            'store_id' => 'required|string',
            'order_id' => 'required|string',
            'remark' => 'required|array',
            'remark.*.name' => 'nullable|string|max:255',
            'remark.*.info' => 'nullable|string|max:255',
        ]);

        $storeId = $request->input('store_id');
        $orderId = $request->input('order_id');
        $remark = $request->input('remark');

        try {
            $this->dynamoDb->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'store_id' => ['S' => $storeId],
                    'order_id' => ['S' => $orderId],
                ],
                'UpdateExpression' => 'SET remark = :remark',
                'ExpressionAttributeValues' => [
                    ':remark' => ['S' => json_encode($remark)],
                ]
            ]);

            return response()->json(
                [
                    'message' => 'Remark update successfully.',
                    'body' =>
                    [
                        true,
                    ]
                ],
                200
            );
        } catch (DynamoDbException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        };
    }
}
