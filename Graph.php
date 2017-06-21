<?php
require_once __DIR__ . '/vendor/autoload.php';
$nonDirectedGraph = new Structures_Graph(false);
$nodes_names = array('a', 'b', 'c' ,'d', 'e');
$nodes = array();
foreach($nodes_names as $node) {
    /* Create a new node / vertex */
    $nodes[$node] = new Structures_Graph_Node();
    /* Add the node to the Graph structure */
    $nonDirectedGraph ->addNode($nodes[$node]);
}
/**
  * Specify connections between different nodes.
  * For example in the following array, 'a-b'
  * specifies that node 'a' is connected to node 'b'.
  * Also refer to the figure above.
  */
$vertices = array('a-b', 'b-c', 'b-d', 'd-c', 'c-e', 'e-d');
foreach($vertices as $vertex) {
    $data = preg_split("/-/",$vertex);
    $nodes[$data[0]]->connectTo($nodes[$data[1]]);
}
$nodes['b']->setData("http://www.google.com");
echo $nodes['b']->getData();
/* Store edge weight as a metadata */
$nodes['a']->setMetadata('node weight', 56);
/* Now get the metadata */
echo $nodes['a']->getMetadata('node weight'); // returns 56
echo $nodes['b']->inDegree(); // returns 3
echo $nodes['b']->outDegree(); // returns 3
$connected_nodes = $nodes['b']->getNeighbours();
echo count($connected_nodes); // returns 3
$gNodes = $nonDirectedGraph ->getNodes();
foreach($gNodes as $node) {
    echo $node->getData();
}
