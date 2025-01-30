<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use BaoPham\DynamoDb\DynamoDbClientService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $dynamoDbClientService = resolve(DynamoDbClientService::class);
        $client = $dynamoDbClientService->getClient();

        $params = [
            'TableName' => 'Test',
            'KeySchema' => [
                [
                    'AttributeName' => 'id',
                    'KeyType' => 'HASH',
                ],
                [
                    'AttributeName' => 'sort_key',
                    'KeyType' => 'RANGE',
                ],
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'id',
                    'AttributeType' => 'N'
                ],
                [
                    'AttributeName' => 'sort_key',
                    'AttributeType' => 'N'
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 10,
                'WriteCapacityUnits' => 10,
            ],
        ];

        $client->createTable($params);

        // Schema::create('test', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dynamoDbClientService = resolve(DynamoDbClientService::class);
        $client = $dynamoDbClientService->getClient();

        $params = [
            'TableName' => 'Test',
        ];

        $client->deleteTable($params);

        // Schema::dropIfExists('test');
    }
};
