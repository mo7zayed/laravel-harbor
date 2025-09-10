<?php

use App\Actions\MergeEnvironmentVariables;

it('it can merge custom variables with source environment variables securely', function ($actual, $expected) {
    expect(
        MergeEnvironmentVariables::run(
            $actual['source'],
            $actual['content']
        )
    )
        ->toBe($expected);
})
    ->with([
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n\nAPP_KEY=\nAPP_ENV=local\n",
                'content' => [
                    'GOOGLE_API' => 'MY_API_KEY',
                    'APP_KEY' => 'APP_KEY_VALUE',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\nAPP_KEY=APP_KEY_VALUE\nAPP_ENV=local\n\nGOOGLE_API=MY_API_KEY\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n\nPUSHER_APP_ID=\n\nAPP_ENV=local\n",
                'content' => [
                    'GOOGLE_API' => 'MY_API_KEY',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\nPUSHER_APP_ID=\n\nAPP_ENV=local\n\nGOOGLE_API=MY_API_KEY\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n\n\n",
                'content' => [
                    'GOOGLE_API' => 'MY_API_KEY',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\n\n\nGOOGLE_API=MY_API_KEY\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n\n",
                'content' => [
                    'APP_NAME' => 'Project Name',
                ],
            ],
            'expected' => "APP_NAME=Project Name\n\n\n",
        ],
        [
            'actual' => [
                'source' => "=Laravel\n\n",
                'content' => [
                    'APP_KEY' => 'APP_KEY_VALUE',
                ],
            ],
            'expected' => "\n\nAPP_KEY=APP_KEY_VALUE\n",
        ],
        [
            'actual' => [
                'source' => '',
                'content' => [
                    'APP_NAME' => 'Project Name',
                ],
            ],
            'expected' => "APP_NAME=Project Name\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n# Here be dragons\nAPP_ENV=local\n",
                'content' => [],
            ],
            'expected' => "APP_NAME=Laravel\n# Here be dragons\nAPP_ENV=local\n\n",
        ],
        // Test cases for GitHub issue #127 - handling empty lines and malformed entries
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n \n\t\nAPP_ENV=local\n",
                'content' => [
                    'APP_ENV' => 'staging',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\n\nAPP_ENV=staging\n\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\nJUST_TEXT_NO_EQUALS\nAPP_ENV=local\n",
                'content' => [
                    'APP_ENV' => 'staging',
                ],
            ],
            'expected' => "APP_NAME=Laravel\nAPP_ENV=staging\n\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n\n\n\nAPP_ENV=local\n",
                'content' => [
                    'NEW_VAR' => 'new_value',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\n\n\nAPP_ENV=local\n\nNEW_VAR=new_value\n",
        ],
        [
            'actual' => [
                'source' => "APP_NAME=Laravel\n   \n\t\t\n  \t  \nAPP_ENV=local\n",
                'content' => [
                    'APP_ENV' => 'production',
                ],
            ],
            'expected' => "APP_NAME=Laravel\n\n\n\nAPP_ENV=production\n\n",
        ],
    ]);
