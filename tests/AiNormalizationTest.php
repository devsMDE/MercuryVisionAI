<?php
declare(strict_types=1);

define('PHPUNIT_RUNNING', true);

// Mock functions required by ai.php if needed (none seem strictly required for the target functions)
function json_err($msg, $code) { throw new Exception("json_err: $msg ($code)"); }
function json_ok($data) { echo "json_ok called\n"; }
function env($key, $default = null) { return $default; }

require_once __DIR__ . '/../api/ai.php';

function assertEquals($expected, $actual, $message = '') {
    if ($expected !== $actual) {
        $expectedStr = json_encode($expected);
        $actualStr = json_encode($actual);
        throw new Exception("Assertion failed: $message\nExpected: $expectedStr\nActual: $actualStr");
    }
}

function test_normalize_string_list() {
    echo "Running test_normalize_string_list...\n";

    // Happy path
    assertEquals(['a', 'b'], normalize_string_list(['a', 'b']), "Basic string list");

    // Empty
    assertEquals([], normalize_string_list([]), "Empty array");

    // Non-array
    assertEquals([], normalize_string_list(null), "Null input");
    assertEquals([], normalize_string_list("string"), "String input");

    // Numeric values
    assertEquals(['1', '2.5'], normalize_string_list([1, 2.5]), "Numeric values should be cast to string");

    // Nested arrays (Edge case: potential warning)
    // Currently (string)['a'] in PHP results in "Array to string conversion" warning and returns "Array"
    // We want to see if it triggers warning or if we should handle it better
    // assertEquals(['Array'], normalize_string_list([['sub']]), "Nested array handling");
}

function test_normalize_problems() {
    echo "Running test_normalize_problems...\n";

    // Happy path
    $input = [
        ['id' => 'p1', 'title' => 'Title 1', 'severity' => 'high', 'description' => 'Desc 1']
    ];
    $expected = [
        ['id' => 'p1', 'title' => 'Title 1', 'severity' => 'high', 'description' => 'Desc 1']
    ];
    assertEquals($expected, normalize_problems($input), "Basic problem");

    // Missing ID
    $input = [['title' => 'No ID']];
    $output = normalize_problems($input);
    assertEquals('problem_1', $output[0]['id'], "Auto-generated ID");

    // Invalid severity
    $input = [['severity' => 'ultra-high']];
    $output = normalize_problems($input);
    assertEquals('medium', $output[0]['severity'], "Fallback severity");

    // Non-array item
    $input = ['not an array'];
    assertEquals([], normalize_problems($input), "Skip non-array items");
}

function test_normalize_solutions_by_problem() {
    echo "Running test_normalize_solutions_by_problem...\n";

    // Happy path
    $input = [
        'p1' => [
            [
                'title' => 'Sol 1',
                'description' => 'Desc 1',
                'pros' => ['Pro 1'],
                'cons' => ['Con 1']
            ]
        ]
    ];
    $expected = [
        'p1' => [
            [
                'title' => 'Sol 1',
                'description' => 'Desc 1',
                'pros' => ['Pro 1'],
                'cons' => ['Con 1']
            ]
        ]
    ];
    assertEquals($expected, normalize_solutions_by_problem($input), "Basic solution mapping");

    // Edge case: Non-array solutions
    $input = ['p1' => 'not an array'];
    assertEquals([], normalize_solutions_by_problem($input), "Skip non-array solution lists");

    // Edge case: Mixed content
    $input = [
        'p1' => [
            'not an array',
            ['title' => 'Valid']
        ]
    ];
    $output = normalize_solutions_by_problem($input);
    assertEquals(1, count($output['p1']), "Only keep valid solution objects");
    assertEquals('Valid', $output['p1'][0]['title'], "Keep valid solution title");
    assertEquals([], $output['p1'][0]['pros'], "Empty pros if missing");

    // Edge case: Nested arrays in pros/cons (Edge case for normalize_string_list)
    $input = [
        'p1' => [
            [
                'title' => 'Sol',
                'pros' => [['nested']]
            ]
        ]
    ];
    // This previously triggered a warning
    $output = normalize_solutions_by_problem($input);
    assertEquals(['["nested"]'], $output['p1'][0]['pros'], "Nested array in pros JSON encoded");
}

try {
    test_normalize_string_list();
    test_normalize_problems();
    test_normalize_solutions_by_problem();
    echo "\nALL TESTS PASSED!\n";
} catch (Exception $e) {
    echo "\nTEST FAILED!\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
