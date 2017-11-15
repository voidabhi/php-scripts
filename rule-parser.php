use nicoSWD\Rule\Rule;

// Composer install
require '/path/to/vendor/autoload.php';

$ruleStr = '
    2 < 3 && (
        // False
        foo in [4, 6, 7] ||
        // True
        [1, 4, 3].join("") === "143" &&
        // True
        "bar" in "foo bar".split(" ") &&
        // True
        "foo bar".substr(4) === "bar"
    ) && (
        // True
        "foo|bar|baz".split("|") === ["foo", /* what */ "bar", "baz"] &&
        // True
        bar > 6
    )';

$variables = [
    'foo' => 'abc',
    'bar' => 321,
    'baz' => 123
];

$rule = new Rule($ruleStr, $variables);

var_dump($rule->isTrue()); // bool(true)
