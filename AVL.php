<?php
// AVL tree
class Node {
    public $value;
    public $left;
    public $right;
    public $height;
    function __construct($value, Node $left = null, Node $right = null, $height = 1) {
        $this->value = $value;
        $this->left = $left;
        $this->right = $right;
        $this->height = $height;
    }
}
function node($value, Node $left = null, Node $right = null, $height = 1) {
    return new Node($value, $left, $right, $height);
}
function node_height(Node $node = null) {
    return $node ? $node->height : 0;
}
function balance_factor(Node $node) {
    return node_height($node->left) - node_height($node->right);
}
function rotate_left(Node $node) {
    $a = $node->left;
    $b = $node->right->left;
    $c = $node->right->right;
    $height = max(node_height($a), node_height($b)) + 1;
    return node(
        $node->right->value,
        node($node->value, $a, $b, $height),
        $c,
        max($height, node_height($c)) + 1
    );
}
function rotate_right(Node $node) {
    $b = $node->left->left;
    $c = $node->left->right;
    $d = $node->right;
    $height = max(node_height($c), node_height($d)) + 1;
    return node(
        $node->left->value,
        $b,
        node($node->value, $c, $d, $height),
        max($height, node_height($b)) + 1
    );
}
function replace_left(Node $node, Node $new_left = null, $height = null) {
    return node(
        $node->value,
        $new_left,
        $node->right,
        $height ?: $node->height
    );
}
function replace_right(Node $node, Node $new_right = null, $height = null) {
    return node(
        $node->value,
        $node->left,
        $new_right,
        $height ?: $node->height
    );
}
function retrace(Node $node) {
    $balance_factor = balance_factor($node);
    $balance_factor_left = $node->left ? balance_factor($node->left) : 0;
    $balance_factor_right = $node->right ? balance_factor($node->right) : 0;
    if ($balance_factor === 2) {
        if ($balance_factor_left === -1) {
            $node = replace_left($node, rotate_left($node->left));
            return rotate_right($node);
        }
        if ($balance_factor_left === 1) {
            return rotate_right($node);
        }
    }
    if ($balance_factor === -2) {
        if ($balance_factor_right === 1) {
            $node = replace_right($node, rotate_right($node->right));
            return rotate_left($node);
        }
        if ($balance_factor_right === -1) {
            return rotate_left($node);
        }
    }
    return $node;
}
function insert($root, $value) {
    if (is_null($root)) {
        return node($value);
    }
    if ($value < $root->value) {
        $new_left = insert($root->left, $value);
        return retrace(replace_left(
            $root,
            $new_left,
            max(node_height($new_left), node_height($root->right)) + 1
        ));
    }
    $new_right = insert($root->right, $value);
    return retrace(replace_right(
        $root,
        $new_right,
        max(node_height($root->left), node_height($new_right)) + 1
    ));
}
// var_dump(node(5));
// var_dump(insert(node(5), 6));
// var_dump(insert(insert(node(5), 6), 7));
// var_dump(insert(insert(insert(node(5), 6), 7), 2));
// $node = node(0);
// foreach (range(1, 100000) as $i) {
//     if ($i % 2 == 0) {
//         $node = insert($node, $i);
//     }
// }
// foreach (range(1, 100000) as $i) {
//     if ($i % 2 == 1) {
//         $node = insert($node, $i);
//     }
// }
// var_dump($node->height);
$node = node(0);
foreach (range(1, 100000) as $i) {
    $node = insert($node, $i);
}
var_dump($node->height);
