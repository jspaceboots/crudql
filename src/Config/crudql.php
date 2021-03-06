<?php

return [
    'limit' => 25,
    'relationDepthLimit' => 20,
    'interfaces' => [
        'html' => [
            'enabled' => true,
            'middleware' => ['auth']
        ],
        'graphql' => [
            'enabled' => true,
            'middleware' => ['auth:api']
        ]
    ],
    'overrideAuthViews' => true,
    'useSubnav' => false,
    'namespaces' => [
        'models' => '\\App\\Models',
        'queries' => '\\App\\GraphQL\\Queries',
        'mutations' => '\\App\\GraphQL\\Mutations',
        'types' => '\\App\\GraphQL\\Types',
        'transformers' => '\\App\\Transformers',
        'repositories' => '\\App\\Repositories',
        'unittests' => '\\Tests\\Unit',
        'integrationtests' => '\\Tests\\Integration'
    ],
    'tableNormalizationExceptions' => [],
    'fieldTypes' => [
        'string',
        'bigIncrements',
        'bigInteger',
        'binary',
        'boolean',
        'char',
        'date',
        'dateTime',
        'dateTimeTz',
        'decimal',
        'double',
        'enum',
        'float',
        'geometry',
        'geometryCollection',
        'increments',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'lineString',
        'longText',
        'macAddress',
        'mediumIncrements',
        'mediumInteger',
        'mediumText',
        'morphs',
        'multiLineString',
        'multiPoint',
        'multiPolygon',
        'nullableMorphs',
        'nullableTimestamps',
        'point',
        'polygon',
        'rememberToken',
        'smallIncrements',
        'smallInteger',
        'softDeletes',
        'softDeletesTz',
        'text',
        'time',
        'timeTz',
        'timestamp',
        'timestamps',
        'timestampsTz',
        'tinyIncrements',
        'tinyInteger',
        'unsignedBigInteger',
        'unsignedDecimal',
        'unsignedInteger',
        'unsignedMediumInteger',
        'unsignedSmallInteger',
        'unsignedTinyInteger',
        'uuid',
        'year'
    ],
    'validators' => [
        'accepted',
        'active_url',
        //'after:date',
        //'after_or_equal:date',
        'alpha',
        'alpha_dash',
        'alpha_num',
        'array',
        //'before:date',
        //'before_or_equal:date',
        //'between:min,max',
        'boolean',
        'confirmed',
        'date',
        //'date_equals:date',
        //'date_format:format',
        //'different:field',
        //'digits:value',
        //'digits_between:min,max',
        'dimensions',
        'distinct',
        'email',
        //'exists:table,column',
        'file',
        'filled',
        'image',
        //'in:foo,bar,...',
        //'in_array:anotherfield',
        'integer',
        'ip',
        'ipv4',
        'ipv6',
        'json',
        //'max:value',
        //'mimetypes:text/plain,...',
        //'mimes:foo,bar,...',
        //'min:value',
        'nullable',
        //'not_in:foo,bar,...',
        'numeric',
        'present',
        //'regex:pattern',
        'required',
        //'required_if:anotherfield,value,...',
        //'required_unless:anotherfield,value,...',
        //'required_with:foo,bar,...',
        //'required_with_all:foo,bar,...',
        //'required_without:foo,bar,...',
        //'required_without_all:foo,bar,...',
        //'same:field',
        //'size:value',
        'string',
        'timezone',
        //'unique:table,column,except,idColumn',
        'url'
    ],
    // Maps a GraphQL scalar type to DB types
    'dbToQlTypeMap' => [
        'Int' => [
            'integer',
            'int',
            'smallint',
            'tinyint',
            'mediumint',
            'bigint'
        ],
        'String' => [
            'varchar',
            'char'
        ],
        'Float' => [
            'float',
            'double'
        ],
        'Boolean' => [
            'boolean'
        ],
        'ID' => [
            'varchar'
        ]
    ],
    // Hooks will pass two arguments to a function of your choice: action, entity
    // Action follows the format EntitynameAction (PostCreated, MediaEdited, PostTagDeleted, etc)
    // Specify hook functions using the fully qualified classname + @function, examples:
    //  - "\\App\\Whatever\\MyClass@functionName"
    //  - \App\Whatever\MyClass::class . '@functionName'
    'createHook' => null,
    'editHook' => null,
    'deleteHook' => null
];