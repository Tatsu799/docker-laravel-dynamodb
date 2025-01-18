<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

class CreateDynamoDbTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-dynamo-db-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $table = 'orders';
        $connection = config('dynamodb.default');
        $config = config("dynamodb.connections.$connection");

        $client = new DynamoDbClient($config);

        try {
            $client->createTable([
                'TableName' => $table,
                'AttributeDefinitions' => [
                    ['AttributeName' => 'store_id', 'AttributeType' => 'S'],
                ],
                'KeySchema' => [
                    ['AttributeName' => 'store_id', 'KeyType' => 'HASH'],
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => 1,
                    'WriteCapacityUnits' => 1,
                ],
            ]);

            $this->info("Table '$table' created successfully.");

            // return response()->json(['massage' => 'success', 'body' => [true]]);
            $this->info('success');
        } catch (DynamoDbException $e) {
            // return response()->json(['massage' => 'Error', 'body' => [$e->getMessage(), $e->getCode()]]);
            $this->info('error');
        }
    }
}
