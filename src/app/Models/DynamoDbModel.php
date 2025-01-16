<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Credentials\Credentials;

class DynamoDbModel extends Model
{
    protected $table = 'TestTable';  // DynamoDBのテーブル名

    // DynamoDBクライアントの設定
    private function getClient()
    {
        $credentials = new Credentials(
            env('AWS_ACCESS_KEY_ID'),      // アクセスキー
            env('AWS_SECRET_ACCESS_KEY')   // シークレットキー
        );

        return new DynamoDbClient([
            'region'   => env('AWS_DEFAULT_REGION', 'us-east-1'),  // デフォルトのリージョンを指定
            'version'  => 'latest',
            'credentials' => $credentials,  // 認証情報をCredentialsオブジェクトで渡す
            'endpoint' => env('AWS_DYNAMODB_ENDPOINT'),  // DynamoDB Localのエンドポイント
        ]);
    }

    // データ挿入用メソッド
    public function insertItem($id, $name)
    {
        $client = $this->getClient();

        $result = $client->putItem([
            'TableName' => $this->table,
            'Item' => [
                'id' => ['S' => $id],
                'name' => ['S' => $name],
            ],
        ]);

        return $result;
    }
}
