<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Aws\DynamoDb\DynamoDbClient;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->timestamp('email_verified_at')->nullable();
        //     $table->string('password');
        //     $table->rememberToken();
        //     $table->timestamps();
        // });

        $client = new DynamoDbClient([
            'region'   => env('AWS_DEFAULT_REGION', 'us-west-2'),
            'version'  => 'latest',
            'endpoint' => env('DYNAMODB_ENDPOINT', 'http://localhost:8000'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // テーブルを作成
        $client->createTable([
            'TableName' => 'users',
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'id',
                    'AttributeType' => 'S', // S = String
                ],
            ],
            'KeySchema' => [
                [
                    'AttributeName' => 'id',
                    'KeyType' => 'HASH', // Partition key
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits'  => 5,
                'WriteCapacityUnits' => 5,
            ],
        ]);

        echo "DynamoDB table 'users' created successfully.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('users');

        $client = new DynamoDbClient([
            'region'   => env('AWS_DEFAULT_REGION', 'us-west-2'),
            'version'  => 'latest',
            'endpoint' => env('DYNAMODB_ENDPOINT', 'http://localhost:8000'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // テーブルを削除
        $client->deleteTable([
            'TableName' => 'users',
        ]);

        echo "DynamoDB table 'users' deleted successfully.\n";
    }
};
